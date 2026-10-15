# TheOct

TheOct is an **intentionally blank WordPress classic starter theme**.

There is **no frontend yet**: no page layout, header, footer, menus, styling, widgets, JavaScript, posts display, or comments display. WordPress loads an empty `index.php` template that outputs nothing.

## Included files

- `style.css` — WordPress theme registration metadata only (no CSS rules).
- `index.php` — required classic theme template (intentionally outputs nothing).
- `functions.php` — loads WordPress content registrations and dashboard-only enhancements; enqueues no public assets.
- `comments.php` — blank comments template.
- `screenshot.png` — blank white preview image.
- `README.txt` — installation instructions.
- `LICENSE.txt` — GPLv2-or-later license notice.

## Install

Download this repository as a ZIP and upload it in WordPress under **Appearance → Themes → Add New → Upload Theme**, or copy the files to `wp-content/themes/theoct/`. Activate **TheOct**. The site frontend will intentionally be blank until templates are implemented.

Version: 1.0.1 · License: GPL-2.0-or-later.

## Dashboard menus — automatically enabled

Activating the theme **automatically registers** Services, Projects, Case Studies, Industries, Teams, Pricing and FAQs, plus their nine taxonomies, directly from `config/theoct-fields-schema.json`. They will appear in **WordPress Dashboard → left sidebar**, even if the separate TheOct Fields plugin has not been installed. The public frontend stays entirely blank.

For the detailed **custom fields**, activate **TheOct Fields 1.4.0**. On your Dashboard, use the **Preview TheOct Fields Setup** button to review the bundled schema in the plugin's own import preview; click **Apply Import** explicitly to add the field groups. No schema is imported silently or used to overwrite plugin settings without approval. If the plugin has already registered the types, its registration takes precedence. See [config/README.md](config/README.md).

## WordPress dashboard editing

The theme includes **admin-only** tabs, editing checklists, list summaries and taxonomy filters for the seven TheOct Fields content types. No frontend assets or markup are activated. See [config/ADMIN-WORKFLOW.md](config/ADMIN-WORKFLOW.md).

## Compatibility tests

Read [tests/TESTING-CHECKLIST.md](tests/TESTING-CHECKLIST.md) for static CI checks, the read-only WP-CLI smoke test, optional staging-only Playwright tests and the manual WordPress matrix. The theme's frontend is deliberately blank.
