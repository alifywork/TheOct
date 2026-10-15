#!/usr/bin/env python3
"""Dependency-free checks for TheOct schema, blank frontend and plugin contract."""
import argparse
import json
import re
import sys
import zipfile
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
CPTS = {
    "service": ("services", True, True, 4),
    "project": ("projects", True, True, 4),
    "case_study": ("case-studies", True, True, 5),
    "industry": ("industries", True, True, 4),
    "team": ("teams", True, True, 4),
    "pricing": ("pricing", True, False, 4),
    "faq": ("faqs", False, True, 3),
}
TAXONOMIES = {
    "service_category": "service", "project_category": "project",
    "project_technology": "project", "case_study_category": "case_study",
    "industry_category": "industry", "team_department": "team",
    "team_skill": "team", "pricing_category": "pricing",
    "faq_category": "faq",
}
# Supported by TheOct Fields 1.4.0; compare with FieldRegistry when --plugin is used.
FIELD_TYPES = set("""
text number integer decimal range email url telephone password date time datetime
month week color hidden textarea code json slug readonly tags key_value money
rating progress query_builder typography gradient background border shadow
spacing date_range time_range datetime_range address coordinates country_state
dimensions weight status priority formula uuid navigation social_profiles map
focal_point link button consent select multi_select radio checkbox button_group
true_false toggle image file audio video icon gallery multiple_files wysiwyg
post_object relationship post_objects taxonomy user group repeater flexible_content
clone message separator tab accordion
""".split())
TEMPLATE_FILES = [f"{kind}-{cpt}.php" for cpt, (_, archive, single, _) in CPTS.items()
                  for kind, enabled in (("archive", archive), ("single", single)) if enabled]
TEMPLATE_FILES.append("index.php")
failures = []
checks = 0


def check(ok, message):
    global checks
    checks += 1
    if not ok:
        failures.append(message)


def inspect_fields(fields, location):
    keys = set()
    names = set()
    for field in fields:
        if not isinstance(field, dict):
            check(False, f"{location}: non-object field")
            continue
        name, key, kind = field.get("name"), field.get("key"), field.get("type")
        check(isinstance(name, str) and bool(name), f"{location}: missing field name")
        check(isinstance(key, str) and bool(key), f"{location}/{name}: missing key")
        check(kind in FIELD_TYPES, f"{location}/{name}: unknown plugin field type {kind}")
        if key in keys:
            check(False, f"{location}: duplicate field key {key}")
        if name in names:
            check(False, f"{location}: duplicate field name {name}")
        keys.add(key)
        names.add(name)
        if kind in ("group", "repeater"):
            children = field.get("sub_fields")
            check(isinstance(children, list) and bool(children), f"{location}/{name}: missing sub_fields")
            check(bool(field.get("sub_fields_schema")), f"{location}/{name}: missing editor sub_fields_schema")
            if isinstance(children, list):
                inspect_fields(children, f"{location}/{name}")
        if kind == "flexible_content":
            layouts = field.get("layouts")
            check(isinstance(layouts, list) and bool(layouts), f"{location}/{name}: no layouts")
            check(bool(field.get("layouts_schema")), f"{location}/{name}: no layouts_schema")
            if isinstance(layouts, list):
                layout_names = set()
                for layout in layouts:
                    lname = layout.get("name")
                    check(lname not in layout_names, f"{location}/{name}: duplicate layout {lname}")
                    layout_names.add(lname)
                    inspect_fields(layout.get("sub_fields") or [], f"{location}/{name}/{lname}")
    for field in fields:
        if not isinstance(field, dict):
            continue
        for group in field.get("conditional_logic", []):
            for clause in group:
                check(clause.get("field") in names,
                      f"{location}/{field.get('name')}: missing conditional dependency {clause.get('field')}")


def inspect_schema():
    source = ROOT / "config" / "theoct-fields-schema.json"
    check(source.is_file(), "Missing import-ready schema JSON")
    if not source.is_file():
        return
    try:
        bundle = json.loads(source.read_text(encoding="utf-8"))
    except (OSError, ValueError) as exc:
        check(False, f"Schema cannot be parsed: {exc}")
        return
    check(bundle.get("schema_version") == 1, "Schema version must be 1")
    check(bundle.get("plugin_version") == "1.4.0", "Schema must target plugin 1.4.0")
    definitions = bundle.get("definitions", [])
    check(len(definitions) == 24, "Expected 7 CPTs + 9 taxonomies + 8 field groups")
    definition_keys = [d.get("definition_key") for d in definitions]
    check(len(definition_keys) == len(set(definition_keys)), "Duplicate definition keys")
    typed = {}
    for definition in definitions:
        typed.setdefault(definition.get("type"), []).append(definition)
        meta = definition.get("meta") or {}
        check(meta.get("_theoct_definition_key") == definition.get("definition_key"),
              f"Definition key mismatch: {definition.get('slug')}")
    posts = {d["meta"].get("_theoct_ct_slug"): d for d in typed.get("theoct_content_type", [])}
    taxes = {d["meta"].get("_theoct_tax_slug"): d for d in typed.get("theoct_taxonomy", [])}
    groups = {}
    for d in typed.get("theoct_field_group", []):
        names = d.get("meta", {}).get("_theoct_location_post_types") or []
        if len(names) == 1:
            groups[names[0]] = d
    check(set(posts) == set(CPTS), "CPT slug set differs from expected seven")
    check(set(taxes) == set(TAXONOMIES), "Taxonomy slug set differs from expected nine")
    check(set(groups) == (set(CPTS) | {"theoct_archive"}), "Expected seven CPT field groups and archive settings group")
    if "theoct_archive" in groups:
        archive = groups["theoct_archive"]["meta"]
        fields = archive.get("_theoct_fields") or []
        check(sum(field.get("type") == "tab" for field in fields) == 4, "Archive settings must have four section tabs")
        check(archive.get("_theoct_show_in_rest") in (False, 0), "Archive group must not be REST exposed")
        inspect_fields(fields, "theoct_archive")
    all_rewrites = []
    for slug, (rewrite, archive, single, tab_count) in CPTS.items():
        if slug not in posts or slug not in groups:
            continue
        meta = posts[slug]["meta"]
        check(meta.get("_theoct_ct_rewrite") == rewrite, f"{slug}: rewrite slug changed")
        check(bool(meta.get("_theoct_ct_archive")) == archive, f"{slug}: unexpected archive setting")
        check(bool(meta.get("_theoct_ct_rest")), f"{slug}: Gutenberg/REST disabled")
        check(meta.get("_theoct_ct_public") in (True, 1), f"{slug}: CPT not public")
        check(len(slug) <= 20, f"{slug}: exceeds WordPress post type key length")
        all_rewrites.append(rewrite)
        group_meta = groups[slug]["meta"]
        fields = group_meta.get("_theoct_fields") or []
        check(group_meta.get("_theoct_show_in_rest") in (False, 0),
              f"{slug}: REST-disabled field group unexpectedly enabled")
        check(sum(f.get("type") == "tab" for f in fields) == tab_count,
              f"{slug}: tab count changed")
        inspect_fields(fields, slug)
    check(len(set(all_rewrites)) == len(all_rewrites), "Duplicate CPT permalink base")
    for name, target in TAXONOMIES.items():
        if name not in taxes:
            continue
        meta = taxes[name]["meta"]
        check(meta.get("_theoct_tax_object_types") == [target],
              f"{name}: unexpected taxonomy association")
        check(bool(meta.get("_theoct_tax_rest")), f"{name}: taxonomy REST disabled")
    if "project" in groups and "case_study" in groups:
        p = {f["name"]: f for f in groups["project"]["meta"]["_theoct_fields"]}
        c = {f["name"]: f for f in groups["case_study"]["meta"]["_theoct_fields"]}
        check(p.get("related_case_studies", {}).get("bidirectional_target") == "related_project",
              "Project relationship back-reference missing")
        check(c.get("related_project", {}).get("bidirectional_target") == "related_case_studies",
              "Case Study relationship back-reference missing")


def inspect_blank_frontend():
    for rel in TEMPLATE_FILES:
        source = ROOT / rel
        check(source.is_file(), f"Missing template: {rel}")
        if not source.is_file():
            continue
        body = source.read_text(encoding="utf-8")
        check("<?php" in body, f"{rel}: no PHP opening tag")
        if rel in {"archive-service.php", "single-service.php"}:
            check("theoct_services_open_document" in body and "theoct_services_close_document" in body,
                  f"{rel}: expected Service-only document wrapper")
        else:
            check(not re.search(r"(?<!\?)\?>|<html|<body|<main|<div|\b(?:echo|print|the_content|get_header|get_footer|wp_head|wp_footer)\s*\(", body, re.I),
                  f"{rel}: must remain blank")
    style = (ROOT / "style.css").read_text(encoding="utf-8")
    check("Theme Name: TheOct" in style, "Theme registration header missing")
    check(re.sub(r"/\*.*?\*/", "", style, flags=re.S).strip() == "", "Theme stylesheet is no longer blank")
    functions = (ROOT / "functions.php").read_text(encoding="utf-8")
    check("if ( is_admin() )" in functions, "Theme admin module is not admin-only")
    check("wp_enqueue_scripts" not in functions, "Frontend scripts/styles are now enqueued")
    check((ROOT / "inc/admin.php").is_file(), "Missing dashboard PHP module")
    archive_module = ROOT / "inc" / "archive-pages.php"
    check(archive_module.is_file(), "Missing private archive editor module")
    if archive_module.is_file():
        code = archive_module.read_text(encoding="utf-8")
        check("'public'              => false" in code, "Internal archive records must not be public")
        check("'publicly_queryable'  => false" in code, "Internal archive records must not have public URLs")
        check("theoct_seed_archive_editors" in code, "Missing idempotent archive record seeding")
        check("theoct_ensure_archive_field_group" in code, "Missing optional archive fields")
    check((ROOT / "tests/test-archive-pages.php").is_file(), "Missing archive registration regression test")
    registration = ROOT / "inc" / "content-types.php"
    check(registration.is_file(), "Missing CPT auto-registration module")
    if registration.is_file():
        code = registration.read_text(encoding="utf-8")
        check("add_action( 'init', 'theoct_register_content_types', 12 )" in code,
              "CPT fallback not attached at priority 12")
        check("post_type_exists( $slug )" in code,
              "Fallback must skip plugin-owned CPTs")
        check("taxonomy_exists( $slug )" in code,
              "Fallback must skip plugin-owned taxonomies")
        check("theoct_preview_theme_schema" in code,
              "Missing explicit import preview action")
    check((ROOT / "tests/test-content-registration.php").is_file(),
          "Missing CPT fallback regression test")
    check((ROOT / "assets/admin/cpt-editor.js").is_file(), "Missing admin tab script")
    check((ROOT / "assets/admin/cpt-editor.css").is_file(), "Missing admin-only style")
    layout = ROOT / "template-parts" / "services-layout.php"
    check(layout.is_file(), "Missing shared Services sections")
    if layout.is_file():
        markup = layout.read_text(encoding="utf-8")
        for section in ("oct-hero", "oct-visual-band", "oct-deliverables", "oct-core",
                        "oct-split", "oct-process", "oct-final-cta"):
            check(section in markup, "Missing screenshot section: " + section)
        check("get_header(" not in markup and "get_footer(" not in markup,
              "Services layout must not render header/footer")
    service_styles = ROOT / "assets" / "css" / "services-pages.css"
    check(service_styles.is_file(), "Missing dedicated Services CSS")
    service_module = ROOT / "inc" / "services-pages.php"
    check(service_module.is_file(), "Missing Services integration")
    if service_module.is_file():
        code = service_module.read_text(encoding="utf-8")
        check("is_post_type_archive( 'service' )" in code and "is_singular( 'service' )" in code,
              "Frontend CSS must load only on Services archive / single")
        check("theoct_services_install_layout_groups" in code,
              "Missing non-overwriting field group installer")
    extra = ROOT / "config" / "services-layout-schema.json"
    check(extra.is_file(), "Missing Services archive/single field schema")
    if extra.is_file():
        specialized = json.loads(extra.read_text(encoding="utf-8"))
        definitions = specialized.get("definitions", [])
        check(len(definitions) == 2, "Require two Service-only field groups")
        targets = sorted([tuple(d.get("meta", {}).get("_theoct_location_post_types", []))
                          for d in definitions])
        check(targets == [("service",), ("theoct_archive",)],
              "Field groups must target Service and private archive records")
        for definition in definitions:
            fields = definition.get("meta", {}).get("_theoct_fields", [])
            check(sum(f.get("type") == "tab" for f in fields) == 6,
                  "Each Services layout requires six editor tabs")
            inspect_fields(fields, definition.get("slug", "services"))


def inspect_plugin(plugin_path):
    if not plugin_path:
        print("SKIP: plugin source contract (pass --plugin PATH to ZIP or extracted folder)")
        return
    location = Path(plugin_path).expanduser().resolve()
    paths = [
        "theoct-fields.php",
        "includes/Core/Plugin.php",
        "includes/Admin/ContentMetaBox.php",
        "includes/Core/FieldRenderer.php",
        "includes/Admin/Admin.php",
        "includes/Support/MetaKey.php",
        "includes/Core/FieldRegistry.php",
    ]
    try:
        if location.is_file() and location.suffix.lower() == ".zip":
            with zipfile.ZipFile(location) as archive:
                files = {
                    item: archive.read("theoct-fields/" + item).decode("utf-8")
                    for item in paths
                }
        elif location.is_dir():
            root = location / "theoct-fields" if (location / "theoct-fields").is_dir() else location
            files = {item: (root / item).read_text(encoding="utf-8") for item in paths}
        else:
            check(False, f"Invalid plugin path: {location}")
            return
    except (KeyError, OSError, ValueError) as exc:
        check(False, f"Cannot read TheOct Fields source: {exc}")
        return
    plugin = files["theoct-fields.php"]
    renderer = files["includes/Core/FieldRenderer.php"]
    box = files["includes/Admin/ContentMetaBox.php"]
    admin = files["includes/Admin/Admin.php"]
    hooks = files["includes/Core/Plugin.php"]
    registry = files["includes/Core/FieldRegistry.php"]
    check("Version: 1.4.0" in plugin, "Plugin version is not 1.4.0")
    check("THEOCT_FIELDS_VERSION" in plugin, "Plugin version guard no longer exists")
    check("'theoct-fields-content'" in box, "Meta box ID has changed")
    check('class="theoct-content-group"' in box, "Field group wrapper has changed")
    check("theoct-content-field" in renderer, "Field wrapper has changed")
    check("theoct-field-type-" in renderer, "Field type class has changed")
    check("add_action('init', [$this, 'registerContentTypes'], 5)" in hooks,
          "CPT registration hook priority changed")
    check("theoct-fields-admin" in admin, "Enqueued plugin admin handle changed")
    check("'theoct_fields_storage_prefix'" in files["includes/Support/MetaKey.php"],
          "Custom field storage prefix behavior changed")
    # The registry is checked for its class construction, independent of the import JSON.
    for c in ("new RepeaterField()", "new FlexibleContentField()", "new RelationshipField(",
              "new PostObjectField()", "new DisplayField('tab'"):
        check(c in registry, f"Plugin no longer registers expected field: {c}")


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--plugin", help="Local plugin ZIP or extracted TheOct Fields directory")
    args = parser.parse_args()
    inspect_schema()
    inspect_blank_frontend()
    inspect_plugin(args.plugin)
    print(f"Static checks: {checks} assertions, {len(failures)} failures")
    for message in failures:
        print("FAIL:", message)
    if failures:
        sys.exit(1)
    print("PASS: Schema, blank frontend and available plugin contract checks")


if __name__ == "__main__":
    main()
