<?php
/**
 * The site footer.
 *
 * @package TheOct
 */
?>
    </div><!-- #content -->
    <footer id="colophon" class="site-footer">
        <div class="container footer-inner">
            <p>
                <?php
                printf(
                    /* translators: 1: current year, 2: site name. */
                    esc_html__( '© %1$s %2$s. All rights reserved.', 'theoct' ),
                    esc_html( gmdate( 'Y' ) ),
                    esc_html( get_bloginfo( 'name' ) )
                );
                ?>
            </p>
            <?php if ( has_nav_menu( 'footer' ) ) : ?>
                <nav class="footer-navigation" aria-label="<?php esc_attr_e( 'Footer navigation', 'theoct' ); ?>">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'footer',
                            'container'      => false,
                            'depth'          => 1,
                        )
                    );
                    ?>
                </nav>
            <?php endif; ?>
        </div>
    </footer>
</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>
