<?php
/**
 * Services archive and service detail page support.
 *
 * Only the Services archive and single Service pages are rendered.
 * No shared header/footer templates are introduced; other post types stay blank.
 *
 * @package TheOct
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Self-hosted fonts and a page-specific stylesheet, never global assets.
 */
function theoct_services_assets() {
    if ( ! is_post_type_archive( 'service' ) && ! is_singular( 'service' ) ) {
        return;
    }
    $version = wp_get_theme()->get( 'Version' );
    wp_enqueue_style( 'theoct-service-fonts', get_theme_file_uri( '/assets/css/fonts.css' ), array(), $version );
    if ( is_post_type_archive( 'service' ) ) {
        wp_enqueue_style( 'theoct-service-pages', get_theme_file_uri( '/assets/css/services-pages.css' ), array( 'theoct-service-fonts' ), $version );
    } else {
        wp_enqueue_style( 'theoct-single-service-detail', get_theme_file_uri( '/assets/css/single-service-detail.css' ), array( 'theoct-service-fonts' ), $version );
    }
}
add_action( 'wp_enqueue_scripts', 'theoct_services_assets' );
add_action( 'after_setup_theme', static function () { add_theme_support( 'title-tag' ); } );

/**
 * Read field values saved by TheOct Fields with its customisable prefix.
 * A saved empty value is respected; defaults are used only when never saved.
 */
function theoct_services_meta( $post_id, $field, $default = '' ) {
    if ( ! $post_id ) {
        return $default;
    }
    $prefix = (string) get_option( 'theoct_fields_storage_prefix', 'theoct_' );
    $key    = $prefix . sanitize_key( $field );
    return metadata_exists( 'post', (int) $post_id, $key )
        ? get_post_meta( (int) $post_id, $key, true )
        : $default;
}

/**
 * Use only trusted link data from the Link field; no dummy /contact URLs.
 */
function theoct_services_button( $link, $fallback, $class = 'oct-button' ) {
    $data   = is_array( $link ) ? $link : array();
    $url    = isset( $data['url'] ) && is_scalar( $data['url'] ) ? esc_url( (string) $data['url'] ) : '';
    $label  = isset( $data['title'] ) && is_scalar( $data['title'] ) && '' !== trim( (string) $data['title'] )
        ? (string) $data['title'] : $fallback;
    $target = ( $data['target'] ?? '' ) === '_blank' ? '_blank' : '_self';

    if ( '' === $url ) {
        echo '<span class="' . esc_attr( $class . ' oct-button-inert' ) . '" title="' .
            esc_attr__( 'Set the button URL in TheOct Fields', 'theoct' ) . '">' . esc_html( $label ) .
            ' <span aria-hidden="true">↗</span></span>';
        return;
    }
    echo '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $url ) . '" target="' . esc_attr( $target ) . '"';
    if ( '_blank' === $target ) {
        echo ' rel="noopener noreferrer"';
    }
    echo '>' . esc_html( $label ) . ' <span aria-hidden="true">↗</span></a>';
}

/**
 * The plugin Media field stores an attachment ID.
 */
function theoct_services_media( $value, $size = 'large', $class = '' ) {
    $id = absint( $value );
    if ( ! $id || ! wp_attachment_is_image( $id ) ) {
        return '';
    }
    return wp_get_attachment_image( $id, $size, false, array( 'class' => $class, 'loading' => 'lazy' ) );
}

/**
 * Repeater arrays are always treated as arrays.
 */
function theoct_services_rows( $value ) {
    return is_array( $value ) ? array_values( array_filter( $value, 'is_array' ) ) : array();
}

/**
 * Compile real Service posts into a dynamic archive card collection.
 * This consumes the current main query and preserves WordPress pagination.
 */
function theoct_services_archive_cards() {
    $cards = array();
    while ( have_posts() ) {
        the_post();
        $id = get_the_ID();
        $summary = theoct_services_meta( $id, 'ssd_hero_description', '' );
        if ( ! is_string( $summary ) || '' === trim( $summary ) ) {
            $summary = get_the_excerpt();
        }
        $cards[] = array(
            'title'       => get_the_title(),
            'description' => wp_trim_words( wp_strip_all_tags( (string) $summary ), 22, '…' ),
            'url'         => get_permalink(),
            'image'       => has_post_thumbnail() ? get_post_thumbnail_id() : theoct_services_meta( $id, 'service_hero_image', 0 ),
            'icon'        => theoct_services_meta( $id, 'service_icon', '' ),
        );
    }
    return $cards;
}

/**
 * Minimal document wrapper for these two templates only.
 * No site header, menu, or footer component is rendered.
 */
function theoct_services_open_document( $title ) {
    ?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'oct-services-page' ); ?>>
<?php wp_body_open(); ?>
<a class="oct-skip" href="#oct-content"><?php esc_html_e( 'Skip to content', 'theoct' ); ?></a>
<?php
}

/** Close the standalone document without adding a footer. */
function theoct_services_close_document() {
    wp_footer();
    echo '</body></html>';
}

/**
 * Create optional per-page advanced groups without overwriting existing
 * TheOct Fields groups. The archive group is restricted to Service's
 * private archive configuration post ID via post_id location rules.
 */
function theoct_services_install_layout_groups() {
    if ( ! current_user_can( 'manage_options' )
        || ! defined( 'THEOCT_FIELDS_VERSION' )
        || ! class_exists( '\TheOct\Fields\Core\DefinitionIO' )
        || ! function_exists( 'theoct_archive_editor_id' ) ) {
        return;
    }

    $path = get_template_directory() . '/config/services-layout-schema.json';
    if ( ! is_readable( $path ) ) {
        return;
    }
    $bundle = json_decode( file_get_contents( $path ), true );
    if ( ! is_array( $bundle ) || ! is_array( $bundle['definitions'] ?? null ) ) {
        return;
    }
    $service_archive_id = theoct_archive_editor_id( 'service' );
    if ( ! $service_archive_id ) {
        return;
    }

    foreach ( $bundle['definitions'] as $definition ) {
        $key = $definition['definition_key'] ?? '';
        if ( \TheOct\Fields\Core\DefinitionIO::findByDefinitionKey( $key ) ) {
            continue;
        }
        if ( 'theoct_field_group_services_archive_sections' === $key ) {
            $definition['meta']['_theoct_location_post_ids'] = array( $service_archive_id );
            $definition['meta']['_theoct_location_rule_groups'] = array(
                array(
                    array( 'param' => 'post_type', 'operator' => '==', 'value' => 'theoct_archive' ),
                    array( 'param' => 'post_id', 'operator' => '==', 'value' => (string) $service_archive_id ),
                ),
            );
        }
        $result = \TheOct\Fields\Core\DefinitionIO::importBundle(
            array(
                'schema_version' => 1,
                'plugin_version' => THEOCT_FIELDS_VERSION,
                'definitions' => array( $definition ),
            )
        );
        if ( is_wp_error( $result ) ) {
            error_log( 'TheOct Services field group: ' . $result->get_error_message() );
        }
    }
}
add_action( 'admin_init', 'theoct_services_install_layout_groups', 50 );


/**
 * Add the screenshot-inspired Single Service field group without touching
 * existing field groups, custom field values, archive pages or templates.
 *
 * Plugin API: TheOct\Fields\Core\DefinitionIO (v1.4.0).
 * Runs only for administrators and imports only if this exact group is absent.
 */
function theoct_services_install_single_detail_fields() {
    if ( ! is_admin()
        || ! current_user_can( 'manage_options' )
        || ! defined( 'THEOCT_FIELDS_VERSION' )
        || ! class_exists( '\TheOct\Fields\Core\DefinitionIO' ) ) {
        return;
    }

    $group_key = 'theoct_field_group_single_service_detail_reference';

    if ( \TheOct\Fields\Core\DefinitionIO::findByDefinitionKey( $group_key ) ) {
        return; // Keep any changes the editor has made to the installed group.
    }

    $schema_path = get_template_directory() . '/config/single-service-detail-schema.json';
    if ( ! is_readable( $schema_path ) ) {
        return;
    }

    $json = file_get_contents( $schema_path ); // Trusted theme bundle, no external requests.
    if ( false === $json ) {
        return;
    }

    $bundle = json_decode( $json, true );
    if ( ! is_array( $bundle ) || 1 !== (int) ( $bundle['schema_version'] ?? 0 ) ) {
        return;
    }

    $definitions = $bundle['definitions'] ?? null;
    if ( ! is_array( $definitions ) || 1 !== count( $definitions ) ) {
        return;
    }

    $definition = $definitions[0];
    if ( ! is_array( $definition )
        || $group_key !== ( $definition['definition_key'] ?? '' )
        || 'theoct_field_group' !== ( $definition['type'] ?? '' )
        || array( 'service' ) !== ( $definition['meta']['_theoct_location_post_types'] ?? null ) ) {
        return;
    }

    $result = \TheOct\Fields\Core\DefinitionIO::importBundle(
        array(
            'schema_version' => 1,
            'plugin_version' => THEOCT_FIELDS_VERSION,
            'definitions'    => array( $definition ),
        )
    );

    if ( is_wp_error( $result ) ) {
        error_log( 'TheOct Single Service details: ' . $result->get_error_message() );
    }
}
add_action( 'admin_init', 'theoct_services_install_single_detail_fields', 55 );
