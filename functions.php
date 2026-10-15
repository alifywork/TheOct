<?php
/**
 * Theme setup, assets, menus and widgets.
 *
 * @package TheOct
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'theoct_setup' ) ) :
    /**
     * Register theme features supported by WordPress.
     */
    function theoct_setup() {
        load_theme_textdomain( 'theoct', get_template_directory() . '/languages' );

        add_theme_support( 'automatic-feed-links' );
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'responsive-embeds' );
        add_theme_support( 'align-wide' );
        add_theme_support( 'editor-styles' );
        add_theme_support(
            'custom-logo',
            array(
                'height'      => 120,
                'width'       => 360,
                'flex-height' => true,
                'flex-width'  => true,
            )
        );
        add_theme_support(
            'html5',
            array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
        );

        add_editor_style( 'assets/css/editor.css' );

        register_nav_menus(
            array(
                'primary' => __( 'Primary Menu', 'theoct' ),
                'footer'  => __( 'Footer Menu', 'theoct' ),
            )
        );
    }
endif;
add_action( 'after_setup_theme', 'theoct_setup' );

/**
 * Set maximum supported content width.
 */
function theoct_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'theoct_content_width', 820 );
}
add_action( 'after_setup_theme', 'theoct_content_width', 0 );

/**
 * Enqueue stylesheet and accessible mobile navigation.
 */
function theoct_scripts() {
    $version = wp_get_theme()->get( 'Version' );

    wp_enqueue_style( 'theoct-style', get_stylesheet_uri(), array(), $version );
    wp_enqueue_script(
        'theoct-navigation',
        get_theme_file_uri( '/assets/js/navigation.js' ),
        array(),
        $version,
        true
    );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'theoct_scripts' );

/**
 * Register the widget area.
 */
function theoct_widgets_init() {
    register_sidebar(
        array(
            'name'          => __( 'Sidebar', 'theoct' ),
            'id'            => 'sidebar-1',
            'description'   => __( 'Widgets shown alongside blog and single posts.', 'theoct' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
}
add_action( 'widgets_init', 'theoct_widgets_init' );

/**
 * Provide an accessible Home link until a menu is assigned.
 */
function theoct_fallback_menu() {
    printf(
        '<ul id="primary-menu" class="menu-list"><li><a href="%1$s">%2$s</a></li></ul>',
        esc_url( home_url( '/' ) ),
        esc_html__( 'Home', 'theoct' )
    );
}
