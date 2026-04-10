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
