<?php
/**
 * Static page template.
 *
 * @package TheOct
 */

get_header();
?>
<div class="container">
    <main id="primary" class="site-main">
        <?php
        while ( have_posts() ) :
            the_post();
            get_template_part( 'template-parts/content', 'page' );

            if ( comments_open() || get_comments_number() ) {
                comments_template();
            }
        endwhile;
        ?>
    </main>
</div>
<?php get_footer(); ?>
