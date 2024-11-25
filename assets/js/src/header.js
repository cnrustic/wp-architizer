/**
 * Header functionality
 */
(function($) {
    'use strict';

    // 当文档加载完成时执行
    $(document).ready(function() {
        const Header = {
            init: function() {
                this.bindEvents();
            },

            // 绑定事件
            bindEvents: function() {
                this.initSearchToggle();
                this.initUserMenu();
                this.handleClickOutside();
            },

            // 初始化搜索功能
            initSearchToggle: function() {
                const $searchToggle = $('.search-toggle');
                const $searchDropdown = $('.search-dropdown');
                const $searchOverlay = $('.search-overlay');

                $searchToggle.on('click', function(e) {
                    e.preventDefault();
                    $searchDropdown.toggleClass('active');
                    $searchOverlay.toggleClass('active');
                });
            },

            // 初始化用户菜单
            initUserMenu: function() {
                const $userToggle = $('.user-toggle');
                const $userDropdown = $('.user-dropdown');

                $userToggle.on('click', function(e) {
                    e.preventDefault();
                    $userDropdown.toggleClass('active');
                });
            },

            // 处理点击外部关闭下拉菜单
            handleClickOutside: function() {
                $(document).on('click', function(event) {
                    // 搜索框点击外部关闭
                    if (!$(event.target).closest('.search-box').length) {
                        $('.search-dropdown').removeClass('active');
                        $('.search-overlay').removeClass('active');
                    }

                    // 用户菜单点击外部关闭
                    if (!$(event.target).closest('.user-menu').length) {
                        $('.user-dropdown').removeClass('active');
                    }
                });
            }
        };

        // 初始化 Header 功能
        Header.init();
    });

})(jQuery); 