# Self-Hosted Fonts for Noble Nest Academy

Place the following WOFF2 files in this directory. Vite will copy them to
`public/build/fonts/` automatically once the `assetsDir` is configured or
the files are imported.

## Required files

### Baloo 2 (kid/parent surfaces — headings)
Download from: https://fonts.google.com/specimen/Baloo+2
Or via Bunny Fonts CDN: https://fonts.bunny.net/css?family=baloo-2:400,700

| File name              | Weight | Google Fonts API subset |
|------------------------|--------|-------------------------|
| Baloo2-Regular.woff2   | 400    | latin                   |
| Baloo2-Bold.woff2      | 700    | latin                   |

Direct CDN URL pattern (replace with actual static URL after inspecting network tab):
```
https://fonts.gstatic.com/s/baloo2/v21/<hash>.woff2
```

### Nunito (parent/kid body text)
Download from: https://fonts.google.com/specimen/Nunito
Or via Bunny Fonts: https://fonts.bunny.net/css?family=nunito:400,600,700

| File name              | Weight |
|------------------------|--------|
| Nunito-Regular.woff2   | 400    |
| Nunito-SemiBold.woff2  | 600    |
| Nunito-Bold.woff2      | 700    |

### Inter (admin/teacher/maternal adult surfaces)
Download from: https://rsms.me/inter/ (includes WOFF2 files for each weight)
Or via Bunny Fonts: https://fonts.bunny.net/css?family=inter:400,500,600,700

| File name              | Weight |
|------------------------|--------|
| Inter-Regular.woff2    | 400    |
| Inter-Medium.woff2     | 500    |
| Inter-SemiBold.woff2   | 600    |
| Inter-Bold.woff2       | 700    |

## Quick download script (run from project root)

```bash
cd resources/fonts

# Inter (from rsms.me)
curl -L "https://rsms.me/inter/font-files/Inter-Regular.woff2" -o Inter-Regular.woff2
curl -L "https://rsms.me/inter/font-files/Inter-Medium.woff2" -o Inter-Medium.woff2
curl -L "https://rsms.me/inter/font-files/Inter-SemiBold.woff2" -o Inter-SemiBold.woff2
curl -L "https://rsms.me/inter/font-files/Inter-Bold.woff2" -o Inter-Bold.woff2
```

For Baloo 2 and Nunito, use the Google Fonts downloader tool:
```
npx google-fonts-helper --fonts="Baloo 2:400,700" --output=.
npx google-fonts-helper --fonts="Nunito:400,600,700" --output=.
```

## Vite asset handling

Vite automatically copies files referenced via `url()` in CSS into the build output.
The `@font-face` src URLs in `resources/css/app.css` point to `/build/fonts/*`,
which Vite resolves during the build. No extra Vite plugin config needed.

## Fallback

Until font files are present, all fonts fall back to `system-ui, sans-serif`.
Pages will render correctly — just with system fonts.
