<?php
/**
 * Static page content.
 *
 * @package TheOct
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'singular-entry' ); ?>>
    <header class="entry-header">
        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
    </header>
    <?php if ( has_post_thumbnail() ) : ?>
        <figure class="post-thumbnail"><?php the_post_thumbnail( 'large' ); ?></figure>
    <?php endif; ?>
    <div class="entry-content">
        <?php
        the_content();
        wp_link_pages(
            array(
                'before' => '<div class="post-page-links">' . esc_html__( 'Pages:', 'theoct' ),
                'after'  => '</div>',
            )
        );
        ?>
    </div>
    <?php edit_post_link( esc_html__( 'Edit page', 'theoct' ), '<footer class="entry-footer">', '</footer>' ); ?>
</article>
