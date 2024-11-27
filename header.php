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
<!-- 将横幅相关的 JavaScript 移到单独的文件中 -->
<script>
		document.addEventListener('DOMContentLoaded', function() {
			// 横幅相关的代码
			const STICKY_BANNER_DISMISSED_COOKIE_NAME = document.getElementById('sticky-banner-link')?.textContent
				?.toLowerCase()
				?.replace(/ /g, '-')
				?.replace(/[^\w-]+/g, '') || 'sticky-banner-dismissed';

			const setAPlusBannerVisibility = (show) => {
				const stickyBannerElm = document.getElementById('sticky-banner');
				const headerElm = document.getElementById('Header');
				
				if (stickyBannerElm && headerElm) {
					if (show) {
						stickyBannerElm.classList.remove('hide');
						headerElm.style.height = '6rem';
					} else {
						stickyBannerElm.classList.add('hide');
						headerElm.style.height = '3rem';
					}
					return true;
				}
				return false;
			};

    const showAPlusBanner = () => setAPlusBannerVisibility(true);
    const hideAPlusBanner = () => setAPlusBannerVisibility(false);

    const handleDismissAPlusBanner = () => {
        const didDismissSuccessfully = hideAPlusBanner();
        if (didDismissSuccessfully) {
            const expirationDate = new Date(Date.now() + (7 * 24 * 60 * 60 * 1000));
            document.cookie = `${STICKY_BANNER_DISMISSED_COOKIE_NAME}=1; expires=${expirationDate}; path=/;`;
        }
    };

    // 立即执行函数初始化横幅状态
    (() => {
        const getCookie = (name) => {
            try {
                const cookieValue = `${name}=`;
                const cookies = document.cookie.split(';');
                
                for (let cookie of cookies) {
                    cookie = cookie.trim();
                    if (cookie.startsWith(cookieValue)) {
                        return cookie.substring(cookieValue.length);
                    }
                }
            } catch (error) {
                console.error('读取cookie时出错:', error);
            }
            return '';
        };

        const populateCookieName = () => {
            try {
                const bannerLink = document.getElementById('sticky-banner-link');
                return bannerLink.textContent
                    .toLowerCase()
                    .replace(/ /g, '-')
                    .replace(/[^\w-]+/g, '');
            } catch (error) {
                console.error('生成cookie名称时出错:', error);
                return 'sticky-banner-dismissed';
            }
        };

        // 等待 DOM 加载完成后执行
        document.addEventListener('DOMContentLoaded', () => {
            STICKY_BANNER_DISMISSED_COOKIE_NAME = populateCookieName();
            if (getCookie(STICKY_BANNER_DISMISSED_COOKIE_NAME)) {
                hideAPlusBanner();
            } else {
                showAPlusBanner();
            }
        });
    })();
    </script>
<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<header id="Header" class="site-header">
		<!-- 未验证用户横幅 -->
		<?php if (!is_user_verified()): ?>
		<div id="unverified-banner" class="banner banner--unverified">
			<div class="row align-middle">
				<div class="columns text-center">
					<i class="material-icons">warning</i>
					<span>请验证您的邮箱地址 </span>
					<a href="<?php echo get_verification_url(); ?>">立即验证</a>
				</div>
			</div>
		</div>
		<?php endif; ?>
<!-- 顶部黄色通知栏 -->
<div id="sticky-banner">
    <div class="sticky-banner__content">
        <i class="icon-info"></i>
        <a id="sticky-banner-link" href="#">
            横幅公告内容
        </a>
    </div>
    <button class="sticky-banner__close" onclick="handleDismissAPlusBanner()">
        <i class="icon-close"></i>
    </button>
</div>
		<!-- 主导航 -->
		<div id="large-screen-header" class="row align-middle bg-black">
			<!-- Logo -->
			<div class="columns align-self-middle shrink pr-0">
				<a href="<?php echo home_url(); ?>">
					<img src="<?php echo get_theme_file_uri('assets/images/logo.png'); ?>" alt="Logo">
				</a>
			</div>

			<!-- 导航菜单 -->
			<div class="columns align-self-middle">
				<nav class="menu horizontal">
					<?php wp_nav_menu(['theme_location' => 'header-menu']); ?>
				</nav>
			</div>

			<!-- 搜索框 -->
			<div class="columns align-self-middle flex-child-grow">
				<form class="search-form">
					<?php get_search_form(); ?>
				</form>
			</div>

			<!-- 用户区域 -->
			<?php if(is_user_logged_in()): ?>
				<!-- 登录后 -->
				<div class="columns align-self-middle shrink">
					<a href="#" class="row align-middle mr-0">
						<div class="columns align-self-middle shrink pr-0">
							<i class="material-icons">favorite_border</i>
						</div>
					</a>
				</div>
				
				<div class="dropdown-link columns align-self-middle shrink">
					<a href="#" class="row align-middle">
						<div class="dropdown-link-avatar columns shrink pr-0">
							<?php echo get_avatar(get_current_user_id(), 28); ?>
						</div>
						<div class="columns">
							<?php echo wp_get_current_user()->display_name; ?>
						</div>
						<div class="columns shrink pl-0 fs-xs">
							<i class="material-icons">arrow_drop_down</i>
						</div>
					</a>
				</div>
			<?php else: ?>
				<!-- 登录前 -->
				<div class="columns align-self-middle shrink">
					<a href="<?php echo wp_login_url(); ?>">登录</a>
				</div>
				<div class="columns align-self-middle shrink">
					<a href="<?php echo wp_registration_url(); ?>">注册</a>
				</div>
			<?php endif; ?>
		</div>

		<!-- 用户下拉菜单 -->
		<?php if(is_user_logged_in()): ?>
		<div id="dropdown-content" class="user-dropdown__content bg-gray-900">
			<?php get_template_part('template-parts/header/user-dropdown-menu'); ?>
		</div>
		<?php endif; ?>
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
