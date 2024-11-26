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
            
            // 状态变量
            this.lastScrollTop = 0;
            this.isSearchActive = false;
            
            this.init();
        }

        init() {
            this.bindEvents();
            this.initScrollBehavior();
            this.initMobileMenu();
            this.initSearchAutocomplete();
        }

        bindEvents() {
            // 搜索相关
            this.$searchToggle.on('click', (e) => {
                e.preventDefault();
                this.toggleSearch();
            });

            // 搜索快捷键
            $(document).on('keydown', (e) => {
                if (e.key === '/' && !this.isSearchActive) {
                    e.preventDefault();
                    this.toggleSearch();
                }
                if (e.key === 'Escape' && this.isSearchActive) {
                    this.closeSearch();
                }
            });

            // 用户菜单
            this.$userToggle.on('click', (e) => {
                e.preventDefault();
                this.toggleUserMenu();
            });

            // 移动菜单
            this.$mobileMenuToggle.on('click', (e) => {
                e.preventDefault();
                this.toggleMobileMenu();
            });

            // 点击外部关闭
            $(document).on('click', (event) => {
                this.handleClickOutside(event);
            });
        }

        // 搜索功能
        toggleSearch() {
            this.isSearchActive = !this.isSearchActive;
            this.$searchDropdown.toggleClass('active');
            this.$searchOverlay.toggleClass('active');
            
            if (this.isSearchActive) {
                this.$searchInput.focus();
                $('body').addClass('search-active');
            } else {
                $('body').removeClass('search-active');
            }
        }

        closeSearch() {
            this.isSearchActive = false;
            this.$searchDropdown.removeClass('active');
            this.$searchOverlay.removeClass('active');
            $('body').removeClass('search-active');
        }

        // 搜索自动完成
        initSearchAutocomplete() {
            let searchTimeout;
            
            this.$searchInput.on('input', (e) => {
                clearTimeout(searchTimeout);
                const query = e.target.value;
                
                if (query.length >= 2) {
                    searchTimeout = setTimeout(() => {
                        this.performSearch(query);
                    }, 300);
                }
            });
        }

        async performSearch(query) {
            try {
                const response = await $.ajax({
                    url: '/wp-json/wp/v2/search',
                    data: {
                        search: query,
                        type: 'post',
                        subtype: ['post', 'project', 'product', 'firm'],
                        per_page: 5
                    }
                });
                this.updateSearchResults(response);
            } catch (error) {
                console.error('Search error:', error);
            }
        }

        updateSearchResults(results) {
            const $resultsContainer = this.$searchDropdown.find('.search-results');
            $resultsContainer.html('');
            
            if (results.length) {
                const items = results.map(item => `
                    <a href="${item.url}" class="search-result-item">
                        <span class="result-type">${item.subtype}</span>
                        <span class="result-title">${item.title}</span>
                    </a>
                `).join('');
                $resultsContainer.html(items);
            } else {
                $resultsContainer.html('<div class="no-results">未找到相关结果</div>');
            }
        }

        // 滚动行为
        initScrollBehavior() {
            let headerHeight = this.$header.outerHeight();
            
            $(window).on('scroll', () => {
                const scrollTop = $(window).scrollTop();
                
                // 向上滚动显示，向下滚动隐藏
                if (scrollTop > this.lastScrollTop && scrollTop > headerHeight) {
                    this.$header.addClass('header-hidden');
                } else {
                    this.$header.removeClass('header-hidden');
                }
                
                // 滚动时添加阴影
                if (scrollTop > 0) {
                    this.$header.addClass('header-shadow');
                } else {
                    this.$header.removeClass('header-shadow');
                }
                
                this.lastScrollTop = scrollTop;
            });
        }

        // 移动端菜单
        initMobileMenu() {
            // 添加子菜单展开功能
            this.$mobileMenu.find('.menu-item-has-children > a').after('<button class="submenu-toggle"><i class="fas fa-chevron-down"></i></button>');
            
            this.$mobileMenu.on('click', '.submenu-toggle', function(e) {
                e.preventDefault();
                const $submenu = $(this).siblings('.sub-menu');
                const $icon = $(this).find('i');
                
                $submenu.slideToggle(300);
                $icon.toggleClass('fa-chevron-down fa-chevron-up');
            });
        }

        toggleMobileMenu() {
            this.$mobileMenu.toggleClass('active');
            this.$mobileMenuToggle.toggleClass('active');
            $('body').toggleClass('mobile-menu-active');
        }

        toggleUserMenu() {
            this.$userDropdown.toggleClass('active');
        }

        handleClickOutside(event) {
            // 搜索框点击外部关闭
            if (!$(event.target).closest('.search-box').length) {
                this.closeSearch();
            }

            // 用户菜单点击外部关闭
            if (!$(event.target).closest('.user-menu').length) {
                this.$userDropdown.removeClass('active');
            }

            // 移动菜单点击外部关闭
            if (!$(event.target).closest('.mobile-menu, .mobile-menu-toggle').length) {
                this.$mobileMenu.removeClass('active');
                this.$mobileMenuToggle.removeClass('active');
                $('body').removeClass('mobile-menu-active');
            }
        }
    }

    // 当文档加载完成时初始化
    $(document).ready(() => {
        new Header();
    });

})(jQuery); 