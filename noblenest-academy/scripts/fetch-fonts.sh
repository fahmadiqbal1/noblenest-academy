#!/usr/bin/env bash
# fetch-fonts.sh — self-host the WOFF2 font files referenced by resources/css/app.css.
#
# Inter comes from rsms.me (canonical upstream).
# Baloo 2 + Nunito come from Bunny Fonts (GDPR-friendly Google Fonts mirror, SIL OFL).
#
# Usage:
#   scripts/fetch-fonts.sh          # download missing files
#   scripts/fetch-fonts.sh --force  # re-download even if files exist
#
# Idempotent: skips files already present unless --force is passed.

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DEST="$ROOT_DIR/public/fonts"
FORCE=0

for arg in "$@"; do
    case "$arg" in
        --force) FORCE=1 ;;
        -h|--help)
            sed -n '2,12p' "$0"
            exit 0
            ;;
    esac
done

mkdir -p "$DEST"

# name|url tuples. URLs are direct WOFF2 endpoints.
FONTS=(
    "Inter-Regular.woff2|https://rsms.me/inter/font-files/Inter-Regular.woff2"
    "Inter-Medium.woff2|https://rsms.me/inter/font-files/Inter-Medium.woff2"
    "Inter-SemiBold.woff2|https://rsms.me/inter/font-files/Inter-SemiBold.woff2"
    "Inter-Bold.woff2|https://rsms.me/inter/font-files/Inter-Bold.woff2"
    "Baloo2-Regular.woff2|https://fonts.bunny.net/baloo-2/files/baloo-2-latin-400-normal.woff2"
    "Baloo2-Bold.woff2|https://fonts.bunny.net/baloo-2/files/baloo-2-latin-700-normal.woff2"
    "Nunito-Regular.woff2|https://fonts.bunny.net/nunito/files/nunito-latin-400-normal.woff2"
    "Nunito-SemiBold.woff2|https://fonts.bunny.net/nunito/files/nunito-latin-600-normal.woff2"
    "Nunito-Bold.woff2|https://fonts.bunny.net/nunito/files/nunito-latin-700-normal.woff2"
)

downloaded=0
skipped=0
failed=0

for entry in "${FONTS[@]}"; do
    name="${entry%%|*}"
    url="${entry##*|}"
    target="$DEST/$name"

    if [[ -s "$target" && $FORCE -eq 0 ]]; then
        echo "skip   $name (already present, $(wc -c < "$target") bytes)"
        skipped=$((skipped + 1))
        continue
    fi

    echo "fetch  $name <- $url"
    if curl --fail --silent --show-error --location --max-time 30 \
            --user-agent "noblenest-fetch-fonts/1.0" \
            --output "$target.tmp" "$url"; then
        if [[ -s "$target.tmp" ]]; then
            mv "$target.tmp" "$target"
            downloaded=$((downloaded + 1))
        else
            echo "  FAIL: zero-byte response for $name" >&2
            rm -f "$target.tmp"
            failed=$((failed + 1))
        fi
    else
        echo "  FAIL: curl error for $name" >&2
        rm -f "$target.tmp"
        failed=$((failed + 1))
    fi
done

echo ""
echo "fonts: $downloaded downloaded, $skipped skipped, $failed failed"
if [[ $failed -gt 0 ]]; then
    exit 1
fi
