class ProductGallery {
    constructor() {
        this.mainContainer = document.querySelector('.gallery-main');
        this.thumbsContainer = document.querySelector('.gallery-thumbs');
        this.mainItems = document.querySelectorAll('.gallery-main-item');
        this.thumbs = document.querySelectorAll('.gallery-thumb');
        this.currentIndex = 0;
        
        this.init();
    }
    
    init() {
        if (!this.mainItems.length) return;
        
        // 初始化状态
        this.setActiveItem(0);
        
        // 绑定事件
        this.bindEvents();
        
        // 添加触摸支持
        this.addTouchSupport();
        
        // 添加键盘支持
        this.addKeyboardSupport();
    }
    
    bindEvents() {
        // 缩略图点击
        this.thumbs.forEach((thumb, index) => {
            thumb.addEventListener('click', () => this.setActiveItem(index));
        });
        
        // 图片点击放大
        this.mainItems.forEach(item => {
            item.addEventListener('click', () => this.openLightbox(item));
        });
    }
    
    setActiveItem(index) {
        // 更新当前索引
        this.currentIndex = index;
        
        // 移除所有激活状态
        this.mainItems.forEach(item => item.classList.remove('active'));
        this.thumbs.forEach(thumb => thumb.classList.remove('active'));
        
        // 设置新的激活状态
        this.mainItems[index].classList.add('active');
        this.thumbs[index].classList.add('active');
        
        // 滚动缩略图到可视区域
        this.scrollThumbIntoView(index);
    }
    
    scrollThumbIntoView(index) {
        const thumb = this.thumbs[index];
        thumb.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest',
            inline: 'center'
        });
    }
    
    addTouchSupport() {
        let startX = 0;
        let isDragging = false;
        
        this.mainContainer.addEventListener('touchstart', e => {
            startX = e.touches[0].clientX;
            isDragging = true;
        });
        
        this.mainContainer.addEventListener('touchmove', e => {
            if (!isDragging) return;
            
            const currentX = e.touches[0].clientX;
            const diff = startX - currentX;
            
            if (Math.abs(diff) > 50) {
                if (diff > 0 && this.currentIndex < this.mainItems.length - 1) {
                    this.setActiveItem(this.currentIndex + 1);
                } else if (diff < 0 && this.currentIndex > 0) {
                    this.setActiveItem(this.currentIndex - 1);
                }
                isDragging = false;
            }
        });
    }
    
    addKeyboardSupport() {
        document.addEventListener('keydown', e => {
            if (e.key === 'ArrowLeft' && this.currentIndex > 0) {
                this.setActiveItem(this.currentIndex - 1);
            } else if (e.key === 'ArrowRight' && this.currentIndex < this.mainItems.length - 1) {
                this.setActiveItem(this.currentIndex + 1);
            }
        });
    }
    
    openLightbox(item) {
        // 实现图片放大查看功能
    }
}

// 初始化画廊
document.addEventListener('DOMContentLoaded', () => {
    new ProductGallery();
}); 