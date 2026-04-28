# Launch TODO — Outstanding Items

## Image Optimization

`vite-plugin-image-optimizer` has been added to `package.json` but is **commented out** in
`vite.config.js` because it requires `sharp` (a native libvips binding) to process PNG/JPG,
and `svgo` for SVG. These must be confirmed available in the build environment before enabling.

### Steps to enable:

1. After running `npm install`, verify sharp is available:
   ```bash
   node -e "require('sharp')"
   ```
   If this fails, install it separately:
   ```bash
   npm install --save-dev sharp
   ```

2. Uncomment the `ViteImageOptimizer` import and plugin block in `vite.config.js`.

3. Re-run `npm run build` and confirm images in `public/images/` are compressed.

### Image directories to optimize:
- `public/images/` — static public images
- `resources/images/` — source images referenced from CSS/JS (processed by Vite)
  _(Create this directory if you add source images that Vite should process.)_

## Self-hosted Fonts

WOFF2 files must be downloaded before launch. See `resources/fonts/README.md` for URLs and
the quick-download script. Files must end up in `public/fonts/` so the preload `<link>` tags
in layouts resolve correctly.

Required files:
- `public/fonts/Baloo2-Regular.woff2`
- `public/fonts/Baloo2-Bold.woff2`
- `public/fonts/Nunito-Regular.woff2`
- `public/fonts/Nunito-SemiBold.woff2`
- `public/fonts/Inter-Regular.woff2`
- `public/fonts/Inter-SemiBold.woff2`
