#!/usr/bin/env node
// migrate-bi-icons.mjs — replace `<i class="bi bi-X ...">` in blades with
// `<x-ui.icon name="Y" />`, where Y is the Lucide name for X (see
// docs/phase1-launch/icon-migration.md for the mapping).
//
// Usage:
//   node scripts/migrate-bi-icons.mjs            # rewrite in place
//   node scripts/migrate-bi-icons.mjs --dry-run  # print diffs only
//
// What it does:
//   - Walks resources/views/ for *.blade.php files containing `bi bi-`.
//   - For each `<i class="...">...</i>` whose class list contains `bi bi-X`,
//     extracts X, finds Y via the mapping below, extracts any *other* classes,
//     and emits `<x-ui.icon name="Y" class="<other>"/>` (omitting class= if
//     no other classes remain).
//   - Reports per-file changes and unknown bi-* names.
//
// What it does NOT do:
//   - Convert Bootstrap utility classes that may sit alongside the bi-* class.
//     Those carry through to the icon's `class=""` and are dealt with by the
//     Bootstrap-purge step.
//   - Rewrite a button that contains only a bi-* icon into a `<x-ui.button
//     icon="…">`. The icon swap is mechanical; button-level rewrites happen
//     during the Bootstrap purge.

import { promises as fs } from 'node:fs';
import { resolve, join } from 'node:path';

const argv = process.argv.slice(2);
const DRY = argv.includes('--dry-run');
const positional = argv.find((a) => !a.startsWith('--'));
const ROOT = resolve(positional ?? 'resources/views');

// bi-X (without the leading `bi-`) -> Lucide name.
// Mirrors docs/phase1-launch/icon-migration.md.
const MAP = {
    'arrow-clockwise': 'rotate-cw',
    'arrow-counterclockwise': 'rotate-ccw',
    'arrow-left': 'arrow-left',
    'arrow-repeat': 'rotate-cw',
    'arrow-right': 'arrow-right',
    'arrow-right-circle': 'circle-arrow-right',
    'bar-chart-line': 'bar-chart',
    'book': 'book',
    'book-half': 'book-open',
    'box-arrow-right': 'log-out',
    'box-arrow-up-right': 'external-link',
    'calendar-check': 'clipboard-check',
    'calendar-event': 'calendar',
    'calendar-heart': 'calendar',
    'calendar-month': 'calendar',
    'calendar-plus': 'calendar',
    'calendar-week': 'calendar',
    'camera-video': 'video',
    'camera-video-fill': 'video',
    'camera-video-off': 'video-off',
    'chat-dots': 'message-circle',
    'chat-left-quote': 'message-square-quote',
    'check-all': 'check-check',
    'check-circle': 'check-circle',
    'check-circle-fill': 'check-circle',
    'check-lg': 'check',
    'check2': 'check',
    'check2-all': 'check-check',
    'check2-circle': 'check-circle',
    'chevron-down': 'chevron-down',
    'chevron-right': 'chevron-right',
    'clipboard': 'clipboard',
    'clipboard-check': 'clipboard-check',
    'clock': 'clock',
    'collection': 'folder',
    'collection-play': 'circle-play',
    'controller': 'gamepad-2',
    'credit-card': 'credit-card',
    'credit-card-fill': 'credit-card',
    'cup-hot': 'coffee',
    'display': 'monitor',
    'download': 'download',
    'egg-fried': 'egg',
    'emoji-smile': 'smile',
    'envelope': 'mail',
    'eraser': 'eraser',
    'eraser-fill': 'eraser',
    'exclamation': 'alert-circle',
    'exclamation-circle': 'alert-circle',
    'exclamation-lg': 'alert-circle',
    'exclamation-octagon-fill': 'octagon-alert',
    'exclamation-triangle': 'alert-triangle',
    'exclamation-triangle-fill': 'alert-triangle',
    'eye': 'eye',
    'file-earmark-pdf': 'file-text',
    'film': 'film',
    'flag': 'flag',
    'flower1': 'flower',
    'flower2': 'flower-2',
    'folder2-open': 'folder-open',
    'gear': 'settings',
    'geo-alt': 'map-pin',
    'graph-up': 'trending-up',
    'headphones': 'headphones',
    'heart': 'heart',
    'heart-fill': 'heart',
    'heart-pulse': 'activity',
    'hourglass-split': 'hourglass',
    'house-fill': 'home',
    'house-heart': 'home',
    'image': 'image',
    'inbox': 'inbox',
    'info-circle': 'info',
    'info-circle-fill': 'info',
    'info-lg': 'info',
    'journal-check': 'book-marked',
    'journal-richtext': 'notebook-pen',
    'lightbulb': 'lightbulb',
    'lightning': 'zap',
    'lightning-charge': 'zap',
    'link-45deg': 'link-2',
    'list-ol': 'list-ordered',
    'list-task': 'list-checks',
    'lock-fill': 'lock',
    'map': 'map',
    'mic': 'mic',
    'mic-fill': 'mic',
    'patch-check': 'badge-check',
    'patch-check-fill': 'badge-check',
    'pause-circle': 'circle-pause',
    'pause-fill': 'pause',
    'paypal': null, // Phase 4 removes PayPal entirely; render no icon.
    'pencil': 'pencil',
    'pencil-square': 'edit',
    'people': 'users',
    'people-fill': 'users',
    'person-arms-up': 'user-round',
    'person-badge': 'badge-check',
    'person-fill': 'user',
    'play-circle': 'circle-play',
    'play-circle-fill': 'circle-play',
    'play-fill': 'play',
    'plug': 'plug',
    'plug-fill': 'plug',
    'plus': 'plus',
    'plus-circle': 'circle-plus',
    'plus-lg': 'plus',
    'robot': 'bot',
    'rocket-takeoff': 'rocket',
    'save': 'save',
    'search': 'search',
    'send': 'send',
    'send-fill': 'send',
    'shield-check': 'shield-check',
    'skip-backward-fill': 'skip-back',
    'skip-forward-fill': 'skip-forward',
    'star-fill': 'star',
    'stars': 'sparkles',
    'stop-circle': 'circle-stop',
    'stopwatch': 'timer',
    'sun': 'sun',
    'table': 'table',
    'telephone-x-fill': 'phone-off',
    'trash': 'trash',
    'trophy': 'trophy',
    'x': 'x',
    'x-circle': 'x-circle',
    'yin-yang': 'sparkles', // No Lucide equivalent; placeholder, revisit Phase 3
};

const stats = { files: 0, replacements: 0, unknown: new Map() };

async function walk(dir) {
    const out = [];
    for (const entry of await fs.readdir(dir, { withFileTypes: true })) {
        const full = join(dir, entry.name);
        if (entry.isDirectory()) out.push(...await walk(full));
        else if (entry.isFile() && entry.name.endsWith('.blade.php')) out.push(full);
    }
    return out;
}

function transform(content, file) {
    let count = 0;
    // Match <i class="..."></i> where the class list contains `bi bi-X`.
    // Lazy match on whitespace inside the tag.
    const re = /<i\s+([^>]*?)class\s*=\s*"([^"]*?)"\s*([^>]*?)>\s*<\/i>/g;
    const out = content.replace(re, (full, pre, classes, post) => {
        // Tokenise the class list.
        const tokens = classes.trim().split(/\s+/);
        const biIdx = tokens.indexOf('bi');
        const biClass = tokens.find((t) => t.startsWith('bi-'));
        if (biIdx === -1 || !biClass) return full;
        const biName = biClass.slice(3);
        const lucide = Object.prototype.hasOwnProperty.call(MAP, biName) ? MAP[biName] : undefined;
        if (lucide === undefined) {
            const arr = stats.unknown.get(biName) ?? [];
            arr.push(file);
            stats.unknown.set(biName, arr);
            return full;
        }
        // PayPal is mapped to null — drop the icon entirely.
        if (lucide === null) {
            count++;
            return '';
        }
        // Preserve any other classes (non-bi, non-bi-X).
        const remaining = tokens.filter((t) => t !== 'bi' && !t.startsWith('bi-'));
        // Preserve any other attributes from pre / post (e.g. `style=""`,
        // `aria-hidden`). We re-emit them verbatim around the new component.
        const otherAttrs = (pre + ' ' + post).replace(/\s+/g, ' ').trim();
        const classAttr = remaining.length ? ` class="${remaining.join(' ')}"` : '';
        const attrTail = otherAttrs ? ` ${otherAttrs}` : '';
        count++;
        return `<x-ui.icon name="${lucide}"${classAttr}${attrTail} />`;
    });
    return { out, count };
}

const files = await walk(ROOT);
const changed = [];

for (const file of files) {
    const original = await fs.readFile(file, 'utf8');
    if (!original.includes('bi bi-')) continue;
    const { out, count } = transform(original, file);
    if (count === 0) continue;
    if (!DRY) await fs.writeFile(file, out);
    changed.push({ file, count });
    stats.files += 1;
    stats.replacements += count;
}

console.log(`${stats.files} files modified, ${stats.replacements} icons replaced.`);
for (const { file, count } of changed) {
    console.log(`  ${count.toString().padStart(3)}  ${file}`);
}
if (stats.unknown.size) {
    console.error(`\nUnknown bi-* names (no mapping):`);
    for (const [name, locs] of stats.unknown) {
        console.error(`  bi-${name}  (${locs.length}×)`);
        for (const loc of locs.slice(0, 3)) console.error(`    ${loc}`);
    }
    process.exit(2);
}
