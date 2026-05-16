"""
Curriculum Designer Agent — AgentScope-powered (v1.0.20).

Uses AgentScope model wrappers directly:
  - OpenAIChatModel  → Grok / xAI (OpenAI-compatible at api.x.ai)
  - AnthropicChatModel → Anthropic Claude

One model instance is created per request so API keys can vary between calls.
Run the sidecar with --workers 1 to avoid state races.
"""

from __future__ import annotations

import json
import re
from typing import Any

SYSTEM_PROMPT = (
    "You are Noble Nest Academy's expert early childhood curriculum designer. "
    "Generate ONE safe, age-appropriate, pedagogically sound activity as JSON only.\n\n"
    "Rules:\n"
    "- Output ONLY a valid JSON object — no markdown fences, no prose\n"
    "- Safe for the specified age group (no hazards, age-appropriate)\n"
    "- Practical — common household materials only\n\n"
    "JSON schema:\n"
    '{\n'
    '  "title": "Short activity name (max 8 words)",\n'
    '  "description": "1-2 sentence overview for parents",\n'
    '  "instructions": "Numbered step-by-step guide (3-6 steps)",\n'
    '  "materials": ["item1", "item2"],\n'
    '  "duration_minutes": 15,\n'
    '  "difficulty": "easy",\n'
    '  "subject": "skill area",\n'
    '  "age_min": 3,\n'
    '  "age_max": 5,\n'
    '  "language": "en",\n'
    '  "is_free": true\n'
    "}\n\n"
    'difficulty: "easy" | "medium" | "hard"\n'
    "Return ONLY the JSON object. No other text."
)


def _extract_json(text: str) -> dict:
    text = text.strip()
    # Strip markdown code fences if present
    text = re.sub(r"^```(?:json)?\s*\n?", "", text, flags=re.IGNORECASE)
    text = re.sub(r"\n?```\s*$", "", text)
    text = text.strip()
    start = text.find("{")
    end = text.rfind("}") + 1
    if start >= 0 and end > start:
        text = text[start:end]
    return json.loads(text)


def _get_text(response: Any) -> str:
    """Extract text content from a ChatResponse or generator."""
    # Collect from AsyncGenerator if streaming
    if hasattr(response, "__aiter__"):
        raise TypeError("Streaming response — call with stream=False")

    content = getattr(response, "content", None)
    if content is None:
        return str(response)

    # content is a sequence of blocks
    parts = []
    for block in content:
        text = getattr(block, "text", None)
        if text:
            parts.append(text)
    return "".join(parts)


def _validate_activity(data: dict, skill: str, age_min: int, age_max: int) -> dict:
    return {
        "title": str(data.get("title") or f"{skill} Activity"),
        "description": str(data.get("description") or ""),
        "instructions": str(data.get("instructions") or ""),
        "materials": list(data.get("materials") or []),
        "duration_minutes": int(data.get("duration_minutes") or 15),
        "difficulty": str(data.get("difficulty") or "easy"),
        "subject": str(data.get("subject") or skill),
        "age_min": int(data.get("age_min") or age_min),
        "age_max": int(data.get("age_max") or age_max),
        "language": str(data.get("language") or "en"),
        "is_free": bool(data.get("is_free", True)),
    }


async def generate_for_gap(
    skill: str,
    age_min: int,
    age_max: int,
    provider: str,
    model: str | None,
    api_key: str,
) -> dict[str, Any]:
    """Generate one curriculum activity for a gap using an AgentScope model."""

    messages = [
        {"role": "system", "content": SYSTEM_PROMPT},
        {
            "role": "user",
            "content": (
                f"Design a '{skill}' activity for children aged {age_min}–{age_max} years. "
                f"Return ONLY a valid JSON object."
            ),
        },
    ]

    if provider == "grok":
        from agentscope.model import OpenAIChatModel

        mdl = OpenAIChatModel(
            model_name=model or "grok-beta",
            api_key=api_key,
            stream=False,
            client_kwargs={"base_url": "https://api.x.ai/v1"},
            generate_kwargs={"temperature": 0.7, "max_tokens": 800},
        )
    else:
        from agentscope.model import AnthropicChatModel

        mdl = AnthropicChatModel(
            model_name=model or "claude-haiku-4-5-20251001",
            api_key=api_key,
            max_tokens=800,
            stream=False,
            generate_kwargs={"temperature": 0.7},
        )

    response = await mdl(messages)
    raw = _get_text(response)
    data = _extract_json(raw)
    return _validate_activity(data, skill, age_min, age_max)
