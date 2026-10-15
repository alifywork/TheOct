<?php
/**
 * Search form used by the theme.
 *
 * @package TheOct
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label>
        <span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'theoct' ); ?></span>
        <input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search…', 'theoct' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" required>
    </label>
    <button type="submit" class="search-submit"><?php esc_html_e( 'Search', 'theoct' ); ?></button>
</form>
