<?php
class WP_Architizer_Performance {
    private $cache_dir;
    
    public function __construct() {
        $this->cache_dir = WP_CONTENT_DIR . '/cache/wp-architizer';
        
        // 初始化缓存目录
        if (!file_exists($this->cache_dir)) {
            wp_mkdir_p($this->cache_dir);
        }
        
        // 基础优化
        add_action('init', array($this, 'init_optimization'));
        add_action('wp_enqueue_scripts', array($this, 'optimize_assets'), 999);
        add_filter('script_loader_tag', array($this, 'add_async_defer'), 10, 3);
        add_action('wp_head', array($this, 'remove_unnecessary_tags'), 1);
        
        // 图片优化
        add_filter('wp_handle_upload', array($this, 'optimize_uploaded_image'));
        add_filter('wp_get_attachment_image_attributes', array($this, 'add_lazy_loading'), 10, 3);
        add_filter('the_content', array($this, 'add_content_lazy_loading'));
        
        // 数据库优化
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_scheduled_auto_draft_delete', array($this, 'cleanup_database'));
        
        // 缓存系统
        add_action('save_post', array($this, 'clear_page_cache'));
        add_action('comment_post', array($this, 'clear_page_cache'));
        add_action('wp_loaded', array($this, 'maybe_serve_cache'));
        
        // AJAX优化
        add_action('wp_ajax_optimize_images', array($this, 'handle_bulk_image_optimization'));
        add_action('wp_ajax_clear_all_cache', array($this, 'handle_clear_all_cache'));
        
        // 监控系统
        add_action('template_redirect', array($this, 'start_page_profiling'));
        add_action('shutdown', array($this, 'end_page_profiling'));
    }

    public function init_optimization() {
        // 移除不必要的功能
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
        
        // 禁用表情符号
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        
        // 优化心跳
        add_filter('heartbeat_settings', function($settings) {
            $settings['interval'] = 60; // 60秒
            return $settings;
        });
    }

    public function optimize_assets() {
        global $wp_scripts, $wp_styles;
        
        // 获取主题设置
        $options = get_option('wp_architizer_performance_options');
        
        // 合并和压缩CSS
        if (!empty($options['combine_css'])) {
            $this->combine_css($wp_styles->queue);
        }
        
        // 合并和压缩JS
        if (!empty($options['combine_js'])) {
            $this->combine_js($wp_scripts->queue);
        }
        
        // 移除查询字符串
        if (!empty($options['remove_query_strings'])) {
            foreach ($wp_scripts->registered as $handle => $script) {
                $wp_scripts->registered[$handle]->ver = null;
            }
            foreach ($wp_styles->registered as $handle => $style) {
                $wp_styles->registered[$handle]->ver = null;
            }
        }
    }

    private function combine_css($handles) {
        if (empty($handles)) return;
        
        $combined = '';
        $cache_key = md5(implode(',', $handles));
        $cache_file = $this->cache_dir . '/css/' . $cache_key . '.css';
        
        if (!file_exists($cache_file) || (defined('WP_DEBUG') && WP_DEBUG)) {
            foreach ($handles as $handle) {
                $src = $this->get_asset_content($handle, 'style');
                if ($src) {
                    $combined .= "/* {$handle} */\n" . $src . "\n";
                }
            }
            
            // 压缩CSS
            $combined = $this->minify_css($combined);
            
            // 保存到缓存
            file_put_contents($cache_file, $combined);
        }
        
        // 注销原始样式并加载合并后的文件
        foreach ($handles as $handle) {
            wp_dequeue_style($handle);
        }
        
        wp_enqueue_style('combined-css', 
                        content_url('cache/wp-architizer/css/' . $cache_key . '.css'), 
                        array(), 
                        null);
    }

    private function combine_js($handles) {
        if (empty($handles)) return;
        
        $combined = '';
        $cache_key = md5(implode(',', $handles));
        $cache_file = $this->cache_dir . '/js/' . $cache_key . '.js';
        
        if (!file_exists($cache_file) || (defined('WP_DEBUG') && WP_DEBUG)) {
            foreach ($handles as $handle) {
                $src = $this->get_asset_content($handle, 'script');
                if ($src) {
                    $combined .= "/* {$handle} */\n" . $src . "\n";
                }
            }
            
            // 压缩JS
            $combined = $this->minify_js($combined);
            
            // 保存到缓存
            file_put_contents($cache_file, $combined);
        }
        
        // 注销原始脚本并加载合并后的文件
        foreach ($handles as $handle) {
            wp_dequeue_script($handle);
        }
        
        wp_enqueue_script('combined-js', 
                         content_url('cache/wp-architizer/js/' . $cache_key . '.js'), 
                         array(), 
                         null, 
                         true);
    }

    private function minify_css($css) {
        // 移除注释
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        // 移除空格
        $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
        return $css;
    }

    private function minify_js($js) {
        if (class_exists('JSMin')) {
            return JSMin::minify($js);
        }
        // 基础压缩
        $js = preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\)\/\/[^\n]*))/', '', $js);
        $js = str_replace(array("\r\n", "\r", "\n", "\t"), '', $js);
        return $js;
    }

    public function optimize_uploaded_image($file) {
        if (!function_exists('imagecreatefromjpeg')) {
            return $file;
        }
        
        $image_path = $file['file'];
        $image_type = wp_check_filetype($image_path);
        
        switch ($image_type['type']) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($image_path);
                imagejpeg($image, $image_path, 85);
                break;
            case 'image/png':
                $image = imagecreatefrompng($image_path);
                imagepng($image, $image_path, 8);
                break;
        }
        
        if (isset($image)) {
            imagedestroy($image);
        }
        
        return $file;
    }

    public function add_lazy_loading($attr, $attachment, $size) {
        if (!is_admin()) {
            $attr['loading'] = 'lazy';
            $attr['data-src'] = $attr['src'];
            $attr['src'] = 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 1 1\'%3E%3C/svg%3E';
            $attr['class'] .= ' lazy';
        }
        return $attr;
    }

    public function cleanup_database() {
        global $wpdb;
        
        // 清理��订版本
        $wpdb->query("DELETE FROM $wpdb->posts WHERE post_type = 'revision'");
        
        // 清理自动草稿
        $wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'auto-draft'");
        
        // 清理垃圾评论
        $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'");
        
        // 清理孤立的元数据
        $wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id NOT IN (SELECT id FROM $wpdb->posts)");
        $wpdb->query("DELETE FROM $wpdb->term_relationships WHERE object_id NOT IN (SELECT id FROM $wpdb->posts)");
        
        // 优化数据表
        $tables = $wpdb->get_results("SHOW TABLES LIKE '$wpdb->prefix%'");
        foreach ($tables as $table) {
            $table = array_values(get_object_vars($table))[0];
            $wpdb->query("OPTIMIZE TABLE $table");
        }
    }

    public function maybe_serve_cache() {
        if (is_admin() || is_user_logged_in() || is_404() || is_search()) {
            return;
        }
        
        $cache_file = $this->get_cache_file();
        
        if (file_exists($cache_file) && time() - filemtime($cache_file) < 3600) {
            readfile($cache_file);
            exit;
        }
    }

    private function get_cache_file() {
        $url_key = md5($_SERVER['REQUEST_URI']);
        return $this->cache_dir . '/pages/' . $url_key . '.html';
    }

    public function start_page_profiling() {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }
        
        $this->profiling_start = microtime(true);
        $this->queries_start = get_num_queries();
        $this->memory_start = memory_get_usage();
    }

    public function end_page_profiling() {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }
        
        $time = microtime(true) - $this->profiling_start;
        $queries = get_num_queries() - $this->queries_start;
        $memory = memory_get_usage() - $this->memory_start;
        
        if (!is_admin()) {
            echo "\n<!-- 性能统计:\n";
            echo sprintf("加载时间: %.3f秒\n", $time);
            echo sprintf("数据库查询: %d\n", $queries);
            echo sprintf("内存使用: %.2f MB\n", $memory / 1024 / 1024);
            echo "-->";
        }
    }
}

// 初始化性能优化模块
new WP_Architizer_Performance(); 