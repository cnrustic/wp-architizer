class AdvancedSearch {
    constructor() {
        this.searchModal = null;
        this.searchInput = null;
        this.searchResults = null;
        this.searchTimeout = null;
        this.recentSearches = [];
        
        this.init();
    }

    init() {
        this.createSearchModal();
        this.loadRecentSearches();
        this.initSearchTrigger();
        this.initSearchEvents();
        this.initKeyboardShortcuts();
        this.initVoiceSearch();
    }

    createSearchModal() {
        const modalHTML = `
            <div class="search-modal">
                <div class="search-modal-content">
                    <div class="search-header">
                        <div class="search-input-wrapper">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="search-input" placeholder="搜索项目、产品或建筑师...">
                            <button class="voice-search-btn" title="语音搜索">
                                <i class="fas fa-microphone"></i>
                            </button>
                        </div>
                        <button class="close-search-btn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="search-filters">
                        <div class="filter-tags">
                            <button class="filter-tag active" data-type="all">全部</button>
                            <button class="filter-tag" data-type="project">项目</button>
                            <button class="filter-tag" data-type="product">产品</button>
                            <button class="filter-tag" data-type="firm">建筑事务所</button>
                        </div>
                    </div>
                    <div class="search-results">
                        <div class="recent-searches">
                            <h3>最近搜索</h3>
                            <ul class="recent-list"></ul>
                        </div>
                        <div class="live-results"></div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.searchModal = document.querySelector('.search-modal');
        this.searchInput = this.searchModal.querySelector('.search-input');
        this.searchResults = this.searchModal.querySelector('.search-results');
    }

    initSearchTrigger() {
        const searchTriggers = document.querySelectorAll('.search-trigger, .hero-search');
        searchTriggers.forEach(trigger => {
            trigger.addEventListener('click', () => this.openSearch());
        });
    }

    initSearchEvents() {
        // 关闭按钮
        this.searchModal.querySelector('.close-search-btn').addEventListener('click', () => {
            this.closeSearch();
        });

        // 搜索输入
        this.searchInput.addEventListener('input', () => {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                const query = this.searchInput.value;
                if (query.length >= 2) {
                    this.performSearch(query);
                } else {
                    this.showRecentSearches();
                }
            }, 300);
        });

        // 筛选标签
        this.searchModal.querySelectorAll('.filter-tag').forEach(tag => {
            tag.addEventListener('click', () => {
                this.searchModal.querySelectorAll('.filter-tag').forEach(t => {
                    t.classList.remove('active');
                });
                tag.classList.add('active');
                if (this.searchInput.value) {
                    this.performSearch(this.searchInput.value);
                }
            });
        });

        // 点击外部关闭
        this.searchModal.addEventListener('click', (e) => {
            if (e.target === this.searchModal) {
                this.closeSearch();
            }
        });
    }

    async performSearch(query) {
        try {
            const type = this.searchModal.querySelector('.filter-tag.active').dataset.type;
            const response = await fetch(wpAjax.ajaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'perform_search',
                    nonce: wpAjax.nonce,
                    query: query,
                    type: type
                })
            });

            const data = await response.json();
            if (data.success) {
                this.renderSearchResults(data.data);
                this.addToRecentSearches(query);
            }
        } catch (error) {
            console.error('搜索失败:', error);
        }
    }

    renderSearchResults(results) {
        const liveResults = this.searchModal.querySelector('.live-results');
        
        if (!results.length) {
            liveResults.innerHTML = '<div class="no-results">未找到相关结果</div>';
            return;
        }

        const resultsHTML = results.map(item => `
            <a href="${item.url}" class="search-result-item">
                <div class="result-thumbnail">
                    <img src="${item.thumbnail}" alt="${item.title}">
                </div>
                <div class="result-content">
                    <h4>
                        ${item.title}
                        <span class="result-type">${item.type}</span>
                    </h4>
                    <p>${item.excerpt}</p>
                </div>
            </a>
        `).join('');

        liveResults.innerHTML = resultsHTML;
    }

    // 其他辅助方法...
    openSearch() {
        this.searchModal.classList.add('active');
        this.searchInput.focus();
        document.body.style.overflow = 'hidden';
        this.showRecentSearches();
    }

    closeSearch() {
        this.searchModal.classList.remove('active');
        document.body.style.overflow = '';
    }

    loadRecentSearches() {
        this.recentSearches = JSON.parse(localStorage.getItem('recentSearches') || '[]');
    }

    addToRecentSearches(query) {
        if (!this.recentSearches.includes(query)) {
            this.recentSearches.unshift(query);
            if (this.recentSearches.length > 5) {
                this.recentSearches.pop();
            }
            localStorage.setItem('recentSearches', JSON.stringify(this.recentSearches));
        }
    }
}

// 初始化
document.addEventListener('DOMContentLoaded', () => {
    window.advancedSearch = new AdvancedSearch();
}); 