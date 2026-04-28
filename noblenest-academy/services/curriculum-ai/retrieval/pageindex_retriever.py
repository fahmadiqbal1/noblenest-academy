"""
PageIndex retriever for the curriculum.

PageIndex builds a hierarchical tree-of-contents from curriculum documents,
letting Claude reason its way to relevant sections rather than using
vector similarity search.

On a fresh install this returns an empty string (no index yet).
Run `python scripts/index_curriculum.py` to build the index after the
first batch of activities is created.
"""

from __future__ import annotations

import json
import pathlib

# Where generated PageIndex trees are stored
INDEX_DIR = pathlib.Path(__file__).parent.parent / "curriculum-index"
INDEX_DIR.mkdir(exist_ok=True)


def get_existing_activity_summaries(age_group: str, subject: str) -> str:
    """
    Return a plain-text list of existing activity titles for this
    age tier + subject combination.

    This is fed into the generation prompt so Claude avoids duplicates.

    The index file is a JSON produced by PageIndex:
        {
          "nodes": [
            {"title": "...", "summary": "...", "age_tier": "4-6", "subject": "math"},
            ...
          ]
        }
    """
    index_file = INDEX_DIR / "activities-index.json"

    if not index_file.exists():
        return ""  # No index yet — first run

    try:
        data = json.loads(index_file.read_text(encoding="utf-8"))
        nodes = data.get("nodes", [])

        matching = [
            node["title"]
            for node in nodes
            if node.get("age_group") == age_group
            and node.get("subject", "").lower() == subject.lower()
        ]

        if not matching:
            return ""

        return "\n".join(f"- {title}" for title in matching[:50])  # limit context size

    except (json.JSONDecodeError, KeyError):
        return ""  # Corrupted index — degrade gracefully


def get_existing_activity_summaries_enriched(age_group: str, subject: str) -> dict:
    """
    Return enriched context including existing activity titles AND
    cognitive domain coverage summary.

    Used by enhanced activity_chain.py to avoid gaps in curriculum balance.

    Returns dict with:
        - existing_context: plain-text activity title list
        - cognitive_domain_summary: summary of current cognitive domain coverage
    """
    index_file = INDEX_DIR / "activities-index.json"

    if not index_file.exists():
        return {
            "existing_context": "",
            "cognitive_domain_summary": "No existing activities yet. Start with foundational domains.",
        }

    try:
        data = json.loads(index_file.read_text(encoding="utf-8"))
        nodes = data.get("nodes", [])

        # Filter for matching age_group + subject
        matching = [
            node
            for node in nodes
            if node.get("age_group") == age_group
            and node.get("subject", "").lower() == subject.lower()
        ]

        if not matching:
            return {
                "existing_context": "",
                "cognitive_domain_summary": "No existing activities yet in this category. Start with foundational domains.",
            }

        # Extract titles for duplicate avoidance
        titles = [node["title"] for node in matching[:50]]
        existing_context = "\n".join(f"- {title}" for title in titles)

        # Analyze cognitive domain coverage
        cognitive_counts = {}
        for node in matching:
            domain = node.get("cognitive_domain", "unknown")
            cognitive_counts[domain] = cognitive_counts.get(domain, 0) + 1

        # Build summary string for LLM
        if cognitive_counts:
            domain_lines = [f"  - {domain}: {count} activities" for domain, count in sorted(cognitive_counts.items())]
            cognitive_summary = "Current cognitive domain coverage:\n" + "\n".join(domain_lines) + "\n\nConsider activities for underrepresented domains."
        else:
            cognitive_summary = "No cognitive domain metadata yet. Prioritize foundational domains: math, language, science, emotional_regulation."

        return {
            "existing_context": existing_context,
            "cognitive_domain_summary": cognitive_summary,
        }

    except (json.JSONDecodeError, KeyError):
        return {
            "existing_context": "",
            "cognitive_domain_summary": "Index corrupted. Start fresh with foundational domains.",
        }
