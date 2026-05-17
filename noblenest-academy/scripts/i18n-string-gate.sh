#!/usr/bin/env bash
#
# i18n-string-gate.sh — Phase 3 CI gate for hard-coded user-visible English.
#
# Heuristically scans resources/views/**/*.blade.php for likely hard-coded
# English text nodes (e.g. >Submit Form<) that should be wrapped in a
# translation helper (__(), @lang(), trans(), I18n::).
#
# MODE:
#   Default = REPORT-ONLY. The codebase currently has ~140 files with
#   hard-coded English (mass extraction is a separate effort). In
#   report-only mode the script ALWAYS exits 0 so CI stays green; it
#   just prints the offending file:line list as guidance.
#
#   --strict = enforce. Exits non-zero if any non-allowlisted match
#   remains. Flip CI to --strict once the extraction effort completes.
#
# Allowlist: scripts/i18n-gate-allowlist.txt — one "path" or
# "path:line-substring" per line; "#" comments allowed.
#
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
VIEWS_DIR="$ROOT/resources/views"
ALLOWLIST="$ROOT/scripts/i18n-gate-allowlist.txt"

STRICT=0
if [[ "${1:-}" == "--strict" ]]; then
  STRICT=1
fi

if [[ $STRICT -eq 1 ]]; then
  echo "== i18n string gate: STRICT mode =="
else
  echo "== i18n string gate: REPORT-ONLY mode (always exits 0) =="
  echo "   Strict enforcement will be enabled once Blade extraction completes."
fi

# Heuristic: a text node starting with a capitalized English word between
# > and < . Exclude lines that already use a translation mechanism or are
# clearly not user-visible copy.
PATTERN='>[A-Z][a-z]+([ '"'"'A-Za-z.,!?:-]*)?<'

declare -a HITS=()

while IFS= read -r -d '' file; do
  rel="${file#"$ROOT"/}"
  while IFS=: read -r lineno content; do
    [[ -z "${lineno:-}" ]] && continue

    # Skip lines that already use i18n or are non-copy constructs.
    if grep -Eq '(__\(|@lang\(|trans\(|I18n::|\{\{--|<x-|@php|lang=|dir=|&copy;|@svg|@include|@yield|@section|@php)' <<<"$content"; then
      continue
    fi
    # Skip pure-variable mustaches like >{{ $x }}<
    if grep -Eq '>\{\{[^<]*\}\}<' <<<"$content"; then
      continue
    fi

    # Allowlist check (path or path:substring).
    skip=0
    if [[ -f "$ALLOWLIST" ]]; then
      while IFS= read -r entry; do
        [[ -z "$entry" || "$entry" == \#* ]] && continue
        if [[ "$entry" == *:* ]]; then
          apath="${entry%%:*}"; asub="${entry#*:}"
          if [[ "$rel" == "$apath" && "$content" == *"$asub"* ]]; then skip=1; break; fi
        elif [[ "$entry" == */ ]]; then
          # Trailing-slash entries match every file under that directory prefix.
          if [[ "$rel" == "$entry"* ]]; then skip=1; break; fi
        else
          if [[ "$rel" == "$entry" ]]; then skip=1; break; fi
        fi
      done <"$ALLOWLIST"
    fi
    [[ $skip -eq 1 ]] && continue

    HITS+=("$rel:$lineno: $(echo "$content" | sed 's/^[[:space:]]*//')")
  done < <(grep -nE "$PATTERN" "$file" || true)
done < <(find "$VIEWS_DIR" -type f -name '*.blade.php' -print0)

COUNT=${#HITS[@]}

if [[ $COUNT -eq 0 ]]; then
  echo "OK: no non-allowlisted hard-coded English detected."
  exit 0
fi

echo ""
echo "Found $COUNT potential hard-coded English string(s):"
for h in "${HITS[@]}"; do
  echo "  $h"
done
echo ""

if [[ $STRICT -eq 1 ]]; then
  echo "FAIL (strict): wrap the strings above in __()/trans()/I18n:: or allowlist them."
  exit 1
fi

echo "REPORT-ONLY: not failing. Run with --strict to enforce."
exit 0
