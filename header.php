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
	<header id="Header" class="site-header">
		<!-- 顶部黄色通知栏 -->
		<div id="sticky-banner" class="bg-yellow-500">
			<div class="row align-middle text-center">
				<div class="column align-middle">
					<a href="#" class="black">
						展示你的作品，参加第13届 A+ 奖项！12月6日前报名 →
					</a>
				</div>
				<div class="shrink cursor-pointer pl-0 text-right">
					<i class="fas fa-times"></i>
				</div>
			</div>
		</div>

		<!-- 桌面端导航 -->
		<div id="large-screen-header" class="bg-black hide-for-small-only">
			<div class="row align-justify">
				<!-- Logo -->
				<div class="shrink align-self-middle">
					<?php the_custom_logo(); ?>
				</div>
				
				<!-- 主导航 -->
				<div class="column align-self-middle">
					<?php
					wp_nav_menu(array(
						'theme_location' => 'primary',
						'menu_class'     => 'main-nav',
						'container'      => false,
					));
					?>
				</div>
				
				<!-- 右侧搜索和用户菜单 -->
				<div class="shrink align-self-middle pr-0">
					<div class="header-actions">
						<a href="#" class="search-trigger">
							<i class="fas fa-search"></i>
						</a>
						<?php if (is_user_logged_in()): ?>
							<a href="<?php echo wp_logout_url(home_url()); ?>">退出</a>
						<?php else: ?>
							<a href="<?php echo wp_login_url(); ?>">登录</a>
						<?php endif; ?>
					</div>
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
