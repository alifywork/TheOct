# TheOct — WordPress CPT Admin Workflow

TheOct has **admin-only** enhancements for content types registered by your separate **TheOct Fields v1.4.0** plugin. These do not register the post types themselves; import the schema from \`config/theoct-fields-schema.json\` first.

## Edit screen

- The plugin's existing section markers (\`tab\` fields) become keyboard-accessible, clickable **editor tabs** when JavaScript runs.
- Each section includes a count of its top-level editing controls.
- **Show all sections** temporarily expands every section when you need to review/copy content at once.
- Keyboard navigation: left/right arrows, Home and End. Focus is visible. Without JavaScript, every field is displayed in the plugin's original layout.
- A **Content checklist** sidebar summarizes suggested saved fields. This is advisory and **never prevents publishing**. It refreshes from saved post meta when the edit page reloads.
- Each content type has a purpose-specific title placeholder (e.g., "Enter the FAQ question").
- No forms, field names, values, or plugin saved data are changed by the tab UI.

## List screens

| Type | Key details shown | Primary taxonomy filter |
|---|---|---|
| Services | Service subtitle | Service Categories |
| Projects | Client | Project Categories |
| Case Studies | Client | Case Study Categories |
| Industries | Industry tagline | Industry Categories |
| Teams | Job title | Team Departments |
| Pricing | Pricing mode / amount | Pricing Categories |
| FAQs | Short answer | FAQ Categories |

- Featured-image thumbnails are shown only for CPTs that support WordPress thumbnails.
- Terms in the primary taxonomy can filter each list screen using WordPress's native query.
- Teams, Pricing and FAQs are ordered by the WordPress **Order** (\`menu_order\`) value, then title, unless the editor chooses another column sort. WordPress's **Page Attributes → Order** field is where that value is edited.
- Other available taxonomy columns are still handled by TheOct Fields.

## Supported & compatible

- TheOct Fields 1.4.0 already renders \`tab\` types as section headings. The theme adds interaction through \`assets/admin/cpt-editor.js\` and \`assets/admin/cpt-editor.css\`.
- The theme checks that \`THEOCT_FIELDS_VERSION\` exists and the expected CPT is registered.
- **Nothing is loaded on the public frontend.** All existing archive and single templates remain blank.
- The plugin is not modified and is not bundled into the theme; its version and future DOM changes may require updates to this small admin enhancement.
- This update doesn't require re-importing fields if your site already has the previous seven groups. It improves the editor presentation around them.
- If no custom-field sections appear, activate TheOct Fields and import the content schema; then verify location rules.
- The plugin's own conditional fields, validation and bidirectional sync run as before. Switching tabs does not disable form fields.
- A content checklist is a guide, **not** access control, privacy enforcement or compliance validation.

## Suggested editorial order

1. Add a clear post title and assign taxonomy terms.
2. Use the section tabs in sequence to enter summary, structured details, relationships and CTA settings.
3. For Case Studies, link a Project and enter evidence alongside any reported metrics.
4. For Pricing, distinguish **Custom quote** and **Free** from numeric-priced plans.
5. Click **Update** or **Publish**, then review the saved-content checklist.
6. Build frontend templates separately when you're ready.
