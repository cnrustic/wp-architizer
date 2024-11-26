class BasicSearch {
    constructor() {
        this.init();
    }

    init() {
        this.searchForm = document.querySelector('.search-form');
        if (!this.searchForm) return;
        
        this.searchType = this.searchForm.querySelector('.search-type');
        this.advancedToggle = this.searchForm.querySelector('.toggle-button');
        this.advancedFields = this.searchForm.querySelector('.advanced-search-fields');
        this.filterGroups = this.searchForm.querySelectorAll('.filter-group');
        
        this.bindEvents();
    }

    bindEvents() {
        // 基础搜索事件
        this.searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.performBasicSearch();
        });

        // 高级搜索切换
        if (this.advancedToggle) {
            this.advancedToggle.addEventListener('click', () => {
                this.toggleAdvancedSearch();
            });
        }

        // 类型筛选
        if (this.searchType) {
            this.searchType.addEventListener('change', () => {
                this.handleTypeChange();
            });
        }
    }

    toggleAdvancedSearch() {
        this.advancedFields.style.display = 
            this.advancedFields.style.display === 'none' ? 'block' : 'none';
    }

    handleTypeChange() {
        const selectedType = this.searchType.value;
        
        // 隐藏所有筛选组
        this.filterGroups.forEach(group => {
            group.style.display = 'none';
        });
        
        // 显示选中类型的筛选组
        if (selectedType !== 'all') {
            const targetGroup = this.searchForm.querySelector(`.${selectedType}-filters`);
            if (targetGroup) {
                targetGroup.style.display = 'grid';
            }
        }
    }

    async performBasicSearch() {
        const formData = new FormData(this.searchForm);
        try {
            const response = await fetch(wpAjax.ajaxUrl, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            
            if (data.success) {
                window.location.href = data.redirect;
            }
        } catch (error) {
            console.error('搜索失败:', error);
        }
    }
}

// 初始化基础搜索
document.addEventListener('DOMContentLoaded', () => {
    new BasicSearch();
}); 