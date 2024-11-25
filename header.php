<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package architizer
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<header id="masthead" class="site-header">
		<div class="header-container">
			<!-- Logo -->
			<div class="site-branding">
				<?php if (has_custom_logo()): ?>
					<?php the_custom_logo(); ?>
				<?php else: ?>
					<a href="<?php echo esc_url(home_url('/')); ?>" class="site-title">
						<?php bloginfo('name'); ?>
					</a>
				<?php endif; ?>
			</div>

			<!-- Main Navigation -->
			<nav id="site-navigation" class="main-navigation">
    			<?php
   	 			wp_nav_menu(array(
        			'theme_location' => 'menu-1',
        			'menu_id'        => 'primary-menu',
        			'container_class' => 'primary-menu-container',
        			'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
        			'fallback_cb'    => false,
    			));
    			?>
			</nav>

			<!-- User Actions -->
			<div class="user-actions">
				<button class="search-toggle">
					<svg width="24" height="24" viewBox="0 0 24 24">
						<path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
					</svg>
				</button>
				<?php if (is_user_logged_in()): ?>
					<a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="logout-link">退出</a>
				<?php else: ?>
					<a href="<?php echo esc_url(wp_login_url()); ?>" class="login-link">登录</a>
					<a href="<?php echo esc_url(wp_registration_url()); ?>" class="register-link">注册</a>
				<?php endif; ?>
			</div>
		</div>

		<!-- Search Form -->
		<div class="header-search">
			<div class="search-container">
				<?php get_search_form(); ?>
			</div>
		</div>
	</header>
