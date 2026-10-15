<?php
/**
 * Search results template.
 *
 * @package TheOct
 */

get_header();
?>
<div class="container content-grid<?php echo is_active_sidebar( 'sidebar-1' ) ? ' has-sidebar' : ''; ?>">
    <main id="primary" class="site-main">
        <header class="page-heading">
            <span class="eyebrow"><?php esc_html_e( 'Search', 'theoct' ); ?></span>
            <h1>
                <?php
                printf(
                    /* translators: %s: user-provided search terms. */
                    esc_html__( 'Results for: %s', 'theoct' ),
                    '<span>' . esc_html( get_search_query() ) . '</span>'
                );
                ?>
            </h1>
            <?php get_search_form(); ?>
        </header>

        <?php if ( have_posts() ) : ?>
            <div class="post-list">
                <?php
                while ( have_posts() ) :
                    the_post();
                    get_template_part( 'template-parts/content' );
                endwhile;
                ?>
            </div>
            <?php the_posts_pagination( array( 'mid_size' => 2 ) ); ?>
        <?php else : ?>
            <?php get_template_part( 'template-parts/content', 'none' ); ?>
        <?php endif; ?>
    </main>
    <?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
