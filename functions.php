<?php
/**
 * architizer functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package architizer
 */

// 定义版本常量
if (!defined('THEME_VERSION')) {
    $theme = wp_get_theme();
    define('THEME_VERSION', $theme->get('Version'));
}

if (!defined('_S_VERSION')) {
    define('_S_VERSION', '1.0.0');
}

/**
 * 主题基础设置
 */
function architizer_setup() {
    // 将多语言支持移动到 init 钩子
    add_action('init', 'architizer_load_theme_textdomain');

    // RSS Feed支持
    add_theme_support('automatic-feed-links');

    // 标题标签支持
    add_theme_support('title-tag');

    // 特色图片支持
    add_theme_support('post-thumbnails');

    // 注册导航菜单
    register_nav_menus(array(
        'menu-1' => esc_html__('Primary', 'architizer'),
        'footer' => esc_html__('页脚菜单', 'architizer'),
    ));

    // HTML5支持
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // 自定义背景支持
    add_theme_support('custom-background', apply_filters('architizer_custom_background_args', array(
        'default-color' => 'ffffff',
        'default-image' => '',
    )));

    // 小工具选择性刷新支持
    add_theme_support('customize-selective-refresh-widgets');

    // 自定义Logo支持
    add_theme_support('custom-logo', array(
        'height'      => 250,
        'width'       => 250,
        'flex-width'  => true,
        'flex-height' => true,
    ));
}
add_action('after_setup_theme', 'architizer_setup');

// 注册导航菜单
function architizer_register_menus() {
    register_nav_menus(array(
        'primary' => '主导航菜单',
        'category' => '分类导航菜单'
    ));
}
add_action('init', 'architizer_register_menus');
/**
 * 设置内容宽度
 */
function architizer_content_width() {
    $GLOBALS['content_width'] = apply_filters('architizer_content_width', 640);
}
add_action('after_setup_theme', 'architizer_content_width', 0);

/**
 * 注册小工具区域
 */
function architizer_widgets_init() {
    register_sidebar(array(
        'name'          => esc_html__('Sidebar', 'architizer'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Add widgets here.', 'architizer'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'architizer_widgets_init');

/**
 * 基础资源加载
 */
function wp_architizer_scripts() {
    $version = defined('WP_DEBUG') && WP_DEBUG ? time() : THEME_VERSION;
    
    if (WP_DEBUG) {
        // 开发环境
        wp_enqueue_style('architizer-header', 
            get_template_directory_uri() . '/assets/css/header.css', 
            array(), 
            $version
        );
        
        wp_enqueue_script('architizer-header', 
            get_template_directory_uri() . '/assets/js/src/header.js', 
            array('jquery'), 
            $version,
            false  // 头部加载
        );
        
        // 其他开发环境资源...
        
    } else {
        // 生产环境
        wp_enqueue_style('architizer-combined', 
            get_template_directory_uri() . '/assets/dist/combined.min.css', 
            array(), 
            $version
        );
        
        wp_enqueue_script('architizer-combined', 
            get_template_directory_uri() . '/assets/dist/combined.min.js', 
            array('jquery'), 
            $version,
            true
        );
    }

    // AJAX 配置
    wp_localize_script('jquery', 'wpAjax', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('architizer-ajax-nonce')
    ));
}

add_action('wp_enqueue_scripts', 'wp_architizer_scripts');
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
            'not_found_in_trash' => '回收站中未找到项目'
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
            'singular_name'     => '项目分类',
            'search_items'      => '搜索项目分类',
            'all_items'         => '所有项目分类',
            'parent_item'       => '父级项目分',
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
 * AJAX 相关功能
 */
function wp_architizer_enqueue_ajax_scripts() {
    if (is_archive() || is_home()) {
        wp_enqueue_script('architizer-ajax-load', 
            get_template_directory_uri() . '/assets/js/ajax-load-more.js', 
            array('jquery'), 
            THEME_VERSION, 
            true
        );
        
        wp_localize_script('architizer-ajax-load', 'wpAjax', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('load_more_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'wp_architizer_enqueue_ajax_scripts', 15);

/**
 * 筛选功能
 */
function wp_architizer_enqueue_filter_scripts() {
    if (is_tax() || is_archive()) {
        wp_enqueue_script('architizer-filter', 
            get_template_directory_uri() . '/assets/js/filter-enhancement.js',
            array('architizer-ajax-load'),
            THEME_VERSION,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'wp_architizer_enqueue_filter_scripts', 20);
/**
 * 交互效果和图片预览功能
 */
function wp_architizer_enqueue_interaction_scripts() {
    wp_enqueue_script('architizer-interactions',
        get_template_directory_uri() . '/assets/js/src/interactions.js',
        array('jquery'),
        THEME_VERSION,
        true
    );
}
add_action('wp_enqueue_scripts', 'wp_architizer_enqueue_interaction_scripts', 25);

function wp_architizer_enqueue_image_preview_scripts() {
    if (is_singular()) {
        wp_enqueue_script('architizer-image-preview',
            get_template_directory_uri() . '/assets/js/src/image-preview.js',
            array('jquery'),
            THEME_VERSION,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'wp_architizer_enqueue_image_preview_scripts', 30);

/**
 * 社交分享功能
 */
function wp_architizer_enqueue_social_share_scripts() {
    if (is_singular()) {
        wp_enqueue_style('font-awesome', 
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'
        );
        
        wp_enqueue_script('qrcode-js',
            'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js',
            array(),
            '1.0.0',
            true
        );
        
        wp_enqueue_script('architizer-social-share',
            get_template_directory_uri() . '/assets/js/src/social-share.js',
            array('qrcode-js'),
            THEME_VERSION,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'wp_architizer_enqueue_social_share_scripts', 35);

// 加载样式和脚本
function architizer_enqueue_scripts() {
    // 加载 Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
    
    // 加载自定义样式
    wp_enqueue_style('architizer-header', get_template_directory_uri() . '/assets/css/header.css');
    
    // 加载自定义脚本
    wp_enqueue_script('architizer-header', get_template_directory_uri() . '/assets/js/src/header.js', array(), '1.0', true);

    // 注册并加载 home.js
    wp_enqueue_script(
        'architizer-home',
        get_template_directory_uri() . '/assets/js/src/home.js',
        array('jquery'),  // 依赖 jQuery
        '1.0.0',         // 版本号
        true             // 在页面底部加
    );

    // 加载合并后的 JS 文件
    wp_enqueue_script(
        'architizer-scripts',
        get_template_directory_uri() . '/assets/js/combined.min.js',
        array('jquery'),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'architizer_enqueue_scripts');
/**
 * 修改搜索查询
 */
function wp_architizer_modify_search_query($query) {
    if (!is_admin() && $query->is_search() && $query->is_main_query()) {
        $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : 'all';
        
        if ($post_type !== 'all') {
            $query->set('post_type', $post_type);
            
            // 添加分类筛选
            $tax_query = array();
            
            if ($post_type === 'project' && !empty($_GET['project_category'])) {
                $tax_query[] = array(
                    'taxonomy' => 'project_category',
                    'field' => 'slug',
                    'terms' => $_GET['project_category']
                );
            }
            
            if ($post_type === 'product' && !empty($_GET['product_category'])) {
                $tax_query[] = array(
                    'taxonomy' => 'product_category',
                    'field' => 'slug',
                    'terms' => $_GET['product_category']
                );
            }
            
            if (!empty($tax_query)) {
                $query->set('tax_query', $tax_query);
            }
            
            // 添加元数据筛选
            $meta_query = array();
            
            if ($post_type === 'project' && !empty($_GET['project_location'])) {
                $meta_query[] = array(
                    'key' => 'project_location',
                    'value' => $_GET['project_location'],
                    'compare' => 'LIKE'
                );
            }
            
            if ($post_type === 'firm' && !empty($_GET['firm_location'])) {
                $meta_query[] = array(
                    'key' => 'firm_location',
                    'value' => $_GET['firm_location'],
                    'compare' => 'LIKE'
                );
            }
            
            if (!empty($meta_query)) {
                $query->set('meta_query', $meta_query);
            }
        } else {
            $query->set('post_type', array('project', 'firm', 'product'));
        }
    }
    return $query;
}
add_action('pre_get_posts', 'wp_architizer_modify_search_query');

/**
 * 修改分类页面查询
 */
function wp_architizer_modify_taxonomy_query($query) {
    if (!is_admin() && $query->is_main_query() && is_tax()) {
        $taxonomy = $query->get('taxonomy');
        
        if ($taxonomy === 'project_category') {
            if (!empty($_GET['project_year'])) {
                $query->set('meta_query', array(
                    array(
                        'key' => 'project_year',
                        'value' => $_GET['project_year'],
                        'compare' => '='
                    )
                ));
            }
            
            if (!empty($_GET['project_location'])) {
                $query->set('meta_query', array(
                    array(
                        'key' => 'project_location',
                        'value' => $_GET['project_location'],
                        'compare' => 'LIKE'
                    )
                ));
            }
        }
        
        if ($taxonomy === 'product_category') {
            $meta_query = array();
            
            if (!empty($_GET['manufacturer'])) {
                $meta_query[] = array(
                    'key' => 'manufacturer',
                    'value' => $_GET['manufacturer'],
                    'compare' => 'LIKE'
                );
            }
            
            if (!empty($_GET['price_range'])) {
                $price_range = explode('-', $_GET['price_range']);
                if (count($price_range) === 2) {
                    $meta_query[] = array(
                        'key' => 'price',
                        'value' => array($price_range[0], $price_range[1]),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN'
                    );
                }
            }
            
            if (!empty($meta_query)) {
                $query->set('meta_query', $meta_query);
            }
        }
    }
}
add_action('pre_get_posts', 'wp_architizer_modify_taxonomy_query');
/**
 * 性能优化相关函数
 */
class WP_Architizer_Performance {
    public function __construct() {
        // 资源优化
        add_action('wp_enqueue_scripts', array($this, 'optimize_assets'), 9999);
        add_action('wp_head', array($this, 'add_preload_hints'), 1);
        add_filter('script_loader_tag', array($this, 'add_async_defer_attributes'), 10, 3);
        
        // 图片优化
        add_filter('wp_get_attachment_image_attributes', array($this, 'add_lazy_loading'), 10, 3);
        add_filter('the_content', array($this, 'optimize_content_images'));
        
        // 缓存优化
        add_action('wp_head', array($this, 'add_cache_control'));
        
        // 数据库优化
        add_action('wp_scheduled_auto_draft_delete', array($this, 'cleanup_database'));
    }

    /**
     * 优化资源加载
     */
    public function optimize_assets() {
        if (!is_admin()) {
            wp_dequeue_style('wp-block-library');
            wp_dequeue_style('global-styles');
            
            // 合并和压缩CSS
            wp_enqueue_style(
                'wp-architizer-combined',
                get_template_directory_uri() . '/assets/dist/combined.min.css',
                array(),
                THEME_VERSION
            );
        }
    }

    /**
     * 添加资源预加载提示
     */
    public function add_preload_hints() {
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
        echo '<link rel="preconnect" href="https://cdnjs.cloudflare.com">';
        echo '<link rel="preload" href="' . get_template_directory_uri() . '/assets/fonts/custom-font.woff2" as="font" type="font/woff2" crossorigin>';
    }

    /**
     * 添加异步和延迟加载属性
     */
    public function add_async_defer_attributes($tag, $handle, $src) {
        $async_scripts = array(
            'wp-architizer-social-share',
            'wp-architizer-image-preview',
            'google-analytics'
        );
        
        if (in_array($handle, $async_scripts)) {
            return str_replace(' src', ' async defer src', $tag);
        }
        return $tag;
    }

    /**
     * 添加图片懒加载
     */
    public function add_lazy_loading($attributes, $image, $attachment) {
        if (!is_admin()) {
            $attributes['loading'] = 'lazy';
            $attributes['class'] .= ' lazy-image';
        }
        return $attributes;
    }

    /**
     * 优化文章内容中的图片
     */
    public function optimize_content_images($content) {
        if (!is_admin()) {
            $content = preg_replace_callback('/<img[^>]+>/', function($matches) {
                $img = $matches[0];
                if (strpos($img, 'loading=') === false) {
                    $img = str_replace('<img', '<img loading="lazy"', $img);
                }
                return $img;
            }, $content);
        }
        return $content;
    }

    /**
     * 添加缓存控制头
     */
    public function add_cache_control() {
        if (!is_user_logged_in()) {
            header('Cache-Control: public, max-age=31536000');
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        }
    }

    /**
     * 清理数据库
     */
    public function cleanup_database() {
        global $wpdb;
        $wpdb->query("DELETE FROM $wpdb->posts WHERE post_type = 'revision'");
        $wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'auto-draft'");
        $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'");
        $wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id NOT IN (SELECT id FROM $wpdb->posts)");
        $wpdb->query("DELETE FROM $wpdb->term_relationships WHERE object_id NOT IN (SELECT id FROM $wpdb->posts)");
    }
}

// 初始化性能优化
new WP_Architizer_Performance();

/**
 * 引入必要的文件
 */
require get_template_directory() . '/inc/custom-header.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/post-types.php';
require get_template_directory() . '/inc/taxonomies.php';

// Jetpack 兼容性文件
if (defined('JETPACK__VERSION')) {
    require get_template_directory() . '/inc/jetpack.php';
}
if (!defined('ABSPATH')) {
    exit;
}

// 加载翻译功能
require_once get_template_directory() . '/inc/translations.php';
function wp_architizer_enqueue_styles() {
    $version = defined('WP_DEBUG') && WP_DEBUG ? time() : THEME_VERSION;
    
    // 加载合并后的 CSS
    wp_enqueue_style('wp-architizer-combined', 
        get_template_directory_uri() . '/assets/css/combined.min.css', 
        array(), 
        $version
    );

    // 添加阿里妈妈字体
    wp_enqueue_style('alimama-font', 
        get_template_directory_uri() . '/assets/fonts/custom-font.woff2', 
        array(), 
        null
    );
}

function wp_architizer_enqueue_scripts() {
    $version = defined('WP_DEBUG') && WP_DEBUG ? time() : THEME_VERSION;
    
    // 加载合并后的 JS
    wp_enqueue_script('wp-architizer-combined',
        get_template_directory_uri() . '/assets/js/combined.min.js',
        array('jquery'),
        $version,
        true
    );
}

add_action('wp_enqueue_scripts', 'wp_architizer_enqueue_styles');
add_action('wp_enqueue_scripts', 'wp_architizer_enqueue_scripts');

// 添加特色项目和事务所支持
function architizer_add_meta_boxes() {
    add_meta_box(
        'featured_project',
        '特色项目',
        'architizer_featured_project_callback',
        'project',
        'side'
    );

    add_meta_box(
        'featured_firm',
        '特色事务所',
        'architizer_featured_firm_callback',
        'firm',
        'side'
    );
}
add_action('add_meta_boxes', 'architizer_add_meta_boxes');

// 特色项目回调
function architizer_featured_project_callback($post) {
    $featured = get_post_meta($post->ID, 'featured_project', true);
    ?>
    <label>
        <input type="checkbox" name="featured_project" value="1" <?php checked($featured, '1'); ?>>
        设为特色项目
    </label>
    <?php
}

// 特色事务所回调
function architizer_featured_firm_callback($post) {
    $featured = get_post_meta($post->ID, 'featured_firm', true);
    ?>
    <label>
        <input type="checkbox" name="featured_firm" value="1" <?php checked($featured, '1'); ?>>
        设为特色事务所
    </label>
    <?php
}

// 保存元数据
function architizer_save_meta_boxes($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (isset($_POST['featured_project'])) {
        update_post_meta($post_id, 'featured_project', '1');
    } else {
        delete_post_meta($post_id, 'featured_project');
    }

    if (isset($_POST['featured_firm'])) {
        update_post_meta($post_id, 'featured_firm', '1');
    } else {
        delete_post_meta($post_id, 'featured_firm');
    }
}
add_action('save_post', 'architizer_save_meta_boxes');

function architizer_scripts() {
    // 开发环境使用未压缩版本
    if (WP_DEBUG) {
        // 开发环境: 加载未压缩的独立文件
        wp_enqueue_style('architizer-header', 
            get_template_directory_uri() . '/assets/css/header.css', 
            array(), 
            THEME_VERSION
        );
        
        wp_enqueue_style('architizer-main', 
            get_template_directory_uri() . '/assets/css/main.css', 
            array(), 
            '1.0.0'  // 修改：使用字符串形式的版本号
        );
        
        // 开发环境的 JS 文件
        wp_enqueue_script('architizer-header', 
            get_template_directory_uri() . '/assets/js/src/header.js', 
            array('jquery'), 
            '1.0.0', 
            true
        );
        wp_enqueue_script('architizer-main', 
            get_template_directory_uri() . '/assets/js/main.js', 
            array('jquery'), 
            '1.0.0',  // 修改：使用字符串形式的版本号
            true
        );
        wp_enqueue_script('architizer-search', 
            get_template_directory_uri() . '/assets/js/search.js', 
            array('jquery'), 
            '1.0.0',  // 修改：使用字符串形式的版本号
            true
        );
        wp_enqueue_script('architizer-advanced-search', 
            get_template_directory_uri() . '/assets/js/advanced-search.js', 
            array('jquery'), 
            '1.0.0',  // 修改：使用字符串形式的版本号
            true
        );
    } else {
        // 生产环境使用压缩版本
        wp_enqueue_style('architizer-combined', 
            get_template_directory_uri() . '/assets/dist/combined.min.css', 
            array(), 
            '1.0.0'  // 修改：使用字符串形式的版本号
        );
        wp_enqueue_script('architizer-combined', 
            get_template_directory_uri() . '/assets/dist/combined.min.js', 
            array('jquery'), 
            '1.0.0',  // 修改：使用字符串形式的版本号
            true
        );
    }

    // 添加 AJAX URL
    wp_localize_script('jquery', 'wpAjax', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('architizer-ajax-nonce')
    ));
}
add_action('wp_enqueue_scripts', 'architizer_scripts');

/**
 * 添加管理菜单
 */
function architizer_add_admin_menus() {
    // 添加主题设置菜单
    add_menu_page(
        '主题设置',           // 页面标题
        '主题设置',           // 菜单标题
        'manage_options',     // 所需权限
        'architizer-settings', // 菜单slug
        'architizer_settings_page', // 回调函数
        'dashicons-admin-generic', // 图标
        60                    // 位置
    );

    // 添加子菜单
    add_submenu_page(
        'architizer-settings',    // 父菜单slug
        '常规设置',              // 页面标题
        '常规设置',              // 菜单标题
        'manage_options',         // 所需权限
        'architizer-settings',    // 菜单slug（与父菜单相同）
        'architizer_settings_page' // 回调函数
    );
}
add_action('admin_menu', 'architizer_add_admin_menus');

/**
 * 设置页面回调
 */
function architizer_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('architizer_options');
            do_settings_sections('architizer-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// 注册导航菜单
register_nav_menus(array(
    'primary' => '主导航菜单',
    'mobile' => '移动端菜单'
));

// 添加自定义logo支持
add_theme_support('custom-logo', array(
    'height'      => 30,
    'width'       => 120,
    'flex-height' => true,
    'flex-width'  => true
));

if (!function_exists('is_user_verified')) {
    function is_user_verified() {
        if (!is_user_logged_in()) {
            return false;
        }
        $user_id = get_current_user_id();
        return (bool) get_user_meta($user_id, 'email_verified', true);
    }
}

if (!function_exists('get_verification_url')) {
    function get_verification_url() {
        return add_query_arg('action', 'verify_email', home_url('/'));
    }
}

// 确保菜单位置已正确注册
function register_theme_menus() {
    register_nav_menus(array(
        'header-menu' => __('Header Menu', 'architizer'),
        'mobile' => __('Mobile Menu', 'architizer')
    ));
}
add_action('after_setup_theme', 'register_theme_menus');

function enqueue_icon_fonts() {
    wp_enqueue_style('material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons');
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
}
add_action('wp_enqueue_scripts', 'enqueue_icon_fonts');