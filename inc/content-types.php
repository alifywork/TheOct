<?php
/**
 * TheOct: register content types and taxonomies from the bundled schema.
 *
 * The theme registers missing post types so their dashboard menus work as
 * soon as the theme is activated. When TheOct Fields registers a type first,
 * its configuration wins. No frontend HTML or assets are rendered.
 *
 * @package TheOct
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Read the trusted JSON definitions packaged with this theme.
 *
 * @return array<string, mixed>
 */
function theoct_schema_bundle() {
    static $cache = null;

    if ( null !== $cache ) {
        return $cache;
    }

    $cache = array();
    $file  = get_template_directory() . '/config/theoct-fields-schema.json';

    if ( ! is_readable( $file ) ) {
        return $cache;
    }

    $raw = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
    if ( false === $raw ) {
        return $cache;
    }

    $bundle = json_decode( $raw, true );
    if ( ! is_array( $bundle )
        || 1 !== (int) ( $bundle['schema_version'] ?? 0 )
        || ! is_array( $bundle['definitions'] ?? null ) ) {
        return $cache;
    }

    $cache = $bundle;
    return $cache;
}

/**
 * Register CPTs and taxonomies only when another plugin has not done so.
 *
 * TheOct Fields 1.4.0 registers its imported definitions on init priority 5.
 * This theme registers anything still missing at priority 12, with identical
 * keys and rewrite bases. Existing CPT content and plugin settings are not
 * changed or overwritten.
 *
 * @return void
 */
function theoct_register_content_types() {
    $schema = theoct_schema_bundle();

    foreach ( $schema['definitions'] ?? array() as $definition ) {
        if ( ! is_array( $definition ) || 'theoct_content_type' !== ( $definition['type'] ?? '' ) ) {
            continue;
        }

        $meta = (array) ( $definition['meta'] ?? array() );
        $slug = sanitize_key( (string) ( $meta['_theoct_ct_slug'] ?? '' ) );

        if ( '' === $slug || strlen( $slug ) > 20 || post_type_exists( $slug ) ) {
            continue;
        }

        $singular = sanitize_text_field( (string) ( $meta['_theoct_ct_singular'] ?? $slug ) );
        $plural   = sanitize_text_field( (string) ( $meta['_theoct_ct_plural'] ?? $singular . 's' ) );
        $supports = array_values(
            array_intersect(
                array_map( 'sanitize_key', (array) ( $meta['_theoct_ct_supports'] ?? array( 'title' ) ) ),
                array( 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'comments', 'revisions', 'page-attributes', 'custom-fields' )
            )
        );

        register_post_type(
            $slug,
            array(
                'labels'          => array(
                    'name'          => $plural,
                    'singular_name' => $singular,
                    'menu_name'     => $plural,
                    'add_new_item'  => sprintf( 'Add New %s', $singular ),
                    'edit_item'     => sprintf( 'Edit %s', $singular ),
                    'new_item'      => sprintf( 'New %s', $singular ),
                    'view_item'     => sprintf( 'View %s', $singular ),
                    'search_items'  => sprintf( 'Search %s', $plural ),
                ),
                'public'          => ! empty( $meta['_theoct_ct_public'] ),
                'show_ui'         => true,
                'show_in_menu'    => true,
                'show_in_rest'    => ! empty( $meta['_theoct_ct_rest'] ),
                'has_archive'     => ! empty( $meta['_theoct_ct_archive'] ),
                'hierarchical'    => ! empty( $meta['_theoct_ct_hierarchical'] ),
                'rewrite'         => array(
                    'slug'       => sanitize_title( (string) ( $meta['_theoct_ct_rewrite'] ?? $slug ) ),
                    'with_front' => false,
                ),
                'supports'        => $supports ?: array( 'title' ),
                'menu_icon'       => sanitize_html_class( (string) ( $meta['_theoct_ct_icon'] ?? 'dashicons-admin-post' ) ),
                'map_meta_cap'    => true,
                'capability_type' => 'post',
            )
        );
    }

    foreach ( $schema['definitions'] ?? array() as $definition ) {
        if ( ! is_array( $definition ) || 'theoct_taxonomy' !== ( $definition['type'] ?? '' ) ) {
            continue;
        }

        $meta        = (array) ( $definition['meta'] ?? array() );
        $slug        = sanitize_key( (string) ( $meta['_theoct_tax_slug'] ?? '' ) );
        $post_types  = array_values(
            array_filter(
                array_map( 'sanitize_key', (array) ( $meta['_theoct_tax_object_types'] ?? array() ) ),
                'post_type_exists'
            )
        );

        if ( '' === $slug || strlen( $slug ) > 32 || taxonomy_exists( $slug ) || empty( $post_types ) ) {
            continue;
        }

        $singular = sanitize_text_field( (string) ( $meta['_theoct_tax_singular'] ?? $slug ) );
        $plural   = sanitize_text_field( (string) ( $meta['_theoct_tax_plural'] ?? $singular . 's' ) );

        register_taxonomy(
            $slug,
            $post_types,
            array(
                'labels'            => array(
                    'name'          => $plural,
                    'singular_name' => $singular,
                ),
                'public'            => true,
                'show_ui'           => true,
                'show_admin_column' => true,
                'show_in_rest'      => ! empty( $meta['_theoct_tax_rest'] ),
                'hierarchical'      => ! empty( $meta['_theoct_tax_hierarchical'] ),
                'rewrite'           => array(
                    'slug'       => sanitize_title( (string) ( $meta['_theoct_tax_rewrite'] ?? $slug ) ),
                    'with_front' => false,
                ),
            )
        );
    }
}
add_action( 'init', 'theoct_register_content_types', 12 );

/**
 * Ensure the newly installed theme's CPT archive URLs work immediately.
 */
function theoct_flush_content_rewrite_rules() {
    theoct_register_content_types();
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'theoct_flush_content_rewrite_rules' );

/**
 * Show dashboard guidance only to administrators if fields are not imported.
 */
function theoct_content_admin_notice() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $screen = get_current_screen();
    if ( ! $screen || 'dashboard' !== $screen->id ) {
        return;
    }

    if ( ! defined( 'THEOCT_FIELDS_VERSION' ) ) {
        echo '<div class="notice notice-info"><p><strong>';
        esc_html_e( 'TheOct: Your seven content types and taxonomies are ready.', 'theoct' );
        echo '</strong> ';
        esc_html_e( 'Install and activate TheOct Fields v1.4.0 to enable their detailed custom fields.', 'theoct' );
        echo ' <a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">' . esc_html__( 'Manage Plugins', 'theoct' ) . '</a></p></div>';
        return;
    }

    if ( ! post_type_exists( 'theoct_field_group' ) ) {
        return;
    }

    $keys = array_map(
        static function ( $slug ) {
            return 'theoct_field_group_' . $slug;
        },
        array( 'service', 'project', 'case_study', 'industry', 'team', 'pricing', 'faq' )
    );

    $group_ids = get_posts(
        array(
            'post_type'      => 'theoct_field_group',
            'post_status'    => 'publish',
            'posts_per_page' => 7,
            'fields'         => 'ids',
            'meta_query'     => array(
                array(
                    'key'     => '_theoct_definition_key',
                    'value'   => $keys,
                    'compare' => 'IN',
                ),
            ),
        )
    );

    if ( count( $group_ids ) >= 7 ) {
        return;
    }

    $url = wp_nonce_url(
        admin_url( 'admin-post.php?action=theoct_preview_theme_schema' ),
        'theoct_preview_theme_schema'
    );

    echo '<div class="notice notice-warning"><p><strong>';
    esc_html_e( 'TheOct: Content menus are available, but some detailed custom field groups are not imported yet.', 'theoct' );
    echo '</strong></p><p>';
    esc_html_e( 'Preview the bundled TheOct Fields schema (7 CPTs, 9 taxonomies and 7 field groups) before applying it. Existing definitions are not overwritten without review.', 'theoct' );
    echo '</p><p><a class="button button-primary" href="' . esc_url( $url ) . '">';
    esc_html_e( 'Preview TheOct Fields Setup', 'theoct' );
    echo '</a></p></div>';
}
add_action( 'admin_notices', 'theoct_content_admin_notice' );

/**
 * Initiate the plugin's own safe, reviewed schema-import flow.
 *
 * This operation creates a short-lived preview only; the admin must
 * explicitly choose "Apply Import" on the plugin's Tools page.
 */
function theoct_preview_theme_schema() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You are not allowed to import TheOct settings.', 'theoct' ) );
    }

    check_admin_referer( 'theoct_preview_theme_schema' );

    if ( ! defined( 'THEOCT_FIELDS_VERSION' )
        || ! class_exists( '\TheOct\Fields\Core\SchemaImportManager' ) ) {
        wp_die( esc_html__( 'Activate TheOct Fields v1.4.0 before importing.', 'theoct' ) );
    }

    $bundle = theoct_schema_bundle();
    if ( empty( $bundle['definitions'] ) ) {
        wp_die( esc_html__( 'The bundled TheOct schema is missing or invalid.', 'theoct' ) );
    }

    $preview = \TheOct\Fields\Core\SchemaImportManager::createPreview( $bundle, get_current_user_id() );
    if ( is_wp_error( $preview ) ) {
        wp_die( esc_html( $preview->get_error_message() ) );
    }

    wp_safe_redirect(
        add_query_arg(
            array(
                'page'           => 'theoct-fields-tools',
                'import_preview' => $preview['token'],
            ),
            admin_url( 'admin.php' )
        )
    );
    exit;
}
add_action( 'admin_post_theoct_preview_theme_schema', 'theoct_preview_theme_schema' );
