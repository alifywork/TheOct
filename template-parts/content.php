<?php
/**
 * Card used in lists and archives.
 *
 * @package TheOct
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry-card' ); ?>>
    <?php if ( has_post_thumbnail() ) : ?>
        <a class="entry-card-image" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
            <?php the_post_thumbnail( 'large' ); ?>
        </a>
    <?php endif; ?>
    <div class="entry-card-body">
        <div class="entry-meta">
            <time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
            <?php
            $theoct_categories = get_the_category();
            if ( ! empty( $theoct_categories ) ) :
                ?>
                <span class="entry-category"><?php echo esc_html( $theoct_categories[0]->name ); ?></span>
            <?php endif; ?>
        </div>
        <h2 class="entry-title">
            <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
        </h2>
        <div class="entry-summary"><?php the_excerpt(); ?></div>
        <a class="read-more" href="<?php the_permalink(); ?>">
            <?php esc_html_e( 'Read story', 'theoct' ); ?>
            <span class="screen-reader-text"><?php echo esc_html( get_the_title() ); ?></span>
        </a>
    </div>
</article>
