<?php
/**
 * Empty-query state.
 *
 * @package TheOct
 */
?>
<section class="no-results not-found">
    <h2><?php esc_html_e( 'Nothing found', 'theoct' ); ?></h2>
    <?php if ( is_search() ) : ?>
        <p><?php esc_html_e( 'No results matched your search. Try another keyword.', 'theoct' ); ?></p>
    <?php else : ?>
        <p><?php esc_html_e( 'There are no posts here yet. Check back soon.', 'theoct' ); ?></p>
    <?php endif; ?>
    <?php get_search_form(); ?>
</section>
