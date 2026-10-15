<?php
/**
 * Theme content registration regression checks without a WordPress database.
 *
 * Run: php tests/test-content-registration.php
 * Intentionally stubs only the WordPress registration functions used by the theme.
 */

define( 'ABSPATH', __DIR__ );
$GLOBALS['theoct_test_types'] = array();
$GLOBALS['theoct_test_taxonomies'] = array();
$GLOBALS['theoct_test_actions'] = array();
$GLOBALS['theoct_test_flushes'] = 0;
$GLOBALS['theoct_test_failures'] = array();
$GLOBALS['theoct_test_checks'] = 0;

function theoct_test_assert( $pass, $message ) {
    ++$GLOBALS['theoct_test_checks'];
    if ( ! $pass ) {
        $GLOBALS['theoct_test_failures'][] = $message;
        fwrite( STDERR, "FAIL: {$message}\n" );
    }
}

function get_template_directory() {
    return dirname( __DIR__ );
}
function sanitize_key( $value ) {
    return preg_replace( '/[^a-z0-9_-]/', '', strtolower( (string) $value ) );
}
function sanitize_text_field( $value ) {
    return trim( strip_tags( (string) $value ) );
}
function sanitize_title( $value ) {
    return preg_replace( '/[^a-z0-9-]/', '-', strtolower( (string) $value ) );
}
function sanitize_html_class( $value ) {
    return preg_replace( '/[^a-zA-Z0-9_-]/', '', (string) $value );
}
function post_type_exists( $slug ) {
    return isset( $GLOBALS['theoct_test_types'][ $slug ] );
}
function taxonomy_exists( $slug ) {
    return isset( $GLOBALS['theoct_test_taxonomies'][ $slug ] );
}
function register_post_type( $slug, $settings ) {
    if ( post_type_exists( $slug ) ) {
        throw new RuntimeException( 'Attempted duplicate CPT registration: ' . $slug );
    }
    $GLOBALS['theoct_test_types'][ $slug ] = $settings;
}
function register_taxonomy( $slug, $post_types, $settings ) {
    if ( taxonomy_exists( $slug ) ) {
        throw new RuntimeException( 'Attempted duplicate taxonomy registration: ' . $slug );
    }
    $GLOBALS['theoct_test_taxonomies'][ $slug ] = array( 'posts' => $post_types, 'settings' => $settings );
}
function add_action( $hook, $callback, $priority = 10 ) {
    $GLOBALS['theoct_test_actions'][ $hook ][] = array( $callback, $priority );
}
function flush_rewrite_rules() {
    ++$GLOBALS['theoct_test_flushes'];
}

require get_template_directory() . '/inc/content-types.php';

$schema = theoct_schema_bundle();
theoct_test_assert( count( $schema['definitions'] ?? array() ) === 23, 'Read the bundled schema with 23 definitions' );
theoct_test_assert(
    in_array( array( 'theoct_register_content_types', 12 ), $GLOBALS['theoct_test_actions']['init'] ?? array(), true ),
    'Content type registration executes at init priority 12 after the plugin'
);

// No TheOct Fields plugin installed: full theme fallback.
theoct_register_content_types();
theoct_test_assert( count( $GLOBALS['theoct_test_types'] ) === 7, 'Seven CPT menus are registered without the plugin' );
theoct_test_assert( count( $GLOBALS['theoct_test_taxonomies'] ) === 9, 'Nine taxonomies are registered without the plugin' );

$expected = array(
    'service'    => array( 'services', true ),
    'project'    => array( 'projects', true ),
    'case_study' => array( 'case-studies', true ),
    'industry'   => array( 'industries', true ),
    'team'       => array( 'teams', true ),
    'pricing'    => array( 'pricing', true ),
    'faq'        => array( 'faqs', false ),
);
foreach ( $expected as $slug => $expect ) {
    $actual = $GLOBALS['theoct_test_types'][ $slug ] ?? array();
    theoct_test_assert( ( $actual['rewrite']['slug'] ?? null ) === $expect[0], $slug . ': rewrite slug matches' );
    theoct_test_assert( ( $actual['has_archive'] ?? null ) === $expect[1], $slug . ': archive flag matches' );
    theoct_test_assert( ( $actual['show_ui'] ?? null ) === true, $slug . ': admin menu enabled' );
    theoct_test_assert( ( $actual['show_in_rest'] ?? null ) === true, $slug . ': REST/block-editor support' );
}

// Simulate TheOct Fields already registering certain definitions before priority 12.
// The plugin must win, and the fallback must fill only missing registrations.
$GLOBALS['theoct_test_types'] = array(
    'project' => array( 'plugin_owner' => true ),
);
$GLOBALS['theoct_test_taxonomies'] = array(
    'project_category' => array( 'plugin_owner' => true ),
);
theoct_register_content_types();
theoct_test_assert( count( $GLOBALS['theoct_test_types'] ) === 7, 'Six missing CPTs registered beside plugin-owned Project' );
theoct_test_assert( count( $GLOBALS['theoct_test_taxonomies'] ) === 9, 'Eight missing taxonomies registered beside plugin-owned category' );
theoct_test_assert( ! empty( $GLOBALS['theoct_test_types']['project']['plugin_owner'] ), 'Plugin-owned CPT not overwritten' );
theoct_test_assert( ! empty( $GLOBALS['theoct_test_taxonomies']['project_category']['plugin_owner'] ), 'Plugin-owned taxonomy not overwritten' );

theoct_register_content_types(); // Re-entry must be safe.
theoct_test_assert( count( $GLOBALS['theoct_test_types'] ) === 7, 'Repeated CPT registration is idempotent' );
theoct_test_assert( count( $GLOBALS['theoct_test_taxonomies'] ) === 9, 'Repeated taxonomy registration is idempotent' );

theoct_flush_content_rewrite_rules();
theoct_test_assert( $GLOBALS['theoct_test_flushes'] === 1, 'Theme switch flushes permalinks once' );

printf(
    "Theme registration assertions: %d; failures: %d\n",
    $GLOBALS['theoct_test_checks'],
    count( $GLOBALS['theoct_test_failures'] )
);
exit( count( $GLOBALS['theoct_test_failures'] ) ? 1 : 0 );
