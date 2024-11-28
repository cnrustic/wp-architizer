/**
 * Header functionality
 */
(function($) {
    'use strict';

    class Header {
        constructor() {
            // DOM 元素
            this.$header = $('.site-header');
            this.$searchToggle = $('.search-toggle');
            this.$searchDropdown = $('.search-dropdown');
            this.$searchOverlay = $('.search-overlay');
            this.$searchInput = $('.search-input');
            this.$userToggle = $('.user-toggle');
            this.$userDropdown = $('.user-dropdown');
            this.$mobileMenuToggle = $('.mobile-menu-toggle');
            this.$mobileMenu = $('.mobile-menu');
            this.$stickyBanner = $('#sticky-banner');
            this.$stickyBannerClose = $('.sticky-banner__close');
            this.$dropdownContent = $('.dropdown-content');
            this.$searchToggleBtn = $('.search-toggle-btn');
            this.$searchClose = $('.search-close');
            this.$mobileSearchExpanded = $('.mobile-search-expanded');
            this.$searchInput = $('.mobile-search-expanded .search-input');
            
            // 状态变量
            this.lastScrollTop = 0;
            this.isSearchActive = false;
            this.isMobileMenuOpen = false;
            this.isUserDropdownOpen = false;
            
            this.init();
        }

        init() {
            this.bindEvents();
            this.initScrollBehavior();
            this.initMobileMenu();
            this.initSearchAutocomplete();
            this.initStickyBanner();
            this.initDropdownHover();
            this.bindMobileEvents();
            this.bindMobileSearchEvents();
        }

        bindEvents() {
            // 绑定横幅关闭事件
            this.$stickyBannerClose.on('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.closeStickyBanner();
                
                // 调试输出
                console.log('Banner close button clicked');
            });
        }

        // 关闭顶部横幅
        closeStickyBanner() {
            console.log('Closing banner...');
            
            this.$stickyBanner.slideUp(300, () => {
                $('body').removeClass('has-banner');
                this.$header.css('top', 0);
                
                // 发送 AJAX 请求
                $.ajax({
                    url: wpAjax.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'close_banner',
                        nonce: wpAjax.nonce
                    },
                    success: (response) => {
                        console.log('Banner close AJAX response:', response);
                    },
                    error: (xhr, status, error) => {
                        console.error('Banner close AJAX error:', error);
                    }
                });
            });
        }

        // 初始化横幅
        initStickyBanner() {
            // 检查是否之前已关闭
            if (localStorage.getItem('stickyBannerClosed') === 'true') {
                this.$stickyBanner.hide();
                this.$header.css('top', 0);
            }
        }

        bindMobileEvents() {
            // 打开移动端菜单
            this.$mobileMenuToggle.on('click', () => {
                this.$mobileMenu.addClass('active');
                $('body').addClass('menu-open');
            });

            // 关闭移动端菜单
            $('.mobile-menu-close').on('click', () => {
                this.$mobileMenu.removeClass('active');
                $('body').removeClass('menu-open');
            });

            // 点击菜单外区域关闭
            $(document).on('click', (e) => {
                if (
                    this.$mobileMenu.hasClass('active') &&
                    !$(e.target).closest('.mobile-menu').length &&
                    !$(e.target).closest('.mobile-menu-toggle').length
                ) {
                    this.$mobileMenu.removeClass('active');
                    $('body').removeClass('menu-open');
                }
            });
        }

        bindMobileSearchEvents() {
            // 打开搜索框
            this.$searchToggleBtn.on('click', () => {
                this.$mobileSearchExpanded.addClass('active');
                setTimeout(() => {
                    this.$searchInput.focus();
                }, 100);
            });

            // 关闭搜索框
            this.$searchClose.on('click', () => {
                this.$mobileSearchExpanded.removeClass('active');
            });

            // 点击外部关闭搜索框
            $(document).on('click', (e) => {
                if (
                    this.$mobileSearchExpanded.hasClass('active') &&
                    !$(e.target).closest('.mobile-search-expanded').length &&
                    !$(e.target).closest('.mobile-search-toggle').length
                ) {
                    this.$mobileSearchExpanded.removeClass('active');
                }
            });
        }
    }

    // 初始化
    $(document).ready(() => {
        console.log('Initializing Header...');
        new Header();
    });

})(jQuery);