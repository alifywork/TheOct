# TheOct v1.0.4 — Single Service screenshot fields

This configuration reproduces the **editable content architecture** shown in your new Single Service screenshot. It is **not** a request to change frontend markup or CSS, so no frontend files were modified in v1.0.4.

## WordPress editor location

**Services → Add New / Edit Service → Single Service — Screenshot Detail Sections** (10 tabs). Requires your separate **TheOct Fields v1.4.0** plugin.

The new group has 81 top-level fields including ten tab headings, 11 repeater fields, 15 conditionally shown form settings and additional regular controls. All keys start with `ssd_` so they do not overwrite existing `ss_` fields.

| Screenshot section | Admin tab | Editable fields |
|---|---|---|
| Hero (excluding site header) | 01 Hero & Section Navigation | Back link, eyebrow, headline, short intro, primary/secondary CTA, section-jump labels/anchors |
| Blue artwork with 20–40% stat | 02 Blue Hero Visual & Results | Banner and mobile images, stat value, label, footnote, extra metrics |
| Problem with "messy" tools | 03 Problem & Standards | Heading, WYSIWYG problem copy, standards cards with icons and descriptions |
| What's included | 04 Deliverables | Heading, 4 suggested columns (Strategy, Build, Handoff, Support) and nested checklist bullets |
| A process built for results | 05 Process Timeline | Intro and reorderable numbered process cards |
| Our stack | 06 Technology Stack Logos | Technology/logo/image, website links and descriptions |
| See what's possible | 07 Automation Examples | Blue background image, input/automation/output cards, badges and optional links |
| What clients say | 08 Client Testimonials | Portrait, client name, role/company, testimonial and consent reminder |
| Common questions | 09 FAQ Accordion | Question/answer repeater, default-open setting, links to existing FAQ CPT records |
| Let's talk about your workflows | 10 Discovery Contact | Blue image, heading, benefits, phone/email, consultation form labels and dropdown choices, privacy and confirmation text |

The screenshot's **site header/navbar and dark global footer are excluded**. The contact section directly above the footer is included as editable content.

## Safe installation and existing data

After updating the theme, activate TheOct Fields v1.4.0 if necessary, then open **Services → Edit Service** as a WordPress administrator. The theme installs the single new definition from `config/single-service-detail-schema.json` once during `admin_init` if its definition key doesn't already exist. It does not overwrite existing TheOct Fields definitions, saved Service posts, Services Archive configuration, or the old `Single Service Sections` group.

If the new group is not visible immediately, reload the WordPress admin and confirm your plugin is active. With no plugin, the schema file remains in the theme but WordPress cannot render those custom field controls.

## Important limitations

- **Fields only:** v1.0.4 does not wire these `ssd_` values to frontend templates. The previous Single Service design and its original `ss_` fields continue functioning unchanged.
- **Not a live contact form:** configurable labels, dropdown values, consent text and recipient address do not create form HTML, submission handling, validation, email delivery or spam protection.
- **No invented client proof:** metrics, testimonials and logos are empty until you supply them. Demo text in field placeholders isn't evidence of a result.
- **FAQ relationship type:** TheOct Fields v1.4.0 does not limit Relationship pickers to a particular CPT, so select only FAQ entries.
- **Test before release:** verify nested deliverables repeaters, saved testimonial text and plugin admin tabs on your WordPress staging instance. GitHub static CI does not exercise a live WordPress editor.

## Quality checks

Run `python3 tests/check_static.py --plugin /path/to/theoct-fields-1.4.0.zip` to validate the screenshot group and plugin source contract. GitHub Actions runs the same static check without requiring your private plugin ZIP. Source details: `config/single-service-detail-schema.json`.
