<?php
/**
 * TheOct starter functions.
 *
 * Intentionally left without hooks, styles, scripts, or
 * frontend features. Add theme functionality here later.
 *
 * @package TheOct
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register missing CPTs and taxonomies automatically (no frontend markup).
require_once get_template_directory() . '/inc/content-types.php';

// Six private, admin-editable archive configuration pages (public URLs unchanged).
require_once get_template_directory() . '/inc/archive-pages.php';

// Dashboard-only improvements for CPTs registered by TheOct Fields.
if ( is_admin() ) {
    require_once get_template_directory() . '/inc/admin.php';
}
