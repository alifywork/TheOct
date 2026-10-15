# TheOct Services — Editable Archive & Single Sections (v1.0.3)

Based on the supplied Services reference screenshot. **Header and footer intentionally excluded.** These are newly designed Services pages; other CPT public pages still render nothing.

## Frontend routes

- Services Archive: `/services/` → `archive-service.php` → `template-parts/services-layout.php`.
- Single Service: `/services/{service-slug}/` → `single-service.php` → the same section component with Service-specific fields.
- Page styles: `assets/css/services-pages.css` and locally hosted `assets/css/fonts.css` are enqueued **only** for those two routes. No Bootstrap/GSAP is needed for this layout.

## WordPress dashboard editing

1. Install/update the theme and activate your TheOct Fields v1.4.0 plugin.
2. In **TheOct → Archive Pages → Services → Edit Archive Page**, look for **Services Archive Sections** below the existing Archive Page Settings group.
3. In **Services → Add New** or edit a published Service post, look for **Single Service Sections** below the existing Service Details group.
4. Those two groups are imported from `config/services-layout-schema.json` automatically on an administrator's `admin_init`, **only if their definition keys do not exist**. Existing TheOct Fields definitions are not overwritten.
5. If the plugin is inactive, the frontend renders a starter layout and live published Service posts, but these advanced editor fields are unavailable until activated.

| Screenshot section | Service Archive fields (prefix `sa_`) | Single Service fields (prefix `ss_`) |
|---|---|---|
| Hero and CTA | eyebrow, title, description, CTA link | same (title falls back to Service title) |
| Abstract black showcase band | visual image (or CSS abstract art) | visual image / existing service hero image |
| What we help automate | heading, nested deliverable columns + checklist rows | same; existing Service benefits fallback |
| Our core automation services | headings and description; cards from published Service posts | headings, description, capability cards repeater / existing Service Features |
| Industries + Typical Projects | industry image, title, description, button; project bullets repeater | same |
| A process built for results | eyebrow, heading, description, process steps repeater | same; existing Service Process fallback |
| Final dark CTA | heading, description, optional art image, button, phone, email | same; existing Service CTA link fallback |

### Important details

- **Services archive cards are dynamic.** Add real Service posts in the dashboard with title, excerpt, featured image and service intro. They show automatically. No fake client/service posts are generated.
- **Single capabilities:** Add rows under Single Service Sections → Core Service Cards to populate cards; alternatively existing Service Features rows are displayed.
- Section headings, checklists and process have example fallback copy from the supplied reference; editors can replace it.
- **Images:** banner, industry panel and final CTA accept WordPress Media Library images via TheOct Fields; if left empty, CSS illustrations are used, not the screenshot image.
- **CTA links:** Use a TheOct Fields Link control to set label, URL and target. Unconfigured primary CTA buttons display as non-clickable previews (no fabricated contact destination).
- **No header/footer:** templates open/close minimal WordPress HTML document and call `wp_head()`/`wp_footer()` for scripts/styles, but they do not include a site navigation/header/footer design.
- **Theme changes:** the only public CSS is enqueued on Service archive and singles. Other CPT archive/single templates and the index remain blank.
- **Import safety:** the new schema is separate from the 24-definition base schema, preventing existing content fields from being replaced. Advanced groups are created once by stable definition key; if you later change schema and want to overwrite existing groups, use an explicit reviewed TheOct Fields import.
- **WordPress tests:** verify the plugin groups' tab UI, repeater saving, CTA URLs, media IDs, active Services templates and empty other routes in a staging WordPress site. GitHub PHP/static CI is not a substitute for live browser testing.

## Section content checklist

- [ ] Archive hero title and intro entered.
- [ ] Main hero and bottom CTA URLs configured (or deliberately left as previews).
- [ ] Three or more deliverable columns populated as needed.
- [ ] Services posts published for archive cards; images and excerpts added.
- [ ] At least four process steps entered.
- [ ] Industry illustration or uploaded image chosen.
- [ ] Responsive desktop, tablet, mobile layouts reviewed.
- [ ] Single Service capabilities and process entered or existing field fallbacks checked.
- [ ] Header/footer excluded; other post types remain blank.

### Single Service screenshot-specific field update (v1.0.4)

Only Service post **admin fields** were added. The 10 screenshot-specific tabs are provided by a separate group at `config/single-service-detail-schema.json` and auto-install on the next admin visit with TheOct Fields active. They do not change the existing Services Archive group or the current frontend markup; see [SINGLE-SERVICE-FIELDS.md](SINGLE-SERVICE-FIELDS.md).
