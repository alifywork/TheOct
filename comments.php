<?php
/**
 * Comments and comment form template.
 *
 * @package TheOct
 */

if ( post_password_required() ) {
    return;
}
?>
<div id="comments" class="comments-area">
    <?php if ( have_comments() ) : ?>
        <h2 class="comments-title">
            <?php
            $theoct_comment_count = get_comments_number();
            printf(
                /* translators: %s: number of comments. */
                esc_html( _n( '%s comment', '%s comments', $theoct_comment_count, 'theoct' ) ),
                esc_html( number_format_i18n( $theoct_comment_count ) )
            );
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments(
                array(
                    'style'       => 'ol',
                    'short_ping'  => true,
                    'avatar_size' => 48,
                )
            );
            ?>
        </ol>

        <?php the_comments_pagination(); ?>

        <?php if ( ! comments_open() ) : ?>
            <p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'theoct' ); ?></p>
        <?php endif; ?>
    <?php endif; ?>

    <?php comment_form(); ?>
</div>
