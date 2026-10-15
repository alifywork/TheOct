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

Download this repository as a ZIP and upload it in WordPress under **Appearance → Themes → Add New → Upload Theme**, or copy the files to `wp-content/themes/theoct/`. Activate **TheOct**. The site frontend will intentionally be blank until templates are implemented.

Version: 1.0.4 · License: GPL-2.0-or-later.

## Dashboard menus — automatically enabled

Activating the theme **automatically registers** Services, Projects, Case Studies, Industries, Teams, Pricing and FAQs, plus their nine taxonomies, directly from `config/theoct-fields-schema.json`. They will appear in **WordPress Dashboard → left sidebar**, even if the separate TheOct Fields plugin has not been installed. The public frontend stays entirely blank.

For the detailed **custom fields**, activate **TheOct Fields 1.4.0**. On your Dashboard, use the **Preview TheOct Fields Setup** button to review the bundled schema in the plugin's own import preview; click **Apply Import** explicitly to add the field groups. No schema is imported silently or used to overwrite plugin settings without approval. If the plugin has already registered the types, its registration takes precedence. See [config/README.md](config/README.md).

## TheOct Archive Pages

Open **TheOct → Archive Pages** in WordPress admin to edit six private archive configurations (Services, Projects, Case Studies, Industries, Teams and Pricing). Edit title, content, featured image and SEO metadata. TheOct Fields 1.4.0 adds an archive-only advanced custom field group automatically if none exists. These records have no public routes, while all six original CPT archive URLs remain unchanged. No frontend content is rendered.

Full guide: [config/ARCHIVE-PAGES.md](config/ARCHIVE-PAGES.md).

## WordPress dashboard editing

The theme includes **admin-only** tabs, editing checklists, list summaries and taxonomy filters for the seven TheOct Fields content types. No frontend assets or markup are activated. See [config/ADMIN-WORKFLOW.md](config/ADMIN-WORKFLOW.md).

## Compatibility tests

Read [tests/TESTING-CHECKLIST.md](tests/TESTING-CHECKLIST.md) for static CI checks, the read-only WP-CLI smoke test, optional staging-only Playwright tests and the manual WordPress matrix. The theme's frontend is deliberately blank.

## Services landing-page sections (without header/footer)

The **Services archive** (`/services/`) and **Single Service** (`/services/{slug}/`) now render seven responsive, screenshot-inspired sections: Hero, dark visual band, deliverables, service/capability cards, industries & typical projects, process, and final CTA. The archive cards use real published Service posts; single capability cards use their own repeater. These two pages intentionally **have no site header or footer**, as requested. Every other archive/single/frontend template stays blank.

Install and activate TheOct Fields v1.4.0 to get two additive field groups: **Services Archive Sections** (only the Services Archive editor) and **Single Service Sections** (individual Service editors). New groups import automatically on an admin visit without overwriting your existing field groups. Documentation: [config/SERVICES-SECTIONS.md](config/SERVICES-SECTIONS.md).

## Single Service screenshot fields (admin-only addition)

The 10-part **Single Service — Screenshot Detail Sections** field group is defined in `config/single-service-detail-schema.json`. When TheOct Fields v1.4.0 is active, an administrator visiting the WordPress dashboard installs this additive group once. It appears only on **Services → Add New / Edit**. It contains 81 top-level editor entries for hero, blue visual/stat, problem/standards, deliverables, process, stack, automation examples, testimonials, FAQs, and contact/form configuration. It does **not** change a single frontend PHP/CSS file, any Services Archive fields, headers or footers. Existing Single Service Sections fields continue working. The form fields are editorial configuration only—not a working contact form. See [config/SINGLE-SERVICE-FIELDS.md](config/SINGLE-SERVICE-FIELDS.md).
