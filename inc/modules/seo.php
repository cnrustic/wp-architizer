<?php
class WP_Architizer_SEO {
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('add_meta_boxes', array($this, 'add_seo_meta_boxes'));
        add_action('save_post', array($this, 'save_seo_meta'));
        
        // 前端SEO优化
        add_action('wp_head', array($this, 'output_meta_tags'), 1);
        add_filter('document_title_parts', array($this, 'custom_title'));
        add_filter('pre_get_document_title', array($this, 'custom_title_format'));
        
        // XML站点地图
        add_action('init', array($this, 'register_sitemap'));
        add_action('save_post', array($this, 'update_sitemap'));
        
        // 结构化数据
        add_action('wp_head', array($this, 'output_schema_data'));
        
        // 社交媒体集成
        add_action('wp_head', array($this, 'output_social_meta'));
        
        // 面包屑导航
        add_shortcode('breadcrumbs', array($this, 'render_breadcrumbs'));
        
        // URL优化
        add_filter('permalink_structure', array($this, 'optimize_permalinks'));
        
        // 图片SEO
        add_filter('wp_get_attachment_image_attributes', array($this, 'optimize_image_attributes'), 10, 2);
    }

    public function init() {
        // 注册SEO设置
        register_setting('wp_architizer_seo', 'wp_architizer_seo_options');
        
        // 添加默认选项
        $default_options = array(
            'title_separator' => '|',
            'homepage_title' => get_bloginfo('name'),
            'homepage_description' => get_bloginfo('description'),
            'homepage_keywords' => '',
            'enable_schema' => 'yes',
            'enable_breadcrumbs' => 'yes',
            'noindex_archives' => 'yes',
            'baidu_verification' => '',
            'google_verification' => ''
        );
        
        add_option('wp_architizer_seo_options', $default_options);
    }

    public function add_admin_menu() {
        add_menu_page(
            'SEO设置',
            'SEO设置',
            'manage_options',
            'wp-architizer-seo',
            array($this, 'render_settings_page'),
            'dashicons-chart-line',
            80
        );
        
        add_submenu_page(
            'wp-architizer-seo',
            '常规设置',
            '常规设置',
            'manage_options',
            'wp-architizer-seo'
        );
        
        add_submenu_page(
            'wp-architizer-seo',
            '社交媒体',
            '社交媒体',
            'manage_options',
            'wp-architizer-seo-social',
            array($this, 'render_social_settings_page')
        );
        
        add_submenu_page(
            'wp-architizer-seo',
            '站点地图',
            '站点地图',
            'manage_options',
            'wp-architizer-seo-sitemap',
            array($this, 'render_sitemap_settings_page')
        );
    }

    public function add_seo_meta_boxes() {
        $post_types = get_post_types(array('public' => true));
        
        foreach ($post_types as $post_type) {
            add_meta_box(
                'wp_architizer_seo_meta',
                'SEO设置',
                array($this, 'render_seo_meta_box'),
                $post_type,
                'normal',
                'high'
            );
        }
    }

    public function render_seo_meta_box($post) {
        wp_nonce_field('wp_architizer_seo_meta', 'wp_architizer_seo_nonce');
        
        $meta = get_post_meta($post->ID);
        $seo_title = isset($meta['_seo_title']) ? $meta['_seo_title'][0] : '';
        $seo_description = isset($meta['_seo_description']) ? $meta['_seo_description'][0] : '';
        $seo_keywords = isset($meta['_seo_keywords']) ? $meta['_seo_keywords'][0] : '';
        $seo_noindex = isset($meta['_seo_noindex']) ? $meta['_seo_noindex'][0] : '';
        
        ?>
        <div class="wp-architizer-seo-meta">
            <p>
                <label for="seo_title">SEO标题:</label>
                <input type="text" id="seo_title" name="seo_title" 
                       value="<?php echo esc_attr($seo_title); ?>" class="widefat">
                <span class="description">如果留空，将使用文章标题</span>
            </p>
            
            <p>
                <label for="seo_description">Meta描述:</label>
                <textarea id="seo_description" name="seo_description" 
                          rows="3" class="widefat"><?php echo esc_textarea($seo_description); ?></textarea>
                <span class="description">建议长度：120-160个字符</span>
            </p>
            
            <p>
                <label for="seo_keywords">关键词:</label>
                <input type="text" id="seo_keywords" name="seo_keywords" 
                       value="<?php echo esc_attr($seo_keywords); ?>" class="widefat">
                <span class="description">用逗号分隔多个关键词</span>
            </p>
            
            <p>
                <label>
                    <input type="checkbox" name="seo_noindex" value="1" 
                           <?php checked($seo_noindex, '1'); ?>>
                    在搜索引擎中隐藏此页面
                </label>
            </p>
            
            <div class="seo-preview">
                <h4>搜索结果预览</h4>
                <div class="preview-title"></div>
                <div class="preview-url"></div>
                <div class="preview-description"></div>
            </div>
        </div>
        <?php
    }

    public function save_seo_meta($post_id) {
        if (!isset($_POST['wp_architizer_seo_nonce']) || 
            !wp_verify_nonce($_POST['wp_architizer_seo_nonce'], 'wp_architizer_seo_meta')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        $fields = array(
            'seo_title',
            'seo_description',
            'seo_keywords',
            'seo_noindex'
        );
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta(
                    $post_id,
                    '_' . $field,
                    sanitize_text_field($_POST[$field])
                );
            }
        }
    }

    public function output_meta_tags() {
        global $post;
        
        // 基础meta标签
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";
        
        if (is_singular()) {
            $meta_description = get_post_meta($post->ID, '_seo_description', true);
            if (empty($meta_description)) {
                $meta_description = wp_trim_words(strip_tags($post->post_content), 30);
            }
            
            $meta_keywords = get_post_meta($post->ID, '_seo_keywords', true);
            if (empty($meta_keywords)) {
                $tags = get_the_tags($post->ID);
                if ($tags) {
                    $keywords = array();
                    foreach ($tags as $tag) {
                        $keywords[] = $tag->name;
                    }
                    $meta_keywords = implode(',', $keywords);
                }
            }
            
            if ($meta_description) {
                echo '<meta name="description" content="' . esc_attr($meta_description) . '">' . "\n";
            }
            
            if ($meta_keywords) {
                echo '<meta name="keywords" content="' . esc_attr($meta_keywords) . '">' . "\n";
            }
            
            // 规范链接
            echo '<link rel="canonical" href="' . esc_url(get_permalink()) . '">' . "\n";
        }
        
        // 站点验证
        $options = get_option('wp_architizer_seo_options');
        if (!empty($options['baidu_verification'])) {
            echo '<meta name="baidu-site-verification" content="' . 
                  esc_attr($options['baidu_verification']) . '">' . "\n";
        }
        if (!empty($options['google_verification'])) {
            echo '<meta name="google-site-verification" content="' . 
                  esc_attr($options['google_verification']) . '">' . "\n";
        }
    }

    public function output_schema_data() {
        if (!is_singular()) {
            return;
        }
        
        global $post;
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title(),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author()
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_site_icon_url()
                )
            )
        );
        
        if (has_post_thumbnail()) {
            $schema['image'] = array(
                '@type' => 'ImageObject',
                'url' => get_the_post_thumbnail_url($post, 'full')
            );
        }
        
        echo '<script type="application/ld+json">' . 
             wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . 
             '</script>' . "\n";
    }

    public function render_breadcrumbs($atts) {
        $atts = shortcode_atts(array(
            'separator' => '›',
            'home_text' => '首页'
        ), $atts);
        
        if (!is_front_page()) {
            $breadcrumbs = array();
            $breadcrumbs[] = array(
                'url' => home_url(),
                'text' => $atts['home_text']
            );
            
            if (is_singular()) {
                $post_type = get_post_type();
                if ($post_type !== 'post') {
                    $post_type_obj = get_post_type_object($post_type);
                    $breadcrumbs[] = array(
                        'url' => get_post_type_archive_link($post_type),
                        'text' => $post_type_obj->labels->name
                    );
                }
                
                $categories = get_the_category();
                if ($categories) {
                    $category = $categories[0];
                    $breadcrumbs[] = array(
                        'url' => get_category_link($category->term_id),
                        'text' => $category->name
                    );
                }
                
                $breadcrumbs[] = array(
                    'url' => '',
                    'text' => get_the_title()
                );
            } elseif (is_archive()) {
                $breadcrumbs[] = array(
                    'url' => '',
                    'text' => get_the_archive_title()
                );
            }
            
            ob_start();
            ?>
            <nav class="breadcrumbs" aria-label="面包屑导航">
                <?php
                $count = count($breadcrumbs);
                foreach ($breadcrumbs as $i => $item) {
                    if ($i > 0) {
                        echo ' <span class="separator">' . esc_html($atts['separator']) . '</span> ';
                    }
                    
                    if ($item['url']) {
                        echo '<a href="' . esc_url($item['url']) . '">' . 
                             esc_html($item['text']) . '</a>';
                    } else {
                        echo '<span class="current">' . esc_html($item['text']) . '</span>';
                    }
                }
                ?>
            </nav>
            <?php
            return ob_get_clean();
        }
        
        return '';
    }
}

// 初始化SEO模块
new WP_Architizer_SEO(); 