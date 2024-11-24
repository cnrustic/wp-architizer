<?php
class WP_Architizer_Testimonials {
    public function __construct() {
        add_action('init', array($this, 'register_testimonial_post_type'));
        add_action('init', array($this, 'register_testimonial_taxonomies'));
        add_action('add_meta_boxes', array($this, 'add_testimonial_meta_boxes'));
        add_action('save_post_testimonial', array($this, 'save_testimonial_meta'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_testimonial_scripts'));
        add_shortcode('testimonials_grid', array($this, 'render_testimonials_grid'));
        add_shortcode('testimonials_slider', array($this, 'render_testimonials_slider'));
        add_shortcode('testimonial_card', array($this, 'render_testimonial_card'));
    }

    public function register_testimonial_post_type() {
        $labels = array(
            'name' => '客户评价',
            'singular_name' => '客户评价',
            'menu_name' => '客户评价',
            'add_new' => '添加评价',
            'add_new_item' => '添加新评价',
            'edit_item' => '编辑评价',
            'new_item' => '新评价',
            'view_item' => '查看评价',
            'search_items' => '搜索评价',
            'not_found' => '未找到评价',
            'not_found_in_trash' => '回收站中未找到评价'
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-format-quote',
            'supports' => array('title', 'editor', 'thumbnail'),
            'rewrite' => array('slug' => 'testimonials'),
            'show_in_rest' => true,
            'menu_position' => 7
        );

        register_post_type('testimonial', $args);
    }

    public function register_testimonial_taxonomies() {
        // 注册项目类型分类
        register_taxonomy('testimonial_type', 'testimonial', array(
            'labels' => array(
                'name' => '评价类型',
                'singular_name' => '评价类型',
                'search_items' => '搜索类型',
                'all_items' => '所有类型',
                'edit_item' => '编辑类型',
                'update_item' => '更新类型',
                'add_new_item' => '添加新类型',
                'new_item_name' => '新类型名称'
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'testimonial-type')
        ));
    }

    public function add_testimonial_meta_boxes() {
        add_meta_box(
            'testimonial_details',
            '评价详情',
            array($this, 'render_testimonial_details_meta_box'),
            'testimonial',
            'normal',
            'high'
        );
    }

    public function render_testimonial_details_meta_box($post) {
        wp_nonce_field('testimonial_details_nonce', 'testimonial_details_nonce');
        
        $testimonial_meta = get_post_meta($post->ID);
        ?>
        <div class="testimonial-details">
            <p>
                <label>客户名称:</label>
                <input type="text" name="client_name" 
                       value="<?php echo esc_attr($testimonial_meta['client_name'][0] ?? ''); ?>" class="widefat">
            </p>
            
            <p>
                <label>公司/职位:</label>
                <input type="text" name="client_company" 
                       value="<?php echo esc_attr($testimonial_meta['client_company'][0] ?? ''); ?>" class="widefat">
            </p>
            
            <p>
                <label>项目名称:</label>
                <input type="text" name="project_name" 
                       value="<?php echo esc_attr($testimonial_meta['project_name'][0] ?? ''); ?>" class="widefat">
            </p>
            
            <p>
                <label>评分 (1-5):</label>
                <input type="number" name="rating" min="1" max="5" 
                       value="<?php echo esc_attr($testimonial_meta['rating'][0] ?? '5'); ?>">
            </p>
            
            <p>
                <label>视频证言链接:</label>
                <input type="url" name="video_testimonial" 
                       value="<?php echo esc_url($testimonial_meta['video_testimonial'][0] ?? ''); ?>" class="widefat">
            </p>
        </div>
        <?php
    }

    public function save_testimonial_meta($post_id) {
        if (!isset($_POST['testimonial_details_nonce']) || 
            !wp_verify_nonce($_POST['testimonial_details_nonce'], 'testimonial_details_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        $fields = array(
            'client_name',
            'client_company',
            'project_name',
            'rating',
            'video_testimonial'
        );

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                if ($field === 'rating') {
                    $rating = max(1, min(5, intval($_POST[$field])));
                    update_post_meta($post_id, $field, $rating);
                } elseif ($field === 'video_testimonial') {
                    update_post_meta($post_id, $field, esc_url_raw($_POST[$field]));
                } else {
                    update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
                }
            }
        }
    }

    public function enqueue_testimonial_scripts() {
        if (is_singular('testimonial') || has_shortcode(get_the_content(), 'testimonials_slider')) {
            wp_enqueue_style(
                'testimonials-style',
                get_template_directory_uri() . '/assets/css/testimonials.css',
                array(),
                '1.0.0'
            );
            
            wp_enqueue_script(
                'testimonials-script',
                get_template_directory_uri() . '/assets/js/testimonials.js',
                array('jquery'),
                '1.0.0',
                true
            );
        }
    }

    public function render_testimonials_grid($atts) {
        $atts = shortcode_atts(array(
            'columns' => 3,
            'posts_per_page' => -1,
            'type' => '',
            'orderby' => 'date',
            'order' => 'DESC'
        ), $atts);

        $query_args = array(
            'post_type' => 'testimonial',
            'posts_per_page' => $atts['posts_per_page'],
            'orderby' => $atts['orderby'],
            'order' => $atts['order']
        );

        if ($atts['type']) {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'testimonial_type',
                    'field' => 'slug',
                    'terms' => explode(',', $atts['type'])
                )
            );
        }

        $testimonials = new WP_Query($query_args);
        
        ob_start();
        ?>
        <div class="testimonials-grid columns-<?php echo esc_attr($atts['columns']); ?>">
            <?php
            while ($testimonials->have_posts()) : $testimonials->the_post();
                echo $this->render_testimonial_card(array('post_id' => get_the_ID()));
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_testimonials_slider($atts) {
        $atts = shortcode_atts(array(
            'posts_per_page' => 5,
            'type' => '',
            'style' => 'default' // default, modern, minimal
        ), $atts);

        $query_args = array(
            'post_type' => 'testimonial',
            'posts_per_page' => $atts['posts_per_page']
        );

        if ($atts['type']) {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'testimonial_type',
                    'field' => 'slug',
                    'terms' => explode(',', $atts['type'])
                )
            );
        }

        $testimonials = new WP_Query($query_args);
        
        ob_start();
        ?>
        <div class="testimonials-slider style-<?php echo esc_attr($atts['style']); ?>">
            <div class="slider-wrapper">
                <?php
                while ($testimonials->have_posts()) : $testimonials->the_post();
                    $client_name = get_post_meta(get_the_ID(), 'client_name', true);
                    $client_company = get_post_meta(get_the_ID(), 'client_company', true);
                    $rating = get_post_meta(get_the_ID(), 'rating', true);
                    ?>
                    <div class="slider-item">
                        <div class="testimonial-content">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="client-photo">
                                    <?php the_post_thumbnail('thumbnail'); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="testimonial-text">
                                <?php the_content(); ?>
                            </div>
                            
                            <div class="client-info">
                                <?php if ($rating) : ?>
                                    <div class="rating">
                                        <?php
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo '<span class="star ' . ($i <= $rating ? 'filled' : '') . '">★</span>';
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="client-name"><?php echo esc_html($client_name); ?></div>
                                <?php if ($client_company) : ?>
                                    <div class="client-company"><?php echo esc_html($client_company); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
            
            <div class="slider-controls">
                <button class="slider-nav prev">
                    <span class="dashicons dashicons-arrow-left-alt2"></span>
                </button>
                <div class="slider-dots"></div>
                <button class="slider-nav next">
                    <span class="dashicons dashicons-arrow-right-alt2"></span>
                </button>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_testimonial_card($atts) {
        $atts = shortcode_atts(array(
            'post_id' => get_the_ID()
        ), $atts);

        $client_name = get_post_meta($atts['post_id'], 'client_name', true);
        $client_company = get_post_meta($atts['post_id'], 'client_company', true);
        $project_name = get_post_meta($atts['post_id'], 'project_name', true);
        $rating = get_post_meta($atts['post_id'], 'rating', true);
        $video_url = get_post_meta($atts['post_id'], 'video_testimonial', true);

        ob_start();
        ?>
        <div class="testimonial-card">
            <?php if (has_post_thumbnail($atts['post_id'])) : ?>
                <div class="client-photo">
                    <?php echo get_the_post_thumbnail($atts['post_id'], 'thumbnail'); ?>
                </div>
            <?php endif; ?>
            
            <div class="testimonial-content">
                <?php if ($rating) : ?>
                    <div class="rating">
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            echo '<span class="star ' . ($i <= $rating ? 'filled' : '') . '">★</span>';
                        }
                        ?>
                    </div>
                <?php endif; ?>
                
                <div class="testimonial-text">
                    <?php echo wp_trim_words(get_post_field('post_content', $atts['post_id']), 30); ?>
                </div>
                
                <div class="client-info">
                    <div class="client-name"><?php echo esc_html($client_name); ?></div>
                    <?php if ($client_company) : ?>
                        <div class="client-company"><?php echo esc_html($client_company); ?></div>
                    <?php endif; ?>
                    <?php if ($project_name) : ?>
                        <div class="project-name"><?php echo esc_html($project_name); ?></div>
                    <?php endif; ?>
                </div>
                
                <?php if ($video_url) : ?>
                    <a href="<?php echo esc_url($video_url); ?>" class="video-testimonial-link" target="_blank">
                        观看视频证言
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

// 初始化客户评价模块
new WP_Architizer_Testimonials(); 