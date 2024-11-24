class ContactForm {
    constructor(formWrapper) {
        this.wrapper = formWrapper;
        this.form = this.wrapper.querySelector('.contact-form');
        this.submitButton = this.form.querySelector('.submit-button');
        this.successMessage = this.wrapper.querySelector('.success-message');
        this.errorMessage = this.wrapper.querySelector('.error-message');
        
        this.init();
    }
    
    init() {
        this.form.addEventListener('submit', this.handleSubmit.bind(this));
        
        // 文件上传预览
        const fileInput = this.form.querySelector('input[type="file"]');
        if (fileInput) {
            fileInput.addEventListener('change', this.handleFileSelect.bind(this));
        }
        
        // 表单验证
        this.form.querySelectorAll('input, textarea').forEach(input => {
            input.addEventListener('invalid', this.handleInvalid.bind(this));
            input.addEventListener('input', this.handleInput.bind(this));
        });
    }
    
    async handleSubmit(e) {
        e.preventDefault();
        
        this.submitButton.disabled = true;
        this.submitButton.textContent = '提交中...';
        
        const formData = new FormData(this.form);
        formData.append('action', 'submit_contact_form');
        formData.append('nonce', this.form.querySelector('[name="contact_form_nonce"]').value);
        
        try {
            const response = await fetch(ajaxurl, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showSuccess(result.data);
                this.form.reset();
            } else {
                this.showError(result.data);
            }
        } catch (error) {
            this.showError('提交失败，请稍后重试');
        } finally {
            this.submitButton.disabled = false;
            this.submitButton.textContent = '提交';
        }
    }
    
    handleFileSelect(e) {
        const file = e.target.files[0];
        const maxSize = 2 * 1024 * 1024; // 2MB
        const allowedTypes = ['application/pdf', 'application/msword', 
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'image/jpeg', 'image/png'];
        
        if (file.size > maxSize) {
            e.target.value = '';
            this.showError('文件大小不能超过2MB');
            return;
        }
        
        if (!allowedTypes.includes(file.type)) {
            e.target.value = '';
            this.showError('不支持的文件类型');
            return;
        }
    }
    
    handleInvalid(e) {
        e.preventDefault();
        const input = e.target;
        input.classList.add('invalid');
        
        const errorMessage = input.getAttribute('data-error') || 
                           '请填写此字段';
        
        let errorElement = input.parentElement.querySelector('.field-error');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'field-error';
            input.parentElement.appendChild(errorElement);
        }
        errorElement.textContent = errorMessage;
    }
    
    handleInput(e) {
        const input = e.target;
        input.classList.remove('invalid');
        
        const errorElement = input.parentElement.querySelector('.field-error');
        if (errorElement) {
            errorElement.remove();
        }
    }
    
    showSuccess(message) {
        this.successMessage.textContent = message;
        this.successMessage.style.display = 'block';
        this.errorMessage.style.display = 'none';
        
        setTimeout(() => {
            this.successMessage.style.display = 'none';
        }, 5000);
    }
    
    showError(message) {
        this.errorMessage.textContent = message;
        this.errorMessage.style.display = 'block';
        this.successMessage.style.display = 'none';
        
        setTimeout(() => {
            this.errorMessage.style.display = 'none';
        }, 5000);
    }
}

// 初始化所有联系表单
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.contact-form-wrapper').forEach(wrapper => {
        new ContactForm(wrapper);
    });
    
    // 初始化地图
    document.querySelectorAll('.contact-map').forEach(mapElement => {
        const latitude = parseFloat(mapElement.dataset.latitude);
        const longitude = parseFloat(mapElement.dataset.longitude);
        
        if (latitude && longitude) {
            const map = new google.maps.Map(mapElement, {
                center: { lat: latitude, lng: longitude },
                zoom: 15
            });
            
            new google.maps.Marker({
                position: { lat: latitude, lng: longitude },
                map: map
            });
        }
    });
}); 