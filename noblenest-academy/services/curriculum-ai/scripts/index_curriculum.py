"""
Index all curriculum activities from a JSON export.

Usage:
    python scripts/index_curriculum.py --input /path/to/activities_export.json

This script takes a Laravel-exported JSON of all activities and converts it
into a PageIndex-compatible tree that the retriever can query.

Laravel artisan command that generates the export (add to console.php):
    php artisan curriculum:export-json --output=/tmp/activities_export.json

Schedule in production (runs every night):
    Schedule::command('curriculum:export-json')->dailyAt('02:30');
    After export, cron calls this script.
"""

from __future__ import annotations

import argparse
import json
import pathlib
import sys


INDEX_DIR = pathlib.Path(__file__).parent.parent / "curriculum-index"
INDEX_DIR.mkdir(exist_ok=True)


def build_index(input_path: str) -> None:
    source = pathlib.Path(input_path)
    if not source.exists():
        print(f"ERROR: file not found: {source}", file=sys.stderr)
        sys.exit(1)

    activities = json.loads(source.read_text(encoding="utf-8"))
    print(f"Indexing {len(activities)} activities …", flush=True)

    nodes = []
    for act in activities:
        nodes.append({
            "title":    act.get("title", "Untitled"),
            "summary":  act.get("description", ""),
            "age_tier": act.get("age_tier", ""),
            "subject":  act.get("subject", ""),
            "language": act.get("language", "en"),
            "is_free":  act.get("is_free", True),
        })

    index = {"nodes": nodes, "total": len(nodes)}
    out = INDEX_DIR / "activities-index.json"
    out.write_text(json.dumps(index, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"Index written to {out} ({len(nodes)} nodes)", flush=True)


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Build PageIndex curriculum tree")
    parser.add_argument("--input", required=True, help="Path to activities JSON export")
    args = parser.parse_args()
    build_index(args.input)
