<?php
/**
 * Single post content.
 *
 * @package TheOct
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'singular-entry' ); ?>>
    <header class="entry-header">
        <span class="eyebrow"><?php esc_html_e( 'Journal', 'theoct' ); ?></span>
        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
        <div class="entry-meta">
            <time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
            <span>
                <?php
                printf(
                    /* translators: %s: author display name. */
                    esc_html__( 'By %s', 'theoct' ),
                    esc_html( get_the_author() )
                );
                ?>
            </span>
        </div>
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
    <?php if ( has_tag() ) : ?>
        <footer class="entry-footer">
            <?php the_tags( '<span class="tags-links">', ' ', '</span>' ); ?>
        </footer>
    <?php endif; ?>
</article>
