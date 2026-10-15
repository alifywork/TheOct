<?php
/**
 * Services page sections shared by the Services archive and single Service.
 *
 * Markup deliberately includes NO site header, navigation or footer.
 *
 * @package TheOct
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$id         = absint( $args['post_id'] ?? 0 );
$prefix     = (string) ( $args['prefix'] ?? 'sa_' );
$mode       = (string) ( $args['mode'] ?? 'archive' );
$is_single  = 'single' === $mode;
$cards      = isset( $args['cards'] ) && is_array( $args['cards'] ) ? $args['cards'] : array();
$pagination = $args['pagination'] ?? '';

$read = static function ( $name, $default = '' ) use ( $id, $prefix ) {
    return theoct_services_meta( $id, $prefix . $name, $default );
};
$archive_title = $id ? get_the_title( $id ) : __( 'Services', 'theoct' );
if ( ! $is_single && ( 'Services Archive' === $archive_title || ! $archive_title ) ) {
    $archive_title = __( 'Services', 'theoct' );
}
$hero_title = (string) $read( 'hero_title', $archive_title );
$intro_fallback = $is_single
    ? theoct_services_meta( $id, 'service_intro', get_the_excerpt( $id ) )
    : ( $id && get_post_field( 'post_content', $id )
        ? wp_strip_all_tags( get_post_field( 'post_content', $id ) )
        : theoct_services_meta( $id, 'archive_subtitle', __( 'We design, build, and deploy AI-powered workflows that automate operations and connect core systems.', 'theoct' ) ) );
$hero_desc = (string) $read( 'hero_description', $intro_fallback );
$hero_cta = $read( 'hero_cta', array() );
$visual = $read( 'visual_image', $is_single
    ? theoct_services_meta( $id, 'service_hero_image', get_post_thumbnail_id( $id ) )
    : theoct_services_meta( $id, 'archive_banner_image', get_post_thumbnail_id( $id ) ) );
$section_label = $is_single ? __( 'Our approach', 'theoct' ) : __( 'Our deliverables', 'theoct' );

$defaults = array(
    array( 'group_title' => __( 'Data & Reporting', 'theoct' ), 'items' => array(
        array( 'item' => __( 'Internal workflows', 'theoct' ) ),
        array( 'item' => __( 'Approval processes', 'theoct' ) ),
        array( 'item' => __( 'Support routing', 'theoct' ) ),
        array( 'item' => __( 'Manual data processing', 'theoct' ) ),
    ) ),
    array( 'group_title' => __( 'Operations', 'theoct' ), 'items' => array(
        array( 'item' => __( 'Data pipelines', 'theoct' ) ),
        array( 'item' => __( 'Automated reporting', 'theoct' ) ),
        array( 'item' => __( 'Real-time dashboards', 'theoct' ) ),
        array( 'item' => __( 'Cross-system reporting', 'theoct' ) ),
    ) ),
    array( 'group_title' => __( 'AI Enablement', 'theoct' ), 'items' => array(
        array( 'item' => __( 'Cross-system reporting', 'theoct' ) ),
        array( 'item' => __( 'Document processing', 'theoct' ) ),
        array( 'item' => __( 'CRM / ERP integrations', 'theoct' ) ),
        array( 'item' => __( 'API-based automation', 'theoct' ) ),
    ) ),
);
$groups = theoct_services_rows( $read( 'deliverable_groups', $defaults ) );
if ( $is_single ) {
    $legacy = theoct_services_rows( theoct_services_meta( $id, 'service_benefits', array() ) );
    if ( $legacy && ! metadata_exists( 'post', $id, (string) get_option( 'theoct_fields_storage_prefix', 'theoct_' ) . $prefix . 'deliverable_groups' ) ) {
        $items = array();
        foreach ( $legacy as $benefit ) {
            if ( ! empty( $benefit['benefit'] ) ) {
                $items[] = array( 'item' => $benefit['benefit'] );
            }
        }
        $groups = array( array( 'group_title' => __( 'Benefits', 'theoct' ), 'items' => $items ) );
    }
}
$process = theoct_services_rows( $read( 'process_steps', array(
    array( 'step_label' => 'STEP 01', 'step_title' => __( 'Discovery & Success Criteria', 'theoct' ),
        'step_description' => __( 'We map your current workflows, identify bottlenecks, and define clear metrics for success.', 'theoct' ) ),
    array( 'step_label' => 'STEP 02', 'step_title' => __( 'Map Workflows + Data Access', 'theoct' ),
        'step_description' => __( 'We document every step, identify bottlenecks, and determine what data and systems need to connect.', 'theoct' ) ),
    array( 'step_label' => 'STEP 03', 'step_title' => __( 'Build + Test + Security Review', 'theoct' ),
        'step_description' => __( 'We build incrementally, test thoroughly, and review security at every stage.', 'theoct' ) ),
    array( 'step_label' => 'STEP 04', 'step_title' => __( 'Launch + Monitor + Iterate', 'theoct' ),
        'step_description' => __( 'Go live confidently, monitor performance, and improve based on real results.', 'theoct' ) ),
) ) );
if ( $is_single ) {
    $old_process = theoct_services_rows( theoct_services_meta( $id, 'service_process', array() ) );
    if ( $old_process && ! metadata_exists( 'post', $id, (string) get_option( 'theoct_fields_storage_prefix', 'theoct_' ) . $prefix . 'process_steps' ) ) {
        $process = array();
        foreach ( $old_process as $i => $step ) {
            $process[] = array(
                'step_label'       => sprintf( 'STEP %02d', $i + 1 ),
                'step_title'       => $step['step_title'] ?? '',
                'step_description' => $step['step_description'] ?? '',
            );
        }
    }
}
$project_bullets = theoct_services_rows( $read( 'project_bullets', array(
    array( 'item' => __( 'Automating internal workflows', 'theoct' ) ),
    array( 'item' => __( 'Automated reporting and dashboards', 'theoct' ) ),
    array( 'item' => __( 'Integrating CRM and accounting systems', 'theoct' ) ),
    array( 'item' => __( 'Document processing and data extraction', 'theoct' ) ),
    array( 'item' => __( 'AI assistants for internal business teams', 'theoct' ) ),
    array( 'item' => __( 'Cross-system workflow automation', 'theoct' ) ),
) ) );
$cta = $read( 'bottom_cta_link', $is_single ? theoct_services_meta( $id, 'service_cta_link', array() ) : array() );
?>
<main id="oct-content" class="oct-services" role="main">
    <section class="oct-hero">
        <div class="oct-container">
            <?php if ( $read( 'hero_eyebrow' ) ) : ?>
                <p class="oct-eyebrow"><?php echo esc_html( (string) $read( 'hero_eyebrow' ) ); ?></p>
            <?php endif; ?>
            <h1><?php echo esc_html( $hero_title ); ?></h1>
            <?php if ( '' !== trim( $hero_desc ) ) : ?>
                <p class="oct-hero-description"><?php echo esc_html( $hero_desc ); ?></p>
            <?php endif; ?>
            <?php theoct_services_button( $hero_cta, __( 'Book a discovery call', 'theoct' ) ); ?>
        </div>
    </section>

    <section class="oct-visual-band" aria-label="<?php esc_attr_e( 'Featured visual', 'theoct' ); ?>">
        <?php $image = theoct_services_media( $visual, 'full', 'oct-visual-image' ); ?>
        <?php if ( $image ) : ?>
            <?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WP image HTML. ?>
        <?php else : ?>
            <div class="oct-visual-art" aria-hidden="true"><span></span><span></span><span></span></div>
        <?php endif; ?>
    </section>

    <section class="oct-section oct-deliverables">
        <div class="oct-container">
            <p class="oct-eyebrow"><?php echo esc_html( (string) $read( 'deliverables_eyebrow', $section_label ) ); ?></p>
            <h2><?php echo esc_html( (string) $read( 'deliverables_title', __( 'What we help automate', 'theoct' ) ) ); ?></h2>
            <?php if ( $is_single && get_post_field( 'post_content', $id ) ) : ?>
                <div class="oct-section-summary oct-rich"><?php echo wp_kses_post( apply_filters( 'the_content', get_post_field( 'post_content', $id ) ) ); ?></div>
            <?php endif; ?>
            <div class="oct-deliverable-grid">
                <?php foreach ( $groups as $group ) : ?>
                    <div class="oct-deliverable-col">
                        <h3><?php echo esc_html( (string) ( $group['group_title'] ?? '' ) ); ?></h3>
                        <ul>
                            <?php foreach ( theoct_services_rows( $group['items'] ?? array() ) as $line ) : ?>
                                <?php if ( '' !== trim( (string) ( $line['item'] ?? '' ) ) ) : ?>
                                    <li><?php echo esc_html( (string) $line['item'] ); ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="oct-section oct-core" id="services-list">
        <div class="oct-container">
            <p class="oct-eyebrow"><?php echo esc_html( (string) $read( 'cards_eyebrow', __( 'What we do', 'theoct' ) ) ); ?></p>
            <h2><?php echo esc_html( (string) $read( 'cards_title', $is_single ? __( 'What this service includes', 'theoct' ) : __( 'Our core automation services', 'theoct' ) ) ); ?></h2>
            <p class="oct-section-summary"><?php echo esc_html( (string) $read( 'cards_description', __( 'From workflow automation to system integration, we deliver outcomes that move your business forward.', 'theoct' ) ) ); ?></p>
            <?php if ( $cards ) : ?>
                <div class="oct-card-grid">
                    <?php foreach ( $cards as $card ) : ?>
                        <article class="oct-card">
                            <div class="oct-card-media">
                                <?php $card_img = theoct_services_media( $card['image'] ?? 0, 'medium_large', 'oct-card-image' ); ?>
                                <?php if ( $card_img ) : ?>
                                    <?php echo $card_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <?php else : ?>
                                    <span class="oct-card-symbol" aria-hidden="true">✳</span>
                                <?php endif; ?>
                            </div>
                            <div class="oct-card-body">
                                <h3><?php echo esc_html( (string) ( $card['title'] ?? '' ) ); ?></h3>
                                <p><?php echo esc_html( (string) ( $card['description'] ?? '' ) ); ?></p>
                            </div>
                            <?php if ( ! empty( $card['url'] ) ) : ?>
                                <a class="oct-card-link" href="<?php echo esc_url( (string) $card['url'] ); ?>">
                                    <span class="screen-reader-text"><?php echo esc_html( (string) ( $card['title'] ?? '' ) ); ?></span>
                                    <span aria-hidden="true">→</span>
                                </a>
                            <?php else : ?>
                                <span class="oct-card-link oct-card-link-inert" aria-hidden="true">→</span>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
                <?php if ( ! empty( $pagination ) ) : ?>
                    <nav class="oct-pagination" aria-label="<?php esc_attr_e( 'Services pagination', 'theoct' ); ?>">
                        <?php echo wp_kses_post( $pagination ); ?>
                    </nav>
                <?php endif; ?>
            <?php else : ?>
                <p class="oct-empty"><?php echo esc_html( $is_single
                    ? __( 'Add capability cards in the Service fields to populate this section.', 'theoct' )
                    : __( 'Published Services will appear here as cards.', 'theoct' ) ); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <section class="oct-section oct-split">
        <div class="oct-container oct-split-grid">
            <div class="oct-industry-panel">
                <?php $industry_img = theoct_services_media( $read( 'industry_image', 0 ), 'large', 'oct-industry-image' ); ?>
                <?php if ( $industry_img ) : ?>
                    <?php echo $industry_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php else : ?>
                    <div class="oct-cubes" aria-hidden="true"><i></i><i></i><i></i></div>
                <?php endif; ?>
                <div class="oct-industry-copy">
                    <h2><?php echo esc_html( (string) $read( 'industry_title', __( 'Built for real business industries', 'theoct' ) ) ); ?></h2>
                    <p><?php echo esc_html( (string) $read( 'industry_description', __( 'Built around real industry workflows. Explore each industry.', 'theoct' ) ) ); ?></p>
                    <?php
                    $industry_link = $read( 'industry_link', array() );
                    if ( ! is_array( $industry_link ) || empty( $industry_link['url'] ) ) {
                        $industry_archive = get_post_type_archive_link( 'industry' );
                        $industry_link = array( 'title' => __( 'View industries', 'theoct' ), 'url' => $industry_archive ?: '' );
                    }
                    theoct_services_button( $industry_link, __( 'View industries', 'theoct' ), 'oct-button oct-button-white' );
                    ?>
                </div>
            </div>
            <div class="oct-project-panel">
                <h2><?php echo esc_html( (string) $read( 'project_title', __( 'Typical projects', 'theoct' ) ) ); ?></h2>
                <p><?php echo esc_html( (string) $read( 'project_description', __( 'From workflow automation to system integration, we deliver outcomes that move your business forward.', 'theoct' ) ) ); ?></p>
                <ul>
                    <?php foreach ( $project_bullets as $row ) : ?>
                        <?php if ( ! empty( $row['item'] ) ) : ?>
                            <li><?php echo esc_html( (string) $row['item'] ); ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </section>

    <section class="oct-section oct-process">
        <div class="oct-container">
            <p class="oct-eyebrow"><?php echo esc_html( (string) $read( 'process_eyebrow', __( 'How we work', 'theoct' ) ) ); ?></p>
            <h2><?php echo esc_html( (string) $read( 'process_title', __( 'A process built for results', 'theoct' ) ) ); ?></h2>
            <p class="oct-section-summary"><?php echo esc_html( (string) $read( 'process_description', __( 'Clear milestones, constant communication, and zero hand-waving. Here’s how we get from idea to impact.', 'theoct' ) ) ); ?></p>
            <div class="oct-process-grid">
                <?php foreach ( $process as $index => $step ) : ?>
                    <article class="oct-step">
                        <span class="oct-step-number"><?php echo esc_html( (string) ( $step['step_label'] ?? sprintf( 'STEP %02d', $index + 1 ) ) ); ?></span>
                        <h3><?php echo esc_html( (string) ( $step['step_title'] ?? '' ) ); ?></h3>
                        <p><?php echo esc_html( (string) ( $step['step_description'] ?? '' ) ); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="oct-final-cta" id="contact-cta">
        <div class="oct-container oct-final-inner">
            <div class="oct-final-art" aria-hidden="true">
                <?php $cta_image = theoct_services_media( $read( 'bottom_cta_image', 0 ), 'large', 'oct-final-image' ); ?>
                <?php if ( $cta_image ) : ?>
                    <?php echo $cta_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php else : ?>
                    <span></span>
                <?php endif; ?>
            </div>
            <div class="oct-final-copy">
                <h2><?php echo esc_html( (string) $read( 'bottom_cta_title', __( 'Automations that ship & keep running.', 'theoct' ) ) ); ?></h2>
                <p><?php echo esc_html( (string) $read( 'bottom_cta_description', __( 'We design and deploy AI workflows and systems that help teams work smarter, not harder.', 'theoct' ) ) ); ?></p>
                <div class="oct-final-actions">
                    <?php theoct_services_button( $cta, __( 'Book a discovery call', 'theoct' ), 'oct-button oct-button-white' ); ?>
                    <?php $phone = (string) $read( 'bottom_cta_phone' ); ?>
                    <?php if ( $phone ) : ?>
                        <a class="oct-pill" href="<?php echo esc_url( 'tel:' . preg_replace( '/[^+0-9]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
                    <?php endif; ?>
                    <?php $email = sanitize_email( (string) $read( 'bottom_cta_email' ) ); ?>
                    <?php if ( $email ) : ?>
                        <a class="oct-pill" href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>
