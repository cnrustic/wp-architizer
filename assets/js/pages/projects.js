class ProjectsPage {
    constructor() {
        this.initFilters();
        this.initInfiniteScroll();
    }
    
    initFilters() {
        const filterForm = document.querySelector('.filter-form');
        if (!filterForm) return;
        
        // 处理筛选表单提交
        filterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitFilter(new FormData(filterForm));
        });
    }
    
    async submitFilter(formData) {
        try {
            const response = await fetch('/wp-json/architizer/v1/projects/filter', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            this.updateProjects(data);
        } catch (error) {
            console.error('筛选失败:', error);
        }
    }
}

// 初始化
new ProjectsPage(); 