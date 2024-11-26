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
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<header class="site-header">
		<div class="header-top">
			<div class="container">
				<div class="header-top-left">
					<a href="<?php echo home_url('/submit-project'); ?>" class="submit-btn">
						<i class="fas fa-plus"></i> 提交项目
					</a>
				</div>
				<div class="header-top-right">
					<?php if (is_user_logged_in()): ?>
						<div class="user-menu">
							<?php 
							$current_user = wp_get_current_user();
							$avatar = get_avatar_url($current_user->ID);
							?>
							<button class="user-menu-trigger">
								<img src="<?php echo $avatar; ?>" alt="用户头像">
								<span><?php echo $current_user->display_name; ?></span>
							</button>
							<div class="user-dropdown">
								<a href="<?php echo get_author_posts_url($current_user->ID); ?>">我的主页</a>
								<a href="<?php echo home_url('/dashboard'); ?>">控制面板</a>
								<a href="<?php echo wp_logout_url(home_url()); ?>">退出登录</a>
							</div>
						</div>
					<?php else: ?>
						<a href="<?php echo wp_login_url(); ?>" class="login-btn">登录</a>
						<a href="<?php echo wp_registration_url(); ?>" class="register-btn">注册</a>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="header-main">
			<div class="container">
				<div class="site-branding">
					<?php if (has_custom_logo()): ?>
						<?php the_custom_logo(); ?>
					<?php else: ?>
						<a href="<?php echo home_url(); ?>" class="site-title">
							<?php bloginfo('name'); ?>
						</a>
					<?php endif; ?>
				</div>

				<nav class="main-navigation">
					<?php
					wp_nav_menu([
						'theme_location' => 'primary',
						'container' => false,
						'menu_class' => 'primary-menu',
						'fallback_cb' => false
					]);
					?>
				</nav>

				<div class="header-actions">
					<button class="search-trigger">
						<i class="fas fa-search"></i>
					</button>
					<button class="mobile-menu-trigger">
						<span></span>
						<span></span>
						<span></span>
					</button>
				</div>
			</div>
		</div>
	</header>

	<div class="mobile-menu">
		<div class="mobile-menu-header">
			<button class="close-menu">
				<i class="fas fa-times"></i>
			</button>
		</div>
		<?php
		wp_nav_menu([
			'theme_location' => 'mobile',
			'container' => false,
			'menu_class' => 'mobile-menu-items',
			'fallback_cb' => false
		]);
		?>
	</div>

	<!-- 搜索遮罩层 -->
	<div class="search-overlay"></div>
