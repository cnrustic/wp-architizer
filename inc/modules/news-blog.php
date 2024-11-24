<?php
class WP_Architizer_News_Blog {
    public function __construct() {
        add_action('init', array($this, 'register_post_meta'));
        add_action('add_meta_boxes', array($this, 'add_post_meta_boxes'));
        add_action('save_post', array($this, 'save_post_meta'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_news_scripts'));
        add_filter('the_content', array($this, 'enhance_content'));
        add_shortcode('featured_posts', array($this, 'render_featured_posts'));
        add_shortcode('posts_timeline', array($this, 'render_posts_timeline'));
        add_shortcode('category_showcase', array($this, 'render_category_showcase'));
        
        // 添加阅读时间估算
        add_filter('the_content', array($this, 'add_reading_time'));
        
        // 添加相关文章功能
        add_action('add_meta_boxes', array($this, 'add_related_posts_meta_box'));
        
        // 添加社交分享按钮
        add_filter('the_content', array($this, 'add_social_sharing'));
        
        // 添加文章系列功能
        add_action('init', array($this, 'register_series_taxonomy'));
        
        // 添加高级搜索功能
        add_action('pre_get_posts', array($this, 'enhance_search'));
    }

    public function register_post_meta() {
        register_post_meta('post', 'featured_style', array(
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_text_field'
        ));
        
        register_post_meta('post', 'subtitle', array(
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_text_field'
        ));
        
        register_post_meta('post', 'featured_video', array(
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'esc_url_raw'
        ));
    }

    public function add_post_meta_boxes() {
        add_meta_box(
            'post_advanced_options',
            '文章高级选项',
            array($this, 'render_post_options_meta_box'),
            'post',
            'normal',
            'high'
        );
    }

    public function render_post_options_meta_box($post) {
        wp_nonce_field('post_options_nonce', 'post_options_nonce');
        
        $featured_style = get_post_meta($post->ID, 'featured_style', true);
        $subtitle = get_post_meta($post->ID, 'subtitle', true);
        $featured_video = get_post_meta($post->ID, 'featured_video', true);
        ?>
        <div class="post-options">
            <p>
                <label>特色样式:</label>
                <select name="featured_style">
                    <option value="">默认</option>
                    <option value="large" <?php selected($featured_style, 'large'); ?>>大图</option>
                    <option value="video" <?php selected($featured_style, 'video'); ?>>视频</option>
                    <option value="gallery" <?php selected($featured_style, 'gallery'); ?>>图库</option>
                </select>
            </p>
            
            <p>
                <label>副标题:</label>
                <input type="text" name="subtitle" value="<?php echo esc_attr($subtitle); ?>" class="widefat">
            </p>
            
            <p>
                <label>特色视频URL:</label>
                <input type="url" name="featured_video" value="<?php echo esc_url($featured_video); ?>" class="widefat">
            </p>
            
            <div class="gallery-items">
                <label>图库图片:</label>
                <div class="gallery-grid">
                    <?php
                    $gallery_images = get_post_meta($post->ID, 'gallery_images', true);
                    if ($gallery_images) {
                        foreach ($gallery_images as $image_id) {
                            echo wp_get_attachment_image($image_id, 'thumbnail');
                        }
                    }
                    ?>
                </div>
                <button type="button" class="button add-gallery-images">添加图片</button>
            </div>
        </div>
        <?php
    }

    public function save_post_meta($post_id) {
        if (!isset($_POST['post_options_nonce']) || 
            !wp_verify_nonce($_POST['post_options_nonce'], 'post_options_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        $fields = array(
            'featured_style',
            'subtitle',
            'featured_video'
        );

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }

        if (isset($_POST['gallery_images'])) {
            $gallery_images = array_map('intval', $_POST['gallery_images']);
            update_post_meta($post_id, 'gallery_images', $gallery_images);
        }
    }

    public function enhance_content($content) {
        if (!is_singular('post')) {
            return $content;
        }

        $enhanced_content = '';
        
        // 添加副标题
        $subtitle = get_post_meta(get_the_ID(), 'subtitle', true);
        if ($subtitle) {
            $enhanced_content .= sprintf('<h2 class="post-subtitle">%s</h2>', esc_html($subtitle));
        }
        
        // 添加特色内容
        $featured_style = get_post_meta(get_the_ID(), 'featured_style', true);
        switch ($featured_style) {
            case 'video':
                $video_url = get_post_meta(get_the_ID(), 'featured_video', true);
                if ($video_url) {
                    $enhanced_content .= wp_oembed_get($video_url);
                }
                break;
                
            case 'gallery':
                $gallery_images = get_post_meta(get_the_ID(), 'gallery_images', true);
                if ($gallery_images) {
                    $enhanced_content .= '<div class="post-gallery">';
                    foreach ($gallery_images as $image_id) {
                        $enhanced_content .= wp_get_attachment_image($image_id, 'large');
                    }
                    $enhanced_content .= '</div>';
                }
                break;
        }
        
        return $enhanced_content . $content;
    }

    public function add_reading_time($content) {
        if (!is_singular('post')) {
            return $content;
        }

        $words = str_word_count(strip_tags($content));
        $minutes = ceil($words / 200); // 假设平均阅读速度为每分钟200字
        
        $reading_time = sprintf(
            '<div class="reading-time">预计阅读时间: %d分钟</div>',
            $minutes
        );
        
        return $reading_time . $content;
    }

    public function add_social_sharing($content) {
        if (!is_singular('post')) {
            return $content;
        }

        $share_buttons = '<div class="social-sharing">';
        $share_buttons .= '<h4>分享文章:</h4>';
        
        $url = urlencode(get_permalink());
        $title = urlencode(get_the_title());
        
        $platforms = array(
            'weixin' => array(
                'label' => '微信',
                'icon' => 'fab fa-weixin',
                'url' => '#'
            ),
            'weibo' => array(
                'label' => '微博',
                'icon' => 'fab fa-weibo',
                'url' => sprintf('http://service.weibo.com/share/share.php?url=%s&title=%s', $url, $title)
            ),
            'qzone' => array(
                'label' => 'QQ空间',
                'icon' => 'fab fa-qq',
                'url' => sprintf('https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=%s&title=%s', $url, $title)
            )
        );
        
        foreach ($platforms as $platform => $data) {
            $share_buttons .= sprintf(
                '<a href="%s" class="share-button %s" target="_blank" rel="noopener">
                    <i class="%s"></i>
                    <span>%s</span>
                </a>',
                esc_url($data['url']),
                esc_attr($platform),
                esc_attr($data['icon']),
                esc_html($data['label'])
            );
        }
        
        $share_buttons .= '</div>';
        
        return $content . $share_buttons;
    }

    public function register_series_taxonomy() {
        $labels = array(
            'name' => '文章系列',
            'singular_name' => '系列',
            'search_items' => '搜索系列',
            'all_items' => '所有系列',
            'edit_item' => '编辑系列',
            'update_item' => '更新系列',
            'add_new_item' => '添加新系列',
            'new_item_name' => '新系列名称',
            'menu_name' => '文章系列'
        );

        register_taxonomy('post_series', 'post', array(
            'labels' => $labels,
            'hierarchical' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'series')
        ));
    }

    public function enhance_search($query) {
        if (!is_admin() && $query->is_main_query() && $query->is_search()) {
            $query->set('post_type', array('post'));
            
            // 添加自定义字段搜索
            $search_term = $query->get('s');
            
            $meta_query = array(
                'relation' => 'OR',
                array(
                    'key' => 'subtitle',
                    'value' => $search_term,
                    'compare' => 'LIKE'
                )
            );
            
            $query->set('meta_query', $meta_query);
        }
    }

    public function render_featured_posts($atts) {
        $atts = shortcode_atts(array(
            'count' => 3,
            'category' => '',
            'style' => 'grid'
        ), $atts);

        $query_args = array(
            'post_type' => 'post',
            'posts_per_page' => $atts['count'],
            'meta_key' => 'featured_style',
            'meta_value' => array('large', 'video', 'gallery'),
            'meta_compare' => 'IN'
        );

        if ($atts['category']) {
            $query_args['category_name'] = $atts['category'];
        }

        $featured_posts = new WP_Query($query_args);
        
        ob_start();
        ?>
        <div class="featured-posts <?php echo esc_attr($atts['style']); ?>">
            <?php
            while ($featured_posts->have_posts()) : $featured_posts->the_post();
                $featured_style = get_post_meta(get_the_ID(), 'featured_style', true);
                $subtitle = get_post_meta(get_the_ID(), 'subtitle', true);
                ?>
                <article class="featured-post <?php echo esc_attr($featured_style); ?>">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="post-content">
                        <h2 class="post-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        
                        <?php if ($subtitle) : ?>
                            <div class="post-subtitle"><?php echo esc_html($subtitle); ?></div>
                        <?php endif; ?>
                        
                        <div class="post-meta">
                            <span class="post-date"><?php echo get_the_date(); ?></span>
                            <span class="post-author"><?php the_author(); ?></span>
                            <?php
                            $categories = get_the_category();
                            if ($categories) {
                                echo '<span class="post-categories">';
                                foreach ($categories as $category) {
                                    echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' 
                                         . esc_html($category->name) . '</a>';
                                }
                                echo '</span>';
                            }
                            ?>
                        </div>
                        
                        <div class="post-excerpt">
                            <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                        </div>
                        
                        <a href="<?php the_permalink(); ?>" class="read-more">阅读全文</a>
                    </div>
                </article>
                <?php
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_posts_timeline($atts) {
        $atts = shortcode_atts(array(
            'count' => 10,
            'category' => ''
        ), $atts);

        $query_args = array(
            'post_type' => 'post',
            'posts_per_page' => $atts['count'],
            'orderby' => 'date',
            'order' => 'DESC'
        );

        if ($atts['category']) {
            $query_args['category_name'] = $atts['category'];
        }

        $posts = new WP_Query($query_args);
        
        ob_start();
        ?>
        <div class="posts-timeline">
            <?php
            $current_year = '';
            while ($posts->have_posts()) : $posts->the_post();
                $year = get_the_date('Y');
                
                if ($year !== $current_year) {
                    if ($current_year !== '') {
                        echo '</div>'; // 关闭上一年的容器
                    }
                    $current_year = $year;
                    echo '<div class="timeline-year">';
                    echo '<h3 class="year-label">' . esc_html($year) . '</h3>';
                }
                ?>
                <article class="timeline-post">
                    <div class="post-date">
                        <span class="month"><?php echo get_the_date('M'); ?></span>
                        <span class="day"><?php echo get_the_date('d'); ?></span>
                    </div>
                    
                    <div class="post-content">
                        <h4 class="post-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                        
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <?php the_post_thumbnail('thumbnail'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="post-excerpt">
                            <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                        </div>
                        
                        <a href="<?php the_permalink(); ?>" class="read-more">阅读全文</a>
                    </div>
                </article>
                <?php
            endwhile;
            if ($current_year !== '') {
                echo '</div>'; // 关闭最后一年的容器
            }
            wp_reset_postdata();
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_category_showcase($atts) {
        $atts = shortcode_atts(array(
            'categories' => '',
            'posts_per_category' => 3
        ), $atts);

        $categories = array_filter(array_map('trim', explode(',', $atts['categories'])));
        if (empty($categories)) {
            $categories = get_categories(array('number' => 5));
        } else {
            $categories = get_categories(array(
                'slug' => $categories,
                'orderby' => 'include'
            ));
        }
        
        ob_start();
        ?>
        <div class="category-showcase">
            <?php
            foreach ($categories as $category) :
                $posts = get_posts(array(
                    'category' => $category->term_id,
                    'posts_per_page' => $atts['posts_per_category']
                ));
                ?>
                <div class="category-section">
                    <h3 class="category-title">
                        <a href="<?php echo get_category_link($category->term_id); ?>">
                            <?php echo esc_html($category->name); ?>
                        </a>
                    </h3>
                    
                    <div class="category-posts">
                        <?php
                        foreach ($posts as $post) :
                            setup_postdata($post);
                            ?>
                            <article class="category-post">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="post-thumbnail">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="post-content">
                                    <h4 class="post-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h4>
                                    
                                    <div class="post-meta">
                                        <span class="post-date"><?php echo get_the_date(); ?></span>
                                    </div>
                                    
                                    <div class="post-excerpt">
                                        <?php echo wp_trim_words(get_the_excerpt(), 10); ?>
                                    </div>
                                </div>
                            </article>
                            <?php
                        endforeach;
                        wp_reset_postdata();
                        ?>
                    </div>
                    
                    <a href="<?php echo get_category_link($category->term_id); ?>" class="view-all">
                        查看更多 <?php echo esc_html($category->name); ?> 的文章
                    </a>
                </div>
                <?php
            endforeach;
            ?>
        </div>
        <?php
        return ob_get_clean();
    }
}

// 初始化新闻/博客模块
new WP_Architizer_News_Blog(); 