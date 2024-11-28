<?php
/**
 * architizer functions and definitions
 */

// 定义版本常量
if (!defined('THEME_VERSION')) {
    $theme = wp_get_theme();
    define('THEME_VERSION', $theme->get('Version'));
}

/**
 * 主题基础设置
 */
function architizer_setup() {
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // 注册菜单位置
    register_nav_menus(array(
        'header-menu' => __('Header Menu', 'architizer'),
        'mobile' => __('Mobile Menu', 'architizer'),
        'footer' => __('Footer Menu', 'architizer'),
    ));
}
add_action('after_setup_theme', 'architizer_setup');

/**
 * 加载主题资源
 */
function architizer_enqueue_assets() {
    $version = defined('WP_DEBUG') && WP_DEBUG ? time() : THEME_VERSION;
    
    // 基础样式
    wp_enqueue_style('architizer-style', get_stylesheet_uri(), array(), $version);
    wp_enqueue_style('architizer-header', get_template_directory_uri() . '/assets/css/header.css', array(), $version);
    
    // 字体图标
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
    wp_enqueue_style('material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons');

    // 脚本
    wp_enqueue_script('jquery');
    wp_enqueue_script('architizer-header', get_template_directory_uri() . '/assets/js/src/header.js', array('jquery'), $version, true);
}
add_action('wp_enqueue_scripts', 'architizer_enqueue_assets');

/**
 * 注册自定义文章类型
 */
function register_custom_post_types() {
    // 注册 Project 文章类型
    register_post_type('project', array(
        'labels' => array(
            'name'               => '项目',
            'singular_name'      => '项目',
            'menu_name'          => '项目',
            'add_new'            => '添加项目',
            'add_new_item'       => '添加新项目',
            'edit_item'          => '编辑项目',
            'new_item'           => '新项目',
            'view_item'          => '查看项目',
            'search_items'       => '搜索项目',
            'not_found'          => '未找到项目',
            'not_found_in_trash' => '回收站中未找到��目'
        ),
        'public'              => true,
        'has_archive'         => true,
        'show_in_menu'        => true,
        'menu_icon'           => 'dashicons-building',
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite'            => array('slug' => 'projects')
    ));

    // 注册 Firm 文章类型
    register_post_type('firm', array(
        'labels' => array(
            'name'               => '公司',
            'singular_name'      => '公司',
            'menu_name'          => '公司',
            'add_new'            => '添加公司',
            'add_new_item'       => '添加新公司',
            'edit_item'          => '编辑公司',
            'new_item'           => '新公司',
            'view_item'          => '查看公司',
            'search_items'       => '搜索公司',
            'not_found'          => '未找到公司',
            'not_found_in_trash' => '回收站中未找到公司'
        ),
        'public'              => true,
        'has_archive'         => true,
        'show_in_menu'        => true,
        'menu_icon'           => 'dashicons-groups',
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite'            => array('slug' => 'firms')
    ));

    // 注册 Product 文章类型
    register_post_type('product', array(
        'labels' => array(
            'name'               => '产品',
            'singular_name'      => '产品',
            'menu_name'          => '产品',
            'add_new'            => '添加产品',
            'add_new_item'       => '添加新产品',
            'edit_item'          => '编辑产品',
            'new_item'           => '新产品',
            'view_item'          => '查看产品',
            'search_items'       => '搜索产品',
            'not_found'          => '未找到产品',
            'not_found_in_trash' => '回收站中未找到产品'
        ),
        'public'              => true,
        'has_archive'         => true,
        'show_in_menu'        => true,
        'menu_icon'           => 'dashicons-products',
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite'            => array('slug' => 'products')
    ));
}
add_action('init', 'register_custom_post_types');

/**
 * 注册自定义分类法
 */
function register_custom_taxonomies() {
    // 项目分类
    register_taxonomy('project_category', 'project', array(
        'labels' => array(
            'name'              => '项目分类',
            'singular_name'     => '��目分类',
            'search_items'      => '搜索项目分类',
            'all_items'         => '所有项目分类',
            'parent_item'       => '父级项目分类',
            'parent_item_colon' => '父级项目分类:',
            'edit_item'         => '编辑项目分类',
            'update_item'       => '更新项目分类',
            'add_new_item'      => '添加新项目分类',
            'new_item_name'     => '新项目分类名称'
        ),
        'hierarchical'      => true,
        'show_ui'          => true,
        'show_admin_column' => true,
        'query_var'        => true,
        'rewrite'          => array('slug' => 'project-category')
    ));

    // 产品分类
    register_taxonomy('product_category', 'product', array(
        'labels' => array(
            'name'              => '产品分类',
            'singular_name'     => '产品分类',
            'search_items'      => '搜索产品分类',
            'all_items'         => '所有产品分类',
            'parent_item'       => '父级产品分类',
            'parent_item_colon' => '父级产品分类:',
            'edit_item'         => '编辑产品分类',
            'update_item'       => '更新产品分类',
            'add_new_item'      => '添加新产品分类',
            'new_item_name'     => '新产品分类名称'
        ),
        'hierarchical'      => true,
        'show_ui'          => true,
        'show_admin_column' => true,
        'query_var'        => true,
        'rewrite'          => array('slug' => 'product-category')
    ));
}
add_action('init', 'register_custom_taxonomies');

/**
 * 修改搜索查询
 */
function architizer_modify_search_query($query) {
    if (!is_admin() && $query->is_search() && $query->is_main_query()) {
        $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : 'all';
        
        if ($post_type !== 'all') {
            $query->set('post_type', $post_type);
        } else {
            $query->set('post_type', array('project', 'firm', 'product'));
        }
    }
    return $query;
}
add_action('pre_get_posts', 'architizer_modify_search_query');

/**
 * 自定义导航菜单 Walker
 */
class Architizer_Nav_Walker extends Walker_Nav_Menu {
    public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        $output .= '<li' . $class_names . '>';
        
        $atts = array();
        $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        $atts['rel']    = !empty($item->xfn) ? $item->xfn : '';
        $atts['href']   = !empty($item->url) ? $item->url : '';
        
        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);
        
        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }
        
        $title = apply_filters('the_title', $item->title, $item->ID);
        
        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= $args->link_before . $title . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}

/**
 * 用户登录时初始化横幅状态
 */
function init_banner_on_login($user_login, $user) {
    delete_user_meta($user->ID, 'banner_shown_this_session');
}
add_action('wp_login', 'init_banner_on_login', 10, 2);

/**
 * 处理横幅显示逻辑
 */
function should_show_banner() {
    if (!is_user_logged_in()) {
        return false;
    }
    
    $user_id = get_current_user_id();
    $banner_shown = get_user_meta($user_id, 'banner_shown_this_session', true);
    
    // 调试输出
    if (WP_DEBUG) {
        error_log('User ID: ' . $user_id);
        error_log('Banner shown: ' . ($banner_shown ? 'yes' : 'no'));
    }
    
    return empty($banner_shown);
}

/**
 * 用户登出时重置横幅状态
 */
function reset_banner_on_logout() {
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        delete_user_meta($user_id, 'banner_shown_this_session');
        
        // 调试输出
        if (WP_DEBUG) {
            error_log('Banner reset for user: ' . $user_id);
        }
    }
}
add_action('wp_logout', 'reset_banner_on_logout');

/**
 * 处理横幅关闭的 AJAX 请求
 */
function handle_banner_close() {
    check_ajax_referer('architizer-ajax-nonce', 'nonce');
    
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        update_user_meta($user_id, 'banner_shown_this_session', '1');
        
        // 调试输出
        if (WP_DEBUG) {
            error_log('Banner closed for user: ' . $user_id);
        }
        
        wp_send_json_success(array('message' => 'Banner state updated'));
    } else {
        wp_send_json_error(array('message' => 'User not logged in'));
    }
}
add_action('wp_ajax_close_banner', 'handle_banner_close');
