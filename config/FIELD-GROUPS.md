# TheOct — Detailed Field Groups (TheOct Fields v1.4.0)

This file documents the schema in `theoct-fields-schema.json`. The CPT and taxonomy definitions from the previous version are **preserved**. Existing field names and keys are retained; new fields are added to the same seven field groups.

| Content type | Fields (including editor dividers) | Repeaters / flexible fields | Conditional fields |
|---|---:|---:|---:|
| `service` | 30 | 4 | 2 |
| `project` | 29 | 3 | 0 |
| `case_study` | 35 | 4 | 1 |
| `industry` | 26 | 4 | 1 |
| `team` | 28 | 2 | 0 |
| `pricing` | 33 | 3 | 2 |
| `faq` | 24 | 1 | 2 |

## Selected field types

- **Services:** hero, intro, benefits, deliverables, process steps, galleries, relationships, service CTA.
- **Projects:** client identity, dates, status, technologies, results, visual story **Flexible Content**, relationships and portfolio options.
- **Case Studies:** challenge, approach, phases, results metrics with baseline and evidence, client feedback, bidirectional Project association, editorial permissions flags.
- **Industries:** business challenges, use cases, compliance notes, market opportunities, industry-specialist links and CTA.
- **Teams:** profile images, job history, social profiles, skill tags, certifications, project/service links and public contact options.
- **Pricing:** plan attributes, existing legacy amount/currency fields, repeater features/limits, structured add-on prices (`money`), included services, CTAs and terms.
- **FAQs:** answer formats, conditional video / resource links, step-based instructions, relationships, audience, and review workflow.

## Compatibility & behavior

- **Import:** Install and activate the separately supplied `TheOct Fields 1.4.0` plugin. In **TheOct Fields → Tools → Import JSON**, select `config/theoct-fields-schema.json` and **review the import preview** before applying.
- The importer matches `definition_key` and **updates** existing field-group definitions with the same keys; it does **not** recreate posts or their saved meta. Back up the WordPress database and export your current schema first if you have already edited these field groups locally.
- **No frontend output:** theme templates are still empty, and no assets or custom fields are rendered/enqueued.
- **Conditional logic:** `conditional_logic` is an **editor visibility / field-validation rule**, not access control or a public-display gate.
- **Editorial flags:** privacy, consent, embargo, `plan_available`, FAQ workflow and `allow_public_profile` are **data-only flags**. They do not make WordPress posts private. Use WordPress status/capabilities and actual consent handling separately.
- **Picker limitation:** in plugin v1.4.0, `relationship` and `post_object` search pickers are not filtered by post type from these field definitions. Their help text identifies the intended CPT; editors must select correct types.
- **Project / Case Study linking:** `related_project` is a single Post Object on Case Studies; `related_case_studies` is a multiple Relationship on Projects. Both use the plugin's bidirectional sync to maintain back-references. A single Case Study should be assigned to only one Project.
- **Nested schema:** Repeater and Flexible Content fields have both `sub_fields`/`layouts` arrays and their matching text-based `sub_fields_schema` / `layouts_schema`, so the plugin admin editor can open and re-save them. Deep child-field validation is limited by plugin version.
- **REST:** these field groups remain **REST-disabled** (`_theoct_show_in_rest=0`), while the CPTs themselves retain their previous REST registration. Some individual contact/internal fields also have `show_in_rest=0` as an additional precaution.
- **CPT Archive/Single placeholder templates:** unchanged. Pricing keeps its archive-only theme file, FAQs keep their single-only theme file.
- **Authoritative document:** This is the data model; it does not define any design or front-end view.

## Import precautions

1. Backup WordPress database and export TheOct Fields JSON from the target website.
2. Review field name changes and taxonomy/CPT slug collisions; this bundle keeps previous definition keys.
3. Import the new JSON and inspect all seven field-group editor screens.
4. Save **Settings → Permalinks** once if this is your first CPT import.
5. Enter test content in each CPT and confirm its custom fields persist after saving.
6. Revisit any relationships if you imported existing content from other sources.

## Relationship fields

| From | Field | Intended target |
|---|---|---|
| Services | `related_projects` | Project |
| Services | `related_industries` | Industry |
| Services | `related_case_studies` | Case Study |
| Projects | `related_case_studies` | Case Study (bidirectional) |
| Case Studies | `related_project` | Project (bidirectional) |
| Industries | `related_services` | Service |
| Teams | `team_related_projects` | Project |
| Pricing | `pricing_services` | Service |
| FAQs | `related_services` | Service |

