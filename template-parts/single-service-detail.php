<?php
/**
 * Single Service — screenshot-detail layout.
 *
 * Every custom value comes exclusively from ssd_* fields in TheOct Fields.
 * No header/navigation/footer template is called.
 *
 * @package TheOct
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$id = absint( $args['post_id'] ?? get_the_ID() );
$read = static function ( $field, $default = '' ) use ( $id ) {
    return theoct_services_meta( $id, 'ssd_' . $field, $default );
};
$rows = static function ( $field ) use ( $read ) {
    return theoct_services_rows( $read( $field, array() ) );
};
$image = static function ( $attachment, $size = 'large', $class = '' ) {
    $markup = theoct_services_media( $attachment, $size, $class );
    if ( $markup ) {
        echo $markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress attachment HTML.
    }
};
$eyebrow = static function ( $value ) {
    if ( is_scalar( $value ) && trim( (string) $value ) ) {
        echo '<p class="oct-detail-kicker">' . esc_html( (string) $value ) . '</p>';
    }
};
$show_text = static function ( $value, $class = '' ) {
    if ( is_scalar( $value ) && '' !== trim( (string) $value ) ) {
        echo '<p class="' . esc_attr( $class ) . '">' . esc_html( (string) $value ) . '</p>';
    }
};
$faq_posts = (array) $read( 'faq_related_posts', array() );
?>
<main id="oct-content" class="oct-detail" role="main">
    <section class="oct-detail-hero">
        <div class="oct-detail-container">
            <?php
            $breadcrumb = $read( 'breadcrumb_link', array() );
            if ( ! is_array( $breadcrumb ) || empty( $breadcrumb['url'] ) ) {
                $breadcrumb = array(
                    'url' => get_post_type_archive_link( 'service' ),
                    'title' => $read( 'breadcrumb_label', __( 'Services', 'theoct' ) ),
                );
            }
            if ( ! empty( $breadcrumb['url'] ) ) :
                ?>
                <a class="oct-detail-back" href="<?php echo esc_url( $breadcrumb['url'] ); ?>">← <?php echo esc_html( $breadcrumb['title'] ?: __( 'Services', 'theoct' ) ); ?></a>
            <?php endif; ?>
            <?php $eyebrow( $read( 'hero_eyebrow' ) ); ?>
            <h1><?php echo esc_html( (string) ( $read( 'hero_heading' ) ?: get_the_title( $id ) ) ); ?></h1>
            <?php $show_text( $read( 'hero_description' ), 'oct-detail-lead' ); ?>
            <div class="oct-detail-buttons">
                <?php
                theoct_services_button( $read( 'hero_primary_cta', array() ), __( 'Book a discovery call', 'theoct' ) );
                $secondary = $read( 'hero_secondary_cta', array() );
                if ( is_array( $secondary ) && ! empty( $secondary['url'] ) ) {
                    theoct_services_button( $secondary, __( 'See example workflow', 'theoct' ), 'oct-detail-secondary' );
                }
                ?>
            </div>
            <?php $navigation = $rows( 'hero_section_nav' ); ?>
            <?php if ( $navigation ) : ?>
                <nav class="oct-detail-section-links" aria-label="<?php esc_attr_e( 'Page sections', 'theoct' ); ?>">
                    <?php foreach ( $navigation as $item ) :
                        $anchor = sanitize_html_class( (string) ( $item['anchor_id'] ?? '' ) );
                        if ( ! $anchor || empty( $item['label'] ) ) { continue; }
                        ?>
                        <a href="#<?php echo esc_attr( $anchor ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>
        </div>
    </section>

    <section class="oct-detail-visual" aria-label="<?php esc_attr_e( 'Service results', 'theoct' ); ?>">
        <?php
        $visual = $read( 'visual_background' );
        if ( $visual ) {
            $image( $visual, 'full', 'oct-detail-visual-photo' );
        } else {
            echo '<div class="oct-detail-visual-art" aria-hidden="true"></div>';
        }
        $mobile_visual = $read( 'visual_mobile_background' );
        if ( $mobile_visual ) {
            $image( $mobile_visual, 'large', 'oct-detail-visual-mobile' );
        }
        ?>
        <div class="oct-detail-container oct-detail-visual-inner">
            <?php
            $metric = $read( 'metric_primary_value' );
            $more_metrics = $rows( 'hero_metrics' );
            if ( $metric || $more_metrics ) :
            ?>
                <div class="oct-detail-metrics">
                    <?php $eyebrow( $read( 'metric_eyebrow', __( "What's included", 'theoct' ) ) ); ?>
                    <?php if ( $metric ) : ?>
                        <div class="oct-detail-main-metric">
                            <strong><?php echo esc_html( (string) $metric ); ?></strong>
                            <span><?php echo esc_html( (string) $read( 'metric_primary_label' ) ); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php $show_text( $read( 'metric_supporting_note' ), 'oct-detail-metric-note' ); ?>
                    <?php foreach ( $more_metrics as $item ) : ?>
                        <p class="oct-detail-small-metric"><strong><?php echo esc_html( (string) ( $item['value'] ?? '' ) ); ?></strong> <?php echo esc_html( (string) ( $item['label'] ?? '' ) ); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="oct-detail-section oct-detail-problem" id="expertise">
        <div class="oct-detail-container oct-detail-two-col">
            <div>
                <?php $eyebrow( $read( 'problem_eyebrow', __( "Why it's needed", 'theoct' ) ) ); ?>
                <h2><?php echo esc_html( (string) $read( 'problem_heading', __( 'The problem with messy internal tools', 'theoct' ) ) ); ?></h2>
                <div class="oct-detail-rich"><?php echo wp_kses_post( (string) $read( 'problem_description' ) ); ?></div>
            </div>
            <aside class="oct-detail-standards">
                <?php $eyebrow( $read( 'standards_eyebrow', __( 'Our standards', 'theoct' ) ) ); ?>
                <?php if ( $read( 'standards_heading' ) ) : ?><h3><?php echo esc_html( (string) $read( 'standards_heading' ) ); ?></h3><?php endif; ?>
                <ul>
                    <?php foreach ( $rows( 'standards_items' ) as $item ) : ?>
                        <li><span class="oct-detail-check" aria-hidden="true">✧</span>
                            <span><strong><?php echo esc_html( (string) ( $item['label'] ?? '' ) ); ?></strong>
                            <?php $show_text( $item['description'] ?? '', 'oct-detail-small' ); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>
        </div>
    </section>

    <section class="oct-detail-section oct-detail-deliverables" id="deliverables">
        <div class="oct-detail-container">
            <?php $eyebrow( $read( 'included_eyebrow', __( 'Deliverables', 'theoct' ) ) ); ?>
            <h2><?php echo esc_html( (string) $read( 'included_heading', __( "What's included", 'theoct' ) ) ); ?></h2>
            <?php $show_text( $read( 'included_description' ), 'oct-detail-lead-small' ); ?>
            <div class="oct-detail-deliverable-grid">
                <?php foreach ( $rows( 'deliverable_columns' ) as $column ) : ?>
                    <article class="oct-detail-deliverable">
                        <span class="oct-detail-step-label"><?php echo esc_html( (string) ( $column['step_number'] ?? '' ) ); ?></span>
                        <h3><?php echo esc_html( (string) ( $column['column_title'] ?? '' ) ); ?></h3>
                        <?php $show_text( $column['summary'] ?? '', 'oct-detail-small' ); ?>
                        <?php $bullets = theoct_services_rows( $column['bullet_points'] ?? array() ); ?>
                        <?php if ( $bullets ) : ?>
                            <ul class="oct-detail-checklist">
                                <?php foreach ( $bullets as $bullet ) : ?>
                                    <li><?php echo esc_html( (string) ( $bullet['bullet'] ?? '' ) ); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="oct-detail-section oct-detail-process" id="process">
        <div class="oct-detail-container">
            <?php $eyebrow( $read( 'process_eyebrow', __( 'How it works', 'theoct' ) ) ); ?>
            <h2><?php echo esc_html( (string) $read( 'process_heading', __( 'A process built for results', 'theoct' ) ) ); ?></h2>
            <?php $show_text( $read( 'process_intro' ), 'oct-detail-lead-small' ); ?>
            <div class="oct-detail-process-grid">
                <?php foreach ( $rows( 'process_steps' ) as $step ) : ?>
                    <article class="oct-detail-process-card<?php echo ! empty( $step['highlight'] ) ? ' is-featured' : ''; ?>">
                        <span class="oct-detail-step-label"><?php echo esc_html( (string) ( $step['step_number'] ?? '' ) ); ?></span>
                        <h3><?php echo esc_html( (string) ( $step['title'] ?? '' ) ); ?></h3>
                        <?php $show_text( $step['description'] ?? '', 'oct-detail-small' ); ?>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php $stack = $rows( 'stack_logos' ); ?>
            <?php if ( $stack ) : ?>
                <div class="oct-detail-stack">
                    <?php $eyebrow( $read( 'stack_eyebrow', __( 'Our stack', 'theoct' ) ) ); ?>
                    <?php if ( $read( 'stack_heading' ) ) : ?><h3><?php echo esc_html( (string) $read( 'stack_heading' ) ); ?></h3><?php endif; ?>
                    <div class="oct-detail-stack-logos">
                        <?php foreach ( $stack as $tool ) :
                            $logo = theoct_services_media( $tool['logo'] ?? 0, 'medium', 'oct-detail-stack-logo' );
                            $link = esc_url( (string) ( $tool['url'] ?? '' ) );
                            ?>
                            <div class="oct-detail-stack-item">
                                <?php if ( $link ) : ?><a href="<?php echo $link; ?>" target="_blank" rel="noopener noreferrer"><?php endif; ?>
                                <?php if ( $logo ) : echo $logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                else : ?><span><?php echo esc_html( (string) ( $tool['name'] ?? '' ) ); ?></span><?php endif; ?>
                                <?php if ( $link ) : ?></a><?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="oct-detail-section oct-detail-examples" id="examples">
        <?php $background = theoct_services_media( $read( 'examples_background' ), 'full', 'oct-detail-examples-image' ); ?>
        <?php if ( $background ) : echo $background; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        endif; ?>
        <div class="oct-detail-container">
            <?php $eyebrow( $read( 'examples_eyebrow', __( 'AI systems that work', 'theoct' ) ) ); ?>
            <h2><?php echo esc_html( (string) $read( 'examples_heading', __( "See what's possible", 'theoct' ) ) ); ?></h2>
            <?php $show_text( $read( 'examples_description' ), 'oct-detail-lead-small' ); ?>
            <div class="oct-detail-example-grid">
                <?php foreach ( $rows( 'example_cards' ) as $example ) : ?>
                    <article class="oct-detail-example">
                        <div class="oct-detail-pills">
                            <?php foreach ( array( 'card_category', 'card_type' ) as $badge ) : ?>
                                <?php if ( ! empty( $example[ $badge ] ) ) : ?>
                                    <span><?php echo esc_html( (string) $example[ $badge ] ); ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <?php $image( $example['card_image'] ?? 0, 'medium', 'oct-detail-example-photo' ); ?>
                        <h3><?php echo esc_html( (string) ( $example['card_title'] ?? '' ) ); ?></h3>
                        <?php $show_text( $example['short_description'] ?? '', 'oct-detail-small' ); ?>
                        <dl>
                            <?php foreach ( array( 'input' => __( 'Input', 'theoct' ), 'automation' => __( 'Automation', 'theoct' ), 'output' => __( 'Output', 'theoct' ) ) as $key => $label ) : ?>
                                <?php if ( ! empty( $example[ $key ] ) ) : ?>
                                    <div><dt><?php echo esc_html( $label ); ?></dt><dd><?php echo esc_html( (string) $example[ $key ] ); ?></dd></div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </dl>
                        <?php if ( ! empty( $example['card_link'] ) ) : ?>
                            <?php theoct_services_button( $example['card_link'], __( 'Learn more', 'theoct' ), 'oct-detail-link' ); ?>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php $testimonials = array_filter( $rows( 'testimonials' ), static function ( $row ) { return ! empty( $row['consent_verified'] ) && ! empty( $row['quote'] ); } ); ?>
    <?php if ( $testimonials ) : ?>
        <section class="oct-detail-section oct-detail-testimonials" id="testimonials">
            <div class="oct-detail-container">
                <?php $eyebrow( $read( 'testimonials_eyebrow' ) ); ?>
                <h2><?php echo esc_html( (string) $read( 'testimonials_heading', __( 'What clients say', 'theoct' ) ) ); ?></h2>
                <div class="oct-detail-testimonial-grid">
                    <?php foreach ( $testimonials as $review ) : ?>
                        <figure class="oct-detail-quote">
                            <div class="oct-detail-person">
                                <?php $image( $review['client_photo'] ?? 0, 'thumbnail', 'oct-detail-avatar' ); ?>
                                <span><strong><?php echo esc_html( (string) ( $review['client_name'] ?? '' ) ); ?></strong>
                                <small><?php echo esc_html( (string) ( $review['client_role'] ?? '' ) ); ?></small></span>
                            </div>
                            <blockquote><?php echo esc_html( (string) $review['quote'] ); ?></blockquote>
                        </figure>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="oct-detail-section oct-detail-faq" id="faq">
        <div class="oct-detail-container oct-detail-two-col">
            <div>
                <?php $eyebrow( $read( 'faq_eyebrow', 'FAQ' ) ); ?>
                <h2><?php echo esc_html( (string) $read( 'faq_heading', __( 'Common questions', 'theoct' ) ) ); ?></h2>
                <?php $show_text( $read( 'faq_description' ), 'oct-detail-lead-small' ); ?>
                <?php
                $faq_link = $read( 'faq_bottom_cta', array() );
                if ( is_array( $faq_link ) && ! empty( $faq_link['url'] ) ) {
                    theoct_services_button( $faq_link, __( 'Book a discovery call', 'theoct' ) );
                }
                ?>
            </div>
            <div class="oct-detail-faq-list">
                <?php foreach ( $rows( 'faq_items' ) as $faq ) : ?>
                    <?php if ( ! empty( $faq['question'] ) ) : ?>
                        <details <?php echo ! empty( $faq['open_by_default'] ) ? 'open' : ''; ?>>
                            <summary><?php echo esc_html( (string) $faq['question'] ); ?></summary>
                            <div class="oct-detail-rich"><?php echo wp_kses_post( (string) ( $faq['answer'] ?? '' ) ); ?></div>
                        </details>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php foreach ( $faq_posts as $faq_id ) :
                    $faq_id = absint( $faq_id );
                    if ( 'faq' !== get_post_type( $faq_id ) || 'publish' !== get_post_status( $faq_id ) ) {
                        continue;
                    }
                    ?>
                    <details>
                        <summary><?php echo esc_html( get_the_title( $faq_id ) ); ?></summary>
                        <div class="oct-detail-rich"><?php echo wp_kses_post( apply_filters( 'the_content', get_post_field( 'post_content', $faq_id ) ) ); ?></div>
                    </details>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="oct-detail-contact" id="contact">
        <?php $image( $read( 'contact_background' ), 'full', 'oct-detail-contact-bg' ); ?>
        <div class="oct-detail-container oct-detail-contact-grid">
            <div class="oct-detail-contact-copy">
                <?php $eyebrow( $read( 'contact_eyebrow', __( 'Get started', 'theoct' ) ) ); ?>
                <h2><?php echo esc_html( (string) $read( 'contact_heading', __( "Let's talk about your workflows", 'theoct' ) ) ); ?></h2>
                <?php $show_text( $read( 'contact_description' ), 'oct-detail-lead-small' ); ?>
                <ul class="oct-detail-checklist">
                    <?php foreach ( $rows( 'contact_benefits' ) as $line ) : ?>
                        <li><?php echo esc_html( (string) ( $line['item'] ?? '' ) ); ?></li>
                    <?php endforeach; ?>
                </ul>
                <div class="oct-detail-contact-options">
                    <?php if ( $read( 'contact_phone' ) ) : ?>
                        <a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', (string) $read( 'contact_phone' ) ) ); ?>"><?php echo esc_html( (string) $read( 'contact_phone' ) ); ?></a>
                    <?php endif; ?>
                    <?php $email = sanitize_email( (string) $read( 'contact_email' ) ); ?>
                    <?php if ( $email ) : ?><a href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a><?php endif; ?>
                </div>
            </div>
            <?php if ( $read( 'contact_form_enabled', '1' ) ) : ?>
                <div class="oct-detail-form-card">
                    <h3><?php echo esc_html( (string) $read( 'form_title', __( 'Schedule a discovery call', 'theoct' ) ) ); ?></h3>
                    <?php $show_text( $read( 'form_intro' ), 'oct-detail-small' ); ?>
                    <div class="oct-detail-form-preview" aria-label="<?php esc_attr_e( 'Contact form preview (not accepting submissions)', 'theoct' ); ?>">
                        <?php foreach ( array(
                            'form_name_label', 'form_phone_label', 'form_email_label',
                            'form_company_label',
                        ) as $control ) :
                            $label = $read( $control );
                            if ( ! $label ) { continue; }
                            ?>
                            <label><?php echo esc_html( (string) $label ); ?><input type="text" placeholder="<?php echo esc_attr( (string) $label ); ?>" disabled></label>
                        <?php endforeach; ?>
                        <?php $service_options = $rows( 'form_service_options' ); ?>
                        <?php if ( $read( 'form_service_label' ) ) : ?>
                            <label><?php echo esc_html( (string) $read( 'form_service_label' ) ); ?>
                                <select disabled>
                                    <option value=""><?php esc_html_e( 'Select', 'theoct' ); ?></option>
                                    <?php foreach ( $service_options as $opt ) : ?>
                                        <option><?php echo esc_html( (string) ( $opt['label'] ?? '' ) ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                        <?php endif; ?>
                        <?php if ( $read( 'form_message_label' ) ) : ?>
                            <label class="oct-detail-form-wide"><?php echo esc_html( (string) $read( 'form_message_label' ) ); ?><textarea rows="4" disabled></textarea></label>
                        <?php endif; ?>
                        <div class="oct-detail-form-wide">
                            <button type="button" class="oct-button" disabled><?php echo esc_html( (string) $read( 'form_submit_label', __( 'Send message', 'theoct' ) ) ); ?></button>
                            <small><?php esc_html_e( 'Form preview — submissions are not enabled yet.', 'theoct' ); ?></small>
                            <?php $show_text( $read( 'form_privacy_note' ), 'oct-detail-small' ); ?>
                            <?php $privacy = $read( 'form_privacy_link', array() ); ?>
                            <?php if ( is_array( $privacy ) && ! empty( $privacy['url'] ) ) : ?>
                                <a href="<?php echo esc_url( $privacy['url'] ); ?>"><?php echo esc_html( (string) ( $privacy['title'] ?? __( 'Privacy policy', 'theoct' ) ) ); ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
