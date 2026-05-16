<!-- markdownlint-disable MD013 -->
# Bootstrap Icons → Lucide migration map

Each `bi bi-X` class used in blades maps to a Lucide icon name. Where Lucide has no direct equivalent, we either reuse a close-meaning icon (and accept the visual shift) or fall back to a registered placeholder.

The Lucide registry lives in [app/View/Components/Ui/Icon.php](../../app/View/Components/Ui/Icon.php). Add a name to the registry there before referencing it in blades; otherwise `<x-ui.icon>` renders a small grey placeholder square (its `isKnown()` returns false).

## Mapping table

| `bi bi-…` (legacy) | `<x-ui.icon name="…">` | Note |
|---|---|---|
| `arrow-clockwise` | `rotate-cw` | new |
| `arrow-counterclockwise` | `rotate-ccw` | new |
| `arrow-left` | `arrow-left` | existing |
| `arrow-repeat` | `rotate-cw` | reuses `rotate-cw` |
| `arrow-right` | `arrow-right` | existing |
| `arrow-right-circle` | `circle-arrow-right` | new (Lucide v0.5x renaming) |
| `bar-chart-line` | `bar-chart` | existing — same meaning |
| `book` | `book` | existing |
| `book-half` | `book-open` | existing — closest visual |
| `box-arrow-right` | `log-out` | existing — semantic match (sign-out) |
| `box-arrow-up-right` | `external-link` | existing |
| `calendar-check` | `clipboard-check` | new — Lucide has no calendar-check; use clipboard-check for action visual |
| `calendar-event` | `calendar` | existing |
| `calendar-heart` | `calendar` | existing |
| `calendar-month` | `calendar` | existing |
| `calendar-plus` | `calendar` | existing — could add a new `calendar-plus` but only one usage |
| `calendar-week` | `calendar` | existing |
| `camera-video` | `video` | new |
| `camera-video-fill` | `video` | reuses |
| `camera-video-off` | `video-off` | new |
| `chat-dots` | `message-circle` | new |
| `chat-left-quote` | `message-square-quote` | new |
| `check-all` | `check-check` | new |
| `check-circle` | `check-circle` | existing |
| `check-circle-fill` | `check-circle` | reuses (Lucide uses stroke, not fill) |
| `check-lg` | `check` | existing |
| `check2` | `check` | existing |
| `check2-all` | `check-check` | new |
| `check2-circle` | `check-circle` | existing |
| `chevron-down` | `chevron-down` | existing |
| `chevron-right` | `chevron-right` | existing |
| `clipboard` | `clipboard` | new |
| `clipboard-check` | `clipboard-check` | new |
| `clock` | `clock` | existing |
| `collection` | `folder` | new — `collection` has no Lucide twin; folder is the closest |
| `collection-play` | `circle-play` | new |
| `controller` | `gamepad-2` | new |
| `credit-card` | `credit-card` | existing |
| `credit-card-fill` | `credit-card` | existing |
| `cup-hot` | `coffee` | new |
| `display` | `monitor` | new |
| `download` | `download` | existing |
| `egg-fried` | `egg` | new (food domain) |
| `emoji-smile` | `smile` | new |
| `envelope` | `mail` | existing |
| `eraser` | `eraser` | new |
| `eraser-fill` | `eraser` | reuses |
| `exclamation` | `alert-circle` | existing |
| `exclamation-circle` | `alert-circle` | existing |
| `exclamation-lg` | `alert-circle` | existing |
| `exclamation-octagon-fill` | `octagon-alert` | new (Lucide modern name; older versions had `alert-octagon`) |
| `exclamation-triangle` | `alert-triangle` | existing |
| `exclamation-triangle-fill` | `alert-triangle` | existing |
| `eye` | `eye` | existing |
| `file-earmark-pdf` | `file-text` | existing — generic doc icon (PDF wordmark not in Lucide) |
| `film` | `film` | new |
| `flag` | `flag` | new |
| `flower1` | `flower` | new |
| `flower2` | `flower-2` | new |
| `folder2-open` | `folder-open` | new |
| `gear` | `settings` | existing — semantic match |
| `geo-alt` | `map-pin` | existing |
| `graph-up` | `trending-up` | existing |
| `headphones` | `headphones` | new |
| `heart` | `heart` | existing |
| `heart-fill` | `heart` | existing |
| `heart-pulse` | `activity` | new |
| `hourglass-split` | `hourglass` | new |
| `house-fill` | `home` | existing |
| `house-heart` | `home` | existing (heart suffix lost) |
| `image` | `image` | new |
| `inbox` | `inbox` | new |
| `info-circle` | `info` | existing |
| `info-circle-fill` | `info` | existing |
| `info-lg` | `info` | existing |
| `journal-check` | `book-marked` | new |
| `journal-richtext` | `notebook-pen` | new |
| `lightbulb` | `lightbulb` | new |
| `lightning` | `zap` | existing — same metaphor |
| `lightning-charge` | `zap` | existing |
| `link-45deg` | `link-2` | new |
| `list-ol` | `list-ordered` | new |
| `list-task` | `list-checks` | new |
| `lock-fill` | `lock` | existing |
| `map` | `map` | new |
| `mic` | `mic` | new |
| `mic-fill` | `mic` | reuses |
| `patch-check` | `badge-check` | new |
| `patch-check-fill` | `badge-check` | reuses |
| `pause-circle` | `circle-pause` | new |
| `pause-fill` | `pause` | existing |
| `paypal` | *(remove)* | Phase 4 will rip PayPal UI; render nothing for now |
| `pencil` | `pencil` | existing |
| `pencil-square` | `edit` | existing |
| `people` | `users` | existing |
| `people-fill` | `users` | existing |
| `person-arms-up` | `user-round` | new |
| `person-badge` | `badge-check` | new — reuses badge-check |
| `person-fill` | `user` | existing |
| `play-circle` | `circle-play` | new |
| `play-circle-fill` | `circle-play` | reuses |
| `play-fill` | `play` | existing |
| `plug` | `plug` | new |
| `plug-fill` | `plug` | reuses |
| `plus` | `plus` | existing |
| `plus-circle` | `circle-plus` | new |
| `plus-lg` | `plus` | existing |
| `robot` | `bot` | new |
| `rocket-takeoff` | `rocket` | existing |
| `save` | `save` | new |
| `search` | `search` | existing |
| `send` | `send` | existing |
| `send-fill` | `send` | existing |
| `shield-check` | `shield-check` | new |
| `skip-backward-fill` | `skip-back` | new |
| `skip-forward-fill` | `skip-forward` | new |
| `star-fill` | `star` | existing |
| `stars` | `sparkles` | existing — meaning match |
| `stop-circle` | `circle-stop` | new |
| `stopwatch` | `timer` | new |
| `sun` | `sun` | new |
| `table` | `table` | new |
| `telephone-x-fill` | `phone-off` | new |
| `trash` | `trash` | existing |
| `trophy` | `trophy` | existing |
| `x` | `x` | existing |
| `x-circle` | `x-circle` | existing |
| `yin-yang` | *(custom)* | No Lucide equivalent. Used in one cultural-module surface; render as a custom 2-color SVG inline (out of registry) or fall back to `sparkles`. Decision: fall back to `sparkles` for Phase 1; revisit during Phase 3 cultural modules. |

## Migration mechanics

A `bi bi-X` invocation in a blade looks like one of these shapes:

```html
<!-- shape 1: plain icon -->
<i class="bi bi-trash"></i>

<!-- shape 2: with size class -->
<i class="bi bi-pencil text-xl"></i>

<!-- shape 3: alongside text in a button -->
<button class="btn btn-primary">
    <i class="bi bi-plus"></i> Add
</button>
```

After migration:

```html
<x-ui.icon name="trash" />
<x-ui.icon name="pencil" class="w-6 h-6" />
<x-ui.button variant="primary" icon="plus">Add</x-ui.button>
```

The `<x-ui.button>` component already has an `icon` slot that calls `<x-ui.icon>` internally — when the `bi-*` lives inside a `btn-*`, the icon migration and the Bootstrap migration happen together.

## Verification step

After every blade is migrated, run:

```bash
grep -r "bi bi-" resources/views/   # must return zero matches
php artisan view:cache              # any registry miss surfaces as a placeholder square
```

A feature test `tests/Feature/Phase1/IconMigrationTest.php` will iterate every public route in the route table, render it, and assert that no rendered HTML contains the `bi bi-` substring and no `<x-ui.icon>` rendered the grey-placeholder fallback.
