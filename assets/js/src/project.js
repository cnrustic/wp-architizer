class Project {
    constructor() {
        this.initGallery();
        this.initShare();
        this.initLike();
    }
    
    // 初始化图片画廊
    initGallery() {
        const viewGalleryBtn = document.querySelector('.view-gallery');
        const galleryModal = document.querySelector('.gallery-modal');
        const closeGalleryBtn = document.querySelector('.close-gallery');
        
        if (!viewGalleryBtn || !galleryModal) return;
        
        // 初始化 Swiper 滑块
        const gallerySlider = new Swiper('.gallery-slider', {
            slidesPerView: 1,
            spaceBetween: 30,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            keyboard: {
                enabled: true,
            },
        });
        
        // 打开画廊
        viewGalleryBtn.addEventListener('click', () => {
            galleryModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
        
        // 关闭画廊
        closeGalleryBtn.addEventListener('click', () => {
            galleryModal.classList.remove('active');
            document.body.style.overflow = '';
        });
        
        // ESC 键关闭
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && galleryModal.classList.contains('active')) {
                galleryModal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }
    
    // 初始化分享功能
    initShare() {
        const shareButtons = document.querySelectorAll('.share-btn');
        
        shareButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const platform = btn.dataset.platform;
                const url = window.location.href;
                const title = document.title;
                
                let shareUrl;
                switch(platform) {
                    case 'facebook':
                        shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                        break;
                    case 'twitter':
                        shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
                        break;
                    case 'linkedin':
                        shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
                        break;
                    case 'pinterest':
                        const image = document.querySelector('.hero-image img').src;
                        shareUrl = `https://pinterest.com/pin/create/button/?url=${url}&media=${image}&description=${title}`;
                        break;
                }
                
                if (shareUrl) {
                    window.open(shareUrl, '_blank', 'width=600,height=400');
                }
            });
        });
    }
    
    // 初始化喜欢功能
    initLike() {
        const likeButtons = document.querySelectorAll('.like-btn');
        
        likeButtons.forEach(btn => {
            btn.addEventListener('click', async () => {
                const projectId = btn.dataset.project;
                
                try {
                    const response = await fetch('/wp-json/architizer/v1/like', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': architizerData.nonce
                        },
                        body: JSON.stringify({ projectId })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        btn.classList.toggle('liked');
                        const icon = btn.querySelector('.material-icons');
                        icon.textContent = btn.classList.contains('liked') ? 'favorite' : 'favorite_border';
                    }
                } catch (error) {
                    console.error('Like failed:', error);
                }
            });
        });
    }
}

// 初始化
document.addEventListener('DOMContentLoaded', () => {
    new Project();
}); 