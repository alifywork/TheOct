# TheOct — Services Archive fields and template (v1.0.5)

**Editor:** WordPress → TheOct → Archive Pages → Services → Edit. **Frontend URL:** `/services/`.

The Services Archive uses only **Services Archive Sections** (`sa_` prefix, 34 fields, six tab headings) plus native WordPress Title, Description and Featured Image as fallbacks. The design is based on the first Services archive screenshot, **without the shared header/footer**.

| Section | Fields |
|---|---|
| Hero + dark banner | `sa_hero_eyebrow`, `sa_hero_title`, `sa_hero_description`, `sa_hero_cta`, `sa_visual_image` |
| Deliverables | `sa_deliverables_eyebrow`, `sa_deliverables_title`, nested `sa_deliverable_groups` checklist columns |
| Core services cards | `sa_cards_eyebrow`, `sa_cards_title`, `sa_cards_description`; live published Service posts supply the cards |
| Industries & typical projects | `sa_industry_title`, description/image/link, `sa_project_title`, description and `sa_project_bullets` |
| Process | `sa_process_eyebrow`, `sa_process_title`, description and repeatable `sa_process_steps` |
| Final CTA | `sa_bottom_cta_title`, description, link, image, phone and email |

**Old fields removed from this editor:** the generic `Archive Page Settings` group no longer matches the Services Archive record. It remains active on Projects, Case Studies, Industries, Teams and Pricing archives. Existing saved meta is retained. The old Service Details and `ss_` Single Service groups are drafted site-wide (Service posts only).

The single Service screenshot now uses its own 10-section template from `ssd_` fields; see `SINGLE-SERVICE-FIELDS.md`. If you have existing Services content in the old groups, export or back up the post meta before migrating values into the new fields. No automatic destructive data conversion is performed.

**Important:** these templates don't call a site header or footer. The home page and all other CPT frontend routes stay blank. CSS/fonts load on Services routes only.

To validate after upgrade: refresh dashboard as an administrator with TheOct Fields active, confirm old groups absent and new group present, edit hero text and save, then check `/services/`. Repeated visits will not delete meta or overwrite custom changes to installed field groups.