(function($) {
    'use strict';

    // 创建一个命名空间
    window.Architizer = window.Architizer || {};
    
    // 首页相关功能
    Architizer.Home = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            // 趋势标签页切换
            $('.trending-tabs .tab').on('click', function() {
                const tabId = $(this).data('tab');
                
                // 更新标签状态
                $('.trending-tabs .tab').removeClass('active');
                $(this).addClass('active');
                
                // 更新内容
                $('.tab-content').removeClass('active');
                $(`#${tabId}`).addClass('active');
            });

            // 在这里添加其他首页相关的事件绑定
        }
    };

    // 当文档加载完成时初始化
    $(document).ready(function() {
        // 只在首页执行初始化
        if ($('.home').length) {
            Architizer.Home.init();
        }
    });

})(jQuery);