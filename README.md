# TheOct

TheOct is a lightweight WordPress classic starter theme with **editable Services landing and detail layouts**. All other public templates, including the homepage, remain intentionally blank. Shared site header and footer are not implemented.

## Included files

- `style.css` — WordPress theme registration metadata only (no CSS rules).
- `index.php` — required classic theme template (intentionally outputs nothing).
- `functions.php` — loads WordPress content registration, dashboard enhancements and Services-only frontend assets.
- `comments.php` — blank comments template.
- `screenshot.png` — blank white preview image.
- `README.txt` — installation instructions.
- `LICENSE.txt` — GPLv2-or-later license notice.

## Install

Download this repository as a ZIP and upload it in WordPress under **Appearance → Themes → Add New → Upload Theme**, or copy the files to `wp-content/themes/theoct/`. Activate **TheOct**. Only the Services archive and individual Service posts have public layouts. All other routes intentionally remain blank.

Version: 1.0.5 · License: GPL-2.0-or-later.

## Dashboard menus — automatically enabled

Activating the theme **automatically registers** Services, Projects, Case Studies, Industries, Teams, Pricing and FAQs, plus their nine taxonomies, directly from `config/theoct-fields-schema.json`. They will appear in **WordPress Dashboard → left sidebar**, even if the separate TheOct Fields plugin has not been installed. The Services archive and Single Service routes render; all other public templates stay blank.

For the detailed **custom fields**, activate **TheOct Fields 1.4.0**. On your Dashboard, use the **Preview TheOct Fields Setup** button to review the bundled schema in the plugin's own import preview; click **Apply Import** explicitly to add the field groups. No schema is imported silently or used to overwrite plugin settings without approval. If the plugin has already registered the types, its registration takes precedence. See [config/README.md](config/README.md).

## TheOct Archive Pages

Open **TheOct → Archive Pages** in WordPress admin to edit six private archive configurations (Services, Projects, Case Studies, Industries, Teams and Pricing). Edit title, content, featured image and SEO metadata. TheOct Fields 1.4.0 adds an archive-only advanced custom field group automatically if none exists. These records have no public routes, while all six original CPT archive URLs remain unchanged. No frontend content is rendered.

Full guide: [config/ARCHIVE-PAGES.md](config/ARCHIVE-PAGES.md).

## WordPress dashboard editing

The theme includes **admin-only** tabs, editing checklists, list summaries and taxonomy filters for the seven TheOct Fields content types. No frontend assets or markup are activated. See [config/ADMIN-WORKFLOW.md](config/ADMIN-WORKFLOW.md).

## Compatibility tests

Read [tests/TESTING-CHECKLIST.md](tests/TESTING-CHECKLIST.md) for static CI checks, the read-only WP-CLI smoke test, optional staging-only Playwright tests and the manual WordPress matrix. The theme's frontend is deliberately blank.

## Services Archive & Single Service — current fields

**Theme v1.0.5:** The Services Archive and Single Service have their own separate screenshot-matched templates and active field groups:

| Page | Active field group | Meta prefix | Rendered template |
|---|---|---|---|
| `/services/` | Services Archive Sections (34 fields) | `sa_` | `template-parts/services-layout.php` |
| `/services/{slug}/` | Single Service — Screenshot Detail Sections (81 fields) | `ssd_` | `template-parts/single-service-detail.php` |

On a WordPress admin visit with TheOct Fields 1.4.0 active, the theme drafts the **old Service Details** and **old Single Service Sections** field groups. It also excludes the Services Archive configuration record from the generic **Archive Page Settings** group; that group remains available to the other five archives. Old saved metadata and content are **not deleted**.

New editor fields auto-install only if not already present. The Contact Form section shows a non-submitting visual preview until a form handler/plugin is configured. No site navigation/header/footer is added, and non-Service CPT frontends remain blank.

Detailed guides: [Services Archive](config/SERVICES-SECTIONS.md) · [Single Service](config/SINGLE-SERVICE-FIELDS.md) · [Archive Pages](config/ARCHIVE-PAGES.md). Back up your WordPress database before replacing an active theme and review stored field-group changes in TheOct Fields after upgrading.
