<?php
/**
 * Optional sidebar widget area.
 *
 * @package TheOct
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
    return;
}
?>
<aside id="secondary" class="widget-area" aria-label="<?php esc_attr_e( 'Sidebar', 'theoct' ); ?>">
    <?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
