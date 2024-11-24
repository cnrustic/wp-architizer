<?php
class WP_Architizer_Multilingual {
    private $languages = array(
        'zh_CN' => '简体中文',
        'en_US' => 'English',
        'ja' => '日本語'
    );
    
    private $current_language;
    
    public function __construct() {
        $this->current_language = $this->get_user_language();
        
        add_action('init', array($this, 'register_language_support'));
        add_action('wp_head', array($this, 'add_language_meta'));
        add_filter('the_content', array($this, 'translate_content'));
        add_action('admin_menu', array($this, 'add_language_menu'));
        add_action('rest_api_init', array($this, 'register_language_api'));
    }

    public function register_language_support() {
        // 注册语言文本域
        load_theme_textdomain('wp-architizer', get_template_directory() . '/languages');
        
        // 注册语言切换器小工具
        add_action('widgets_init', function() {
            register_widget('WP_Widget_Language_Switcher');
        });
        
        // 添加语言切换支持
        add_filter('locale', array($this, 'set_locale'));
    }

    public function add_language_meta() {
        echo '<link rel="alternate" hreflang="x-default" href="' . home_url() . '">' . "\n";
        foreach ($this->languages as $code => $name) {
            echo '<link rel="alternate" hreflang="' . $code . '" href="' . $this->get_language_url($code) . '">' . "\n";
        }
    }

    public function translate_content($content) {
        // 如果内容包含翻译标记，则提取当前语言的内容
        if (preg_match_all('/\[lang_([^\]]+)\](.*?)\[\/lang_\1\]/s', $content, $matches)) {
            foreach ($matches[1] as $i => $lang) {
                if ($lang === $this->current_language) {
                    return $matches[2][$i];
                }
            }
        }
        return $content;
    }

    private function get_user_language() {
        // 优先使用用户选择的语言
        if (isset($_COOKIE['wp_architizer_language'])) {
            return $_COOKIE['wp_architizer_language'];
        }
        
        // 其次使用浏览器语言
        $browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        foreach ($this->languages as $code => $name) {
            if (strpos($code, $browser_lang) === 0) {
                return $code;
            }
        }
        
        // 默认使用中文
        return 'zh_CN';
    }

    private function get_language_url($lang_code) {
        $current_url = home_url(add_query_arg(array()));
        $parsed_url = parse_url($current_url);
        
        // 移除现有的语言参数
        if (isset($parsed_url['query'])) {
            parse_str($parsed_url['query'], $query_vars);
            unset($query_vars['lang']);
            $parsed_url['query'] = http_build_query($query_vars);
        }
        
        // 添加新的语言参数
        $url = $parsed_url['scheme'] . '://' . $parsed_url['host'];
        if (isset($parsed_url['path'])) {
            $url .= $parsed_url['path'];
        }
        if (isset($parsed_url['query']) && $parsed_url['query']) {
            $url .= '?' . $parsed_url['query'] . '&lang=' . $lang_code;
        } else {
            $url .= '?lang=' . $lang_code;
        }
        
        return $url;
    }

    public function set_locale($locale) {
        return $this->current_language;
    }

    public function add_language_menu() {
        add_submenu_page(
            'options-general.php',
            '语言设置',
            '语言设置',
            'manage_options',
            'wp-architizer-languages',
            array($this, 'render_language_page')
        );
    }

    public function render_language_page() {
        // 渲染语言设置页面
        include(get_template_directory() . '/inc/views/language-settings.php');
    }

    public function register_language_api() {
        register_rest_route('wp-architizer/v1', '/languages', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_languages_api'),
            'permission_callback' => '__return_true'
        ));
        
        register_rest_route('wp-architizer/v1', '/translate', array(
            'methods' => 'POST',
            'callback' => array($this, 'translate_text_api'),
            'permission_callback' => function() {
                return current_user_can('edit_posts');
            }
        ));
    }

    public function get_languages_api() {
        return rest_ensure_response($this->languages);
    }

    public function translate_text_api($request) {
        $text = $request->get_param('text');
        $target_lang = $request->get_param('target_lang');
        
        // 这里可以集成第三方翻译API
        // 示例使用Google Translate API
        $translated_text = $this->translate_with_google($text, $target_lang);
        
        return rest_ensure_response(array(
            'original' => $text,
            'translated' => $translated_text,
            'language' => $target_lang
        ));
    }

    private function translate_with_google($text, $target_lang) {
        // 实现Google Translate API调用
        // 需要配置API密钥
        return $text; // 临时返回原文
    }
}

// 初始化多语言支持
new WP_Architizer_Multilingual(); 