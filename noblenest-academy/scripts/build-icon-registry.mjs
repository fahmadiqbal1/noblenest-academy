#!/usr/bin/env node
// build-icon-registry.mjs — emit PHP registry lines for the Lucide icons we use.
//
// Usage:
//   node scripts/build-icon-registry.mjs > /tmp/icons.php
//
// The output is a list of PHP-array entries (one per icon) that you paste into
// the static $registry in app/View/Components/Ui/Icon.php. The script reads icon
// data straight from the installed `lucide` package so paths are canonical.
//
// Why a script: copy-pasting SVG path strings from lucide.dev is error-prone.
// This script also doubles as documentation — re-run it any time you need to
// add a new icon.

import { pathToFileURL } from 'node:url';
import { existsSync } from 'node:fs';
import { resolve } from 'node:path';

const ICONS_DIR = resolve('node_modules/lucide/dist/esm/icons');

async function loadIcon(name) {
    const file = resolve(ICONS_DIR, `${name}.js`);
    if (!existsSync(file)) return null;
    const mod = await import(pathToFileURL(file).href);
    return mod.default ?? null;
}

// Names of NEW icons (not already in the hand-written $registry) needed to
// cover the 128 bi-* legacy classes. The existing registry keeps its old
// names ('home', 'check-circle', 'play-circle', etc.) because the SVGs are
// hand-baked and the names are already referenced across the codebase. Lucide
// v0.5x renamed those icons (`home`→`house`, `play-circle`→`circle-play`,
// etc.), so this list uses the MODERN names.
const WANTED = [
    'rotate-cw', 'rotate-ccw', 'circle-arrow-right', 'video', 'video-off',
    'message-circle', 'message-square-quote', 'check-check', 'clipboard',
    'clipboard-check', 'folder', 'circle-play', 'gamepad-2', 'coffee',
    'monitor', 'egg', 'smile', 'eraser', 'octagon-alert', 'film', 'flag',
    'flower', 'flower-2', 'folder-open', 'headphones', 'activity',
    'hourglass', 'image', 'inbox', 'book-marked', 'notebook-pen',
    'lightbulb', 'link-2', 'list-ordered', 'list-checks', 'map', 'mic',
    'badge-check', 'circle-pause', 'plug', 'circle-plus', 'bot', 'save',
    'shield-check', 'skip-back', 'skip-forward', 'circle-stop', 'timer',
    'sun', 'table', 'phone-off', 'user-round',
];

// Lucide icon nodes are [tagName, attrs, children] tuples — render each as a
// self-closing SVG element (matching the hand-written paths already in the
// registry).
function nodeToSvg(node) {
    const [tag, attrs] = node;
    const attrStr = Object.entries(attrs)
        .map(([k, v]) => `${k}="${v}"`)
        .join(' ');
    return `<${tag} ${attrStr}/>`;
}

const missing = [];
const longest = Math.max(...WANTED.map((n) => n.length));

for (const name of WANTED) {
    const icon = await loadIcon(name);
    if (!icon) {
        missing.push(name);
        continue;
    }
    const svg = icon.map(nodeToSvg).join('');
    const key = `'${name}'`.padEnd(longest + 3);
    console.log(`        ${key}=> '${svg.replace(/'/g, "\\'")}',`);
}

if (missing.length) {
    console.error(`\n# Missing in lucide package (${missing.length}):`);
    for (const m of missing) console.error(`#   ${m}`);
    process.exit(2);
}
