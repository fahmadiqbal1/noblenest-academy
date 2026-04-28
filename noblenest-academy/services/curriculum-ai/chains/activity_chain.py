"""
LangChain curriculum activity generation chain with Phase 2 metadata.

Flow:
  1. PageIndex retriever checks existing activities, includes cognitive_domain coverage context
  2. Prompt template builds a structured generation request for ALL Phase 2 fields
  3. Claude generates JSON activity data
  4. Pydantic model validates output
  5. Return validated ActivityPayload
"""

from __future__ import annotations

import os
import json
from typing import Any

from langchain_anthropic import ChatAnthropic
from langchain_core.output_parsers import PydanticOutputParser
from langchain_core.prompts import ChatPromptTemplate

from models.activity_model import ActivityPayload
from retrieval.pageindex_retriever import get_existing_activity_summaries_enriched

# ---------------------------------------------------------------------------
# LLM — Claude via Anthropic
# ---------------------------------------------------------------------------
_llm = ChatAnthropic(
    model_name="claude-sonnet-4-6",  # type: ignore[call-arg]
    max_tokens=2048,  # type: ignore[call-arg]
    temperature=0.7,  # type: ignore[call-arg]
    api_key=os.environ.get("ANTHROPIC_API_KEY"),  # type: ignore[call-arg]
)

# ---------------------------------------------------------------------------
# Output Parser with Pydantic Validation
# ---------------------------------------------------------------------------
_parser = PydanticOutputParser(pydantic_object=ActivityPayload)

# ---------------------------------------------------------------------------
# Prompt with Full Phase 2 Schema
# ---------------------------------------------------------------------------
_SYSTEM = """You are an expert curriculum designer for an early-childhood online academy.
You are designing activities that help children grow across multiple developmental domains.

EXISTING activities already in the curriculum for {age_tier}/{subject} (DO NOT duplicate these):
{existing_context}

COGNITIVE DOMAIN COVERAGE (aim for variety):
{cognitive_domain_summary}

Your task: design ONE brand-new, age-appropriate curriculum activity that is DISTINCT from the existing list and fills a gap in cognitive domain coverage.

You must emit a COMPLETE JSON activity with ALL required fields below.

Required JSON structure:
{{
  "title": "short activity name (max 8 words)",
  "description": "1-2 sentence overview for parents",
  "instructions": "step-by-step guide for the child (3-6 steps)",
  "materials": ["item1", "item2"],
  "duration_minutes": <integer 5-60>,
  "difficulty": "easy" | "medium" | "hard",
  "age_tier": "{age_tier}",
  "subject": "{subject}",
  "language": "{language}",
  "is_free": {is_free_json},
  "mess_level": "low" | "medium" | "high",
  "safety_warnings": ["choking_hazard", "heat_source", etc.],
  "adaptations": {{
    "easier": "How to make this easier (10+ words)",
    "harder": "How to make this harder (10+ words)"
  }},
  "cognitive_domain": "{suggested_cognitive_domain}",
  "developmental_domains": ["gross_motor", "language", etc.],
  "materials_cost": <0-100 cents>,
  "parent_involvement": "minimal" | "moderate" | "high",
  "instructions_for_parent": "Guidance for parents (20+ words)"
}}

RULES:
- age_tier {age_tier} is {age_description}
- Keep language {language}; use culturally appropriate examples
- difficulty: easy = self-directed, medium = needs guidance, hard = parent-led
- mess_level: assess realistically (paint = high, blocks = low)
- safety_warnings: only include ACTUAL hazards for this age
- adaptations: must be concrete and actionable
- cognitive_domain: choose wisely to balance existing coverage (see summary above)
- developmental_domains: list 1-5 secondary domains this activity addresses
- materials_cost: sum of estimated costs (0 = all household items, 100 = $1 max)
- parent_involvement: realistic assessment of parent participation needed
- instructions_for_parent: be specific about how parent facilitates learning

Return ONLY valid JSON. No markdown, no commentary, no additional text."""

_prompt = ChatPromptTemplate.from_messages(  # type: ignore[misc]
    [
        ("system", _SYSTEM),
        (
            "human",
            "Generate a {subject} activity for age tier {age_tier}, "
            "language {language}, cognitive domain preferably {target_cognitive_domain}. "
            "Return valid JSON only."
        ),
    ]
)

# ---------------------------------------------------------------------------
# Chain with Pydantic validation
# ---------------------------------------------------------------------------
_chain: Any = _prompt | _llm | _parser  # type: ignore[misc]


# ---------------------------------------------------------------------------
# Public API
# ---------------------------------------------------------------------------

async def generate_batch(
    subject: str,
    age_group: str,
    language: str,
    is_free: bool,
    count: int,
    target_cognitive_domains: list[str] | None = None,
) -> dict[str, Any]:
    """
    Generate `count` activities with full Phase 2 metadata.

    Args:
        subject: Activity subject (math, language, science, art, music, physical, social, cooking)
        age_group: Age tier (baby, toddler, preschool, school)
        language: Language code (english, french, spanish, etc.)
        is_free: Whether activity should be free tier
        count: Number of activities to generate
        target_cognitive_domains: Optional list of cognitive domains to fill gaps in
                                 (executive_function, emotional_regulation, etc.)
    """
    activities: list[ActivityPayload] = []
    target_cognitive_domains = target_cognitive_domains or []

    for i in range(count):
        # Enriched context includes cognitive domain coverage
        context_data = get_existing_activity_summaries_enriched(age_group, subject)
        existing_context = context_data.get("existing_context", "None yet.")
        cognitive_summary = context_data.get("cognitive_domain_summary", "Balanced coverage.")

        # Round-robin through suggested cognitive domains
        suggested_domain = target_cognitive_domains[i % len(target_cognitive_domains)] if target_cognitive_domains else "math"

        result: ActivityPayload = await _chain.ainvoke({
            "existing_context": existing_context,
            "cognitive_domain_summary": cognitive_summary,
            "age_tier": age_group,
            "age_description": _age_description(age_group),
            "subject": subject,
            "language": language,
            "is_free_json": "true" if is_free else "false",
            "target_cognitive_domain": suggested_domain,
        })

        activities.append(result)

    return {
        "activities": [a.model_dump() for a in activities],
        "model": "claude-sonnet-4-6",
        "count": len(activities),
        "validation": "pydantic",
    }


def _age_description(age_tier: str) -> str:
    """Return human-readable age description."""
    return {
        "baby": "0-24 months: babies are developing sensory, motor, and early language skills",
        "toddler": "2-3 years: toddlers are developing independence, language, and basic problem-solving",
        "preschool": "3-5 years: preschoolers are developing social skills, early literacy, and advanced motor control",
        "school": "6-8 years: school-age children are developing academic skills and independence",
    }.get(age_tier, age_tier)
