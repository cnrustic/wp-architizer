class ImagePreview {
    constructor() {
        this.lightbox = this.createLightbox();
        this.currentIndex = 0;
        this.images = [];
        this.init();
    }

    createLightbox() {
        const lightbox = document.createElement('div');
        lightbox.className = 'lightbox';
        lightbox.innerHTML = `
            <div class="lightbox-content">
                <div class="lightbox-header">
                    <span class="image-counter"></span>
                    <div class="lightbox-controls">
                        <button class="zoom-in-btn" title="放大">
                            <i class="fas fa-search-plus"></i>
                        </button>
                        <button class="zoom-out-btn" title="缩小">
                            <i class="fas fa-search-minus"></i>
                        </button>
                        <button class="rotate-btn" title="旋转">
                            <i class="fas fa-redo"></i>
                        </button>
                        <button class="download-btn" title="下载">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="close-btn" title="关闭">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="lightbox-main">
                    <button class="nav-btn prev-btn">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="image-container">
                        <img src="" alt="" class="preview-image">
                    </div>
                    <button class="nav-btn next-btn">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div class="lightbox-footer">
                    <div class="image-caption"></div>
                    <div class="thumbnails-container"></div>
                </div>
            </div>
        `;
        document.body.appendChild(lightbox);
        return lightbox;
    }

    init() {
        this.initGalleryImages();
        this.bindEvents();
        this.initGestures();
    }

    initGalleryImages() {
        // 获取所有可预览的图片
        document.querySelectorAll('.project-thumbnail img, .product-thumbnail img').forEach(img => {
            img.classList.add('preview-enabled');
            img.addEventListener('click', (e) => {
                const gallery = e.target.closest('.project-card, .product-card');
                if (gallery) {
                    this.openGallery(gallery, e.target);
                }
            });
        });
    }

    bindEvents() {
        // 关闭按钮
        this.lightbox.querySelector('.close-btn').addEventListener('click', () => this.closeLightbox());

        // 导航按钮
        this.lightbox.querySelector('.prev-btn').addEventListener('click', () => this.showPrevImage());
        this.lightbox.querySelector('.next-btn').addEventListener('click', () => this.showNextImage());

        // 图片控制按钮
        this.lightbox.querySelector('.zoom-in-btn').addEventListener('click', () => this.zoomImage(1.2));
        this.lightbox.querySelector('.zoom-out-btn').addEventListener('click', () => this.zoomImage(0.8));
        this.lightbox.querySelector('.rotate-btn').addEventListener('click', () => this.rotateImage());
        this.lightbox.querySelector('.download-btn').addEventListener('click', () => this.downloadImage());

        // 键盘事件
        document.addEventListener('keydown', (e) => {
            if (!this.lightbox.classList.contains('active')) return;

            switch(e.key) {
                case 'Escape':
                    this.closeLightbox();
                    break;
                case 'ArrowLeft':
                    this.showPrevImage();
                    break;
                case 'ArrowRight':
                    this.showNextImage();
                    break;
            }
        });

        // 点击外部关闭
        this.lightbox.addEventListener('click', (e) => {
            if (e.target === this.lightbox) {
                this.closeLightbox();
            }
        });
    }

    initGestures() {
        let touchStartX = 0;
        let touchEndX = 0;
        
        this.lightbox.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        });

        this.lightbox.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            this.handleGesture();
        });

        const handleGesture = () => {
            const difference = touchStartX - touchEndX;
            if (Math.abs(difference) > 50) {
                if (difference > 0) {
                    this.showNextImage();
                } else {
                    this.showPrevImage();
                }
            }
        };
    }

    openGallery(gallery, clickedImage) {
        this.images = Array.from(gallery.querySelectorAll('img')).map(img => ({
            src: img.src,
            caption: img.alt || img.getAttribute('data-caption') || ''
        }));

        this.currentIndex = this.images.findIndex(img => img.src === clickedImage.src);
        this.showImage(this.currentIndex);
        this.lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        this.updateThumbnails();
    }

    showImage(index) {
        const image = this.images[index];
        const previewImage = this.lightbox.querySelector('.preview-image');
        const caption = this.lightbox.querySelector('.image-caption');
        const counter = this.lightbox.querySelector('.image-counter');

        previewImage.src = image.src;
        previewImage.style.transform = 'scale(1) rotate(0deg)';
        caption.textContent = image.caption;
        counter.textContent = `${index + 1} / ${this.images.length}`;

        // 更新缩略图选中状态
        this.updateThumbnailSelection(index);
    }

    updateThumbnails() {
        const container = this.lightbox.querySelector('.thumbnails-container');
        container.innerHTML = this.images.map((image, index) => `
            <div class="thumbnail ${index === this.currentIndex ? 'active' : ''}"
                 style="background-image: url('${image.src}')"
                 data-index="${index}">
            </div>
        `).join('');

        container.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.addEventListener('click', () => {
                const index = parseInt(thumb.dataset.index);
                this.currentIndex = index;
                this.showImage(index);
            });
        });
    }

    updateThumbnailSelection(index) {
        const thumbnails = this.lightbox.querySelectorAll('.thumbnail');
        thumbnails.forEach(thumb => thumb.classList.remove('active'));
        thumbnails[index]?.classList.add('active');
    }

    showPrevImage() {
        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        this.showImage(this.currentIndex);
    }

    showNextImage() {
        this.currentIndex = (this.currentIndex + 1) % this.images.length;
        this.showImage(this.currentIndex);
    }

    zoomImage(scale) {
        const image = this.lightbox.querySelector('.preview-image');
        const currentScale = this.getImageScale(image);
        const newScale = currentScale * scale;
        
        if (newScale >= 0.5 && newScale <= 3) {
            image.style.transform = `scale(${newScale}) rotate(${this.getImageRotation(image)}deg)`;
        }
    }

    rotateImage() {
        const image = this.lightbox.querySelector('.preview-image');
        const currentRotation = this.getImageRotation(image);
        const currentScale = this.getImageScale(image);
        image.style.transform = `scale(${currentScale}) rotate(${currentRotation + 90}deg)`;
    }

    getImageScale(image) {
        const transform = image.style.transform;
        const match = transform.match(/scale\((.*?)\)/);
        return match ? parseFloat(match[1]) : 1;
    }

    getImageRotation(image) {
        const transform = image.style.transform;
        const match = transform.match(/rotate\((.*?)deg\)/);
        return match ? parseFloat(match[1]) : 0;
    }

    async downloadImage() {
        const image = this.images[this.currentIndex];
        try {
            const response = await fetch(image.src);
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `image-${this.currentIndex + 1}.jpg`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        } catch (error) {
            console.error('下载失败:', error);
        }
    }

    closeLightbox() {
        this.lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// 添加样式
const lightboxStyles = document.createElement('style');
lightboxStyles.textContent = `
    .lightbox {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.9);
        display: none;
        z-index: 1000;
    }

    .lightbox.active {
        display: flex;
    }

    .lightbox-content {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .lightbox-header {
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #fff;
    }

    .lightbox-controls {
        display: flex;
        gap: 10px;
    }

    .lightbox-controls button {
        background: none;
        border: none;
        color: #fff;
        cursor: pointer;
        padding: 8px;
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }

    .lightbox-controls button:hover {
        background: rgba(255,255,255,0.1);
    }

    .lightbox-main {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .image-container {
        max-width: 90%;
        max-height: 80vh;
        position: relative;
    }

    .preview-image {
        max-width: 100%;
        max-height: 80vh;
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    .nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0,0,0,0.5);
        border: none;
        color: #fff;
        padding: 15px;
        cursor: pointer;
        border-radius: 50%;
        transition: background-color 0.3s ease;
    }

    .prev-btn {
        left: 20px;
    }

    .next-btn {
        right: 20px;
    }

    .nav-btn:hover {
        background: rgba(0,0,0,0.8);
    }

    .lightbox-footer {
        padding: 15px;
        color: #fff;
        text-align: center;
    }

    .thumbnails-container {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 15px;
        overflow-x: auto;
        padding: 10px 0;
    }

    .thumbnail {
        width: 60px;
        height: 60px;
        background-size: cover;
        background-position: center;
        cursor: pointer;
        border: 2px solid transparent;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .thumbnail.active {
        border-color: #fff;
    }

    .preview-enabled {
        cursor: pointer;
        transition: opacity 0.3s ease;
    }

    .preview-enabled:hover {
        opacity: 0.8;
    }

    @media (max-width: 768px) {
        .lightbox-controls {
            display: none;
        }

        .nav-btn {
            padding: 10px;
        }

        .thumbnail {
            width: 40px;
            height: 40px;
        }
    }
`;
document.head.appendChild(lightboxStyles);

// 初始化
document.addEventListener('DOMContentLoaded', () => {
    new ImagePreview();
}); 