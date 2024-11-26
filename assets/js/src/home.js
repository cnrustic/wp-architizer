(function($) {
    'use strict';

    window.Architizer = window.Architizer || {};
    
    Architizer.Home = {
        init: function() {
            this.initHeroSlider();
            this.initProjectsGrid();
            this.initTrendingTabs();
            this.initFeaturedFirms();
            this.initCategoryShowcase();
            this.initLatestProducts();
            this.bindEvents();
        },

        bindEvents: function() {
            // 趋势标签页切换
            $('.trending-tabs .tab').on('click', function() {
                const tabId = $(this).data('tab');
                
                $('.trending-tabs .tab').removeClass('active');
                $(this).addClass('active');
                
                $('.tab-content').removeClass('active');
                $(`#${tabId}`).addClass('active');
            });

            // 筛选按钮点击事件
            $('.filter-btn').on('click', this.handleFilter.bind(this));

            // 加载更多按钮
            $('.load-more-btn').on('click', this.loadMoreItems.bind(this));
        },

        // 首页大图轮播
        initHeroSlider: function() {
            if ($('.hero-slider').length) {
                new Swiper('.hero-slider', {
                    slidesPerView: 1,
                    effect: 'fade',
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev'
                    }
                });
            }
        },

        // 项目网格
        initProjectsGrid: function() {
            if ($('.projects-grid').length) {
                const $grid = $('.projects-grid').masonry({
                    itemSelector: '.project-card',
                    columnWidth: '.project-card',
                    percentPosition: true,
                    transitionDuration: '0.3s'
                });

                // 图片加载完成后重新布局
                $grid.imagesLoaded().progress(function() {
                    $grid.masonry('layout');
                });
            }
        },

        // 趋势内容切换
        initTrendingTabs: function() {
            $('.trending-tabs .tab').first().addClass('active');
            $('.tab-content').first().addClass('active');

            // 添加内容切换动画
            $('.tab-content').hide();
            $('.tab-content.active').show();
        },

        // 特色事务所轮播
        initFeaturedFirms: function() {
            if ($('.featured-firms-slider').length) {
                new Swiper('.featured-firms-slider', {
                    slidesPerView: 1,
                    spaceBetween: 30,
                    loop: true,
                    autoplay: {
                        delay: 3000
                    },
                    breakpoints: {
                        640: {
                            slidesPerView: 2
                        },
                        768: {
                            slidesPerView: 3
                        },
                        1024: {
                            slidesPerView: 4
                        }
                    }
                });
            }
        },

        // 分类展示动画
        initCategoryShowcase: function() {
            $('.category-card').each(function(index) {
                $(this).css({
                    'animation-delay': `${index * 0.2}s`
                });
            });
        },

        // 最新产品展示
        initLatestProducts: function() {
            if ($('.latest-products-slider').length) {
                new Swiper('.latest-products-slider', {
                    slidesPerView: 1,
                    spaceBetween: 20,
                    loop: true,
                    autoplay: {
                        delay: 4000
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true
                    },
                    breakpoints: {
                        640: {
                            slidesPerView: 2
                        },
                        768: {
                            slidesPerView: 3
                        },
                        1024: {
                            slidesPerView: 4
                        }
                    }
                });
            }
        },

        // 处理筛选
        handleFilter: function(e) {
            const $btn = $(e.currentTarget);
            const filter = $btn.data('filter');

            $('.filter-btn').removeClass('active');
            $btn.addClass('active');

            if (filter === 'all') {
                $('.grid-item').show();
            } else {
                $('.grid-item').hide();
                $(`.grid-item[data-category="${filter}"]`).show();
            }

            // 重新布局
            if ($('.projects-grid').length) {
                $('.projects-grid').masonry('layout');
            }
        },

        // 加载更多内容
        loadMoreItems: async function(e) {
            const $btn = $(e.currentTarget);
            const $container = $btn.closest('section').find('.grid');
            const page = $btn.data('page') || 1;

            try {
                $btn.addClass('loading');
                const response = await $.ajax({
                    url: architizer_ajax.ajax_url,
                    data: {
                        action: 'load_more_items',
                        page: page,
                        type: $btn.data('type')
                    }
                });

                if (response.success) {
                    // 添加新内容
                    const $items = $(response.data.html);
                    $container.append($items);
                    
                    // 更新按钮状态
                    $btn.data('page', page + 1);
                    if (!response.data.has_more) {
                        $btn.hide();
                    }

                    // 重新布局
                    if ($container.hasClass('projects-grid')) {
                        $container.masonry('appended', $items);
                    }
                }
            } catch (error) {
                console.error('加载失败:', error);
            } finally {
                $btn.removeClass('loading');
            }
        }
    };

    // 初始化
    $(document).ready(function() {
        if ($('.home').length) {
            Architizer.Home.init();
        }
    });

})(jQuery);