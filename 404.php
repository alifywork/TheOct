<?php
/**
 * Not-found page.
 *
 * @package TheOct
 */

get_header();
?>
<div class="container">
    <main id="primary" class="site-main">
        <section class="not-found error-404">
            <span class="eyebrow"><?php esc_html_e( 'Error 404', 'theoct' ); ?></span>
            <h1><?php esc_html_e( 'That page could not be found.', 'theoct' ); ?></h1>
            <p><?php esc_html_e( 'The page may have moved. Try a search or return to the homepage.', 'theoct' ); ?></p>
            <?php get_search_form(); ?>
            <p><a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'theoct' ); ?></a></p>
        </section>
    </main>
</div>
<?php get_footer(); ?>
