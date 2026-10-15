# TheOct's production assets

These files are intentionally **not enqueued or displayed**. The WordPress theme remains blank until its frontend is built.

- Bootstrap 5.3.8 — `css/bootstrap.min.css` and `js/bootstrap.bundle.min.js` (Popper included).
- GSAP 3.15.0 — `js/gsap.min.js` and optional `js/ScrollTrigger.min.js`.
- Font Awesome Free 7.3.1 — `css/fontawesome.min.css` (base icons) and `css/solid.min.css`, `css/regular.min.css`, `css/brands.min.css`; three matching `webfonts/*.woff2`.
- Montserrat / Outfit / Poppins — 4 styles each: Regular (400), Medium (500), SemiBold (600), Bold (700). Optimized Latin WOFF2 assets under `fonts/`, configured in `css/fonts.css`.

CSS font variables include `--font-montserrat`, `--font-outfit`, `--font-poppins`, and a family and numeric weight per face (for example `--font-outfit-semibold` and `--font-outfit-semibold-weight`).

**To use later**, enqueue `assets/css/fonts.css`, Bootstrap and whichever scripts/icon styles you need using `wp_enqueue_style()`/`wp_enqueue_script()`. None are currently loaded.

Font Awesome requires its base CSS plus relevant style CSS (solid, regular, brands). Their relative font URLs point at `../webfonts/`. `licenses/` contains third-party notices.

Unused source files, demos, maps, full SVG icon collections, and bundled GSAP plugins have been omitted.
