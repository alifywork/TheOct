<?php
/**
 * Single Service screenshot layout using exclusively ssd_* TheOct Fields.
 * Header/footer intentionally excluded.
 *
 * @package TheOct
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

while ( have_posts() ) {
    the_post();
    theoct_services_open_document( get_the_title() );
    get_template_part(
        'template-parts/single-service-detail',
        null,
        array( 'post_id' => get_the_ID() )
    );
    theoct_services_close_document();
}
