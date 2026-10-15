<?php
/**
 * The site header.
 *
 * @package TheOct
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'theoct' ); ?></a>
<div id="page" class="site">
    <header id="masthead" class="site-header">
        <div class="container header-inner">
            <div class="site-branding">
                <?php if ( has_custom_logo() ) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <?php if ( is_front_page() && is_home() ) : ?>
                        <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                    <?php else : ?>
                        <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
                    <?php endif; ?>
                    <?php $description = get_bloginfo( 'description', 'display' ); ?>
                    <?php if ( $description || is_customize_preview() ) : ?>
                        <p class="site-description"><?php echo esc_html( $description ); ?></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <button class="menu-toggle" type="button" aria-controls="primary-menu" aria-expanded="false">
                <?php esc_html_e( 'Menu', 'theoct' ); ?>
            </button>
            <nav id="site-navigation" class="primary-navigation" aria-label="<?php esc_attr_e( 'Primary navigation', 'theoct' ); ?>">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'menu_class'     => 'menu-list',
                        'container'      => false,
                        'fallback_cb'    => 'theoct_fallback_menu',
                    )
                );
                ?>
            </nav>
        </div>
    </header>
    <div id="content" class="site-content">
