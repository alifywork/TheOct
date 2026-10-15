<?php
/**
 * Read-only WordPress integration smoke checks for TheOct.
 *
 * Run on a BACKED-UP STAGING SITE with TheOct Fields activated and its JSON
 * definitions imported:
 *     wp eval-file wp-content/themes/theoct/tests/wp-smoke.php
 *
 * Does not create, edit or delete posts, terms, options or permalinks.
 */
if ( ! defined( 'ABSPATH' ) ) {
    fwrite( STDERR, "ERROR: run with wp eval-file from a WordPress installation.\n" );
    exit( 2 );
}

$theoct_failures = array();
$theoct_count = 0;
$theoct_assert = static function ( $condition, $message ) use ( &$theoct_failures, &$theoct_count ) {
    ++$theoct_count;
    if ( ! $condition ) {
        $theoct_failures[] = $message;
        echo "FAIL: {$message}\n";
    } else {
        echo "PASS: {$message}\n";
    }
};

global $wp_version;
$theoct_assert( version_compare( PHP_VERSION, '8.1', '>=' ), 'PHP 8.1 or newer' );
$theoct_assert( version_compare( $wp_version, '6.5', '>=' ), 'WordPress 6.5 or newer' );
$theoct_assert( wp_get_theme()->get( 'Name' ) === 'TheOct', 'TheOct theme active' );
$theoct_assert( defined( 'THEOCT_FIELDS_VERSION' ), 'TheOct Fields plugin active' );

if ( defined( 'THEOCT_FIELDS_VERSION' ) ) {
    $theoct_assert( THEOCT_FIELDS_VERSION === '1.4.0', 'TheOct Fields version 1.4.0' );
}

$theoct_types = array(
    'service'    => array( 'services', true, 10 ),
    'project'    => array( 'projects', true, 4 ),
    'case_study' => array( 'case-studies', true, 5 ),
    'industry'   => array( 'industries', true, 4 ),
    'team'       => array( 'teams', true, 4 ),
    'pricing'    => array( 'pricing', true, 4 ),
    'faq'        => array( 'faqs', false, 3 ),
);

foreach ( $theoct_types as $slug => $expected ) {
    $object = get_post_type_object( $slug );
    $theoct_assert( $object !== null, "CPT '{$slug}' registered (requires JSON import)" );
    if ( ! $object ) {
        continue;
    }
    $theoct_assert( ! empty( $object->public ), "{$slug}: public registration" );
    $theoct_assert( ! empty( $object->show_in_rest ), "{$slug}: REST/editor enabled" );
    $theoct_assert( (bool) $object->has_archive === $expected[1], "{$slug}: archive flag" );
    $theoct_assert( ( $object->rewrite['slug'] ?? null ) === $expected[0], "{$slug}: rewrite base" );
    $theoct_assert( post_type_supports( $slug, 'title' ), "{$slug}: title support" );

    $groups = get_posts(
        array(
            'post_type'      => 'theoct_field_group',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'meta_key'       => '_theoct_definition_key',
            'meta_value'     => 'service' === $slug
                ? 'theoct_field_group_single_service_detail_reference'
                : 'theoct_field_group_' . $slug,
        )
    );
    $theoct_assert( count( $groups ) === 1, "{$slug}: imported field group exists" );
    if ( $groups ) {
        $fields = get_post_meta( (int) $groups[0], '_theoct_fields', true );
        $actual_tabs = is_array( $fields ) ? count( array_filter(
            $fields,
            static function ( $field ) {
                return is_array( $field ) && ( $field['type'] ?? '' ) === 'tab';
            }
        ) ) : 0;
        $theoct_assert( $actual_tabs === $expected[2], "{$slug}: admin section tabs imported" );
    }
}

$theoct_taxonomies = array(
    'service_category' => 'service',
    'project_category' => 'project',
    'project_technology' => 'project',
    'case_study_category' => 'case_study',
    'industry_category' => 'industry',
    'team_department' => 'team',
    'team_skill' => 'team',
    'pricing_category' => 'pricing',
    'faq_category' => 'faq',
);
foreach ( $theoct_taxonomies as $taxonomy => $post_type ) {
    $theoct_assert( taxonomy_exists( $taxonomy ), "Taxonomy '{$taxonomy}' registered" );
    if ( taxonomy_exists( $taxonomy ) ) {
        $theoct_assert( is_object_in_taxonomy( $post_type, $taxonomy ), "{$taxonomy}: attached to {$post_type}" );
        $object = get_taxonomy( $taxonomy );
        $theoct_assert( ! empty( $object->show_in_rest ), "{$taxonomy}: REST/editor enabled" );
    }
}

$theoct_assert( ! wp_script_is( 'theoct-cpt-editor', 'enqueued' ), 'No theme admin JS loaded on CLI/frontend' );
$theoct_assert( ! wp_style_is( 'theoct-cpt-admin', 'enqueued' ), 'No theme admin CSS loaded on CLI/frontend' );

$theme_path = get_template_directory();
$theoct_assert( is_file( $theme_path . '/index.php' ), 'Required theme index.php exists' );
$theoct_assert( is_file( $theme_path . '/style.css' ), 'Required theme style.css exists' );

echo "\nRESULT: {$theoct_count} assertions, " . count( $theoct_failures ) . " failures.\n";
if ( $theoct_failures ) {
    exit( 1 );
}
exit( 0 );
