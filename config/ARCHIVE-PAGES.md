# TheOct — Archive Pages (v1.0.2)

Dashboard **TheOct → Archive Pages** provides six private content configuration editors. These are NOT public WordPress Pages and have no public routes of their own.

| Admin record | Original CPT archive | Frontend template |
|---|---|---|
| Services | `/services/` | `archive-service.php` |
| Projects | `/projects/` | `archive-project.php` |
| Case Studies | `/case-studies/` | `archive-case_study.php` |
| Industries | `/industries/` | `archive-industry.php` |
| Teams | `/teams/` | `archive-team.php` |
| Pricing | `/pricing/` | `archive-pricing.php` |

Each Edit screen uses the native WordPress title, body editor and featured-image interface. A native **Archive SEO Settings** meta box stores SEO title, description, canonical URL and robots preference privately. Those fields do **not** emit HTML/meta tags until a frontend is developed.

When TheOct Fields v1.4.0 is active, the theme creates an **Archive Page Settings** field group only if no matching group is already installed. It uses exactly one definition in `config/theoct-fields-schema.json`; no existing group is automatically overwritten. The group includes 4 editable sections:

- **Overview:** eyebrow, subtitle, introduction, extra image/video.
- **Archive Content Settings:** listing layout and columns preference, items-per-page preference, featured related items, empty state, highlights repeater.
- **Call to Action:** optional CTA with conditional link and supporting text.
- **Editorial Notes:** internal note, featured priority.

**Important:** Editing this data does not change query pagination, archive markup or output SEO tags because the theme's frontend templates are intentionally blank.

The internal `theoct_archive` post type is registered with `public=false`, `publicly_queryable=false`, `show_in_rest=false`, `has_archive=false`, and `rewrite=false`. The first admin visit after installing/updating the theme creates exactly six records and stores a version marker. Subsequent visits do not overwrite edited records. Creation and deletion capabilities for these six records are disabled, and deletion/trashing is blocked by theme filters.

Schema now includes **7 regular CPT definitions, 9 taxonomies and 8 field groups**: 24 definitions. The original seven groups continue to use reviewed plugin import; the new archive-only group is created automatically if needed.

To verify: `python3 tests/check_static.py`, `php tests/test-archive-pages.php`, WordPress staging admin edit/save/reload, and the GitHub Actions workflow. Live WordPress tests must be run on a connected staging site.

**Services-specific update:** Services archive now renders content in `archive-service.php` from its own Services Archive Sections fields. The private config editor remains TheOct → Archive Pages → Services. Five other archive frontend templates remain blank. For sections, see `SERVICES-SECTIONS.md`.


### v1.0.5: Services Archive field cleanup

The generic Archive Page Settings group no longer appears on the **Services** archive editing screen, because the theme excludes only that archive post ID via the plugin's visual location rules. It remains for the five other archives. Services Archive uses the dedicated **Services Archive Sections** group (`sa_`). The original `/services/` route and other archive routes are preserved. Previous stored meta values are not deleted.
