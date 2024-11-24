class AjaxLoadMore {
    constructor() {
        this.grid = document.querySelector('.projects-grid, .products-grid');
        this.loadMoreBtn = document.querySelector('.load-more-btn');
        this.loading = false;
        this.page = 1;
        
        this.init();
    }
    
    init() {
        if (this.loadMoreBtn) {
            this.loadMoreBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.loadMore();
            });
        }
        
        // 初始化无限滚动
        this.initInfiniteScroll();
        
        // 初始化筛选表单
        this.initFilters();
    }
    
    initInfiniteScroll() {
        const options = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !this.loading) {
                    this.loadMore();
                }
            });
        }, options);
        
        if (this.loadMoreBtn) {
            observer.observe(this.loadMoreBtn);
        }
    }
    
    initFilters() {
        const filterForm = document.querySelector('.filters-form');
        if (filterForm) {
            filterForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.page = 1;
                this.grid.innerHTML = '';
                this.loadMore(true);
            });
        }
    }
    
    async loadMore(isFilter = false) {
        if (this.loading) return;
        
        this.loading = true;
        this.showLoading();
        
        const formData = new FormData();
        formData.append('action', 'load_more_posts');
        formData.append('nonce', wpAjax.nonce);
        formData.append('page', this.page);
        formData.append('post_type', wpAjax.postType);
        
        if (wpAjax.taxonomy) {
            formData.append('taxonomy', wpAjax.taxonomy);
            formData.append('term_id', wpAjax.termId);
        }
        
        // 添加筛选条件
        const filterForm = document.querySelector('.filters-form');
        if (filterForm) {
            const filters = {};
            new FormData(filterForm).forEach((value, key) => {
                filters[key] = value;
            });
            formData.append('filters', JSON.stringify(filters));
        }
        
        try {
            const response = await fetch(wpAjax.ajaxUrl, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                if (isFilter) {
                    this.grid.innerHTML = data.posts;
                } else {
                    this.grid.insertAdjacentHTML('beforeend', data.posts);
                }
                
                if (!data.has_more) {
                    this.loadMoreBtn.style.display = 'none';
                }
                
                this.page++;
            }
        } catch (error) {
            console.error('加载失败:', error);
        } finally {
            this.loading = false;
            this.hideLoading();
        }
    }
    
    showLoading() {
        if (this.loadMoreBtn) {
            this.loadMoreBtn.classList.add('loading');
            this.loadMoreBtn.textContent = '加载中...';
        }
    }
    
    hideLoading() {
        if (this.loadMoreBtn) {
            this.loadMoreBtn.classList.remove('loading');
            this.loadMoreBtn.textContent = '加载更多';
        }
    }
}

// 初始化
document.addEventListener('DOMContentLoaded', () => {
    new AjaxLoadMore();
}); 