# TheOct

TheOct is an **intentionally blank WordPress classic starter theme**.

There is **no frontend yet**: no page layout, header, footer, menus, styling, widgets, JavaScript, posts display, or comments display. WordPress loads an empty `index.php` template that outputs nothing.

## Included files

- `style.css` — WordPress theme registration metadata only (no CSS rules).
- `index.php` — required classic theme template (intentionally outputs nothing).
- `functions.php` — placeholder for future functions (no hooks or enqueued assets).
- `comments.php` — blank comments template.
- `screenshot.png` — blank white preview image.
- `README.txt` — installation instructions.
- `LICENSE.txt` — GPLv2-or-later license notice.

## Install

Download this repository as a ZIP and upload it in WordPress under **Appearance → Themes → Add New → Upload Theme**, or copy the files to `wp-content/themes/theoct/`. Activate **TheOct**. The site frontend will intentionally be blank until templates are implemented.

Version: 1.0.0 · License: GPL-2.0-or-later.

## Custom content types (not activated by the theme)

The separate **TheOct Fields 1.4.0** plugin can import the content model stored at `config/theoct-fields-schema.json`. It defines Services, Projects, Case Studies, Industries, Teams, Pricing, and FAQs, their taxonomies, and field groups. For import instructions and template names, see [config/README.md](config/README.md). The existing frontend remains intentionally blank.

## WordPress dashboard editing

The theme includes **admin-only** tabs, editing checklists, list summaries and taxonomy filters for the seven TheOct Fields content types. No frontend assets or markup are activated. See [config/ADMIN-WORKFLOW.md](config/ADMIN-WORKFLOW.md).

## Compatibility tests

Read [tests/TESTING-CHECKLIST.md](tests/TESTING-CHECKLIST.md) for static CI checks, the read-only WP-CLI smoke test, optional staging-only Playwright tests and the manual WordPress matrix. The theme's frontend is deliberately blank.
