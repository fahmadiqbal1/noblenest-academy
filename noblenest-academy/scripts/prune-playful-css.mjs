#!/usr/bin/env node
// prune-playful-css.mjs — drop unused `.nn-*` rules from resources/css/playful.css.
//
// Usage:
//   node scripts/prune-playful-css.mjs            # rewrite in place
//   node scripts/prune-playful-css.mjs --dry-run  # report only
//
// Approach:
//   1. Walk resources/views/**/*.blade.php and collect every `nn-<token>` substring.
//      That's the conservative "used" set: a token wrongly counted as used merely
//      keeps an extra rule (safe direction).
//   2. Parse playful.css with PostCSS.
//   3. For each top-level rule, look at the selectors. If a selector references a
//      `.nn-X` class and NONE of those classes are in the used set, drop the rule.
//      Selectors with no `.nn-*` reference (plain element selectors, attribute
//      selectors, @keyframes children) are kept as-is.
//   4. After pruning, drop now-empty at-rules.

import { promises as fs } from 'node:fs';
import { join, resolve } from 'node:path';
import postcss from 'postcss';

const argv = process.argv.slice(2);
const DRY = argv.includes('--dry-run');
const CSS_PATH = resolve('resources/css/playful.css');
const VIEWS_DIR = resolve('resources/views');

async function walk(dir) {
    const out = [];
    for (const entry of await fs.readdir(dir, { withFileTypes: true })) {
        const full = join(dir, entry.name);
        if (entry.isDirectory()) out.push(...await walk(full));
        else if (entry.isFile() && entry.name.endsWith('.blade.php')) out.push(full);
    }
    return out;
}

// Step 1: build used-class set from blades.
const usedClasses = new Set();
for (const file of await walk(VIEWS_DIR)) {
    const text = await fs.readFile(file, 'utf8');
    for (const m of text.matchAll(/\bnn-[a-z][a-z0-9-]*\b/g)) {
        usedClasses.add(m[0]);
    }
}
// Also drop CSS-variable names (var(--nn-X)) — keep anything that looks like a class.
// CSS var refs to --nn-X are not what we care about for class selectors, but our
// match already catches them. We err on the side of "keep" so this is fine.

console.error(`Used nn-* tokens found in blades: ${usedClasses.size}`);

// Step 2 + 3: parse CSS and walk rules.
const css = await fs.readFile(CSS_PATH, 'utf8');
const beforeBytes = Buffer.byteLength(css, 'utf8');
const root = postcss.parse(css);

let droppedRules = 0;
let droppedSelectors = 0;

root.walkRules((rule) => {
    // Don't touch rules inside @keyframes — selectors there are percentages.
    if (rule.parent && rule.parent.type === 'atrule' && rule.parent.name === 'keyframes') return;

    const original = rule.selectors;
    const kept = original.filter((sel) => {
        const nnRefs = sel.match(/\.nn-[a-z][a-z0-9-]*/g) || [];
        // No .nn-* reference -> keep (element selector, :root, etc.)
        if (nnRefs.length === 0) return true;
        // Keep if ANY referenced .nn-X is in the used set.
        return nnRefs.some((m) => usedClasses.has(m.slice(1)));
    });

    if (kept.length === 0) {
        droppedRules++;
        droppedSelectors += original.length;
        rule.remove();
    } else if (kept.length < original.length) {
        droppedSelectors += original.length - kept.length;
        rule.selectors = kept;
    }
});

// Step 4: drop now-empty at-rules (an @media that lost all its inner rules).
root.walkAtRules((atrule) => {
    if (atrule.name === 'keyframes' || atrule.name === 'font-face') return;
    if (atrule.nodes && atrule.nodes.length === 0) {
        atrule.remove();
    }
});

const out = root.toString();
const afterBytes = Buffer.byteLength(out, 'utf8');

console.error(`Rules dropped:       ${droppedRules}`);
console.error(`Selectors dropped:   ${droppedSelectors}`);
console.error(`Size before:         ${beforeBytes} bytes`);
console.error(`Size after:          ${afterBytes} bytes (${((1 - afterBytes / beforeBytes) * 100).toFixed(1)}% smaller)`);

if (DRY) {
    console.error('(dry run — not writing)');
} else {
    await fs.writeFile(CSS_PATH, out);
    console.error('Wrote pruned CSS.');
}
