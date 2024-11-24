<?php
class WP_Architizer_Theme_Options {
    private $options;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_theme_options_page'));
        add_action('admin_init', array($this, 'register_settings'));
        $this->options = get_option('wp_architizer_options');
    }

    public function add_theme_options_page() {
        add_menu_page(
            '主题设置',
            '主题设置',
            'manage_options',
            'wp-architizer-options',
            array($this, 'render_options_page'),
            'dashicons-admin-customizer',
            60
        );
    }

    public function register_settings() {
        register_setting('wp_architizer_options', 'wp_architizer_options', array($this, 'sanitize_options'));

        // 基础设置
        add_settings_section(
            'basic_settings',
            '基础设置',
            null,
            'wp-architizer-options'
        );

        // Logo设置
        $this->add_field('logo', '网站Logo', 'image');
        $this->add_field('logo_width', 'Logo宽度', 'number');
        
        // 布局设置
        add_settings_section(
            'layout_settings',
            '布局设置',
            null,
            'wp-architizer-options'
        );
        
        $this->add_field('layout_style', '布局风格', 'select', array(
            'wide' => '宽屏',
            'boxed' => '居中固定宽度',
            'fluid' => '自适应'
        ));
        
        $this->add_field('sidebar_position', '侧边栏位置', 'select', array(
            'right' => '右侧',
            'left' => '左侧',
            'none' => '无侧边栏'
        ));

        // 颜色设置
        add_settings_section(
            'color_settings',
            '颜色设置',
            null,
            'wp-architizer-options'
        );
        
        $this->add_field('primary_color', '主色调', 'color');
        $this->add_field('secondary_color', '次要色调', 'color');
        $this->add_field('text_color', '文字颜色', 'color');

        // 性能设置
        add_settings_section(
            'performance_settings',
            '性能设置',
            null,
            'wp-architizer-options'
        );
        
        $this->add_field('enable_lazy_load', '启用图片懒加载', 'checkbox');
        $this->add_field('enable_minification', '启用资源压缩', 'checkbox');
        $this->add_field('enable_cache', '启用页面缓存', 'checkbox');

        // 社交媒体设置
        add_settings_section(
            'social_settings',
            '社交媒体设置',
            null,
            'wp-architizer-options'
        );
        
        $this->add_field('weixin_qrcode', '微信二维码', 'image');
        $this->add_field('weibo_url', '微博链接', 'url');
        $this->add_field('linkedin_url', 'LinkedIn链接', 'url');
    }

    private function add_field($id, $title, $type, $options = array()) {
        add_settings_field(
            $id,
            $title,
            array($this, 'render_field'),
            'wp-architizer-options',
            $this->get_section_for_field($id),
            array(
                'id' => $id,
                'type' => $type,
                'options' => $options
            )
        );
    }

    private function get_section_for_field($id) {
        $sections = array(
            'logo' => 'basic_settings',
            'logo_width' => 'basic_settings',
            'layout_style' => 'layout_settings',
            'sidebar_position' => 'layout_settings',
            'primary_color' => 'color_settings',
            'secondary_color' => 'color_settings',
            'text_color' => 'color_settings',
            'enable_lazy_load' => 'performance_settings',
            'enable_minification' => 'performance_settings',
            'enable_cache' => 'performance_settings',
            'weixin_qrcode' => 'social_settings',
            'weibo_url' => 'social_settings',
            'linkedin_url' => 'social_settings'
        );
        
        return isset($sections[$id]) ? $sections[$id] : 'basic_settings';
    }

    public function render_field($args) {
        $id = $args['id'];
        $type = $args['type'];
        $value = isset($this->options[$id]) ? $this->options[$id] : '';
        
        switch ($type) {
            case 'text':
            case 'url':
            case 'number':
                echo "<input type='$type' id='$id' name='wp_architizer_options[$id]' value='$value' class='regular-text'>";
                break;
                
            case 'checkbox':
                $checked = checked($value, 1, false);
                echo "<input type='checkbox' id='$id' name='wp_architizer_options[$id]' value='1' $checked>";
                break;
                
            case 'select':
                echo "<select id='$id' name='wp_architizer_options[$id]'>";
                foreach ($args['options'] as $key => $label) {
                    $selected = selected($value, $key, false);
                    echo "<option value='$key' $selected>$label</option>";
                }
                echo "</select>";
                break;
                
            case 'color':
                echo "<input type='color' id='$id' name='wp_architizer_options[$id]' value='$value'>";
                break;
                
            case 'image':
                echo "<div class='image-upload-field'>";
                if ($value) {
                    echo "<img src='$value' style='max-width:200px;'><br>";
                }
                echo "<input type='text' id='$id' name='wp_architizer_options[$id]' value='$value' class='regular-text'>";
                echo "<button type='button' class='button image-upload-button'>选择图片</button>";
                echo "</div>";
                break;
                
            case 'textarea':
                echo "<textarea id='$id' name='wp_architizer_options[$id]' rows='5' cols='50'>$value</textarea>";
                break;
        }
    }

    public function render_options_page() {
        ?>
        <div class="wrap">
            <h1>主题设置</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('wp_architizer_options');
                do_settings_sections('wp-architizer-options');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function sanitize_options($input) {
        $new_input = array();
        
        foreach ($input as $key => $value) {
            switch ($key) {
                case 'custom_css':
                case 'custom_js':
                case 'header_code':
                case 'footer_code':
                    $new_input[$key] = wp_kses_post($value);
                    break;
                    
                case 'weibo_url':
                case 'linkedin_url':
                    $new_input[$key] = esc_url_raw($value);
                    break;
                    
                default:
                    $new_input[$key] = sanitize_text_field($value);
            }
        }
        
        return $new_input;
    }
}

// 初始化主题选项
new WP_Architizer_Theme_Options();

// 添加主题选项页面的样式和脚本
function wp_architizer_admin_scripts($hook) {
    if ($hook != 'toplevel_page_wp-architizer-options') {
        return;
    }
    
    wp_enqueue_media();
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    
    wp_enqueue_script(
        'wp-architizer-admin',
        get_template_directory_uri() . '/assets/js/admin.js',
        array('jquery', 'wp-color-picker'),
        '1.0.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'wp_architizer_admin_scripts');