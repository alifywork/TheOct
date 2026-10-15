<?php
/**
 * Services archive landing page. No shared header/footer.
 *
 * @package TheOct
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$archive_id = function_exists( 'theoct_archive_editor_id' ) ? theoct_archive_editor_id( 'service' ) : 0;
$cards      = theoct_services_archive_cards();
ob_start();
the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => '←', 'next_text' => '→' ) );
$pagination = ob_get_clean();

theoct_services_open_document( __( 'Services', 'theoct' ) );
get_template_part( 'template-parts/services-layout', null, array(
    'post_id'     => $archive_id,
    'prefix'      => 'sa_',
    'mode'        => 'archive',
    'cards'       => $cards,
    'pagination'  => $pagination,
) );
theoct_services_close_document();
