(function($) {
    'use strict';

    window.Architizer = window.Architizer || {};
    
    Architizer.Home = {
        init: function() {
            this.initHeroSlider();
            this.initProjectsGrid();
            this.initSearchSystem();
            this.initTrendingTabs();
            this.initFeaturedFirms();
            this.initCategoryShowcase();
            this.initLatestProducts();
            this.initScrollAnimations();
            this.bindEvents();
        },

        bindEvents: function() {
            // 趋势标签页切换
            $('.trending-tabs .tab').on('click', function() {
                const tabId = $(this).data('tab');
                
                $('.trending-tabs .tab').removeClass('active');
                $(this).addClass('active');
                
                $('.tab-content').removeClass('active').hide();
                $(`#${tabId}`).addClass('active').fadeIn();
            });

            // 筛选按钮点击事件
            $('.filter-btn').on('click', this.handleFilter.bind(this));

            // 加载更多按钮
            $('.load-more-btn').on('click', this.loadMoreItems.bind(this));

            // 新增：搜索框交互
            $('.hero-search input').on('focus', function() {
                $('.search-suggestions').slideDown();
            }).on('blur', function() {
                setTimeout(() => {
                    $('.search-suggestions').slideUp();
                }, 200);
            });
        },

        // 首页大图轮播优化
        initHeroSlider: function() {
            if ($('.hero-slider').length) {
                const heroSlider = new Swiper('.hero-slider', {
                    slidesPerView: 1,
                    effect: 'fade',
                    speed: 1000,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                        renderBullet: function (index, className) {
                            return `<span class="${className}"><span class="progress"></span></span>`;
                        }
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev'
                    }
                });

                // 添加进度条动画
                heroSlider.on('slideChange', function() {
                    $('.swiper-pagination-bullet').removeClass('animate');
                    $('.swiper-pagination-bullet-active').addClass('animate');
                });
            }
        },

        // 项目网格优化
        initProjectsGrid: function() {
            if ($('.projects-grid').length) {
                const $grid = $('.projects-grid').masonry({
                    itemSelector: '.project-card',
                    columnWidth: '.project-card',
                    percentPosition: true,
                    transitionDuration: '0.3s',
                    initLayout: false
                });

                // 图片延迟加载
                $grid.imagesLoaded().progress(function() {
                    $grid.masonry('layout');
                });

                // 添加悬停效果
                $('.project-card').hover(
                    function() {
                        $(this).find('.project-info').slideDown();
                    },
                    function() {
                        $(this).find('.project-info').slideUp();
                    }
                );
            }
        },

        // 搜索系统初始化
        initSearchSystem: function() {
            this.initSearchModal();
            this.initSearchEvents();
            this.initKeyboardShortcuts();
            this.initVoiceSearch();
            this.initTypeahead();
        },

        // 创建搜索模态框
        initSearchModal: function() {
            const modalHTML = `
                <div class="search-modal">
                    <div class="search-modal-content">
                        <div class="search-header">
                            <div class="search-input-wrapper">
                                <i class="fas fa-search"></i>
                                <input type="text" class="search-input" placeholder="搜索项目、产品或建筑师...">
                                <button class="voice-search-btn">
                                    <i class="fas fa-microphone"></i>
                                </button>
                            </div>
                            <button class="close-search-btn">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="search-type-tabs">
                            <button class="type-tab active" data-type="all">全部</button>
                            <button class="type-tab" data-type="project">项目</button>
                            <button class="type-tab" data-type="product">产品</button>
                            <button class="type-tab" data-type="firm">建筑事务所</button>
                        </div>
                        <div class="search-results"></div>
                    </div>
                </div>
            `;
            
            $('body').append(modalHTML);
            this.$searchModal = $('.search-modal');
            this.$searchInput = this.$searchModal.find('.search-input');
            this.$searchResults = this.$searchModal.find('.search-results');
        },

        // 搜索事件绑定
        initSearchEvents: function() {
            // 打开搜索
            $('.search-trigger, .hero-search').on('click', () => {
                this.openSearchModal();
            });

            // 关闭搜索
            $('.close-search-btn').on('click', () => {
                this.closeSearchModal();
            });

            // 类型切换
            $('.type-tab').on('click', function() {
                $('.type-tab').removeClass('active');
                $(this).addClass('active');
                this.performSearch();
            }.bind(this));

            // 搜索输入
            let searchTimeout;
            this.$searchInput.on('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.performSearch();
                }, 300);
            });
        },

        // 键盘快捷键
        initKeyboardShortcuts: function() {
            $(document).on('keydown', (e) => {
                // Ctrl/Cmd + K 打开搜索
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    this.openSearchModal();
                }
                // ESC 关闭搜索
                if (e.key === 'Escape' && this.$searchModal.is(':visible')) {
                    this.closeSearchModal();
                }
            });
        },

        // 语音搜索
        initVoiceSearch: function() {
            if ('webkitSpeechRecognition' in window) {
                const recognition = new webkitSpeechRecognition();
                recognition.continuous = false;
                recognition.interimResults = false;
                recognition.lang = 'zh-CN';

                $('.voice-search-btn').on('click', () => {
                    recognition.start();
                    $('.voice-search-btn').addClass('listening');
                });

                recognition.onresult = (event) => {
                    this.$searchInput.val(event.results[0][0].transcript);
                    this.performSearch();
                };

                recognition.onend = () => {
                    $('.voice-search-btn').removeClass('listening');
                };
            } else {
                $('.voice-search-btn').hide();
            }
        },

        // 搜索建议
        initTypeahead: function() {
            this.$searchInput.on('input', function() {
                const query = $(this).val();
                if (query.length >= 2) {
                    this.fetchSuggestions(query);
                }
            }.bind(this));
        },

        // 执行搜索
        async performSearch() {
            const query = this.$searchInput.val();
            const type = $('.type-tab.active').data('type');

            if (!query) {
                this.showRecentSearches();
                return;
            }

            try {
                const response = await $.ajax({
                    url: architizer_ajax.ajax_url,
                    data: {
                        action: 'perform_search',
                        query: query,
                        type: type,
                        nonce: architizer_ajax.nonce
                    }
                });

                if (response.success) {
                    this.renderSearchResults(response.data);
                }
            } catch (error) {
                console.error('搜索失败:', error);
            }
        },

        // 渲染搜索结果
        renderSearchResults: function(results) {
            if (!results.length) {
                this.$searchResults.html('<div class="no-results">未找到相关结果</div>');
                return;
            }

            const resultsHTML = results.map(item => `
                <div class="search-result-item">
                    <div class="result-image">
                        <img src="${item.thumbnail}" alt="${item.title}">
                    </div>
                    <div class="result-info">
                        <h4>${item.title}</h4>
                        <p>${item.excerpt}</p>
                        <span class="result-type">${item.type}</span>
                    </div>
                </div>
            `).join('');

            this.$searchResults.html(resultsHTML);
        },

        // 新增：滚动动画
        initScrollAnimations: function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });

            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                observer.observe(el);
            });
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