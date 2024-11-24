class AdvancedSearch {
    constructor() {
        this.searchModal = this.createSearchModal();
        this.searchResults = null;
        this.searchTimeout = null;
        this.init();
    }

    init() {
        this.initSearchTrigger();
        this.initSearchEvents();
        this.initKeyboardShortcuts();
        this.initVoiceSearch();
    }

    createSearchModal() {
        const modal = document.createElement('div');
        modal.className = 'search-modal';
        modal.innerHTML = `
            <div class="search-modal-content">
                <div class="search-header">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="search-input" placeholder="搜索项目、产品或公司...">
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
                        <button class="filter-tag" data-type="firm">公司</button>
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
        `;
        document.body.appendChild(modal);
        return modal;
    }

    initSearchTrigger() {
        // 添加搜索触发按钮到导航栏
        const searchTrigger = document.createElement('button');
        searchTrigger.className = 'search-trigger';
        searchTrigger.innerHTML = '<i class="fas fa-search"></i>';
        document.querySelector('.site-header .container').appendChild(searchTrigger);

        searchTrigger.addEventListener('click', () => this.openSearch());
    }

    initSearchEvents() {
        const searchInput = this.searchModal.querySelector('.search-input');
        const closeBtn = this.searchModal.querySelector('.close-search-btn');
        const filterTags = this.searchModal.querySelectorAll('.filter-tag');

        searchInput.addEventListener('input', () => {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.performSearch(searchInput.value);
            }, 300);
        });

        closeBtn.addEventListener('click', () => this.closeSearch());

        filterTags.forEach(tag => {
            tag.addEventListener('click', () => {
                filterTags.forEach(t => t.classList.remove('active'));
                tag.classList.add('active');
                if (searchInput.value) {
                    this.performSearch(searchInput.value);
                }
            });
        });

        // 点击模态框外部关闭
        this.searchModal.addEventListener('click', (e) => {
            if (e.target === this.searchModal) {
                this.closeSearch();
            }
        });
    }

    initKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + K 打开搜索
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                this.openSearch();
            }
            // Esc 关闭搜索
            if (e.key === 'Escape' && this.searchModal.classList.contains('active')) {
                this.closeSearch();
            }
        });
    }

    initVoiceSearch() {
        const voiceBtn = this.searchModal.querySelector('.voice-search-btn');
        
        if ('webkitSpeechRecognition' in window) {
            const recognition = new webkitSpeechRecognition();
            recognition.continuous = false;
            recognition.interimResults = false;
            recognition.lang = 'zh-CN';

            recognition.onresult = (event) => {
                const searchInput = this.searchModal.querySelector('.search-input');
                searchInput.value = event.results[0][0].transcript;
                this.performSearch(searchInput.value);
            };

            voiceBtn.addEventListener('click', () => {
                recognition.start();
                voiceBtn.classList.add('listening');
            });

            recognition.onend = () => {
                voiceBtn.classList.remove('listening');
            };
        } else {
            voiceBtn.style.display = 'none';
        }
    }

    async performSearch(query) {
        if (!query) {
            this.showRecentSearches();
            return;
        }

        const activeType = this.searchModal.querySelector('.filter-tag.active').dataset.type;
        const formData = new FormData();
        formData.append('action', 'advanced_search');
        formData.append('query', query);
        formData.append('type', activeType);
        formData.append('nonce', wpAjax.nonce);

        try {
            const response = await fetch(wpAjax.ajaxUrl, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                this.displayResults(data.results);
                this.addToRecentSearches(query);
            }
        } catch (error) {
            console.error('搜索失败:', error);
        }
    }

    displayResults(results) {
        const resultsContainer = this.searchModal.querySelector('.live-results');
        resultsContainer.innerHTML = '';

        if (results.length === 0) {
            resultsContainer.innerHTML = '<div class="no-results">未找到相关结果</div>';
            return;
        }

        const resultsHTML = results.map(result => `
            <div class="search-result-item ${result.type}">
                ${result.thumbnail ? `
                    <div class="result-thumbnail">
                        <img src="${result.thumbnail}" alt="${result.title}">
                    </div>
                ` : ''}
                <div class="result-content">
                    <h4>
                        <a href="${result.url}">${result.title}</a>
                        <span class="result-type">${result.type}</span>
                    </h4>
                    <p>${result.excerpt}</p>
                </div>
            </div>
        `).join('');

        resultsContainer.innerHTML = resultsHTML;
    }

    addToRecentSearches(query) {
        let recentSearches = JSON.parse(localStorage.getItem('recentSearches') || '[]');
        recentSearches = recentSearches.filter(item => item !== query);
        recentSearches.unshift(query);
        recentSearches = recentSearches.slice(0, 5);
        localStorage.setItem('recentSearches', JSON.stringify(recentSearches));
        this.updateRecentSearchesList();
    }

    showRecentSearches() {
        const recentContainer = this.searchModal.querySelector('.recent-searches');
        const resultsContainer = this.searchModal.querySelector('.live-results');
        recentContainer.style.display = 'block';
        resultsContainer.innerHTML = '';
        this.updateRecentSearchesList();
    }

    updateRecentSearchesList() {
        const recentList = this.searchModal.querySelector('.recent-list');
        const recentSearches = JSON.parse(localStorage.getItem('recentSearches') || '[]');

        recentList.innerHTML = recentSearches.map(query => `
            <li>
                <button class="recent-search-item">
                    <i class="fas fa-history"></i>
                    <span>${query}</span>
                </button>
            </li>
        `).join('');

        // 添加点击事件
        recentList.querySelectorAll('.recent-search-item').forEach((item, index) => {
            item.addEventListener('click', () => {
                const query = recentSearches[index];
                this.searchModal.querySelector('.search-input').value = query;
                this.performSearch(query);
            });
        });
    }

    openSearch() {
        this.searchModal.classList.add('active');
        const searchInput = this.searchModal.querySelector('.search-input');
        searchInput.focus();
        document.body.style.overflow = 'hidden';
        this.showRecentSearches();
    }

    closeSearch() {
        this.searchModal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// 添加样式
const searchStyles = document.createElement('style');
searchStyles.textContent = `
    .search-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        display: none;
        justify-content: center;
        align-items: flex-start;
        padding-top: 100px;
        z-index: 1000;
    }

    .search-modal.active {
        display: flex;
    }

    .search-modal-content {
        width: 90%;
        max-width: 800px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }

    .search-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
    }

    .search-input-wrapper {
        flex: 1;
        display: flex;
        align-items: center;
        margin-right: 20px;
    }

    .search-input {
        flex: 1;
        padding: 10px;
        font-size: 18px;
        border: none;
        outline: none;
        margin: 0 10px;
    }

    .voice-search-btn {
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        padding: 5px;
    }

    .voice-search-btn.listening {
        color: #f44336;
        animation: pulse 1s infinite;
    }

    .close-search-btn {
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        padding: 5px;
    }

    .search-filters {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
    }

    .filter-tags {
        display: flex;
        gap: 10px;
    }

    .filter-tag {
        padding: 5px 15px;
        border: 1px solid #ddd;
        border-radius: 20px;
        background: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .filter-tag.active {
        background: #333;
        color: #fff;
        border-color: #333;
    }

    .search-results {
        max-height: 60vh;
        overflow-y: auto;
        padding: 20px;
    }

    .search-result-item {
        display: flex;
        gap: 15px;
        padding: 15px;
        border-bottom: 1px solid #eee;
        transition: background-color 0.3s ease;
    }

    .search-result-item:hover {
        background: #f5f5f5;
    }

    .result-thumbnail {
        width: 100px;
        height: 100px;
    }

    .result-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 4px;
    }

    .result-content h4 {
        margin: 0 0 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .result-type {
        font-size: 12px;
        padding: 2px 8px;
        border-radius: 10px;
        background: #eee;
        color: #666;
    }

    .recent-searches {
        margin-bottom: 20px;
    }

    .recent-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .recent-search-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px;
        width: 100%;
        border: none;
        background: none;
        cursor: pointer;
        text-align: left;
    }

    .recent-search-item:hover {
        background: #f5f5f5;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    @media (max-width: 768px) {
        .search-modal {
            padding-top: 50px;
        }

        .result-thumbnail {
            width: 60px;
            height: 60px;
        }
    }
`;
document.head.appendChild(searchStyles);

// 初始化
document.addEventListener('DOMContentLoaded', () => {
    new AdvancedSearch();
}); 