# TheOct content model — TheOct Fields 1.4.0

This repository stores seven CPT definitions, seven base field groups and an additional separate Services layout field-group bundle for **TheOct Fields**. Services archive and single pages now render a reference-inspired layout with no shared header/footer; other CPT frontends remain blank. Only Services-specific fonts/CSS are enqueued.

## Install and register

1. Install and activate the latest **TheOct theme (v1.0.5 or later)**; reload your WordPress Dashboard. **Seven CPT menus and nine taxonomies now register automatically even without the plugin.**
2. For the detailed custom fields, install and activate the separate `theoct-fields-1.4.0.zip` plugin (**requires WordPress 6.5+ and PHP 8.1+**).
3. On the WordPress **Dashboard**, click **Preview TheOct Fields Setup**. The theme passes its bundled JSON to the plugin's own import-preview workflow. Review the 23 definitions and explicitly click **Apply Import**. Alternatively, use **TheOct Fields → Tools → Import JSON** and select the bundled `config/theoct-fields-schema.json` file.
4. Go to **Settings → Permalinks → Save Changes** if you need to refresh existing route settings.
5. Manage entries using the left-side WordPress CPT menus. All theme archive and single PHP templates deliberately output nothing.

**CPT menus do not require the plugin or a JSON import.** Field groups do require the plugin and a reviewed import. If TheOct Fields is installed and has already registered any CPT or taxonomy, the plugin's definitions win and the theme safely skips that registration. The plugin ZIP is never bundled into the theme.

## Archive Page configuration editors

**Dashboard → TheOct → Archive Pages** lists six protected configuration records. The theme seeds them once; original public archive URLs remain unchanged. Title, main description, image, and native SEO settings are editable without TheOct Fields. With plugin v1.4.0 active, one non-overwriting archive-specific group is auto-created. Schema now has 7 CPT + 9 taxonomies + 7 base field groups (23 definitions). See ARCHIVE-PAGES.md.

## Post types and routes

| Post type key | URL base | Archive | Single template |
|---|---|---|---|
| `service` | `/services/` | `archive-service.php` | `single-service.php` |
| `project` | `/projects/` | `archive-project.php` | `single-project.php` |
| `case_study` | `/case-studies/` | `archive-case_study.php` | `single-case_study.php` |
| `industry` | `/industries/` | `archive-industry.php` | `single-industry.php` |
| `team` | `/teams/` | `archive-team.php` | `single-team.php` |
| `pricing` | `/pricing/` | `archive-pricing.php` | Not designed |
| `faq` | `/faqs/{entry}/` | Disabled | `single-faq.php` |

**Routing limitation:** TheOct Fields currently makes public Pricing posts individually queryable, even though the theme provides no Pricing single design. The archive flag is enabled for Pricing. An FAQ archive is disabled, while individual FAQ permalinks are available. No frontend is displayed yet.

## Taxonomies

- Services: `service_category` (hierarchical).
- Projects: `project_category` (hierarchical), `project_technology` (tags).
- Case Studies: `case_study_category` (hierarchical).
- Industries: `industry_category` (hierarchical).
- Teams: `team_department` (hierarchical), `team_skill` (tags).
- Pricing: `pricing_category` (hierarchical).
- FAQs: `faq_category` (hierarchical).

Industry content is modeled as the `industry` CPT, not duplicated as a project taxonomy; other records can link to industry posts through TheOct Fields relationship fields.

## Projects vs Case Studies

- Project = portfolio delivery: images, client, link, services and technologies.
- Case Study = challenge, approach, outcomes and metrics.
- Each Case Study has a `related_project` field (Post Object) that may link to a Project.
- Each Project has a `related_case_studies` field (Relationship) to show linked Case Studies.
- Both fields are configured with the plugin's built-in bidirectional sync mechanism. Use TheOct Fields when editing these relations.

## Existing site safety

Import updates definitions with matching keys. Export your current site schema and back up the database before importing over previously edited field groups. This theme still renders no frontend. See `FIELD-GROUPS.md` for field specifications and caveats.


## Services-only frontend

See [SERVICES-SECTIONS.md](SERVICES-SECTIONS.md) for the two extra auto-installed field groups in `config/services-layout-schema.json`. This adds seven screenshot-inspired sections to the Services archive/single pages only; it does not change other blank CPT templates or the shared header/footer.

## Service field consolidation (v1.0.5)

Two Service editor groups from older versions (`theoct_field_group_service` and `theoct_field_group_services_single_sections`) are put in **Draft**, not deleted. The generic Archive Page Settings group is excluded from only the Services Archive post. Other five archive editors remain unaffected. The active bundles are `services-layout-schema.json` (one `sa_` Archive group) and `single-service-detail-schema.json` (one `ssd_` Single group). Only these two routes render their layout; no site header/footer has been added.
