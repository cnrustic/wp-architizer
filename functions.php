<?php
/**
 * architizer functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package architizer
 */
if (!defined('THEME_VERSION')) {
    $theme = wp_get_theme();
    define('THEME_VERSION', $theme->get('Version'));
}
if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function architizer_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on architizer, use a find and replace
		* to change 'architizer' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'architizer', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'architizer' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'architizer_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'architizer_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function architizer_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'architizer_content_width', 640 );
}
add_action( 'after_setup_theme', 'architizer_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function architizer_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'architizer' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'architizer' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'architizer_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function architizer_scripts() {
    // 开发模式判断
    $is_development = defined('WP_DEBUG') && WP_DEBUG;
    
    if ($is_development) {
        // 开发模式：加载独立文件
        wp_enqueue_style(
            'wp-architizer-style',
            get_stylesheet_uri(),
            array(),
            THEME_VERSION
        );

        wp_enqueue_style(
            'wp-architizer-main',
            get_template_directory_uri() . '/assets/css/main.css',
            array('wp-architizer-style'),
            THEME_VERSION
        );
    } else {
        // 生产模式：加载合并文件
        wp_enqueue_style(
            'wp-architizer-combined',
            get_template_directory_uri() . '/assets/dist/combined.min.css',
            array(),
            THEME_VERSION
        );
    }

    // JS文件处理
    if ($is_development) {
        wp_enqueue_script(
            'wp-architizer-navigation',
            get_template_directory_uri() . '/js/navigation.js',
            array(),
            THEME_VERSION,
            true
        );
    } else {
        wp_enqueue_script(
            'wp-architizer-combined',
            get_template_directory_uri() . '/assets/dist/combined.min.js',
            array(),
            THEME_VERSION,
            true
        );
    }

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action( 'wp_enqueue_scripts', 'architizer_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

function wp_architizer_setup() {
    // 添加主题支持
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('custom-logo');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    // 注册菜单位置
    register_nav_menus(array(
        'primary' => esc_html__('主菜单', 'wp-architizer'),
        'footer' => esc_html__('页脚菜单', 'wp-architizer'),
    ));
}
add_action('after_setup_theme', 'wp_architizer_setup');

// 注册样式和脚本
function wp_architizer_scripts() {
    wp_enqueue_style('wp-architizer-style', get_stylesheet_uri());
    wp_enqueue_style('wp-architizer-main', get_template_directory_uri() . '/assets/css/main.css');
    wp_enqueue_script('wp-architizer-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0.0', true);
    
    // 只在产品详情页加载画廊脚本
    if (is_singular('product')) {
        wp_enqueue_script('wp-architizer-product-gallery', 
            get_template_directory_uri() . '/assets/js/product-gallery.js', 
            array(), 
            '1.0.0', 
            true
        );
    }
    
    wp_enqueue_script('wp-architizer-search', 
        get_template_directory_uri() . '/assets/js/search.js', 
        array('jquery'), 
        '1.0.0', 
        true
    );
}
add_action('wp_enqueue_scripts', 'wp_architizer_scripts');

// 引入自定义文章类
require get_template_directory() . '/inc/post-types.php';

// 引入自定义分类
require get_template_directory() . '/inc/taxonomies.php';

/**
 * 修改搜索查询
 */
function wp_architizer_modify_search_query($query) {
    if (!is_admin() && $query->is_search() && $query->is_main_query()) {
        // 获取搜索类型
        $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : 'all';
        
        if ($post_type !== 'all') {
            $query->set('post_type', $post_type);
            
            // 添加分类筛选
            $tax_query = array();
            
            // 项目分类筛选
            if ($post_type === 'project' && !empty($_GET['project_category'])) {
                $tax_query[] = array(
                    'taxonomy' => 'project_category',
                    'field' => 'slug',
                    'terms' => $_GET['project_category']
                );
            }
            
            // 产品分类筛选
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
            
            // 项目位置筛选
            if ($post_type === 'project' && !empty($_GET['project_location'])) {
                $meta_query[] = array(
                    'key' => 'project_location',
                    'value' => $_GET['project_location'],
                    'compare' => 'LIKE'
                );
            }
            
            // 公司位置筛选
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
        // 获取当前分类法
        $taxonomy = $query->get('taxonomy');
        
        // 项目分类筛选
        if ($taxonomy === 'project_category') {
            // 年份筛选
            if (!empty($_GET['project_year'])) {
                $query->set('meta_query', array(
                    array(
                        'key' => 'project_year',
                        'value' => $_GET['project_year'],
                        'compare' => '='
                    )
                ));
            }
            
            // 位置筛选
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
        
        // 产品分类筛选
        if ($taxonomy === 'product_category') {
            $meta_query = array();
            
            // 制造商筛选
            if (!empty($_GET['manufacturer'])) {
                $meta_query[] = array(
                    'key' => 'manufacturer',
                    'value' => $_GET['manufacturer'],
                    'compare' => 'LIKE'
                );
            }
            
            // 价格区间筛选
            if (!empty($_GET['price_range'])) {
                $price_range = explode('-', $_GET['price_range']);
                if (count($price_range) === 2) {
                    $meta_query[] = array(
                        'key' => 'price',
                        'value' => array($price_range[0], $price_range[1]),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN'
                    );
                } elseif (strpos($_GET['price_range'], '+') !== false) {
                    $min_price = intval($price_range[0]);
                    $meta_query[] = array(
                        'key' => 'price',
                        'value' => $min_price,
                        'type' => 'NUMERIC',
                        'compare' => '>='
                    );
                }
            }
            
            if (!empty($meta_query)) {
                $query->set('meta_query', $meta_query);
            }
        }
        
        // 排序处
        if (!empty($_GET['orderby'])) {
            switch ($_GET['orderby']) {
                case 'title':
                    $query->set('orderby', 'title');
                    $query->set('order', 'ASC');
                    break;
                case 'price':
                    $query->set('orderby', 'meta_value_num');
                    $query->set('meta_key', 'price');
                    $query->set('order', 'ASC');
                    break;
                case 'menu_order':
                    $query->set('orderby', 'menu_order');
                    $query->set('order', 'ASC');
                    break;
                default:
                    $query->set('orderby', 'date');
                    $query->set('order', 'DESC');
            }
        }
    }
}
add_action('pre_get_posts', 'wp_architizer_modify_taxonomy_query');

/**
 * 注册 AJAX 动作
 */
function wp_architizer_register_ajax_actions() {
    add_action('wp_ajax_load_more_posts', 'wp_architizer_load_more_posts');
    add_action('wp_ajax_nopriv_load_more_posts', 'wp_architizer_load_more_posts');
}
add_action('init', 'wp_architizer_register_ajax_actions');

/**
 * AJAX 加载更多内容
 */
function wp_architizer_load_more_posts() {
    // 验证 nonce
    check_ajax_referer('load_more_nonce', 'nonce');
    
    $post_type = $_POST['post_type'] ?? 'post';
    $taxonomy = $_POST['taxonomy'] ?? '';
    $term_id = $_POST['term_id'] ?? '';
    $page = $_POST['page'] ?? 1;
    $posts_per_page = $_POST['posts_per_page'] ?? get_option('posts_per_page');
    
    // 构建查询参数
    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => $posts_per_page,
        'paged' => $page,
        'post_status' => 'publish'
    );
    
    // 添加分类条件
    if ($taxonomy && $term_id) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => $taxonomy,
                'field' => 'term_id',
                'terms' => $term_id
            )
        );
    }
    
    // 添加筛选条件
    if (!empty($_POST['filters'])) {
        $filters = $_POST['filters'];
        
        // 元数据查询
        $meta_query = array();
        
        if ($post_type === 'project') {
            if (!empty($filters['project_year'])) {
                $meta_query[] = array(
                    'key' => 'project_year',
                    'value' => $filters['project_year']
                );
            }
            if (!empty($filters['project_location'])) {
                $meta_query[] = array(
                    'key' => 'project_location',
                    'value' => $filters['project_location'],
                    'compare' => 'LIKE'
                );
            }
        } elseif ($post_type === 'product') {
            // 产品筛选逻辑
            if (!empty($filters['manufacturer'])) {
                $meta_query[] = array(
                    'key' => 'manufacturer',
                    'value' => $filters['manufacturer'],
                    'compare' => 'LIKE'
                );
            }
            if (!empty($filters['price_range'])) {
                $price_range = explode('-', $filters['price_range']);
                if (count($price_range) === 2) {
                    $meta_query[] = array(
                        'key' => 'price',
                        'value' => array($price_range[0], $price_range[1]),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN'
                    );
                }
            }
        }
        
        if (!empty($meta_query)) {
            $args['meta_query'] = $meta_query;
        }
        
        // 排序
        if (!empty($filters['orderby'])) {
            switch ($filters['orderby']) {
                case 'title':
                    $args['orderby'] = 'title';
                    $args['order'] = 'ASC';
                    break;
                case 'price':
                    $args['orderby'] = 'meta_value_num';
                    $args['meta_key'] = 'price';
                    break;
                case 'menu_order':
                    $args['orderby'] = 'menu_order';
                    break;
            }
        }
    }
    
    $query = new WP_Query($args);
    $response = array(
        'success' => true,
        'posts' => array(),
        'has_more' => false
    );
    
    if ($query->have_posts()) {
        ob_start();
        while ($query->have_posts()) {
            $query->the_post();
            // 根据文章类型加载不同的模板
            if ($post_type === 'project') {
                get_template_part('template-parts/content', 'project');
            } elseif ($post_type === 'product') {
                get_template_part('template-parts/content', 'product');
            }
        }
        $response['posts'] = ob_get_clean();
        $response['has_more'] = $page < $query->max_num_pages;
    }
    
    wp_reset_postdata();
    wp_send_json($response);
}

function wp_architizer_enqueue_ajax_scripts() {
    wp_enqueue_script('wp-architizer-ajax-load-more', 
        get_template_directory_uri() . '/assets/js/ajax-load-more.js', 
        array('jquery'), 
        '1.0.0', 
        true
    );
    
    wp_localize_script('wp-architizer-ajax-load-more', 'wpAjax', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('load_more_nonce'),
        'postType' => get_post_type(),
        'taxonomy' => get_query_var('taxonomy'),
        'termId' => get_queried_object_id()
    ));
}
add_action('wp_enqueue_scripts', 'wp_architizer_enqueue_ajax_scripts');

function wp_architizer_enqueue_filter_scripts() {
    if (is_tax() || is_archive()) {
        wp_enqueue_script(
            'wp-architizer-filter-enhancement',
            get_template_directory_uri() . '/assets/js/filter-enhancement.js',
            array('wp-architizer-ajax-load-more'),
            '1.0.0',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'wp_architizer_enqueue_filter_scripts');

function wp_architizer_enqueue_interaction_scripts() {
    wp_enqueue_script(
        'wp-architizer-interactions',
        get_template_directory_uri() . '/assets/js/interactions.js',
        array('wp-architizer-filter-enhancement'),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'wp_architizer_enqueue_interaction_scripts');

/**
 * 处理高级搜索 AJAX 请求
 */
function wp_architizer_advanced_search() {
    check_ajax_referer('advanced_search_nonce', 'nonce');

    $query = sanitize_text_field($_POST['query']);
    $type = sanitize_text_field($_POST['type']);

    $args = array(
        'post_status' => 'publish',
        's' => $query,
        'posts_per_page' => 10
    );

    if ($type !== 'all') {
        $args['post_type'] = $type;
    } else {
        $args['post_type'] = array('project', 'product', 'firm');
    }

    $search_query = new WP_Query($args);
    $results = array();

    if ($search_query->have_posts()) {
        while ($search_query->have_posts()) {
            $search_query->the_post();
            $results[] = array(
                'title' => get_the_title(),
                'url' => get_permalink(),
                'excerpt' => wp_trim_words(get_the_excerpt(), 20),
                'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                'type' => get_post_type()
            );
        }
    }

    wp_reset_postdata();

    wp_send_json_success(array(
        'results' => $results
    ));
}
add_action('wp_ajax_advanced_search', 'wp_architizer_advanced_search');
add_action('wp_ajax_nopriv_advanced_search', 'wp_architizer_advanced_search');

function wp_architizer_enqueue_image_preview_scripts() {
    wp_enqueue_script(
        'wp-architizer-image-preview',
        get_template_directory_uri() . '/assets/js/image-preview.js',
        array('wp-architizer-interactions'),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'wp_architizer_enqueue_image_preview_scripts');

function wp_architizer_enqueue_social_share_scripts() {
    // 加载 Font Awesome
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'
    );
    
    // 加载 QRCode.js
    wp_enqueue_script(
        'qrcode-js',
        'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js',
        array(),
        '1.0.0',
        true
    );
    
    // 加载社交分享脚本
    wp_enqueue_script(
        'wp-architizer-social-share',
        get_template_directory_uri() . '/assets/js/social-share.js',
        array('qrcode-js'),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'wp_architizer_enqueue_social_share_scripts');

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
        // 移除不必要的资源
        wp_dequeue_style('wp-block-library'); // 如果不使用区块编辑器
        wp_dequeue_style('global-styles'); // 如果不使用全局样式
        
        // 合并和压缩CSS
        wp_enqueue_style(
            'wp-architizer-combined',
            get_template_directory_uri() . '/assets/css/combined.min.css',
            array(),
            THEME_VERSION
        );
        
        // 合并和压缩JS
        wp_enqueue_script(
            'wp-architizer-combined',
            get_template_directory_uri() . '/assets/js/combined.min.js',
            array('jquery'),
            THEME_VERSION,
            true
        );
        
        // 条件加载特定页面的资源
        if (is_single()) {
            wp_enqueue_style('wp-architizer-single');
            wp_enqueue_script('wp-architizer-single');
        }
    }

    /**
     * 添加资源预加载提示
     */
    public function add_preload_hints() {
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
        echo '<link rel="preconnect" href="https://cdnjs.cloudflare.com">';
        
        // 预加载关键资源
        echo '<link rel="preload" href="' . get_template_directory_uri() . '/assets/fonts/custom-font.woff2" as="font" type="font/woff2" crossorigin>';
        echo '<link rel="preload" href="' . get_template_directory_uri() . '/assets/css/combined.min.css" as="style">';
    }

    /**
     * 添加异步和延迟加载属性
     */
    public function add_async_defer_attributes($tag, $handle, $src) {
        // 非关键脚本异步加载
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
            
            // 添加模糊占位图
            $attributes['data-src'] = $attributes['src'];
            $attributes['src'] = $this->get_placeholder_image($attachment);
            $attributes['class'] .= ' lazy-image';
        }
        return $attributes;
    }

    /**
     * 生成占位图
     */
    private function get_placeholder_image($attachment) {
        $width = 60;
        $height = 60;
        
        // 生成缩略图并模糊处理
        $thumb = wp_get_attachment_image_src($attachment->ID, array($width, $height));
        
        if ($thumb) {
            return $thumb[0];
        }
        
        return 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $width . ' ' . $height . '"%3E%3C/svg%3E';
    }

    /**
     * 优化文章内容中的图片
     */
    public function optimize_content_images($content) {
        if (!is_admin()) {
            $content = preg_replace_callback('/<img[^>]+>/', function($matches) {
                $img = $matches[0];
                
                // 添加懒加载
                if (strpos($img, 'loading=') === false) {
                    $img = str_replace('<img', '<img loading="lazy"', $img);
                }
                
                // 添加尺寸属性
                if (preg_match('/src="([^"]+)"/', $img, $src)) {
                    $image_path = str_replace(site_url(), ABSPATH, $src[1]);
                    if (file_exists($image_path)) {
                        $size = getimagesize($image_path);
                        if ($size) {
                            $img = preg_replace('/<img/', '<img width="' . $size[0] . '" height="' . $size[1] . '"', $img);
                        }
                    }
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
        
        // 删除修订版本
        $wpdb->query("DELETE FROM $wpdb->posts WHERE post_type = 'revision'");
        
        // 删除自动草稿
        $wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'auto-draft'");
        
        // 清理垃圾评论
        $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'");
        
        // 清理孤立的元数据
        $wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id NOT IN (SELECT id FROM $wpdb->posts)");
        $wpdb->query("DELETE FROM $wpdb->term_relationships WHERE object_id NOT IN (SELECT id FROM $wpdb->posts)");
    }
}

// 初始化性能优化
new WP_Architizer_Performance();

function wp_architizer_combine_css() {
    // 创建缓存目录
    $cache_dir = get_template_directory() . '/assets/cache';
    if (!file_exists($cache_dir)) {
        wp_mkdir_p($cache_dir);
    }

    // CSS文件列表
    $css_files = array(
        get_template_directory() . '/style.css'
        // 如果有其他CSS文件,在这里添加
    );

    // 合并文件路径
    $combined_file = $cache_dir . '/combined.min.css';
    
    // 如果合并文件不存在,则创建
    if (!file_exists($combined_file)) {
        $combined_css = '';
        foreach ($css_files as $file) {
            if (file_exists($file)) {
                $combined_css .= file_get_contents($file) . "\n";
            }
        }
        file_put_contents($combined_file, $combined_css);
    }
}

// 在主题初始化时合并CSS
add_action('after_setup_theme', 'wp_architizer_combine_css');

/**
 * 资源文件合并和压缩
 */
function wp_architizer_build_assets() {
    // 只在生产模式下执行
    if (defined('WP_DEBUG') && WP_DEBUG) {
        return;
    }

    // 创建dist目录
    $dist_dir = get_template_directory() . '/assets/dist';
    if (!file_exists($dist_dir)) {
        wp_mkdir_p($dist_dir);
    }

    // CSS文件列表
    $css_files = array(
        get_template_directory() . '/style.css',
        get_template_directory() . '/assets/css/main.css',
        // 添加其他CSS文件
    );

    // JS文件列表
    $js_files = array(
        get_template_directory() . '/js/navigation.js',
        // 添加其他JS文件
    );

    // 合并和压缩CSS
    $combined_css = '';
    foreach ($css_files as $file) {
        if (file_exists($file)) {
            $css = file_get_contents($file);
            // 简单的CSS压缩
            $css = preg_replace('/\s+/', ' ', $css);
            $css = preg_replace('/\/\*[^*]*\*+([^\/][^*]*\*+)*\//', '', $css);
            $css = str_replace(array(': ', ' {', '{ ', ', ', '} ', ';}'), array(':','{','{',',','}',';}'), $css);
            $combined_css .= $css . "\n";
        }
    }
    file_put_contents($dist_dir . '/combined.min.css', $combined_css);

    // 合并和压缩JS
    $combined_js = '';
    foreach ($js_files as $file) {
        if (file_exists($file)) {
            $js = file_get_contents($file);
            // 这里可以添加JS压缩逻辑
            $combined_js .= $js . ";\n";
        }
    }
    file_put_contents($dist_dir . '/combined.min.js', $combined_js);
}

// 在主题初始化时合并资源文件
add_action('after_setup_theme', 'wp_architizer_build_assets');

// 在主题更新或激活时构建资源
add_action('after_switch_theme', 'wp_architizer_build_assets');
add_action('customize_save_after', 'wp_architizer_build_assets');

// 添加手动构建命令（可以在管理后台添加一个按钮）
function wp_architizer_rebuild_assets() {
    if (current_user_can('manage_options')) {
        wp_architizer_build_assets();
    }
}
add_action('admin_post_rebuild_assets', 'wp_architizer_rebuild_assets');

