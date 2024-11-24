class SEOPreview {
    constructor() {
        this.titleInput = document.getElementById('seo_title');
        this.descriptionInput = document.getElementById('seo_description');
        this.previewTitle = document.querySelector('.preview-title');
        this.previewUrl = document.querySelector('.preview-url');
        this.previewDescription = document.querySelector('.preview-description');
        
        this.init();
    }
    
    init() {
        if (this.titleInput && this.descriptionInput) {
            this.titleInput.addEventListener('input', () => this.updatePreview());
            this.descriptionInput.addEventListener('input', () => this.updatePreview());
            
            // 初始预览
            this.updatePreview();
        }
    }
    
    updatePreview() {
        const title = this.titleInput.value || document.getElementById('title').value;
        const description = this.descriptionInput.value;
        const url = window.location.origin + '/' + 
                   document.getElementById('post_name').value;
        
        this.previewTitle.textContent = title;
        this.previewUrl.textContent = url;
        this.previewDescription.textContent = description;
        
        // 更新字符计数
        this.updateCharacterCount(this.titleInput, 60);
        this.updateCharacterCount(this.descriptionInput, 160);
    }
    
    updateCharacterCount(input, limit) {
        const count = input.value.length;
        const countElement = input.nextElementSibling.querySelector('.char-count');
        
        if (!countElement) {
            const span = document.createElement('span');
            span.className = 'char-count';
            input.nextElementSibling.appendChild(span);
        }
        
        countElement.textContent = `${count}/${limit}`;
        countElement.style.color = count > limit ? '#e74c3c' : '#666';
    }
}

// 初始化SEO预览
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.wp-architizer-seo-meta')) {
        new SEOPreview();
    }
});

// 社交分享功能
class SocialShare {
    constructor() {
        this.bindShareButtons();
    }
    
    bindShareButtons() {
        document.querySelectorAll('.share-button').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                
                const url = button.getAttribute('href');
                const width = 600;
                const height = 400;
                const left = (screen.width/2)-(width/2);
                const top = (screen.height/2)-(height/2);
                
                window.open(
                    url,
                    '分享',
                    `toolbar=no, location=no, directories=no, status=no, menubar=no, 
                     scrollbars=no, resizable=no, copyhistory=no, width=${width}, 
                     height=${height}, top=${top}, left=${left}`
                );
            });
        });
    }
}

// 初始化社交分享
new SocialShare(); 