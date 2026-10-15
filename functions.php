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

// Dashboard-only improvements for CPTs registered by TheOct Fields.
if ( is_admin() ) {
    require_once get_template_directory() . '/inc/admin.php';
}
