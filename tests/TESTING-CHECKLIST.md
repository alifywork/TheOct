# TheOct — CPT Admin Testing & Compatibility Checklist

**Scope:** TheOct classic WordPress theme and separately installed **TheOct Fields v1.4.0** plugin. The public homepage and non-Service CPT templates remain deliberately blank; Services archive and single now render sections without a shared header/footer.

## 0. Safety & staging prerequisites

- [ ] Use **staging**, not production; backup the WordPress database and export existing TheOct Fields definitions.
- [ ] Activate TheOct and TheOct Fields v1.4.0; import config/theoct-fields-schema.json via **TheOct Fields → Tools → Import JSON**.
- [ ] Inspect import preview first: **7 CPTs, 9 taxonomies, 8 field groups** (24 definitions). Avoid unexpected changes to existing keys.
- [ ] Save **Settings → Permalinks** once after first import. Enable WP_DEBUG_LOG on staging and disable WP_DEBUG_DISPLAY.
- [ ] Use disposable test content, roles and credentials; never commit the uploaded plugin ZIP, production data or credentials to this public repository.
- [ ] Browser tests require the explicit WP_E2E_STAGING=1 opt-in. Opening Add New may create WordPress auto-drafts, but tests never click Publish/Update.

## 1. Automated checks

### A. Static checks (local and GitHub Actions)

Run from the theme repository root:

~~~bash
python3 tests/check_static.py
~~~

- [ ] **PASS:** Seven post types (correct keys and archives), nine taxonomy mappings and eight field groups (including archive settings).
- [ ] **PASS:** Unique import keys and field names, supported field types, correctly nested repeater/flexible layouts, conditional dependencies.
- [ ] **PASS:** Projects ↔ Case Studies reciprocal relationship config.
- [ ] **PASS:** All intentional blank templates, blank style.css, admin-only PHP/JS/CSS.
- [ ] GitHub Actions → **TheOct quality checks** also runs PHP syntax checks on all PHP files and Node syntax checks on JavaScript.

To check compatibility against the supplied **private** plugin ZIP without uploading it into Git:

~~~bash
python3 tests/check_static.py --plugin /path/to/theoct-fields-1.4.0.zip
~~~

- [ ] **PASS:** Plugin v1.4.0, registration priority, field handlers, admin script handles, meta box DOM hooks, storage-prefix option.
- [ ] On a future plugin update, rerun contract checks and inspect any failing selector/hook before rollout.

### B. WP-CLI integration smoke checks (read-only)

On a **staging WordPress root** where the plugin is active and schema imported:

~~~bash
wp eval-file wp-content/themes/theoct/tests/wp-smoke.php
~~~

- [ ] **PASS:** PHP 8.1+, WordPress 6.5+, active TheOct theme and TheOct Fields v1.4.0.
- [ ] **PASS:** Seven registered CPTs, nine registered taxonomies, rewrite bases, archive flags, REST support.
- [ ] **PASS:** Imported definitions exist under stable keys, expected 3–5 section-tab markers per field group.
- [ ] **PASS:** Theme admin assets are not enqueued on a CLI/public request.
- [ ] Script creates/edits/deletes **no** content, taxonomies, options or permalinks.

### C. Browser admin tests (staging-only Playwright)

From the repository root:

~~~bash
npm install
npx playwright install chromium
export WP_E2E_STAGING=1
export WP_BASE_URL=https://staging.example.com
export WP_ADMIN_USER=staging-editor
export WP_ADMIN_PASSWORD='staging-test-password'
npm run test:admin
~~~

- [ ] **PASS:** 8 cases total — 7 CPT editors/list screens and blank homepage.
- [ ] Each CPT: filter dropdown, key-details column, correct tab count, tab switching with mouse and Home key, Show All/Show One, editorial checklist.
- [ ] Browser login must use a non-2FA/non-CAPTCHA staging account with adequate permissions (or adapt the test login helper).
- [ ] No post form is submitted by this suite; WordPress may still create auto-drafts when Add New is opened.

## 2. Run these manual steps for each CPT

| ID | CPT | Archive | Single | Tab sections |
|---|---|---|---|---:|
| CPT-01 | Services / service | Yes | Yes | 4 |
| CPT-02 | Projects / project | Yes | Yes | 4 |
| CPT-03 | Case Studies / case_study | Yes | Yes | 5 |
| CPT-04 | Industries / industry | Yes | Yes | 4 |
| CPT-05 | Teams / team | Yes | Yes | 4 |
| CPT-06 | Pricing / pricing | Yes | No custom single | 4 |
| CPT-07 | FAQs / faq | Disabled | Yes | 3 |

For **each** CPT:

- [ ] Add New opens, correct title placeholder shown, metabox and tab sections visible, no fatal PHP/JS errors.
- [ ] Switch sections by mouse and keyboard: Left/Right, Home, End; selected states and focus ring work.
- [ ] Show All reveals every section; Show One collapses to selected section. No custom-field values are lost.
- [ ] Sidebar Content Checklist shows last saved completion state; incomplete steps do not block saving.
- [ ] Fill title, rich text, text field, a taxonomy term, nested repeater, media/URL where available; **Save Draft** and reload. Values persist exactly.
- [ ] Change one field in a different tab, Save Draft and reload; hidden-tab values remain intact.
- [ ] Add/reorder/remove nested repeater rows and flexible layout sections; validate allowed formats and row limits.
- [ ] Verify correct field-level conditional visibility, required controls and error feedback.
- [ ] Enter quotes, HTML-looking text and ampersands into text fields; no script execution or broken escaping.
- [ ] List table has key details, appropriate featured-image thumbnail, correct taxonomy dropdown and filtering.
- [ ] CPT content routes respond, but **no theme-generated visual frontend** is output.

## 3. CPT-specific field tests

- [ ] **Services:** intro, benefits, process, deliverables, tools, galleries, related Project/Industry, CTA toggle and conditional link.
- [ ] **Projects:** client, logo, date/status, technologies, gallery, results, Flexible Content (text/image/quote/video), linked Case Study.
- [ ] **Case Studies:** challenge, approach, metrics with before/result, evidence, testimonial consent flag; connect a Project and verify reciprocal link.
- [ ] **Industries:** opportunities, challenges, use cases, specialist links, CTA and related Services.
- [ ] **Teams:** bio, photo, skills, certification rows, social links, privacy notes; WordPress Page Attributes → Order controls list order.
- [ ] **Pricing:** fixed, starting-at, free, custom quote; numeric amounts and Money add-ons, features, CTA and optional secondary CTA.
- [ ] **FAQs:** short/full answers, Video and External conditional controls, answer steps, review workflow, category, manual Order.
- [ ] **Relationship caveat:** plugin v1.4.0 relationship pickers may not restrict results by CPT; editors must select the intended content type.

## 4. Plugin compatibility, permissions & negative scenarios

- [ ] With plugin disabled: no theme-generated fatal error; frontend remains blank; custom CPT editing features are not expected.
- [ ] Before schema import: no CPT definition pages expected; the theme remains stable.
- [ ] Re-import JSON twice on a **disposable staging DB**: definition keys remain unique, without duplicated CPT/field-group definitions.
- [ ] With JavaScript disabled: plugin's original custom-field layout stays usable (enhanced tabs are optional).
- [ ] With zero-valued image or relationship: checklist correctly reports it as incomplete.
- [ ] Change field storage prefix on a test copy only after planned migration; admin summaries follow the configured prefix.
- [ ] Editor/Contributor roles obey WordPress permissions; forged saves without valid nonce/capability do not update custom meta.
- [ ] Review CPT-level REST responses and field-group REST settings separately. Client notes, contact details and confidential data must not leak unintentionally.
- [ ] Editorial fields such as "consent", "embargo" or "allow public profile" are informational **not** access controls.
- [ ] Upgrade TheOct Fields only after the source contract and browser suite have been retested.

## 5. Frontend must remain blank

- [ ] Homepage, posts, pages, missing routes, all CPT archive/single templates remain blank by design.
- [ ] No public Bootstrap, GSAP, Font Awesome, fonts.css, or assets/admin assets enqueued by TheOct.
- [ ] No accidental wp_head, wp_footer, get_header, get_footer, the_content or markup added to placeholder templates.
- [ ] Distinguish output generated by other plugins from output generated by the theme.
- [ ] The deliberately incomplete blank theme is **not prepared for WordPress.org Theme Directory submission**; Theme Check can flag expected missing-feature issues.

## 6. Compatibility matrix (fill with actual results)

The plugin declares **WordPress >= 6.5** and **PHP >= 8.1**. Do not assume untested versions work.

| Environment | Browser / role | Result |
|---|---|---|
| WordPress 6.5 + PHP 8.1 | Chrome / Admin | Not run |
| Current stable WordPress + PHP 8.2 | Chrome / Editor | Not run |
| Current stable WordPress + PHP 8.3 | Firefox / Editor | Not run |
| Current stable WordPress + PHP 8.4 | Edge / Admin | Not run |
| Current stable WordPress + supported PHP | Safari + mobile | Not run |
| Multisite (if relevant) | Admin + Editor | Not run |

Record timestamp, WordPress/PHP/plugin/theme versions, browser and role, test ID, console/PHP logs, reproduction steps and screenshot for every failure.

## 7. Release acceptance criteria

- [ ] Static CI and plugin v1.4.0 contract both green.
- [ ] WordPress read-only smoke suite passes **after JSON import**.
- [ ] Seven CPT editing journeys tested (with no silent data loss).
- [ ] Admin tabs, conditional fields, repeaters and bidirectional relationships work.
- [ ] Role security checked; no unintended public data exposure.
- [ ] **Frontend still blank**; no theme-generated frontend styles or scripts.

| Test | Environment | Result | Evidence / issue |
|---|---|---|---|
| Static schema/blank frontend | Local or CI | Not run | |
| Plugin source compatibility | v1.4.0 ZIP | Not run | |
| WP-CLI smoke | Staging | Not run | |
| Seven CPT editor flows | Staging | Not run | |
| Browser tabs / lists | Staging | Not run | |
| Security / roles | Staging | Not run | |
| Public blank frontend | Staging | Not run | |

## Private Archive Page editor tests

- [ ] TheOct → Archive Pages shows six rows with canonical CPT archive URLs and edit links.
- [ ] Each entry opens a protected admin editor with title, main content, featured image and SEO metadata controls.
- [ ] Save/reload preserves edited title, description, image and SEO values.
- [ ] With TheOct Fields 1.4.0 active, four archive-only custom-field sections appear. Their values and conditional CTA controls save/reload.
- [ ] Editing any archive does not change its original public URL and does not render frontend content.
- [ ] Additional configuration posts cannot be created or deleted through the normal UI.

## Services-specific frontend verification (theme v1.0.3)

- [ ] Services archive and a Single Service render their seven sections without a site header/footer.
- [ ] TheOct Fields editor shows the six Services Archive Sections tabs only on the Services archive configuration, and six Single Service Sections tabs on Service posts.
- [ ] Saving nested deliverables, cards, steps and CTA fields correctly updates the rendered sections.
- [ ] Archive cards show real published Service posts; single cards show the Service capability repeater.
- [ ] Stylesheets appear only on Services routes; other CPT routes/homepage remain blank.
- [ ] Test mobile/tablet widths and uploaded image replacements in staging.

## Single Service screenshot fields (v1.0.4)

- [ ] **Services → Edit** displays `Single Service — Screenshot Detail Sections` with 10 tabs.
- [ ] Only Service CPT records show the new screenshot group; **TheOct → Archive Pages → Services** does not.
- [ ] Nested deliverables checklist, process, tech logos, example cards, testimonials and FAQ repeater values persist after saving.
- [ ] Contact form labels/choices are configurable, but do **not** submit data (admin schema only).
- [ ] Existing `ss_` fields and old Service frontend markup/CSS are unchanged.
- [ ] Header/footer and other CPT frontends remain untouched.

## v1.0.5 — Old Service field cleanup / new template acceptance

- [ ] Admin Services Archive shows **only Services Archive Sections**, not generic Archive Page Settings.
- [ ] Other five archive editors **still show** generic Archive Page Settings.
- [ ] Service post editor shows **only Single Service — Screenshot Detail Sections** from theme-managed groups; old Service Details and `Single Service Sections` are Draft.
- [ ] Old Service custom-field meta remains saved in the database after updating theme (back up first).
- [ ] Both templates render exclusively their active fields: `sa_` for archive and `ssd_` for singles.
- [ ] Hero, banner, deliverables, process, examples, FAQ and contact sections render from current fields; repeaters survive save/reload.
- [ ] No shared header/footer appears; other CPT frontends remain blank.
- [ ] Contact inputs and Send button are disabled until a backend form handler is installed.
