"""
LangChain curriculum activity generation chain.

Flow:
  1. PageIndex retriever checks existing activities to avoid duplicates
  2. Prompt template builds a structured generation request
  3. Claude generates JSON activity data
  4. JsonOutputParser parses and validates
"""

from __future__ import annotations

import os
from typing import Any

from langchain_anthropic import ChatAnthropic
from langchain_core.output_parsers import JsonOutputParser
from langchain_core.prompts import ChatPromptTemplate

from retrieval.pageindex_retriever import get_existing_activity_summaries

# ---------------------------------------------------------------------------
# LLM — Claude via Anthropic
# ---------------------------------------------------------------------------
_llm = ChatAnthropic(
    model="claude-sonnet-4-6",
    max_tokens=2048,
    temperature=0.7,
    api_key=os.environ.get("ANTHROPIC_API_KEY"),
)

# ---------------------------------------------------------------------------
# Prompt
# ---------------------------------------------------------------------------
_SYSTEM = """You are an expert curriculum designer for an early-childhood online academy.

EXISTING activities already in the curriculum for this age tier and subject (DO NOT duplicate these):
{existing_context}

Your task: design ONE brand-new, age-appropriate curriculum activity that is DISTINCT from the list above.

STRICT JSON output format:
{{
  "title": "short activity name (max 8 words)",
  "description": "1-2 sentence overview for parents",
  "instructions": "step-by-step guide for the child (3-6 steps)",
  "materials": ["item1", "item2"],
  "duration_minutes": <integer 5-45>,
  "difficulty": "easy" | "medium" | "hard",
  "age_tier": "{age_tier}",
  "subject": "{subject}",
  "language": "{language}",
  "is_free": {is_free_json}
}}

Rules:
- age_tier {age_tier} means the child is approximately that many years old
- Keep language {language}; use culturally appropriate examples
- difficulty: easy = 4-6 yr old unaided, medium = needs some help, hard = parent-guided
- Return ONLY the JSON object — no markdown, no commentary
"""

_prompt = ChatPromptTemplate.from_messages([
    ("system", _SYSTEM),
    ("human", "Generate a {subject} activity for age tier {age_tier}, language {language}."),
])

# ---------------------------------------------------------------------------
# Chain
# ---------------------------------------------------------------------------
_chain = _prompt | _llm | JsonOutputParser()


# ---------------------------------------------------------------------------
# Public API
# ---------------------------------------------------------------------------

async def generate_batch(
    subject: str,
    age_group: str,
    language: str,
    is_free: bool,
    count: int,
) -> dict[str, Any]:
    """
    Generate `count` activities. Each call is independent so PageIndex
    coherence context is refreshed between them.
    """
    existing_context = get_existing_activity_summaries(age_group, subject)
    activities = []

    for _ in range(count):
        result = await _chain.ainvoke({
            "existing_context": existing_context or "None yet — this is the first activity.",
            "age_tier": age_group,
            "subject": subject,
            "language": language,
            "is_free_json": "true" if is_free else "false",
        })

        # Add required fields if model omitted them
        result.setdefault("age_group", age_group)
        result.setdefault("subject", subject)
        result.setdefault("language", language)
        result.setdefault("is_free", is_free)
        result.setdefault("materials", [])

        activities.append(result)
        # Update context so the next iteration in the same batch avoids
        # the activity we just generated
        existing_context = (existing_context or "") + f"\n- {result['title']}"

    return {
        "activities": activities,
        "model": "claude-sonnet-4-6",
        "usage_tokens": 0,   # Anthropic SDK v3 doesn't surface token count here easily
    }
