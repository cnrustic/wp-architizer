<?php
/**
 * 初始化主题功能
 */
class Architizer_Theme_Init {
    public function __construct() {
        // 加载必要的文件
        $this->load_files();
        
        // 初始化各个模块
        $this->init_modules();
    }
    
    private function load_files() {
        $base_dir = get_template_directory() . '/inc';
        
        // 核心文件
        require_once $base_dir . '/theme-options.php';
        require_once $base_dir . '/template-functions.php';
        require_once $base_dir . '/widgets.php';
        require_once $base_dir . '/post-types.php';
        require_once $base_dir . '/customizer.php';
        require_once $base_dir . '/taxonomies.php';
        require_once $base_dir . '/ajax-handlers.php';
        require_once $base_dir . '/api-manager.php';
        require_once $base_dir . '/custom-header.php';
        require_once $base_dir . '/jetpack.php';
        require_once $base_dir . '/multilingual.php';
        require_once $base_dir . '/statistics.php';
        require_once $base_dir . '/template-tags.php';
        require_once $base_dir . '/translations.php';
        // 模块文件
        require_once $base_dir . '/modules/seo.php';
        require_once $base_dir . '/modules/projects.php';
        require_once $base_dir . '/modules/performance.php';
        require_once $base_dir . '/modules/team.php';
        require_once $base_dir . '/modules/case-studies.php';
        require_once $base_dir . '/modules/contact-form.php';
        require_once $base_dir . '/modules/news-blog.php';
        require_once $base_dir . '/modules/testimonials.php';
    }
    
    private function init_modules() {
        // 初始化各个类
        new WP_Architizer_Theme_Options();
        new Architizer_Template_Functions();
        new WP_Architizer_SEO();
        new WP_Architizer_Projects();
    }
}

// 初始化主题
function architizer_init() {
    new Architizer_Theme_Init();
}
add_action('after_setup_theme', 'architizer_init');