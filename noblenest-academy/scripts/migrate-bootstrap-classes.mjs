#!/usr/bin/env node
// migrate-bootstrap-classes.mjs — replace Bootstrap utility classes with Tailwind
// equivalents in resources/views/**/*.blade.php.
//
// Usage:
//   node scripts/migrate-bootstrap-classes.mjs --dry-run
//   node scripts/migrate-bootstrap-classes.mjs
//
// Scope: tokens that are unambiguously Bootstrap-only OR have a different
// meaning in Tailwind. Tokens that work identically in both (mb-4, px-4,
// gap-2, mx-auto, border, rounded, shadow-sm, flex-1, w-full, items-center
// via Tailwind, etc.) are LEFT UNTOUCHED — they already render correctly
// under Tailwind v4.
//
// Out of scope (handled by Tier-2/3 manual rewrites):
//   - `btn btn-primary` → `<x-ui.button variant="primary">` (changes the tag)
//   - `card / card-body / card-header` → `<x-ui.card>` (component split)
//   - `nav-link / navbar / dropdown` → role-layout nav components
//   - `form-control / form-label` → `<x-ui.input>` / `<x-ui.field>` (component)
//   - `alert` → `<x-ui.alert>`
//   - `badge bg-X` → `<x-ui.badge variant="X">`
//   - `modal`, `offcanvas`, `collapse`, `tooltip` — Bootstrap JS plugins; not used at runtime
//     since Bootstrap JS isn't loaded. Strip the classes; leave the DOM.

import { promises as fs } from 'node:fs';
import { resolve, join } from 'node:path';

const argv = process.argv.slice(2);
const DRY = argv.includes('--dry-run');
const positional = argv.find((a) => !a.startsWith('--'));
const ROOT = resolve(positional ?? 'resources/views');

// Tier-2 mappings: long Tailwind utility chains for component-shaped classes.
// These replace the Bootstrap class with the styling that approximates it.
// We intentionally don't change the surrounding tag — `<button class="btn btn-primary">`
// stays a `<button>`, gets the long chain. Refactoring to `<x-ui.button>` is a
// follow-up if/when the Blade markup needs more component re-use.
const BTN_BASE = 'inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';
const BTN_SIZE_SM = 'px-3 py-1.5 text-sm';
const BTN_SIZE_LG = 'px-5 py-3 text-lg';
const BTN_SIZE_XS = 'px-2 py-1 text-xs';
const CARD_BASE = 'bg-white rounded-xl border border-gray-200 shadow-sm';
const FORM_INPUT = 'block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500';
const ALERT_BASE = 'flex items-start gap-3 p-4 rounded-lg border';

// 1:1 token replacements. Apply ONLY when the token is a complete class name
// (regex word-boundary). Empty string means "remove the token".
const REPLACE = {
    // Display
    'd-flex': 'flex',
    'd-inline-flex': 'inline-flex',
    'd-block': 'block',
    'd-inline': 'inline',
    'd-inline-block': 'inline-block',
    'd-none': 'hidden',
    'd-grid': 'grid',
    'd-md-flex': 'md:flex',
    'd-md-block': 'md:block',
    'd-md-none': 'md:hidden',
    'd-lg-flex': 'lg:flex',
    'd-lg-block': 'lg:block',
    'd-lg-none': 'lg:hidden',
    'd-sm-flex': 'sm:flex',
    'd-sm-none': 'sm:hidden',

    // Flex direction
    'flex-column': 'flex-col',
    'flex-md-row': 'md:flex-row',
    'flex-md-column': 'md:flex-col',
    'flex-lg-row': 'lg:flex-row',
    'flex-fill': 'flex-1',

    // align-items
    'align-items-center': 'items-center',
    'align-items-start': 'items-start',
    'align-items-end': 'items-end',
    'align-items-baseline': 'items-baseline',
    'align-items-stretch': 'items-stretch',

    // align-content
    'align-content-center': 'content-center',
    'align-content-start': 'content-start',
    'align-content-end': 'content-end',
    'align-content-between': 'content-between',
    'align-content-around': 'content-around',

    // align-self
    'align-self-center': 'self-center',
    'align-self-start': 'self-start',
    'align-self-end': 'self-end',

    // justify-content
    'justify-content-center': 'justify-center',
    'justify-content-start': 'justify-start',
    'justify-content-end': 'justify-end',
    'justify-content-between': 'justify-between',
    'justify-content-around': 'justify-around',
    'justify-content-evenly': 'justify-evenly',

    // Position
    'position-absolute': 'absolute',
    'position-relative': 'relative',
    'position-fixed': 'fixed',
    'position-sticky': 'sticky',
    'position-static': 'static',

    // Size (Bootstrap fractional → Tailwind)
    'w-100': 'w-full',
    'w-75': 'w-3/4',
    'w-50': 'w-1/2',
    'w-25': 'w-1/4',
    'w-auto': 'w-auto',
    'h-100': 'h-full',
    'h-75': 'h-3/4',
    'h-50': 'h-1/2',
    'h-25': 'h-1/4',
    'mh-100': 'max-h-full',
    'mw-100': 'max-w-full',
    'vh-100': 'h-screen',
    'vw-100': 'w-screen',

    // Font weight
    'fw-bold': 'font-bold',
    'fw-bolder': 'font-extrabold',
    'fw-semibold': 'font-semibold',
    'fw-medium': 'font-medium',
    'fw-normal': 'font-normal',
    'fw-light': 'font-light',
    'fw-lighter': 'font-thin',

    // Font style
    'fst-italic': 'italic',
    'fst-normal': 'not-italic',

    // Text transformations
    'text-uppercase': 'uppercase',
    'text-lowercase': 'lowercase',
    'text-capitalize': 'capitalize',
    'text-decoration-none': 'no-underline',
    'text-decoration-underline': 'underline',
    'text-truncate': 'truncate',
    'text-break': 'break-words',
    'text-wrap': 'whitespace-normal',
    'text-nowrap': 'whitespace-nowrap',

    // Text align
    'text-start': 'text-left',
    'text-end': 'text-right',
    // text-center already same in Tailwind — no-op.

    // Brand semantic colours → Tailwind palette via CSS-var tokens
    'text-muted': 'text-[var(--color-text-muted)]',
    'text-primary': 'text-[var(--color-primary)]',
    'text-secondary': 'text-gray-500',
    'text-success': 'text-emerald-600',
    'text-danger': 'text-red-600',
    'text-warning': 'text-amber-600',
    'text-info': 'text-sky-600',
    'text-light': 'text-gray-300',
    'text-dark': 'text-gray-900',
    'text-body': 'text-[var(--color-text)]',
    'text-white': 'text-white',

    'bg-primary': 'bg-[var(--color-primary)]',
    'bg-secondary': 'bg-gray-500',
    'bg-success': 'bg-emerald-600',
    'bg-danger': 'bg-red-600',
    'bg-warning': 'bg-amber-600',
    'bg-info': 'bg-sky-600',
    'bg-light': 'bg-gray-50',
    'bg-dark': 'bg-gray-900',
    'bg-white': 'bg-white',
    'bg-body': 'bg-[var(--color-surface)]',
    'bg-transparent': 'bg-transparent',

    'bg-primary-subtle': 'bg-violet-50',
    'bg-secondary-subtle': 'bg-gray-100',
    'bg-success-subtle': 'bg-emerald-50',
    'bg-danger-subtle': 'bg-red-50',
    'bg-warning-subtle': 'bg-amber-50',
    'bg-info-subtle': 'bg-sky-50',
    'bg-light-subtle': 'bg-gray-50',

    'text-primary-emphasis': 'text-violet-700',
    'text-secondary-emphasis': 'text-gray-700',
    'text-success-emphasis': 'text-emerald-800',
    'text-danger-emphasis': 'text-red-800',
    'text-warning-emphasis': 'text-amber-800',
    'text-info-emphasis': 'text-sky-800',

    'text-bg-primary': 'bg-[var(--color-primary)] text-white',
    'text-bg-secondary': 'bg-gray-500 text-white',
    'text-bg-success': 'bg-emerald-600 text-white',
    'text-bg-danger': 'bg-red-600 text-white',
    'text-bg-warning': 'bg-amber-500 text-gray-900',
    'text-bg-info': 'bg-sky-500 text-white',
    'text-bg-light': 'bg-gray-100 text-gray-900',
    'text-bg-dark': 'bg-gray-900 text-white',

    // Rounded (Bootstrap-specific)
    'rounded-pill': 'rounded-full',
    'rounded-0': 'rounded-none',
    'rounded-1': 'rounded-sm',
    'rounded-2': 'rounded',
    'rounded-3': 'rounded-lg',
    'rounded-4': 'rounded-xl',
    'rounded-5': 'rounded-2xl',
    'rounded-circle': 'rounded-full',
    'rounded-top': 'rounded-t',
    'rounded-bottom': 'rounded-b',
    'rounded-start': 'rounded-l',
    'rounded-end': 'rounded-r',

    // Border helpers
    'border-top': 'border-t',
    'border-bottom': 'border-b',
    'border-start': 'border-l',
    'border-end': 'border-r',
    // border, border-0 already work identically in Tailwind.

    // Typography sizing
    'small': 'text-sm',
    'lead': 'text-lg leading-relaxed',
    'fs-1': 'text-5xl',
    'fs-2': 'text-4xl',
    'fs-3': 'text-3xl',
    'fs-4': 'text-2xl',
    'fs-5': 'text-xl',
    'fs-6': 'text-base',
    'display-1': 'text-7xl font-bold',
    'display-2': 'text-6xl font-bold',
    'display-3': 'text-5xl font-bold',
    'display-4': 'text-4xl font-bold',
    'display-5': 'text-3xl font-bold',
    'display-6': 'text-2xl font-bold',

    // Line height
    'lh-1': 'leading-none',
    'lh-sm': 'leading-tight',
    'lh-base': 'leading-normal',
    'lh-lg': 'leading-loose',

    // Visibility / accessibility
    'visible': 'visible',
    'invisible': 'invisible',
    'visually-hidden': 'sr-only',
    'visually-hidden-focusable': 'sr-only focus:not-sr-only',

    // Overflow
    'overflow-auto': 'overflow-auto',
    'overflow-hidden': 'overflow-hidden',
    'overflow-visible': 'overflow-visible',
    'overflow-scroll': 'overflow-scroll',
    'overflow-x-auto': 'overflow-x-auto',
    'overflow-y-auto': 'overflow-y-auto',
    'overflow-x-hidden': 'overflow-x-hidden',
    'overflow-y-hidden': 'overflow-y-hidden',

    // Float
    'float-start': 'float-left',
    'float-end': 'float-right',
    'float-none': 'float-none',

    // User-select
    'user-select-all': 'select-all',
    'user-select-auto': 'select-auto',
    'user-select-none': 'select-none',

    // Cursor (Bootstrap supports `cursor-pointer` via util? No — keep)

    // Bootstrap container becomes Tailwind container (which is also called `container`).
    // Same name; Tailwind v4 sizes are slightly different but the visual is the same.
    // Leave 'container' untouched.

    // Grid: row + g-* gap. row becomes `flex flex-wrap -mx-2` in classic Bootstrap,
    // but in Tailwind we prefer a CSS-grid pattern. row → 'flex flex-wrap'; gap is set
    // via g-* below.
    'row': 'flex flex-wrap',
    'g-0': 'gap-0',
    'g-1': 'gap-1',
    'g-2': 'gap-2',
    'g-3': 'gap-3',
    'g-4': 'gap-4',
    'g-5': 'gap-6',
    'gx-1': 'gap-x-1',
    'gx-2': 'gap-x-2',
    'gx-3': 'gap-x-3',
    'gx-4': 'gap-x-4',
    'gy-1': 'gap-y-1',
    'gy-2': 'gap-y-2',
    'gy-3': 'gap-y-3',
    'gy-4': 'gap-y-4',

    // ── Tier 2: button / card / form / alert / badge / table / pagination / list-group ──
    'btn': BTN_BASE,
    'btn-sm': BTN_SIZE_SM,
    'btn-lg': BTN_SIZE_LG,
    'btn-xs': BTN_SIZE_XS,
    'btn-primary': 'bg-violet-600 text-white hover:bg-violet-700',
    'btn-secondary': 'bg-gray-500 text-white hover:bg-gray-600',
    'btn-success': 'bg-emerald-600 text-white hover:bg-emerald-700',
    'btn-danger': 'bg-red-600 text-white hover:bg-red-700',
    'btn-warning': 'bg-amber-500 text-gray-900 hover:bg-amber-600',
    'btn-info': 'bg-sky-600 text-white hover:bg-sky-700',
    'btn-light': 'bg-gray-100 text-gray-900 hover:bg-gray-200',
    'btn-dark': 'bg-gray-900 text-white hover:bg-gray-800',
    'btn-link': 'bg-transparent text-violet-600 hover:underline shadow-none',
    'btn-outline-primary': 'border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white',
    'btn-outline-secondary': 'border-2 border-gray-300 text-gray-700 hover:bg-gray-100',
    'btn-outline-success': 'border-2 border-emerald-600 text-emerald-600 hover:bg-emerald-600 hover:text-white',
    'btn-outline-danger': 'border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white',
    'btn-outline-warning': 'border-2 border-amber-500 text-amber-600 hover:bg-amber-500 hover:text-gray-900',
    'btn-outline-info': 'border-2 border-sky-600 text-sky-600 hover:bg-sky-600 hover:text-white',
    'btn-outline-dark': 'border-2 border-gray-900 text-gray-900 hover:bg-gray-900 hover:text-white',
    'btn-close': 'inline-flex items-center justify-center w-6 h-6 opacity-60 hover:opacity-100',

    'card': CARD_BASE,
    'card-body': 'p-5',
    'card-header': 'px-5 py-3 border-b border-gray-200 font-semibold',
    'card-footer': 'px-5 py-3 border-t border-gray-200',
    'card-title': 'text-lg font-bold mb-2',
    'card-text': '',
    'card-img-top': 'w-full rounded-t-xl',
    'card-img-bottom': 'w-full rounded-b-xl',
    'card-subtitle': 'text-sm text-gray-500',

    'alert': ALERT_BASE,
    'alert-primary': 'bg-violet-50 border-violet-200 text-violet-800',
    'alert-secondary': 'bg-gray-50 border-gray-200 text-gray-800',
    'alert-success': 'bg-emerald-50 border-emerald-200 text-emerald-800',
    'alert-danger': 'bg-red-50 border-red-200 text-red-800',
    'alert-warning': 'bg-amber-50 border-amber-200 text-amber-800',
    'alert-info': 'bg-sky-50 border-sky-200 text-sky-800',
    'alert-light': 'bg-gray-50 border-gray-200 text-gray-800',
    'alert-dark': 'bg-gray-100 border-gray-300 text-gray-900',
    'alert-dismissible': '',

    'badge': 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',

    'form-control': FORM_INPUT,
    'form-control-sm': 'px-2 py-1 text-sm',
    'form-control-lg': 'px-4 py-3 text-lg',
    'form-control-plaintext': 'block w-full bg-transparent',
    'form-select': FORM_INPUT,
    'form-select-sm': 'px-2 py-1 text-sm',
    'form-select-lg': 'px-4 py-3 text-lg',
    'form-label': 'block text-sm font-medium text-gray-700 mb-1',
    'form-text': 'mt-1 text-sm text-[var(--color-text-muted)]',
    'form-check': 'flex items-center gap-2',
    'form-check-input': 'w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500',
    'form-check-label': 'text-sm',
    'form-floating': 'relative',
    'input-group': 'flex w-full items-stretch',
    'input-group-sm': 'text-sm',
    'input-group-lg': 'text-lg',
    'input-group-text': 'inline-flex items-center px-3 bg-gray-50 border border-gray-300',

    'table': 'w-full text-sm border-collapse',
    'table-bordered': 'border border-gray-200',
    'table-striped': 'table-striped-tw',           // resolved via CSS rule in app.css
    'table-hover': 'table-hover-tw',               // resolved via CSS rule in app.css
    'table-responsive': 'overflow-x-auto',
    'table-light': 'bg-gray-50',
    'table-sm': 'text-xs',

    'progress': 'h-2 w-full bg-gray-200 rounded-full overflow-hidden',
    'progress-bar': 'h-full bg-violet-600 transition-all',
    'spinner-border': 'inline-block w-6 h-6 border-2 border-current border-t-transparent rounded-full animate-spin',
    'spinner-border-sm': 'w-4 h-4',
    'spinner-grow': 'inline-block w-6 h-6 bg-current rounded-full animate-pulse',

    'list-group': 'divide-y divide-gray-200 border border-gray-200 rounded-lg bg-white',
    'list-group-item': 'px-4 py-3',
    'list-group-flush': 'rounded-none border-0 divide-y divide-gray-200',

    'breadcrumb': 'flex items-center gap-2 text-sm flex-wrap',
    'breadcrumb-item': '',
    'pagination': 'flex gap-1',
    'page-item': '',
    'page-link': 'inline-flex items-center px-3 py-1 border border-gray-200 rounded hover:bg-gray-50',

    'nav-tabs': 'flex border-b border-gray-200 flex-wrap',
    'nav-pills': 'flex gap-1 flex-wrap',
    'nav-fill': '',
    'nav-link': 'px-4 py-2 rounded-md hover:bg-gray-50 text-sm font-medium',
    'nav-item': '',
    'tab-content': '',
    'tab-pane': '',

    'modal': 'fixed inset-0 z-50 hidden',
    'modal-dialog': 'relative w-full max-w-lg mx-auto mt-12',
    'modal-content': 'bg-white rounded-xl shadow-xl border border-gray-200',
    'modal-header': 'px-5 py-3 border-b border-gray-200 font-semibold flex items-center justify-between',
    'modal-title': 'text-lg font-bold',
    'modal-body': 'p-5',
    'modal-footer': 'px-5 py-3 border-t border-gray-200 flex justify-end gap-2',
    'modal-backdrop': 'fixed inset-0 bg-black/50 z-40',

    'offcanvas': 'fixed top-0 bottom-0 z-50 hidden bg-white',
    'offcanvas-header': 'px-5 py-3 border-b border-gray-200 flex items-center justify-between',
    'offcanvas-title': 'text-lg font-bold',
    'offcanvas-body': 'p-5 overflow-y-auto',
    'offcanvas-start': 'left-0 w-80',
    'offcanvas-end': 'right-0 w-80',

    'accordion': 'flex flex-col gap-2',
    'accordion-item': 'border border-gray-200 rounded-lg overflow-hidden bg-white',
    'accordion-header': '',
    'accordion-button': 'w-full text-left px-4 py-3 flex items-center justify-between font-medium hover:bg-gray-50',
    'accordion-collapse': '',
    'accordion-body': 'px-4 py-3',

    'carousel': 'relative',
    'carousel-inner': 'relative overflow-hidden',
    'carousel-item': 'relative',
    'carousel-caption': 'absolute bottom-4 left-1/2 -translate-x-1/2 text-white',
    'carousel-control-prev': 'absolute left-0 top-0 bottom-0 px-4 hover:bg-black/20',
    'carousel-control-next': 'absolute right-0 top-0 bottom-0 px-4 hover:bg-black/20',

    'navbar': 'flex items-center gap-4 px-4 py-3',
    'navbar-brand': 'font-bold text-lg',
    'navbar-nav': 'flex items-center gap-2',
    'navbar-toggler': 'inline-flex md:hidden items-center justify-center w-10 h-10 rounded',
    'navbar-collapse': 'flex-1',
    'navbar-expand-md': 'md:flex-row',
    'navbar-expand-lg': 'lg:flex-row',
    'navbar-light': 'bg-white text-gray-700',
    'navbar-dark': 'bg-gray-900 text-gray-100',

    'dropdown': 'relative',
    'dropdown-menu': 'absolute z-10 mt-2 min-w-[10rem] bg-white border border-gray-200 rounded-lg shadow-lg py-1 hidden',
    'dropdown-item': 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100',
    'dropdown-divider': 'my-1 border-t border-gray-200',
    'dropdown-toggle': '',
    'dropdown-header': 'px-4 py-2 text-xs font-bold uppercase text-gray-500',

    'fade': '',
    'show': '',
    'collapse': '',
    'collapsing': '',
    'fade-in': '',
    'fade-out': '',

    // Container — also Tailwind, leave alone. But Bootstrap fluid containers:
    'container-fluid': 'w-full px-4',
    'container-lg': 'max-w-screen-lg mx-auto px-4',
    'container-md': 'max-w-screen-md mx-auto px-4',
    'container-sm': 'max-w-screen-sm mx-auto px-4',
    'container-xl': 'max-w-screen-xl mx-auto px-4',
    'container-xxl': 'max-w-screen-2xl mx-auto px-4',

    // Stragglers identified in the second-pass dry run.
    'btn-outline-light': 'border-2 border-gray-200 text-gray-100 hover:bg-gray-100 hover:text-gray-900',
    'btn-check': 'sr-only',  // visually hidden radio/checkbox driving a button
    'form-range': 'w-full accent-violet-600',
    'form-switch': 'flex items-center gap-2',
    'form-control-color': 'h-10 w-16 cursor-pointer p-0 border border-gray-300 rounded-md',
    'form-check-inline': 'inline-flex',
    'modal-dialog-centered': 'flex items-center min-h-full',
    'modal-dialog-scrollable': 'max-h-[90vh] overflow-y-auto',
    'modal-lg': 'max-w-2xl',
    'modal-sm': 'max-w-sm',
    'modal-xl': 'max-w-4xl',
    'modal-fullscreen': 'max-w-none w-screen h-screen rounded-none',
    'list-group-item-action': 'cursor-pointer hover:bg-gray-50',
};

// Column-span helper: Bootstrap col-X / col-md-X / col-lg-X. With `row` → flex,
// columns become widths. col-X uses 12-col grid, so col-X → w-{X/12}.
function expandCol(token) {
    // col → flex-1 (auto)
    if (token === 'col') return 'flex-1';
    // col-auto → w-auto
    if (token === 'col-auto') return 'w-auto';
    // col-12, col-6, col-md-6, etc.
    const m = token.match(/^col(?:-(sm|md|lg|xl|xxl))?-(\d+|auto)$/);
    if (!m) return null;
    const bp = m[1];
    const span = m[2];
    if (span === 'auto') return bp ? `${bpTw(bp)}:w-auto` : 'w-auto';
    const n = Number(span);
    if (!Number.isFinite(n) || n < 1 || n > 12) return null;
    const tw = n === 12 ? 'w-full' : `w-${n}/12`;
    return bp ? `${bpTw(bp)}:${tw}` : tw;
}

function bpTw(bp) {
    return { sm: 'sm', md: 'md', lg: 'lg', xl: 'xl', xxl: '2xl' }[bp] ?? bp;
}

// Tokens we strip silently — they are pure Bootstrap JS state classes and are
// non-functional under our (Tailwind-only) build.
const STRIP = new Set([
    'visible',  // Bootstrap visibility; Tailwind's `visible` is identical but unnecessary
]);

// Tokens we leave alone deliberately — they have no Tailwind equivalent and
// only matter when the corresponding Bootstrap JS plugin is loaded. Phase 1
// strips Bootstrap entirely, so these tokens become inert; reviewer should
// rewrite the surrounding markup to drop them where they appear.
const MANUAL = new Set([
    'tooltip', 'popover',
    'toast', 'toast-header', 'toast-body',
]);

const manualHits = new Map();

// Tokeniser that treats `{{ ... }}` Blade expressions as a single opaque token.
// Splitting on raw whitespace destroyed Blade ternaries like
// `{{ $x === 'a' ? 'p' : 'q' }}` by tokenising on the spaces inside.
function tokeniseClassList(value) {
    const tokens = [];
    let depth = 0;
    let buf = '';
    for (let i = 0; i < value.length; i++) {
        const c = value[i];
        const c2 = value.slice(i, i + 2);
        if (c2 === '{{') { depth++; buf += '{{'; i++; continue; }
        if (c2 === '}}') { depth = Math.max(0, depth - 1); buf += '}}'; i++; continue; }
        if (depth === 0 && /\s/.test(c)) {
            if (buf) { tokens.push(buf); buf = ''; }
            continue;
        }
        buf += c;
    }
    if (buf) tokens.push(buf);
    return tokens;
}

function transformClassList(value, file) {
    const tokens = tokeniseClassList(value);
    const out = [];
    for (const tok of tokens) {
        // Never touch Blade expressions — pass through opaquely.
        if (tok.includes('{{')) { out.push(tok); continue; }
        if (REPLACE[tok] !== undefined) {
            // Replacement may be empty string (drop) or a multi-token string.
            const rep = REPLACE[tok];
            if (rep) out.push(...rep.split(/\s+/));
            continue;
        }
        // col-X / col-md-X handling.
        const col = expandCol(tok);
        if (col) {
            out.push(...col.split(/\s+/));
            continue;
        }
        // Strip silently.
        if (STRIP.has(tok)) continue;
        // Manual review — keep the token but record for the report.
        // Also keep btn-{primary,outline-*,*-subtle} prefix matches.
        if (
            MANUAL.has(tok) ||
            tok.startsWith('btn-') ||
            tok.startsWith('alert-') ||
            tok.startsWith('nav-') ||
            tok.startsWith('navbar-') ||
            tok.startsWith('card-') ||
            tok.startsWith('form-') ||
            tok.startsWith('modal-') ||
            tok.startsWith('offcanvas-') ||
            tok.startsWith('table-') ||
            tok.startsWith('list-group') ||
            tok.startsWith('accordion-') ||
            tok.startsWith('carousel-') ||
            tok.startsWith('breadcrumb') ||
            tok.startsWith('page-') ||
            tok.startsWith('spinner-') ||
            tok.startsWith('progress-')
        ) {
            const arr = manualHits.get(tok) ?? [];
            arr.push(file);
            manualHits.set(tok, arr);
        }
        out.push(tok);
    }
    // De-dupe while preserving order — but ONLY for tokens that don't contain
    // a Blade expression (which is already passed through opaquely above).
    const seen = new Set();
    return out
        .filter((t) => (t.includes('{{') ? true : (seen.has(t) ? false : (seen.add(t), true))))
        .join(' ');
}

async function walk(dir) {
    const out = [];
    for (const entry of await fs.readdir(dir, { withFileTypes: true })) {
        const full = join(dir, entry.name);
        if (entry.isDirectory()) out.push(...await walk(full));
        else if (entry.isFile() && entry.name.endsWith('.blade.php')) out.push(full);
    }
    return out;
}

const files = await walk(ROOT);
let totalChanged = 0;
let totalTokens = 0;
const perFile = [];

for (const file of files) {
    const original = await fs.readFile(file, 'utf8');
    let changed = 0;
    const out = original.replace(/class\s*=\s*"([^"]*)"/g, (full, classes) => {
        const next = transformClassList(classes, file);
        if (next !== classes) changed++;
        return `class="${next}"`;
    });
    if (changed === 0) continue;
    if (!DRY) await fs.writeFile(file, out);
    totalChanged += 1;
    totalTokens += changed;
    perFile.push({ file, changed });
}

console.log(`${totalChanged} files modified, ${totalTokens} class-attribute changes.`);
for (const { file, changed } of perFile.sort((a, b) => b.changed - a.changed).slice(0, 25)) {
    console.log(`  ${changed.toString().padStart(3)}  ${file}`);
}
if (manualHits.size) {
    console.log(`\nManual review needed for these tokens (Tier-2/3, kept in place):`);
    const sorted = [...manualHits.entries()].sort((a, b) => b[1].length - a[1].length);
    for (const [tok, locs] of sorted) {
        console.log(`  ${tok.padEnd(28)} ${locs.length}× across ${new Set(locs).size} files`);
    }
}
