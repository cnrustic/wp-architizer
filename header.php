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
<body <?php body_class(get_theme_mod('show_sticky_banner', true) ? 'has-banner' : ''); ?>>
	<?php wp_body_open(); ?>
	<header id="Header" class="site-header">
		<?php 
		// 获取横幅设置
		$show_sticky_banner = should_show_banner();

		// 调试输出
		if (WP_DEBUG) {
			error_log('Show banner check result: ' . ($show_sticky_banner ? 'yes' : 'no'));
		}

		if ($show_sticky_banner): 
		?>
		<div id="sticky-banner" class="sticky-banner">
			<div class="sticky-banner__content">
				<span class="banner-text">
					<?php echo esc_html(get_theme_mod('sticky_banner_text', '欢迎来到 Architizer！')); ?>
				</span>
				<a href="<?php echo esc_url(get_theme_mod('sticky_banner_link', '#')); ?>" class="banner-link">
					<?php echo esc_html(get_theme_mod('sticky_banner_cta', '了解更多')); ?> →
				</a>
			</div>
			<button class="sticky-banner__close" aria-label="关闭横幅">
				<i class="fas fa-times"></i>
			</button>
		</div>
		<?php endif; ?>

		<div class="site-header__nav">
			<!-- Logo -->
			<div class="logo-area">
				<a href="<?php echo esc_url(home_url('/')); ?>" class="mobile-logo">
					<img src="<?php echo esc_url(get_theme_file_uri('assets/images/logo-mobile.png')); ?>" 
						 alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
						 width="32" 
						 height="32">
				</a>
				<a href="<?php echo esc_url(home_url('/')); ?>" class="desktop-logo">
					<img src="<?php echo esc_url(get_theme_file_uri('assets/images/logo.png')); ?>" 
						 alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
				</a>
			</div>

			<!-- 移动端控制按钮组 -->
			<div class="mobile-controls">
				<button class="mobile-search-toggle">
					<i class="material-icons">search</i>
				</button>
				<button class="mobile-menu-toggle">
					<i class="material-icons">menu</i>
				</button>
			</div>

			<!-- 导航菜单 - 桌面端 -->
			<nav class="menu-area desktop-menu">
				<?php
				wp_nav_menu(array(
					'theme_location' => 'header-menu',
					'container' => false,
					'menu_class' => 'menu horizontal',
					'items_wrap' => '<ul class="%2$s">%3$s</ul>'
				));
				?>
			</nav>

			<!-- 搜索框 -->
			<div class="search-area">
				<form class="search-form" action="<?php echo esc_url(home_url('/')); ?>" method="get">
					<i class="material-icons gray-300">search</i>
					<input type="text" name="s" class="gray-300 search-input" 
						   placeholder="Search for projects, products, firms..." 
						   value="<?php echo get_search_query(); ?>">
				</form>
			</div>

			<!-- 登录注册区域 -->
			<div class="auth-area">
				<?php if (is_user_logged_in()): ?>
					<div class="user-menu">
						<button class="user-toggle">
							<i class="material-icons">person</i>
							<?php echo wp_get_current_user()->display_name; ?>
						</button>
					</div>
				<?php else: ?>
					<a href="<?php echo wp_login_url(); ?>" class="desktop-only">登录</a>
					<a href="<?php echo wp_registration_url(); ?>" class="desktop-only">注册</a>
				<?php endif; ?>
			</div>
		</div>

		<!-- 移动端菜单 -->
		<div class="mobile-menu">
			<div class="mobile-menu-header">
				<button class="mobile-menu-close">
					<i class="material-icons">close</i>
				</button>
			</div>
			<?php
			wp_nav_menu(array(
				'theme_location' => 'mobile',
				'container' => false,
				'menu_class' => 'mobile-menu-items',
				'fallback_cb' => false
			));
			?>
			<!-- 移动端登录注册按钮 -->
			<?php if (!is_user_logged_in()): ?>
			<div class="mobile-auth">
				<a href="<?php echo wp_login_url(); ?>" class="mobile-auth-btn">登录</a>
				<a href="<?php echo wp_registration_url(); ?>" class="mobile-auth-btn">注册</a>
			</div>
			<?php endif; ?>
		</div>
	</header>

	<!-- 搜索遮罩层 -->
	<div class="search-overlay"></div>
