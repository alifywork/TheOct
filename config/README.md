# TheOct content model — TheOct Fields 1.4.0

This repository stores the **theme template placeholders** and a **portable data-model schema** for your separate **TheOct Fields** plugin. The theme itself intentionally renders no frontend HTML, CSS or JavaScript. Existing asset files remain un-enqueued.

## Install and register

1. Install and activate your separate `theoct-fields-1.4.0.zip` plugin in WordPress (**requires WordPress 6.5+ and PHP 8.1+**).
2. Go to **TheOct Fields → Tools → Import JSON** and select **`config/theoct-fields-schema.json`**. Review the preview before applying. This imports the seven CPT definitions, nine taxonomies and seven field groups.
3. Go to **Settings → Permalinks → Save Changes** once to refresh rewrite rules.
4. Manage records under the new WordPress CPT menus. All theme archive and single PHP templates deliberately output nothing.

The JSON file in GitHub is a portable import file, **not automatically active**. Installing/activating the plugin and importing into your WordPress site's database is required. The plugin ZIP is not embedded or duplicated inside the theme.

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

The schema imports definition posts with deterministic keys, so imports can be repeated by your plugin. **Preview first**, especially on a site with existing CPTs/taxonomies, to avoid key/rewrite collisions and inadvertent updates. No imported content records are created. No WordPress database connection was available for live activation/testing.
