<?php
/**
 * Regression tests for six private, admin-editable archive configurations.
 *
 * Run without WordPress: php tests/test-archive-pages.php
 */
define( 'ABSPATH', __DIR__ );
$GLOBALS['oct_post_types'] = array();
$GLOBALS['oct_posts'] = array();
$GLOBALS['oct_meta'] = array();
$GLOBALS['oct_opts'] = array();
$GLOBALS['oct_errors'] = array();
$GLOBALS['oct_checked'] = 0;

function oct_check( $pass, $label ) {
    ++$GLOBALS['oct_checked'];
    if ( ! $pass ) {
        $GLOBALS['oct_errors'][] = $label;
        fwrite( STDERR, "FAIL: {$label}\n" );
    }
}
function __( $text, $domain = '' ) { return $text; }
function current_user_can( $cap ) { return in_array( $cap, array( 'manage_options', 'edit_pages' ), true ); }
function post_type_exists( $name ) { return isset( $GLOBALS['oct_post_types'][ $name ] ); }
function register_post_type( $name, $opts ) { $GLOBALS['oct_post_types'][ $name ] = $opts; }
function register_post_meta( $type, $key, $opts ) {}
function add_theme_support( $feature, $types = array() ) {}
function add_action( $name, $callback, $prio = 10 ) {}
function add_filter( $name, $callback, $prio = 10, $args = 1 ) {}
function sanitize_key( $text ) { return preg_replace( '/[^a-z0-9_\\-]/', '', strtolower( (string) $text ) ); }
function get_current_user_id() { return 1; }
function get_option( $key, $default = false ) { return $GLOBALS['oct_opts'][ $key ] ?? $default; }
function update_option( $key, $value, $autoload = true ) { $GLOBALS['oct_opts'][ $key ] = $value; }
function update_post_meta( $id, $key, $value ) { $GLOBALS['oct_meta'][ $id ][ $key ] = $value; }
function get_posts( $query ) {
    $out = array();
    foreach ( $GLOBALS['oct_posts'] as $id => $post ) {
        if ( $post['post_type'] === $query['post_type'] &&
            ( $GLOBALS['oct_meta'][ $id ][ $query['meta_key'] ] ?? '' ) === $query['meta_value'] ) {
            $out[] = $id;
        }
    }
    return $out;
}
function wp_insert_post( $post, $wp_error = false ) {
    $id = count( $GLOBALS['oct_posts'] ) + 1;
    $GLOBALS['oct_posts'][ $id ] = $post;
    return $id;
}
function is_wp_error( $value ) { return false; }

require dirname( __DIR__ ) . '/inc/archive-pages.php';

theoct_register_archive_editor_type();
$settings = $GLOBALS['oct_post_types']['theoct_archive'] ?? array();
oct_check( ( $settings['public'] ?? null ) === false, 'Editor CPT is non-public' );
oct_check( ( $settings['publicly_queryable'] ?? null ) === false, 'Editor CPT has no public routes' );
oct_check( ( $settings['rewrite'] ?? null ) === false, 'Editor CPT does not modify rewrite bases' );
oct_check( ( $settings['has_archive'] ?? null ) === false, 'Editor CPT has no archive itself' );
oct_check( ( $settings['capabilities']['create_posts'] ?? null ) === 'do_not_allow', 'Cannot create extra archive pages' );

theoct_seed_archive_editors();
oct_check( count( $GLOBALS['oct_posts'] ) === 6, 'Creates exactly six archive editor records' );
$targets = array();
foreach ( $GLOBALS['oct_meta'] as $item ) {
    $targets[] = $item['_theoct_archive_target'] ?? '';
}
sort( $targets );
$expected = array_keys( theoct_archive_targets() );
sort( $expected );
oct_check( $targets === $expected, 'All six target CPT keys correctly linked' );
oct_check( get_option( 'theoct_archive_editors_v1' ) === '1', 'Seed marker prevents repeated creation' );
$id = theoct_archive_editor_id( 'service' );
oct_check( $id > 0, 'Service editor resolves by stable meta key' );
$GLOBALS['oct_posts'][ $id ]['post_title'] = 'User Custom Heading';
theoct_seed_archive_editors();
oct_check( count( $GLOBALS['oct_posts'] ) === 6, 'Repeated seed does not duplicate editors' );
oct_check( $GLOBALS['oct_posts'][ $id ]['post_title'] === 'User Custom Heading', 'Saved custom title not overwritten' );
oct_check( theoct_archive_editor_id( 'faq' ) === 0, 'FAQ correctly has no archive editor' );

printf( "Archive editor: %d checks, %d failures\n", $GLOBALS['oct_checked'], count( $GLOBALS['oct_errors'] ) );
exit( $GLOBALS['oct_errors'] ? 1 : 0 );
