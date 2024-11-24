jQuery(document).ready(function($) {
    // 初始化颜色选择器
    $('.wp-architizer-options .color-field').wpColorPicker();
    
    // 图片上传
    $('.image-upload-button').click(function(e) {
        e.preventDefault();
        
        var button = $(this);
        var field = button.siblings('input');
        var customUploader = wp.media({
            title: '选择图片',
            library: {
                type: 'image'
            },
            button: {
                text: '使用此图片'
            },
            multiple: false
        }).on('select', function() {
            var attachment = customUploader.state().get('selection').first().toJSON();
            field.val(attachment.url);
            
            // 更新预览
            var preview = button.siblings('img');
            if (preview.length === 0) {
                preview = $('<img style="max-width:200px;">').insertBefore(field);
            }
            preview.attr('src', attachment.url);
        }).open();
    });
    
    // 表单验证
    $('form').on('submit', function(e) {
        var customCSS = $('#custom_css').val();
        var customJS = $('#custom_js').val();
        
        // 验证CSS
        if (customCSS && !isValidCSS(customCSS)) {
            e.preventDefault();
            alert('自定义CSS格式不正确');
            return false;
        }
        
        // 验证JavaScript
        if (customJS && !isValidJS(customJS)) {
            e.preventDefault();
            alert('自定义JavaScript格式不正确');
            return false;
        }
    });
    
    // CSS验证
    function isValidCSS(css) {
        try {
            var sheet = new CSSStyleSheet();
            sheet.insertRule(css, 0);
            return true;
        } catch (e) {
            return false;
        }
    }
    
    // JavaScript验证
    function isValidJS(js) {
        try {
            new Function(js);
            return true;
        } catch (e) {
            return false;
        }
    }
}); 