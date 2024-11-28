<?php
/**
 * 主题核心功能增强
 * 
 * @package Architizer
 */

class Architizer_Template_Functions {
    /**
     * 初始化
     */
    public function __construct() {
        add_filter('body_class', array($this, 'body_classes'));
        add_action('wp_head', array($this, 'add_meta_tags'));
        add_filter('excerpt_length', array($this, 'custom_excerpt_length'));
    }

    /**
     * 添加自定义 body 类
     */
    public function body_classes($classes) {
        // 非单页添加 hfeed 类
        if (!is_singular()) {
            $classes[] = 'hfeed';
        }

        // 无侧边栏时添加 no-sidebar 类
        if (!is_active_sidebar('sidebar-1')) {
            $classes[] = 'no-sidebar';
        }

        // 根据页面类型添加特定类
        if (is_singular('project')) {
            $classes[] = 'single-project-page';
            $view_mode = get_post_meta(get_the_ID(), 'project_view_mode', true);
            if ($view_mode) {
                $classes[] = 'view-mode-' . $view_mode;
            }
        }

        return $classes;
    }

    /**
     * 统计事务所项目数量
     */
    public static function count_firm_projects($firm_id) {
        static $cache = array();
        
        if (isset($cache[$firm_id])) {
            return $cache[$firm_id];
        }

        $args = array(
            'post_type' => 'project',
            'meta_query' => array(
                array(
                    'key' => 'project_firm',
                    'value' => $firm_id,
                    'compare' => '='
                )
            ),
            'posts_per_page' => -1,
            'fields' => 'ids' // 优化查询性能
        );

        $query = new WP_Query($args);
        $cache[$firm_id] = $query->found_posts;
        
        return $cache[$firm_id];
    }

    /**
     * 获取项目统计信息
     */
    public static function get_project_stats($project_id) {
        return array(
            'views' => self::get_post_views($project_id),
            'likes' => self::get_post_likes($project_id),
            'comments' => get_comments_number($project_id)
        );
    }

    /**
     * 获取文章浏览量
     */
    private static function get_post_views($post_id) {
        $count_key = 'post_views_count';
        $count = get_post_meta($post_id, $count_key, true);
        return empty($count) ? 0 : $count;
    }

    /**
     * 获取文章点赞数
     */
    private static function get_post_likes($post_id) {
        $count_key = 'post_likes_count';
        $count = get_post_meta($post_id, $count_key, true);
        return empty($count) ? 0 : $count;
    }

    /**
     * 自定义摘要长度
     */
    public function custom_excerpt_length($length) {
        return 30;
    }
}

// 初始化
new Architizer_Template_Functions();

