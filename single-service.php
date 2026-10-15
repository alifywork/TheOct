<?php
/**
 * Single Service landing page. No shared header/footer.
 *
 * @package TheOct
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
while ( have_posts() ) {
    the_post();
    $id = get_the_ID();
    theoct_services_open_document( get_the_title() );
    get_template_part( 'template-parts/services-layout', null, array(
        'post_id'    => $id,
        'prefix'     => 'ss_',
        'mode'       => 'single',
        'cards'      => theoct_services_single_cards( $id, 'ss_' ),
        'pagination' => '',
    ) );
    theoct_services_close_document();
}
