<?php
/**
 * WordPress dashboard enhancements for TheOct content types.
 *
 * Admin-only. The theme's public frontend remains intentionally blank.
 * Relies on the separate TheOct Fields plugin for registration and storage.
 *
 * @package TheOct
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Labels, filters and editor guidance for the seven content types.
 *
 * @return array<string,array<string,mixed>>
 */
function theoct_admin_content_types() {
    return array(
        'service'    => array(
            'placeholder' => 'Enter the service name',
            'taxonomy'    => 'service_category',
            'summary'     => 'service_subtitle',
            'checklist'   => array(
                array( 'title', 'Service title' ),
                array( 'service_intro', 'Service introduction' ),
                array( 'service_benefits', 'Benefits' ),
            ),
        ),
        'project'    => array(
            'placeholder' => 'Enter the project name',
            'taxonomy'    => 'project_category',
            'summary'     => 'client_name',
            'checklist'   => array(
                array( 'title', 'Project title' ),
                array( 'client_name', 'Client name' ),
                array( 'project_summary', 'Project summary' ),
                array( 'project_gallery', 'Portfolio images' ),
            ),
        ),
        'case_study' => array(
            'placeholder' => 'Enter the case study headline',
            'taxonomy'    => 'case_study_category',
            'summary'     => 'client_name',
            'checklist'   => array(
                array( 'title', 'Case study title' ),
                array( 'related_project', 'Linked project' ),
                array( 'challenge', 'Challenge' ),
                array( 'results_metrics', 'Results metrics' ),
            ),
        ),
        'industry'   => array(
            'placeholder' => 'Enter the industry name',
            'taxonomy'    => 'industry_category',
            'summary'     => 'industry_tagline',
            'checklist'   => array(
                array( 'title', 'Industry title' ),
                array( 'industry_summary', 'Industry summary' ),
                array( 'industry_challenges', 'Challenges' ),
            ),
        ),
        'team'       => array(
            'placeholder' => 'Enter the team member name',
            'taxonomy'    => 'team_department',
            'summary'     => 'job_title',
            'checklist'   => array(
                array( 'title', 'Team member name' ),
                array( 'job_title', 'Job title' ),
                array( 'professional_bio', 'Professional bio' ),
                array( 'team_headshot', 'Photo' ),
            ),
        ),
        'pricing'    => array(
            'placeholder' => 'Enter the pricing plan name',
            'taxonomy'    => 'pricing_category',
            'summary'     => 'price_amount',
            'checklist'   => array(
                array( 'title', 'Plan name' ),
                array( 'pricing_mode', 'Pricing mode' ),
                array( 'plan_features', 'Included features' ),
            ),
        ),
        'faq'        => array(
            'placeholder' => 'Enter the FAQ question',
            'taxonomy'    => 'faq_category',
            'summary'     => 'short_answer',
            'checklist'   => array(
                array( 'title', 'FAQ question' ),
                array( 'content', 'Full answer' ),
                array( 'faq_category', 'FAQ category' ),
            ),
        ),
    );
}

/**
 * Set up hooks after TheOct Fields registers its post types at init priority 5.
 */
function theoct_admin_setup() {
    if ( ! defined( 'THEOCT_FIELDS_VERSION' ) ) {
        return;
    }

    foreach ( theoct_admin_content_types() as $post_type => $settings ) {
        if ( ! post_type_exists( $post_type ) ) {
            continue;
        }
        add_filter( "manage_{$post_type}_posts_columns", 'theoct_admin_columns' );
        add_action( "manage_{$post_type}_posts_custom_column", 'theoct_admin_column_content', 10, 2 );
    }

    add_filter( 'enter_title_here', 'theoct_admin_title_placeholder', 10, 2 );
    add_action( 'restrict_manage_posts', 'theoct_admin_taxonomy_filter', 10, 2 );
    add_action( 'add_meta_boxes', 'theoct_admin_guide_meta_box', 30, 2 );
    add_action( 'admin_enqueue_scripts', 'theoct_admin_enqueue' );
    add_action( 'pre_get_posts', 'theoct_admin_sort_order' );
}
add_action( 'init', 'theoct_admin_setup', 30 );

/**
 * Read custom field values using the plugin-configurable storage prefix.
 */
function theoct_admin_field_value( $post_id, $field_name ) {
    $prefix = (string) get_option( 'theoct_fields_storage_prefix', 'theoct_' );
    return get_post_meta( (int) $post_id, $prefix . sanitize_key( $field_name ), true );
}

/**
 * Add a small preview and a human-readable editorial summary.
 */
function theoct_admin_columns( $columns ) {
    $type = isset( $_GET['post_type'] ) && is_string( $_GET['post_type'] )
        ? sanitize_key( wp_unslash( $_GET['post_type'] ) )
        : '';
    if ( ! isset( theoct_admin_content_types()[ $type ] ) ) {
        return $columns;
    }

    $result = array();
    foreach ( $columns as $key => $value ) {
        if ( 'title' === $key && post_type_supports( $type, 'thumbnail' ) ) {
            $result['theoct_preview'] = __( 'Preview', 'theoct' );
        }
        $result[ $key ] = $value;
        if ( 'title' === $key ) {
            $result['theoct_details'] = __( 'Key details', 'theoct' );
        }
    }
    return $result;
}

/**
 * Render escaped values only. Never expose private editorial fields here.
 */
function theoct_admin_column_content( $column, $post_id ) {
    if ( 'theoct_preview' === $column ) {
        if ( has_post_thumbnail( $post_id ) ) {
            echo get_the_post_thumbnail( $post_id, array( 52, 52 ), array( 'class' => 'theoct-admin-thumbnail', 'alt' => '' ) );
        } else {
            echo '<span aria-hidden="true">—</span>';
        }
        return;
    }

    if ( 'theoct_details' !== $column ) {
        return;
    }

    $type   = get_post_type( $post_id );
    $config = theoct_admin_content_types();
    if ( ! isset( $config[ $type ] ) ) {
        return;
    }

    if ( 'pricing' === $type ) {
        $mode   = theoct_admin_field_value( $post_id, 'pricing_mode' );
        $amount = theoct_admin_field_value( $post_id, 'price_amount' );
        $unit   = theoct_admin_field_value( $post_id, 'currency' );
        if ( 'custom' === $mode ) {
            $value = __( 'Custom quote', 'theoct' );
        } elseif ( 'free' === $mode ) {
            $value = __( 'Free', 'theoct' );
        } elseif ( '' !== (string) $amount && is_numeric( $amount ) ) {
            $value = strtoupper( sanitize_text_field( (string) $unit ) ) . ' ' . number_format_i18n( (float) $amount, 2 );
        } else {
            $value = '';
        }
    } else {
        $raw = theoct_admin_field_value( $post_id, $config[ $type ]['summary'] );
        $value = is_scalar( $raw ) ? wp_strip_all_tags( (string) $raw ) : '';
    }
    echo $value !== '' ? esc_html( wp_trim_words( $value, 16, '…' ) ) : '<span class="theoct-admin-muted">—</span>';
}

/**
 * Add native taxonomy filters to the seven CPT list screens.
 */
function theoct_admin_taxonomy_filter( $post_type, $which = 'top' ) {
    $config = theoct_admin_content_types();
    if ( 'top' !== $which || ! isset( $config[ $post_type ] ) ) {
        return;
    }

    $taxonomy = $config[ $post_type ]['taxonomy'];
    if ( ! taxonomy_exists( $taxonomy ) ) {
        return;
    }

    $term_taxonomy = get_taxonomy( $taxonomy );
    $selected      = isset( $_GET[ $taxonomy ] ) && is_scalar( $_GET[ $taxonomy ] )
        ? sanitize_title( wp_unslash( $_GET[ $taxonomy ] ) )
        : '';

    wp_dropdown_categories(
        array(
            'show_option_all' => sprintf(
                /* translators: %s: taxonomy label. */
                __( 'All %s', 'theoct' ),
                $term_taxonomy->labels->name
            ),
            'taxonomy'        => $taxonomy,
            'name'            => $taxonomy,
            'selected'        => $selected,
            'value_field'     => 'slug',
            'hide_empty'      => false,
            'hierarchical'    => (bool) $term_taxonomy->hierarchical,
            'show_count'      => false,
            'orderby'         => 'name',
        )
    );
}

/**
 * Context-specific title placeholders.
 */
function theoct_admin_title_placeholder( $placeholder, $post ) {
    $config = theoct_admin_content_types();
    return isset( $config[ $post->post_type ] )
        ? $config[ $post->post_type ]['placeholder']
        : $placeholder;
}

/**
 * Sidebar guidance based on saved field values, without blocking publishing.
 */
function theoct_admin_guide_meta_box( $post_type, $post ) {
    $config = theoct_admin_content_types();
    if ( ! isset( $config[ $post_type ] ) || ! post_type_exists( $post_type ) ) {
        return;
    }

    add_meta_box(
        'theoct-editing-guide',
        __( 'Content checklist', 'theoct' ),
        'theoct_admin_render_guide',
        $post_type,
        'side',
        'default'
    );
}

/**
 * Check meaningful saved values; zero can be valid in a numeric field.
 */
function theoct_admin_filled( $value ) {
    if ( is_array( $value ) ) {
        return count( array_filter( $value, 'theoct_admin_filled' ) ) > 0;
    }
    if ( is_object( $value ) ) {
        return ! empty( (array) $value );
    }
    return null !== $value && '' !== trim( (string) $value );
}

function theoct_admin_render_guide( $post ) {
    $config = theoct_admin_content_types();
    $items  = $config[ $post->post_type ]['checklist'];
    $done   = 0;
    echo '<p class="description">' . esc_html__( 'Suggested fields, based on the last saved version. This checklist does not block publishing.', 'theoct' ) . '</p>';
    echo '<ul class="theoct-editor-checklist">';
    foreach ( $items as $item ) {
        list( $field, $label ) = $item;
        if ( 'title' === $field ) {
            $filled = theoct_admin_filled( $post->post_title );
        } elseif ( 'content' === $field ) {
            $filled = theoct_admin_filled( $post->post_content );
        } elseif ( 'faq_category' === $field ) {
            $filled = has_term( '', 'faq_category', $post->ID );
        } else {
            $filled = theoct_admin_filled( theoct_admin_field_value( $post->ID, $field ) );
        }
        if ( $filled ) {
            ++$done;
        }
        printf(
            '<li class="%1$s"><span class="dashicons %2$s" aria-hidden="true"></span> %3$s <span class="screen-reader-text">%4$s</span></li>',
            esc_attr( $filled ? 'theoct-guide-done' : 'theoct-guide-todo' ),
            esc_attr( $filled ? 'dashicons-yes-alt' : 'dashicons-marker' ),
            esc_html( $label ),
            esc_html( $filled ? __( 'Complete', 'theoct' ) : __( 'Not filled in', 'theoct' ) )
        );
    }
    echo '</ul>';
    printf(
        '<p class="theoct-guide-progress">%1$d / %2$d %3$s</p>',
        (int) $done,
        count( $items ),
        esc_html__( 'suggested items filled', 'theoct' )
    );
    echo '<p class="description">' . esc_html__( 'The public-facing theme intentionally remains blank.', 'theoct' ) . '</p>';
}

/**
 * Preserve manual menu_order for people, plans and FAQs.
 */
function theoct_admin_sort_order( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() || $query->get( 'orderby' ) ) {
        return;
    }
    $post_type = $query->get( 'post_type' );
    if ( in_array( $post_type, array( 'team', 'pricing', 'faq' ), true ) ) {
        $query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
    }
}

/**
 * Never enqueue the editor enhancements on the public frontend.
 */
function theoct_admin_enqueue( $hook ) {
    if ( ! defined( 'THEOCT_FIELDS_VERSION' ) || ! in_array( $hook, array( 'post.php', 'post-new.php', 'edit.php' ), true ) ) {
        return;
    }
    $screen = get_current_screen();
    $config = theoct_admin_content_types();
    if ( ! $screen || ! isset( $config[ $screen->post_type ] ) ) {
        return;
    }

    $version = wp_get_theme()->get( 'Version' );
    wp_enqueue_style(
        'theoct-cpt-admin',
        get_theme_file_uri( '/assets/admin/cpt-editor.css' ),
        array( 'theoct-fields-admin' ),
        $version
    );

    if ( 'post' === $screen->base ) {
        wp_enqueue_script(
            'theoct-cpt-editor',
            get_theme_file_uri( '/assets/admin/cpt-editor.js' ),
            array( 'theoct-fields-admin' ),
            $version,
            true
        );
    }
}
