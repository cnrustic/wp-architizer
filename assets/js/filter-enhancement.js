class FilterEnhancement {
    constructor() {
        this.filterForm = document.querySelector('.filters-form');
        this.filterInputs = document.querySelectorAll('.filter-input, .filter-select');
        this.activeFilters = document.querySelector('.active-filters');
        this.debounceTimer = null;
        this.init();
    }

    init() {
        this.initializeFilterState();
        this.bindEvents();
        this.createActiveFiltersDisplay();
    }

    initializeFilterState() {
        this.filterState = new URLSearchParams(window.location.search);
        this.updateActiveFiltersDisplay();
    }

    bindEvents() {
        // 实时筛选输入
        this.filterInputs.forEach(input => {
            if (input.tagName === 'INPUT') {
                input.addEventListener('input', () => this.debounceFilter(input));
            } else {
                input.addEventListener('change', () => this.handleFilterChange(input));
            }
        });

        // 清除筛选按钮
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('clear-filter')) {
                e.preventDefault();
                const filterName = e.target.dataset.filter;
                this.clearFilter(filterName);
            }
        });

        // 清除所有筛选
        const clearAllBtn = document.querySelector('.clear-all-filters');
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.clearAllFilters();
            });
        }
    }

    debounceFilter(input) {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => {
            this.handleFilterChange(input);
        }, 500);
    }

    handleFilterChange(input) {
        const value = input.value.trim();
        const name = input.name;

        if (value) {
            this.filterState.set(name, value);
        } else {
            this.filterState.delete(name);
        }

        this.updateURL();
        this.updateActiveFiltersDisplay();
        this.triggerFilterUpdate();
    }

    clearFilter(filterName) {
        this.filterState.delete(filterName);
        const input = this.filterForm.querySelector(`[name="${filterName}"]`);
        if (input) {
            input.value = '';
        }
        this.updateURL();
        this.updateActiveFiltersDisplay();
        this.triggerFilterUpdate();
    }

    clearAllFilters() {
        this.filterState = new URLSearchParams();
        this.filterInputs.forEach(input => {
            input.value = '';
        });
        this.updateURL();
        this.updateActiveFiltersDisplay();
        this.triggerFilterUpdate();
    }

    updateURL() {
        const newURL = `${window.location.pathname}${this.filterState.toString() ? '?' + this.filterState.toString() : ''}`;
        window.history.pushState({}, '', newURL);
    }

    createActiveFiltersDisplay() {
        if (!this.activeFilters) {
            this.activeFilters = document.createElement('div');
            this.activeFilters.className = 'active-filters';
            this.filterForm.insertAdjacentElement('afterend', this.activeFilters);
        }
    }

    updateActiveFiltersDisplay() {
        if (!this.activeFilters) return;

        const filterLabels = {
            'project_year': '年份',
            'project_location': '位置',
            'manufacturer': '制造商',
            'price_range': '价格区间',
            'orderby': '排序'
        };

        let filtersHTML = '';
        let hasActiveFilters = false;

        this.filterState.forEach((value, key) => {
            if (value) {
                hasActiveFilters = true;
                filtersHTML += `
                    <span class="active-filter">
                        ${filterLabels[key] || key}: ${value}
                        <button class="clear-filter" data-filter="${key}">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                `;
            }
        });

        if (hasActiveFilters) {
            filtersHTML = `
                <div class="active-filters-wrapper">
                    <span class="active-filters-label">当前筛选：</span>
                    ${filtersHTML}
                    <button class="clear-all-filters">清除所有</button>
                </div>
            `;
        }

        this.activeFilters.innerHTML = filtersHTML;
        this.animateFilterChanges();
    }

    animateFilterChanges() {
        const filters = document.querySelectorAll('.active-filter');
        filters.forEach((filter, index) => {
            filter.style.animation = `fadeInSlide 0.3s ease forwards ${index * 0.1}s`;
        });
    }

    triggerFilterUpdate() {
        // 触发 AJAX 加载更多类中的筛选更新
        const event = new CustomEvent('filterUpdate', {
            detail: {
                filters: Object.fromEntries(this.filterState)
            }
        });
        document.dispatchEvent(event);
    }
}

// 添加动画样式
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInSlide {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .active-filters-wrapper {
        margin: 20px 0;
        padding: 15px;
        background: #f5f5f5;
        border-radius: 8px;
        animation: fadeIn 0.3s ease;
    }

    .active-filters-label {
        color: #666;
        margin-right: 10px;
    }

    .active-filter {
        display: inline-flex;
        align-items: center;
        margin: 0 10px 10px 0;
        padding: 5px 10px;
        background: #fff;
        border-radius: 20px;
        font-size: 14px;
        opacity: 0;
    }

    .clear-filter {
        background: none;
        border: none;
        color: #666;
        margin-left: 5px;
        cursor: pointer;
        padding: 2px;
    }

    .clear-filter:hover {
        color: #ff4444;
    }

    .clear-all-filters {
        background: none;
        border: none;
        color: #666;
        text-decoration: underline;
        cursor: pointer;
        margin-left: 15px;
    }

    .clear-all-filters:hover {
        color: #333;
    }
`;
document.head.appendChild(style);

// 初始化
document.addEventListener('DOMContentLoaded', () => {
    new FilterEnhancement();
}); 