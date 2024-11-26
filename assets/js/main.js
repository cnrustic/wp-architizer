// 初始化所有需要的模块
document.addEventListener('DOMContentLoaded', function() {
    // 初始化 Swiper 轮播
    initProductGallery();
    // 初始化产品交互
    initProductInteractions();
    // 初始化文档下载
    initDocumentDownload();
});

// 产品画廊初始化
function initProductGallery() {
    if (document.querySelector('.gallery-main-slider')) {
        const galleryThumbs = new Swiper('.gallery-thumbs-slider', {
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesVisibility: true,
            watchSlidesProgress: true,
            breakpoints: {
                768: {
                    slidesPerView: 5
                },
                1024: {
                    slidesPerView: 6
                }
            }
        });

        const galleryMain = new Swiper('.gallery-main-slider', {
            spaceBetween: 10,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            thumbs: {
                swiper: galleryThumbs
            }
        });
    }
}

// 产品交互功能
function initProductInteractions() {
    // 分享按钮
    const shareBtn = document.querySelector('.share-btn');
    if (shareBtn) {
        shareBtn.addEventListener('click', function() {
            if (navigator.share) {
                navigator.share({
                    title: document.title,
                    url: window.location.href
                });
            }
        });
    }

    // 规格筛选
    const specFilters = document.querySelectorAll('.specs-filter button');
    specFilters.forEach(filter => {
        filter.addEventListener('click', function() {
            const category = this.dataset.category;
            filterSpecifications(category);
        });
    });
}

// 文档下载处理
function initDocumentDownload() {
    const downloadBtns = document.querySelectorAll('.document-download');
    downloadBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.href;
            const filename = this.dataset.filename;
            
            // 显示下载进度
            showDownloadProgress();
            
            fetch(url)
                .then(response => response.blob())
                .then(blob => {
                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;
                    link.click();
                    hideDownloadProgress();
                });
        });
    });
}

// 辅助函数
function filterSpecifications(category) {
    const rows = document.querySelectorAll('.specs-table tr');
    rows.forEach(row => {
        if (category === 'all' || row.dataset.category === category) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function showDownloadProgress() {
    // 实现下载进度提示
}

function hideDownloadProgress() {
    // 隐藏下载进度提示
}
