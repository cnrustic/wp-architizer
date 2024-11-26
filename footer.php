<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package architizer
 */

?>

<footer class="site-footer">
    <div class="footer-widgets">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-widget about-widget">
                    <?php if (has_custom_logo()): ?>
                        <?php the_custom_logo(); ?>
                    <?php else: ?>
                        <h3 class="site-title"><?php bloginfo('name'); ?></h3>
                    <?php endif; ?>
                    <p class="site-description"><?php bloginfo('description'); ?></p>
                    <div class="social-links">
                        <?php
                        $social_links = get_theme_mod('social_links', []);
                        foreach ($social_links as $platform => $url):
                            if (!empty($url)):
                        ?>
                            <a href="<?php echo esc_url($url); ?>" target="_blank">
                                <i class="fab fa-<?php echo esc_attr($platform); ?>"></i>
                            </a>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    </div>
                </div>

                <div class="footer-widget">
                    <h3>快速链接</h3>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'footer-1',
                        'container' => false,
                        'menu_class' => 'footer-menu'
                    ]);
                    ?>
                </div>

                <div class="footer-widget">
                    <h3>资源中心</h3>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'footer-2',
                        'container' => false,
                        'menu_class' => 'footer-menu'
                    ]);
                    ?>
                </div>

                <div class="footer-widget contact-widget">
                    <h3>联系我们</h3>
                    <?php if ($email = get_theme_mod('contact_email')): ?>
                        <p><i class="fas fa-envelope"></i> <?php echo $email; ?></p>
                    <?php endif; ?>
                    <?php if ($phone = get_theme_mod('contact_phone')): ?>
                        <p><i class="fas fa-phone"></i> <?php echo $phone; ?></p>
                    <?php endif; ?>
                    <?php if ($address = get_theme_mod('contact_address')): ?>
                        <p><i class="fas fa-map-marker-alt"></i> <?php echo $address; ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <div class="copyright">
                © <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.
            </div>
            <?php
            wp_nav_menu([
                'theme_location' => 'footer-bottom',
                'container' => false,
                'menu_class' => 'footer-bottom-menu',
                'depth' => 1
            ]);
            ?>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
