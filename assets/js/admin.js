// 管理界面核心功能
class WPArchitizerAdmin {
    constructor() {
        this.init();
    }

    init() {
        this.initColorPicker();
        this.initImageUploader();
        this.initFormValidation();
    }

    initColorPicker() {
        jQuery('.wp-architizer-options .color-field').wpColorPicker();
    }

    initImageUploader() {
        jQuery('.image-upload-button').on('click', this.handleImageUpload.bind(this));
    }

    handleImageUpload(e) {
        e.preventDefault();
        const button = jQuery(e.currentTarget);
        const field = button.siblings('input');
        
        const uploader = wp.media({
            title: '选择图片',
            library: { type: 'image' },
            button: { text: '使用此图片' },
            multiple: false
        });

        uploader
            .on('select', () => {
                const attachment = uploader.state().get('selection').first().toJSON();
                field.val(attachment.url);
                this.updateImagePreview(button, attachment.url);
            })
            .open();
    }

    updateImagePreview(button, url) {
        let preview = button.siblings('img');
        if (!preview.length) {
            preview = jQuery('<img>', {
                style: 'max-width:200px;'
            }).insertBefore(button.siblings('input'));
        }
        preview.attr('src', url);
    }

    initFormValidation() {
        jQuery('form').on('submit', this.validateForm);
    },

    validateForm(e) {
        const customCSS = jQuery('#custom_css').val();
        const customJS = jQuery('#custom_js').val();
        
        if (!this.validateCSS(customCSS) || !this.validateJS(customJS)) {
            e.preventDefault();
            return false;
        }
    }
};

// 初始化
jQuery(document).ready(() => new WPArchitizerAdmin()); 