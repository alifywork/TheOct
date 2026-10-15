# TheOct — Single Service screenshot fields & rendering (v1.0.5)

**Editor:** WordPress → Services → Add New / Edit Service. **Frontend:** `/services/{service-slug}/`.

The **only theme-managed visible group** for individual Services is **Single Service — Screenshot Detail Sections** (`ssd_` prefix). It contains **81 top-level fields**, **10 tabs**, and **11 repeaters**. Older Service Details and Single Service Sections groups are set to **Draft** by the theme (not deleted); their saved field values remain untouched in the database.

| Screenshot section | What the Single Service template renders |
|---|---|
| 01 Hero | Page title or `ssd_hero_heading`, breadcrumb, subtitle, two CTA links and optional anchor nav |
| 02 Blue visual | `ssd_visual_background` (and mobile variant), primary metric/value/label and extra metrics |
| 03 Problem & standards | Problem rich text and repeatable standards checklist |
| 04 Deliverables | Repeatable columns with nested bullet lists |
| 05 Process | Reorderable numbered step cards and highlighted step style |
| 06 Technology stack | Named partner/tool logos and external links |
| 07 Automation examples | Background image, example cards with input → automation → output |
| 08 Testimonials | Author name, role/photo and quote; **only rows with the editorial consent flag enabled are displayed** |
| 09 FAQ | Native `<details>` accordion from `ssd_faq_items` plus linked published FAQ posts |
| 10 Contact / discovery | Headline, blue image, checklist, phone/email and a **non-submitting** form preview |

**Images and copy:** Use your own uploads and approved content. The template renders empty repeaters as empty grids/sections rather than inventing testimonials, client results or case studies.

**No working contact handler yet:** The form's labels/options are visible, but the inputs and submit are disabled to avoid pretending that inquiries are delivered. Fields for future destination email, success/error messages and privacy policy are stored but not used for email. Ask for form integration when you want a real submission workflow.

**Compatibility:** the theme still loads no shared header/footer. Other CPT templates are blank. The Services Archive keeps its independent `sa_` schema; neither its fields nor template are replaced by the Single Service screenshot.

**Activation/update:** Activate TheOct Fields 1.4.0, update TheOct, then open WordPress admin as an Administrator. The new group imports once if absent. If old groups already exist, they are drafted reversibly. Take a database backup before updating because your installed schema configuration changes (although values are never deleted).

**Frontend code:** `single-service.php` → `template-parts/single-service-detail.php` with `assets/css/single-service-detail.css`. Auto-generated WordPress image HTML is escaped by core, rich text is passed through `wp_kses_post`, links use `esc_url`.

**Local test:** static and PHP syntax in GitHub Actions; for live validation, create staging Service content and test nested repeaters, FAQs, responsive layouts, sample form preview, page IDs and permalink handling.
