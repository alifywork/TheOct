<?php
/**
 * Private dashboard editors for TheOct CPT archive configurations.
 *
 * The actual /services/, /projects/, etc URLs continue to be WordPress CPT
 * archives. These six private records are ONLY editorial configuration.
 * Nothing here loads assets or outputs markup on the public frontend.
 *
 * @package TheOct
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Single source of truth for the six archive targets.
 *
 * @return array<string,string>
 */
function theoct_archive_targets() {
    return array(
        'service'    => __( 'Services', 'theoct' ),
        'project'    => __( 'Projects', 'theoct' ),
        'case_study' => __( 'Case Studies', 'theoct' ),
        'industry'   => __( 'Industries', 'theoct' ),
        'team'       => __( 'Teams', 'theoct' ),
        'pricing'    => __( 'Pricing', 'theoct' ),
    );
}

/**
 * Internal editor records: intentionally NOT public WordPress Pages.
 */
function theoct_register_archive_editor_type() {
    register_post_type(
        'theoct_archive',
        array(
            'labels'              => array(
                'name'          => __( 'Archive Pages', 'theoct' ),
                'singular_name' => __( 'Archive Page', 'theoct' ),
                'edit_item'     => __( 'Edit Archive Page', 'theoct' ),
            ),
            'public'              => false,
            'publicly_queryable'  => false,
            'exclude_from_search' => true,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'show_in_admin_bar'   => false,
            'show_in_rest'        => false,
            'has_archive'         => false,
            'rewrite'             => false,
            'query_var'           => false,
            'capability_type'     => 'page',
            'map_meta_cap'        => true,
            'capabilities'        => array(
                'create_posts'           => 'do_not_allow',
                'delete_posts'           => 'do_not_allow',
                'delete_others_posts'    => 'do_not_allow',
                'delete_private_posts'   => 'do_not_allow',
                'delete_published_posts' => 'do_not_allow',
            ),
            'supports'            => array( 'title', 'editor', 'thumbnail' ),
        )
    );
}
add_action( 'init', 'theoct_register_archive_editor_type', 11 );

/**
 * Support featured images in edit screens without rendering them publicly.
 */
function theoct_archive_featured_image_support() {
    add_theme_support(
        'post-thumbnails',
        array( 'theoct_archive', 'service', 'project', 'case_study', 'industry', 'team' )
    );
}
add_action( 'after_setup_theme', 'theoct_archive_featured_image_support' );

/**
 * Stable ID lookup based on reserved post meta, not editable post titles.
 */
function theoct_archive_editor_id( $target ) {
    $target = sanitize_key( (string) $target );
    if ( ! array_key_exists( $target, theoct_archive_targets() ) ) {
        return 0;
    }

    $ids = get_posts(
        array(
            'post_type'        => 'theoct_archive',
            'post_status'      => array( 'publish', 'private', 'draft', 'pending', 'future', 'trash' ),
            'posts_per_page'   => 1,
            'fields'           => 'ids',
            'meta_key'         => '_theoct_archive_target',
            'meta_value'       => $target,
            'suppress_filters' => true,
        )
    );
    return ! empty( $ids ) ? (int) $ids[0] : 0;
}

/**
 * Create only missing configuration records; never overwrite user edits.
 * This is run on activation AND during upgrade of an already active theme.
 */
function theoct_seed_archive_editors() {
    if ( ! current_user_can( 'edit_pages' ) || ! post_type_exists( 'theoct_archive' ) ) {
        return;
    }

    $already_seeded = get_option( 'theoct_archive_editors_v1', '' );
    if ( '1' === (string) $already_seeded ) {
        return;
    }

    $all_created = true;
    foreach ( theoct_archive_targets() as $target => $label ) {
        if ( theoct_archive_editor_id( $target ) ) {
            continue;
        }

        $id = wp_insert_post(
            array(
                'post_type'    => 'theoct_archive',
                'post_status'  => 'publish',
                'post_title'   => sprintf(
                    /* translators: %s: Archive content type name. */
                    __( '%s Archive', 'theoct' ),
                    $label
                ),
                'post_name'   => 'theoct-archive-' . $target,
                'post_content' => '',
                'post_author'  => get_current_user_id(),
                'menu_order'   => array_search( $target, array_keys( theoct_archive_targets() ), true ),
            ),
            true
        );

        if ( is_wp_error( $id ) || ! $id ) {
            $all_created = false;
            continue;
        }

        update_post_meta( $id, '_theoct_archive_target', $target );
    }

    if ( $all_created ) {
        update_option( 'theoct_archive_editors_v1', '1', false );
    }
}
add_action( 'after_switch_theme', 'theoct_seed_archive_editors', 25 );
add_action( 'admin_init', 'theoct_seed_archive_editors', 12 );

/**
 * Protect canonical records from accidental deletion.
 */
function theoct_prevent_archive_delete( $delete, $post ) {
    if ( $post instanceof WP_Post && 'theoct_archive' === $post->post_type ) {
        return false;
    }
    return $delete;
}
add_filter( 'pre_delete_post', 'theoct_prevent_archive_delete', 10, 2 );
add_filter( 'pre_trash_post', 'theoct_prevent_archive_delete', 10, 2 );

/**
 * Show a dedicated dashboard screen: TheOct → Archive Pages.
 */
function theoct_archive_admin_menu() {
    add_menu_page(
        __( 'TheOct', 'theoct' ),
        __( 'TheOct', 'theoct' ),
        'edit_pages',
        'theoct',
        'theoct_render_archive_directory',
        'dashicons-layout',
        24
    );

    add_submenu_page(
        'theoct',
        __( 'Archive Pages', 'theoct' ),
        __( 'Archive Pages', 'theoct' ),
        'edit_pages',
        'theoct',
        'theoct_render_archive_directory'
    );
}
add_action( 'admin_menu', 'theoct_archive_admin_menu', 20 );

/**
 * Render WordPress admin-only directory; links lead to normal post editors.
 */
function theoct_render_archive_directory() {
    if ( ! current_user_can( 'edit_pages' ) ) {
        wp_die( esc_html__( 'You cannot manage archive pages.', 'theoct' ) );
    }

    echo '<div class="wrap">';
    echo '<h1>' . esc_html__( 'TheOct — Archive Pages', 'theoct' ) . '</h1>';
    echo '<p>' . esc_html__( 'Edit the title, description, featured image and SEO metadata of each CPT archive. The public archive URLs stay unchanged and all frontend templates remain blank.', 'theoct' ) . '</p>';
    echo '<table class="widefat fixed striped"><thead><tr>';
    echo '<th scope="col">' . esc_html__( 'Archive', 'theoct' ) . '</th>';
    echo '<th scope="col">' . esc_html__( 'Public archive URL', 'theoct' ) . '</th>';
    echo '<th scope="col">' . esc_html__( 'Action', 'theoct' ) . '</th>';
    echo '</tr></thead><tbody>';

    foreach ( theoct_archive_targets() as $target => $label ) {
        $post_id = theoct_archive_editor_id( $target );
        $url     = get_post_type_archive_link( $target );

        echo '<tr><th scope="row">' . esc_html( $label ) . '</th><td>';
        if ( $url ) {
            echo '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer"><code>' . esc_html( wp_parse_url( $url, PHP_URL_PATH ) ?: '/' ) . '</code></a>';
        } else {
            echo '<span>—</span>';
        }
        echo '</td><td>';
        if ( $post_id && current_user_can( 'edit_post', $post_id ) ) {
            echo '<a class="button button-secondary" href="' . esc_url( get_edit_post_link( $post_id, 'raw' ) ) . '">';
            esc_html_e( 'Edit Archive Page', 'theoct' );
            echo '</a>';
        } else {
            esc_html_e( 'Record unavailable', 'theoct' );
        }
        echo '</td></tr>';
    }
    echo '</tbody></table>';

    if ( ! defined( 'THEOCT_FIELDS_VERSION' ) ) {
        echo '<div class="notice notice-info inline"><p>';
        esc_html_e( 'TheOct Fields is not active. Title, content, image and native SEO settings work now; activate TheOct Fields v1.4.0 to enable the advanced archive fields.', 'theoct' );
        echo '</p></div>';
    } else {
        echo '<p class="description">';
        esc_html_e( 'Advanced fields are provided by TheOct Fields when its archive field group is available. Stored fields will be used later when you build the frontend.', 'theoct' );
        echo '</p>';
    }
    echo '</div>';
}

/**
 * Private metadata for search-engine information, not yet emitted as tags.
 */
function theoct_archive_register_meta() {
    foreach ( array( '_theoct_archive_seo_title', '_theoct_archive_seo_description', '_theoct_archive_canonical' ) as $key ) {
        register_post_meta(
            'theoct_archive',
            $key,
            array(
                'type'         => 'string',
                'single'       => true,
                'show_in_rest' => false,
                'sanitize_callback' => 'sanitize_text_field',
                'auth_callback' => static function () {
                    return current_user_can( 'edit_pages' );
                },
            )
        );
    }
    register_post_meta(
        'theoct_archive',
        '_theoct_archive_robots',
        array(
            'type'         => 'string',
            'single'       => true,
            'show_in_rest' => false,
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback' => static function () {
                return current_user_can( 'edit_pages' );
            },
        )
    );
}
add_action( 'init', 'theoct_archive_register_meta', 15 );

/**
 * SEO editorial form for the internal archive record; no public rendering.
 */
function theoct_archive_seo_box() {
    add_meta_box(
        'theoct-archive-seo',
        __( 'Archive SEO Settings', 'theoct' ),
        'theoct_render_archive_seo_box',
        'theoct_archive',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes_theoct_archive', 'theoct_archive_seo_box' );

function theoct_render_archive_seo_box( $post ) {
    wp_nonce_field( 'theoct_save_archive_seo', 'theoct_archive_seo_nonce' );
    $fields = array(
        '_theoct_archive_seo_title'       => array( __( 'SEO Title', 'theoct' ), 'text', 160 ),
        '_theoct_archive_seo_description' => array( __( 'Meta Description', 'theoct' ), 'textarea', 350 ),
        '_theoct_archive_canonical'       => array( __( 'Canonical URL (optional)', 'theoct' ), 'url', 500 ),
    );

    echo '<p class="description">' . esc_html__( 'These are saved settings only; the blank frontend does not output SEO tags.', 'theoct' ) . '</p>';
    foreach ( $fields as $key => $settings ) {
        $value = (string) get_post_meta( $post->ID, $key, true );
        printf(
            '<p><label for="%1$s"><strong>%2$s</strong></label></p>',
            esc_attr( $key ),
            esc_html( $settings[0] )
        );

        if ( 'textarea' === $settings[1] ) {
            printf(
                '<p><textarea class="large-text" rows="3" id="%1$s" name="theoct_archive_seo[%1$s]" maxlength="%2$d">%3$s</textarea></p>',
                esc_attr( $key ),
                (int) $settings[2],
                esc_textarea( $value )
            );
        } else {
            printf(
                '<p><input class="large-text" type="%1$s" id="%2$s" name="theoct_archive_seo[%2$s]" value="%3$s" maxlength="%4$d"></p>',
                esc_attr( $settings[1] ),
                esc_attr( $key ),
                esc_attr( $value ),
                (int) $settings[2]
            );
        }
    }

    $robots = (string) get_post_meta( $post->ID, '_theoct_archive_robots', true );
    echo '<p><label for="theoct_archive_robots"><strong>' . esc_html__( 'Robots Preference (saved only)', 'theoct' ) . '</strong></label></p>';
    echo '<select id="theoct_archive_robots" name="theoct_archive_seo[_theoct_archive_robots]">';
    foreach ( array(
        'index,follow' => __( 'Index, Follow', 'theoct' ),
        'noindex,follow' => __( 'Noindex, Follow', 'theoct' ),
        'noindex,nofollow' => __( 'Noindex, Nofollow', 'theoct' ),
    ) as $value => $label ) {
        echo '<option value="' . esc_attr( $value ) . '" ' . selected( $robots, $value, false ) . '>' . esc_html( $label ) . '</option>';
    }
    echo '</select>';
}

/**
 * Save native SEO values; TheOct Fields independently saves its field group.
 */
function theoct_save_archive_seo( $post_id, $post ) {
    if ( ! isset( $_POST['theoct_archive_seo_nonce'] )
        || ! is_string( $_POST['theoct_archive_seo_nonce'] )
        || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['theoct_archive_seo_nonce'] ) ), 'theoct_save_archive_seo' )
        || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        || wp_is_post_revision( $post_id )
        || ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( ! isset( $_POST['theoct_archive_seo'] ) || ! is_array( $_POST['theoct_archive_seo'] ) ) {
        return;
    }

    $fields = wp_unslash( $_POST['theoct_archive_seo'] );
    $limits = array(
        '_theoct_archive_seo_title'       => 160,
        '_theoct_archive_seo_description' => 350,
        '_theoct_archive_canonical'       => 500,
    );

    foreach ( $limits as $key => $limit ) {
        if ( ! isset( $fields[ $key ] ) || ! is_scalar( $fields[ $key ] ) ) {
            continue;
        }
        $value = (string) $fields[ $key ];
        if ( '_theoct_archive_canonical' === $key ) {
            $value = esc_url_raw( $value, array( 'http', 'https' ) );
        } else {
            $value = sanitize_text_field( $value );
        }
        update_post_meta( $post_id, $key, substr( $value, 0, $limit ) );
    }

    if ( isset( $fields['_theoct_archive_robots'] ) && is_scalar( $fields['_theoct_archive_robots'] ) ) {
        $allowed = array( 'index,follow', 'noindex,follow', 'noindex,nofollow' );
        $robots  = sanitize_text_field( (string) $fields['_theoct_archive_robots'] );
        if ( in_array( $robots, $allowed, true ) ) {
            update_post_meta( $post_id, '_theoct_archive_robots', $robots );
        }
    }
}
add_action( 'save_post_theoct_archive', 'theoct_save_archive_seo', 20, 2 );

/**
 * No-override behavior: create the archive field group in TheOct Fields only
 * when the plugin is installed and there is no matching custom group.
 */
function theoct_ensure_archive_field_group() {
    if ( ! current_user_can( 'manage_options' )
        || ! defined( 'THEOCT_FIELDS_VERSION' )
        || ! class_exists( '\TheOct\Fields\Core\DefinitionIO' )
        || ! class_exists( '\TheOct\Fields\Core\FieldGroupRepository' ) ) {
        return;
    }

    $key = 'theoct_field_group_archive_pages';
    if ( \TheOct\Fields\Core\DefinitionIO::findByDefinitionKey( $key ) ) {
        return;
    }

    if ( \TheOct\Fields\Core\FieldGroupRepository::instance()->forPostType( 'theoct_archive' ) ) {
        return; // Respect an existing custom group made by the user.
    }

    if ( ! function_exists( 'theoct_schema_bundle' ) ) {
        return;
    }

    $bundle = theoct_schema_bundle();
    foreach ( (array) ( $bundle['definitions'] ?? array() ) as $definition ) {
        if ( ( $definition['definition_key'] ?? '' ) !== $key ) {
            continue;
        }

        $result = \TheOct\Fields\Core\DefinitionIO::importBundle(
            array(
                'schema_version' => 1,
                'plugin_version' => THEOCT_FIELDS_VERSION,
                'definitions'    => array( $definition ),
            )
        );
        if ( is_wp_error( $result ) ) {
            error_log( 'TheOct archive field group: ' . $result->get_error_message() );
        }
        break;
    }
}
add_action( 'admin_init', 'theoct_ensure_archive_field_group', 35 );

/**
 * Tell editors which internal configuration they are editing.
 */
function theoct_archive_edit_message( $post ) {
    if ( ! $post || 'theoct_archive' !== $post->post_type ) {
        return;
    }
    $target = get_post_meta( $post->ID, '_theoct_archive_target', true );
    if ( ! array_key_exists( $target, theoct_archive_targets() ) ) {
        return;
    }
    $url = get_post_type_archive_link( $target );
    echo '<div class="notice notice-info inline"><p>';
    if ( 'service' === $target ) {
        esc_html_e( 'Editing the Services Archive. Use Services Archive Sections to control its frontend; SEO metadata is stored but not yet output.', 'theoct' );
    } else {
        printf(
            /* translators: %s: The CPT archive name. */
            esc_html__( 'Editing the %s archive settings. These values are stored but not yet displayed on the public frontend.', 'theoct' ),
            esc_html( theoct_archive_targets()[ $target ] )
        );
    }
    if ( $url ) {
        echo ' <a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">';
        esc_html_e( 'Open archive URL', 'theoct' );
        echo '</a>';
    }
    echo ' | <a href="' . esc_url( admin_url( 'admin.php?page=theoct' ) ) . '">';
    esc_html_e( 'All Archive Pages', 'theoct' );
    echo '</a></p></div>';
}
add_action( 'edit_form_top', 'theoct_archive_edit_message' );
