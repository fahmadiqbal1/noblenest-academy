# Accessibility — Noble Nest Academy

## Running pa11y-ci

```bash
# Start the dev server first
php artisan serve

# In a second terminal (after npm install):
npm run a11y
```

Or run against a specific URL:
```bash
npx pa11y-ci http://localhost:8000/pricing
```

## a11y Checklist for Rebuilt Surfaces

### Landmark Roles
- [ ] `<main>` wraps primary content on every page
- [ ] `<nav>` used for navigation regions; each has a distinct `aria-label`
- [ ] `<header>`, `<footer>`, `<aside>` used semantically where appropriate
- [ ] No orphaned content outside landmark regions

### Heading Order
- [ ] One `<h1>` per page (the page title or hero headline)
- [ ] Headings follow strict nesting: h1 → h2 → h3; no skipped levels
- [ ] Decorative headings use `aria-hidden="true"` if they break logical flow

### Form Labels
- [ ] Every `<input>`, `<select>`, `<textarea>` has an associated `<label>` (for/id pair or `aria-label`)
- [ ] Required fields have `aria-required="true"` and visible indicator
- [ ] Error messages linked via `aria-describedby`
- [ ] Fieldsets use `<legend>` for grouped inputs (radio/checkbox)

### Focus Visibility
- [ ] `focus-visible` ring is visible on all interactive elements (2px min)
- [ ] Focus order follows visual reading order (no `tabindex > 0`)
- [ ] Skip-to-main-content link is the first focusable element on each page

### Color Contrast
- [ ] Body text (font-size >= 16px): minimum **4.5:1** contrast ratio vs background
- [ ] Large text (font-size >= 18px bold or 24px): minimum **3:1**
- [ ] UI components (borders, icons, input outlines): minimum **3:1**
- [ ] Checked: `--color-brand-600` (#7C3AED) on white → passes AA for large text; check body text sizes
- [ ] Checked: `--color-accent` tones on their card backgrounds

### Keyboard Reachability
- [ ] All interactive elements reachable via Tab/Shift+Tab
- [ ] Dropdowns operable with arrow keys; Escape closes them
- [ ] Modals trap focus within the dialog while open
- [ ] `<x-ui.dropdown>` and `<x-ui.menu>` pass keyboard nav checks

### aria-live for Dynamic Content
- [ ] `<x-ui.toast>` has `aria-live="polite"` and `role="status"`
- [ ] Form inline validation errors announced via `aria-live="assertive"`
- [ ] Activity step-player progress updates announced

### Focus Trap in Modals
- [ ] `<x-ui.modal>` traps focus: Tab cycles within the modal, Escape closes
- [ ] Body scroll locked while modal is open (`overflow: hidden`)
- [ ] Focus returns to the trigger element on modal close

## Known Issues / Post-Launch

| Issue | Surface | Priority | Notes |
|-------|---------|----------|-------|
| `classroom/room.blade.php` still uses Bootstrap — not yet a11y-audited | Classroom | Medium | Bootstrap 5 has reasonable a11y but custom overrides may break it |
| Font preloads point to `/fonts/*.woff2` — files not yet downloaded | All layouts | High | System-ui fallback renders correctly; download fonts before launch |
| No skip-to-content link implemented yet | All layouts | Medium | Add `<a href="#main-content" class="sr-only focus:not-sr-only">Skip to content</a>` as first `<body>` child |
| `aria-live` on toast needs verification with screen reader | All | Low | Tested visually; needs VoiceOver/NVDA pass |
| Color contrast on `--color-accent-400` badge text not formally audited | Various | Low | Run against WCAG color contrast checker |
