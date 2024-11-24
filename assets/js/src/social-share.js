class SocialShare {
    constructor() {
        this.shareData = {
            title: document.title,
            url: window.location.href,
            description: this.getMetaDescription(),
            image: this.getShareImage()
        };
        
        this.platforms = {
            weixin: {
                name: '微信',
                icon: 'fab fa-weixin',
                action: () => this.shareWeixin()
            },
            weibo: {
                name: '微博',
                icon: 'fab fa-weibo',
                action: () => this.shareWeibo()
            },
            qzone: {
                name: 'QQ空间',
                icon: 'fab fa-qq',
                action: () => this.shareQzone()
            },
            linkedin: {
                name: 'LinkedIn',
                icon: 'fab fa-linkedin',
                action: () => this.shareLinkedin()
            },
            copyLink: {
                name: '复制链接',
                icon: 'fas fa-link',
                action: () => this.copyLink()
            }
        };

        this.init();
    }

    init() {
        this.createShareButtons();
        this.initShareModal();
        this.initQRCode();
    }

    getMetaDescription() {
        const metaDesc = document.querySelector('meta[name="description"]');
        return metaDesc ? metaDesc.getAttribute('content') : '';
    }

    getShareImage() {
        const ogImage = document.querySelector('meta[property="og:image"]');
        return ogImage ? ogImage.getAttribute('content') : '';
    }

    createShareButtons() {
        document.querySelectorAll('.share-trigger').forEach(trigger => {
            const shareContainer = document.createElement('div');
            shareContainer.className = 'share-container';
            
            Object.entries(this.platforms).forEach(([key, platform]) => {
                const button = document.createElement('button');
                button.className = `share-button ${key}`;
                button.innerHTML = `<i class="${platform.icon}"></i><span>${platform.name}</span>`;
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    platform.action();
                });
                shareContainer.appendChild(button);
            });

            trigger.appendChild(shareContainer);
        });
    }

    initShareModal() {
        this.modal = document.createElement('div');
        this.modal.className = 'share-modal';
        this.modal.innerHTML = `
            <div class="share-modal-content">
                <div class="share-modal-header">
                    <h3>分享到</h3>
                    <button class="close-modal"><i class="fas fa-times"></i></button>
                </div>
                <div class="share-modal-body">
                    <div class="qrcode-container">
                        <div id="share-qrcode"></div>
                        <p>微信扫码分享</p>
                    </div>
                    <div class="share-preview">
                        <div class="preview-image">
                            <img src="${this.shareData.image || ''}" alt="分享预览图">
                        </div>
                        <div class="preview-content">
                            <h4>${this.shareData.title}</h4>
                            <p>${this.shareData.description}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(this.modal);

        // 关闭模态框
        this.modal.querySelector('.close-modal').addEventListener('click', () => {
            this.modal.classList.remove('active');
        });

        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.modal.classList.remove('active');
            }
        });
    }

    initQRCode() {
        // 使用 qrcode.js 生成二维码
        if (typeof QRCode !== 'undefined') {
            new QRCode(document.getElementById('share-qrcode'), {
                text: this.shareData.url,
                width: 128,
                height: 128
            });
        }
    }

    shareWeixin() {
        this.modal.classList.add('active');
    }

    shareWeibo() {
        const params = new URLSearchParams({
            url: this.shareData.url,
            title: this.shareData.title,
            pic: this.shareData.image
        });
        
        window.open(
            `http://service.weibo.com/share/share.php?${params.toString()}`,
            '_blank',
            'width=600,height=500'
        );
    }

    shareQzone() {
        const params = new URLSearchParams({
            url: this.shareData.url,
            title: this.shareData.title,
            desc: this.shareData.description,
            pics: this.shareData.image
        });
        
        window.open(
            `http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?${params.toString()}`,
            '_blank',
            'width=600,height=500'
        );
    }

    shareLinkedin() {
        const params = new URLSearchParams({
            url: this.shareData.url,
            title: this.shareData.title,
            summary: this.shareData.description
        });
        
        window.open(
            `https://www.linkedin.com/shareArticle?mini=true&${params.toString()}`,
            '_blank',
            'width=600,height=500'
        );
    }

    async copyLink() {
        try {
            await navigator.clipboard.writeText(this.shareData.url);
            this.showToast('链接已复制到剪贴板');
        } catch (err) {
            this.showToast('复制失败，请手动复制');
        }
    }

    showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'share-toast';
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('show');
        }, 100);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 2000);
    }
}

// 添加样式
const shareStyles = document.createElement('style');
shareStyles.textContent = `
    .share-container {
        display: flex;
        gap: 10px;
        margin: 15px 0;
    }

    .share-button {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 8px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #f5f5f5;
    }

    .share-button:hover {
        background: #e5e5e5;
    }

    .share-button i {
        font-size: 16px;
    }

    .share-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .share-modal.active {
        display: flex;
    }

    .share-modal-content {
        background: #fff;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        padding: 20px;
    }

    .share-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .close-modal {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 20px;
        color: #666;
    }

    .qrcode-container {
        text-align: center;
        margin-bottom: 20px;
    }

    .share-preview {
        border: 1px solid #eee;
        border-radius: 4px;
        padding: 10px;
        display: flex;
        gap: 15px;
    }

    .preview-image img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 4px;
    }

    .preview-content h4 {
        margin: 0 0 10px;
        font-size: 16px;
    }

    .preview-content p {
        margin: 0;
        color: #666;
        font-size: 14px;
    }

    .share-toast {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%) translateY(100%);
        background: rgba(0,0,0,0.8);
        color: #fff;
        padding: 10px 20px;
        border-radius: 4px;
        transition: transform 0.3s ease;
    }

    .share-toast.show {
        transform: translateX(-50%) translateY(0);
    }

    @media (max-width: 768px) {
        .share-container {
            flex-wrap: wrap;
        }

        .share-button {
            flex: 1;
            min-width: 100px;
        }

        .preview-image img {
            width: 60px;
            height: 60px;
        }
    }
`;
document.head.appendChild(shareStyles);

// 初始化
document.addEventListener('DOMContentLoaded', () => {
    new SocialShare();
}); 