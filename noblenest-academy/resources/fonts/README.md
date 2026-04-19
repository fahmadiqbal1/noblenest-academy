# Self-Hosted Fonts for Noble Nest Academy

WOFF2 font files must be placed directly in **`public/fonts/`** so the web
server serves them at `/fonts/*.woff2`. That is the path both the CSS
`@font-face src` declarations and the layout `<link rel="preload">` tags
reference.

This `resources/fonts/` directory only holds the download manifest — Vite
does not process the font files; they are static assets served out of
`public/`.

## Required files (place in `public/fonts/`)

### Baloo 2 (kid/parent surfaces — display headings)
Download from: https://fonts.google.com/specimen/Baloo+2
Or via Bunny Fonts CDN: https://fonts.bunny.net/css?family=baloo-2:400,700

| File name              | Weight |
|------------------------|--------|
| Baloo2-Regular.woff2   | 400    |
| Baloo2-Bold.woff2      | 700    |

### Nunito (parent/kid body text)
Download from: https://fonts.google.com/specimen/Nunito
Or via Bunny Fonts: https://fonts.bunny.net/css?family=nunito:400,600,700

| File name              | Weight |
|------------------------|--------|
| Nunito-Regular.woff2   | 400    |
| Nunito-SemiBold.woff2  | 600    |
| Nunito-Bold.woff2      | 700    |

### Inter (admin/teacher/maternal adult surfaces)
Download from: https://rsms.me/inter/
Or via Bunny Fonts: https://fonts.bunny.net/css?family=inter:400,500,600,700

| File name              | Weight |
|------------------------|--------|
| Inter-Regular.woff2    | 400    |
| Inter-Medium.woff2     | 500    |
| Inter-SemiBold.woff2   | 600    |
| Inter-Bold.woff2       | 700    |

## Quick download (run from project root)

```bash
mkdir -p public/fonts
cd public/fonts

# Inter (rsms.me serves the canonical weights)
curl -L "https://rsms.me/inter/font-files/Inter-Regular.woff2" -o Inter-Regular.woff2
curl -L "https://rsms.me/inter/font-files/Inter-Medium.woff2"  -o Inter-Medium.woff2
curl -L "https://rsms.me/inter/font-files/Inter-SemiBold.woff2" -o Inter-SemiBold.woff2
curl -L "https://rsms.me/inter/font-files/Inter-Bold.woff2"    -o Inter-Bold.woff2
```

For Baloo 2 and Nunito, use a Google Fonts downloader (google-webfonts-helper,
google-fonts-helper, etc.) or grab the WOFF2 URLs from the Bunny Fonts
stylesheet linked above and `curl` them into `public/fonts/`.

## Fallback

Until files exist under `public/fonts/`, fonts fall back to `system-ui,
sans-serif` — pages render fine, just without brand typography.
