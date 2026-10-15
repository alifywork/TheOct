<?php
/**
 * Main fallback template for blog posts.
 *
 * @package TheOct
 */

get_header();
?>
<div class="container content-grid<?php echo is_active_sidebar( 'sidebar-1' ) ? ' has-sidebar' : ''; ?>">
    <main id="primary" class="site-main">
        <header class="page-heading">
            <span class="eyebrow"><?php esc_html_e( 'The journal', 'theoct' ); ?></span>
            <h1><?php echo esc_html( is_home() && ! is_front_page() ? single_post_title( '', false ) : __( 'Latest stories', 'theoct' ) ); ?></h1>
            <p><?php esc_html_e( 'Thoughtful stories, ideas, and updates.', 'theoct' ); ?></p>
        </header>

        <?php if ( have_posts() ) : ?>
            <div class="post-list">
                <?php
                while ( have_posts() ) :
                    the_post();
                    get_template_part( 'template-parts/content', get_post_type() === 'post' ? null : get_post_type() );
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
