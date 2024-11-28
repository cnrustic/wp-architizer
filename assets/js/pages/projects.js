class ProjectsPage {
    constructor() {
        this.projectsContainer = document.querySelector('.projects-grid');
        this.filterForm = document.querySelector('.filter-form');
        this.initializeComponents();
    }

    initializeComponents() {
        this.initFilters();
        this.initMasonryGrid();
        this.initInfiniteScroll();
        this.initImageLazyLoad();
    }

    initMasonryGrid() {
        if (!this.projectsContainer) return;

        this.masonry = new Masonry(this.projectsContainer, {
            itemSelector: '.project-card',
            columnWidth: '.project-card',
            percentPosition: true,
            transitionDuration: '0.3s'
        });

        imagesLoaded(this.projectsContainer).on('progress', () => {
            this.masonry.layout();
        });
    }

    initImageLazyLoad() {
        const lazyImages = document.querySelectorAll('.lazy-load');
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy-load');
                    imageObserver.unobserve(img);
                }
            });
        });

        lazyImages.forEach(img => imageObserver.observe(img));
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