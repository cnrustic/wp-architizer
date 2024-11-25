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
	<header class="site-header">
		<div class="header-top">
			<div class="container">
				<div class="header-left">
					<a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo">
						<?php
						if (has_custom_logo()) {
							the_custom_logo();
						} else {
							echo esc_html(get_bloginfo('name'));
						}
						?>
					</a>
				</div>
				
				<div class="header-center">
					<nav class="main-navigation">
						<?php
						wp_nav_menu(array(
							'theme_location' => 'primary',
							'menu_class' => 'primary-menu',
							'container' => false,
						));
						?>
					</nav>
				</div>

				<div class="header-right">
					<div class="search-box">
						<button class="search-toggle">
							<span class="screen-reader-text">搜索</span>
							<i class="fas fa-search"></i>
						</button>
						<div class="search-dropdown">
							<?php get_search_form(); ?>
						</div>
					</div>

					<?php if (is_user_logged_in()) : ?>
						<div class="user-menu">
							<button class="user-toggle">
								<?php
								$current_user = wp_get_current_user();
								echo get_avatar($current_user->ID, 32);
								?>
							</button>
							<div class="user-dropdown">
								<ul>
									<li><a href="<?php echo esc_url(home_url('/profile')); ?>">个人中心</a></li>
									<li><a href="<?php echo esc_url(home_url('/favorites')); ?>">我的收藏</a></li>
									<li><a href="<?php echo wp_logout_url(home_url()); ?>">退出登录</a></li>
								</ul>
							</div>
						</div>
					<?php else : ?>
						<div class="auth-buttons">
							<a href="<?php echo esc_url(home_url('/login')); ?>" class="btn btn-login">登录</a>
							<a href="<?php echo esc_url(home_url('/register')); ?>" class="btn btn-register">注册</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="header-bottom">
			<div class="container">
				<nav class="category-navigation">
					<?php
					wp_nav_menu(array(
						'theme_location' => 'category',
						'menu_class' => 'category-menu',
						'container' => false,
					));
					?>
				</nav>
			</div>
		</div>
	</header>

	<!-- 搜索遮罩层 -->
	<div class="search-overlay"></div>
