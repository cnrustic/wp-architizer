<?php
class WP_Architizer_Projects {
    public function __construct() {
        add_action('init', array($this, 'register_project_post_type'));
        add_action('init', array($this, 'register_project_taxonomies'));
        add_action('add_meta_boxes', array($this, 'add_project_meta_boxes'));
        add_action('save_post_project', array($this, 'save_project_meta'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_project_scripts'));
        add_shortcode('projects_grid', array($this, 'render_projects_grid'));
        add_shortcode('project_slider', array($this, 'render_project_slider'));
    }

    public function register_project_post_type() {
        $labels = array(
            'name' => '项目',
            'singular_name' => '项目',
            'menu_name' => '项目管理',
            'add_new' => '添加项目',
            'add_new_item' => '添加新项目',
            'edit_item' => '编辑项目',
            'new_item' => '新项目',
            'view_item' => '查看项目',
            'search_items' => '搜索项目',
            'not_found' => '未找到项目',
            'not_found_in_trash' => '回收站中未找到项目'
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-portfolio',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'rewrite' => array('slug' => 'projects'),
            'show_in_rest' => true,
            'menu_position' => 5,
            'taxonomies' => array('project_category', 'project_tag')
        );

        register_post_type('project', $args);
    }

    public function register_project_taxonomies() {
        // 注册项目分类
        register_taxonomy('project_category', 'project', array(
            'labels' => array(
                'name' => '项目分类',
                'singular_name' => '项目分类',
                'search_items' => '搜索分类',
                'all_items' => '所有分类',
                'parent_item' => '父级分类',
                'parent_item_colon' => '父级分类:',
                'edit_item' => '编辑分类',
                'update_item' => '更新分类',
                'add_new_item' => '添加新分类',
                'new_item_name' => '新分类名称'
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'project-category')
        ));

        // 注册项目标签
        register_taxonomy('project_tag', 'project', array(
            'labels' => array(
                'name' => '项目标签',
                'singular_name' => '项目标签',
                'search_items' => '搜索标签',
                'all_items' => '所有标签',
                'edit_item' => '编辑标签',
                'update_item' => '更新标签',
                'add_new_item' => '添加新标签',
                'new_item_name' => '新标签名称'
            ),
            'hierarchical' => false,
            'show_ui' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'project-tag')
        ));
    }

    public function add_project_meta_boxes() {
        add_meta_box(
            'project_details',
            '项目详情',
            array($this, 'render_project_details_meta_box'),
            'project',
            'normal',
            'high'
        );

        add_meta_box(
            'project_gallery',
            '项目图库',
            array($this, 'render_project_gallery_meta_box'),
            'project',
            'normal',
            'high'
        );
    }

    public function render_project_details_meta_box($post) {
        wp_nonce_field('project_details_nonce', 'project_details_nonce');
        
        $project_meta = get_post_meta($post->ID);
        ?>
        <div class="project-details">
            <p>
                <label>客户名称:</label>
                <input type="text" name="project_client" 
                       value="<?php echo esc_attr($project_meta['project_client'][0] ?? ''); ?>">
            </p>
            
            <p>
                <label>项目地点:</label>
                <input type="text" name="project_location" 
                       value="<?php echo esc_attr($project_meta['project_location'][0] ?? ''); ?>">
            </p>
            
            <p>
                <label>项目面积:</label>
                <input type="text" name="project_area" 
                       value="<?php echo esc_attr($project_meta['project_area'][0] ?? ''); ?>">
            </p>
            
            <p>
                <label>完成时间:</label>
                <input type="date" name="project_completion_date" 
                       value="<?php echo esc_attr($project_meta['project_completion_date'][0] ?? ''); ?>">
            </p>
            
            <p>
                <label>项目预算:</label>
                <input type="number" name="project_budget" 
                       value="<?php echo esc_attr($project_meta['project_budget'][0] ?? ''); ?>">
            </p>
            
            <p>
                <label>项目状态:</label>
                <select name="project_status">
                    <?php
                    $status = $project_meta['project_status'][0] ?? '';
                    $statuses = array(
                        'planning' => '规划中',
                        'in_progress' => '进行中',
                        'completed' => '已完成'
                    );
                    foreach ($statuses as $value => $label) {
                        printf(
                            '<option value="%s" %s>%s</option>',
                            $value,
                            selected($status, $value, false),
                            $label
                        );
                    }
                    ?>
                </select>
            </p>
        </div>
        <?php
    }

    public function render_project_gallery_meta_box($post) {
        wp_nonce_field('project_gallery_nonce', 'project_gallery_nonce');
        
        $gallery_images = get_post_meta($post->ID, 'project_gallery', true);
        ?>
        <div class="project-gallery">
            <div class="gallery-preview">
                <?php
                if ($gallery_images) {
                    foreach ($gallery_images as $image_id) {
                        echo wp_get_attachment_image($image_id, 'thumbnail');
                    }
                }
                ?>
            </div>
            
            <input type="hidden" name="project_gallery" id="project_gallery" 
                   value="<?php echo esc_attr(implode(',', (array)$gallery_images)); ?>">
            
            <button type="button" class="button add-gallery-images">
                添加图片
            </button>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('.add-gallery-images').click(function(e) {
                e.preventDefault();
                
                var frame = wp.media({
                    title: '选择项目图片',
                    multiple: true,
                    library: {type: 'image'}
                });
                
                frame.on('select', function() {
                    var selection = frame.state().get('selection');
                    var imageIds = [];
                    
                    $('.gallery-preview').empty();
                    
                    selection.each(function(attachment) {
                        imageIds.push(attachment.id);
                        $('.gallery-preview').append(
                            $('<img>', {
                                src: attachment.attributes.sizes.thumbnail.url,
                                'data-id': attachment.id
                            })
                        );
                    });
                    
                    $('#project_gallery').val(imageIds.join(','));
                });
                
                frame.open();
            });
        });
        </script>
        <?php
    }

    public function save_project_meta($post_id) {
        // 验证nonce
        if (!isset($_POST['project_details_nonce']) || 
            !wp_verify_nonce($_POST['project_details_nonce'], 'project_details_nonce')) {
            return;
        }
        
        if (!isset($_POST['project_gallery_nonce']) || 
            !wp_verify_nonce($_POST['project_gallery_nonce'], 'project_gallery_nonce')) {
            return;
        }

        // 保存项目详情
        $meta_fields = array(
            'project_client',
            'project_location',
            'project_area',
            'project_completion_date',
            'project_budget',
            'project_status'
        );

        foreach ($meta_fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta(
                    $post_id,
                    $field,
                    sanitize_text_field($_POST[$field])
                );
            }
        }

        // 保存图库
        if (isset($_POST['project_gallery'])) {
            $gallery_ids = explode(',', $_POST['project_gallery']);
            $gallery_ids = array_map('absint', $gallery_ids);
            update_post_meta($post_id, 'project_gallery', $gallery_ids);
        }
    }

    public function enqueue_project_scripts() {
        if (is_singular('project') || has_shortcode(get_the_content(), 'projects_grid')) {
            wp_enqueue_style(
                'project-styles',
                get_template_directory_uri() . '/assets/css/projects.css',
                array(),
                '1.0.0'
            );
            
            wp_enqueue_script(
                'project-scripts',
                get_template_directory_uri() . '/assets/js/projects.js',
                array('jquery'),
                '1.0.0',
                true
            );
        }
    }

    public function render_projects_grid($atts) {
        $atts = shortcode_atts(array(
            'category' => '',
            'tag' => '',
            'columns' => 3,
            'posts_per_page' => 9
        ), $atts);

        $query_args = array(
            'post_type' => 'project',
            'posts_per_page' => $atts['posts_per_page']
        );

        if ($atts['category']) {
            $query_args['tax_query'][] = array(
                'taxonomy' => 'project_category',
                'field' => 'slug',
                'terms' => explode(',', $atts['category'])
            );
        }

        if ($atts['tag']) {
            $query_args['tax_query'][] = array(
                'taxonomy' => 'project_tag',
                'field' => 'slug',
                'terms' => explode(',', $atts['tag'])
            );
        }

        $projects = new WP_Query($query_args);
        
        ob_start();
        ?>
        <div class="projects-grid columns-<?php echo esc_attr($atts['columns']); ?>">
            <?php
            while ($projects->have_posts()) : $projects->the_post();
                ?>
                <div class="project-item">
                    <div class="project-thumbnail">
                        <?php the_post_thumbnail('large'); ?>
                        <div class="project-overlay">
                            <h3><?php the_title(); ?></h3>
                            <div class="project-meta">
                                <?php
                                $location = get_post_meta(get_the_ID(), 'project_location', true);
                                if ($location) {
                                    echo '<span class="location">' . esc_html($location) . '</span>';
                                }
                                ?>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="project-link">查看详情</a>
                        </div>
                    </div>
                </div>
                <?php
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_project_slider($atts) {
        $atts = shortcode_atts(array(
            'ids' => '',
            'category' => '',
            'posts_per_page' => 5
        ), $atts);

        $query_args = array(
            'post_type' => 'project',
            'posts_per_page' => $atts['posts_per_page']
        );

        if ($atts['ids']) {
            $query_args['post__in'] = explode(',', $atts['ids']);
            $query_args['orderby'] = 'post__in';
        } elseif ($atts['category']) {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'project_category',
                    'field' => 'slug',
                    'terms' => explode(',', $atts['category'])
                )
            );
        }

        $projects = new WP_Query($query_args);
        
        ob_start();
        ?>
        <div class="project-slider">
            <div class="slider-wrapper">
                <?php
                while ($projects->have_posts()) : $projects->the_post();
                    ?>
                    <div class="slider-item">
                        <div class="project-image">
                            <?php the_post_thumbnail('full'); ?>
                        </div>
                        <div class="project-info">
                            <h2><?php the_title(); ?></h2>
                            <div class="project-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="button">了解更多</a>
                        </div>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
            <button class="slider-nav prev">
                <span class="dashicons dashicons-arrow-left-alt2"></span>
            </button>
            <button class="slider-nav next">
                <span class="dashicons dashicons-arrow-right-alt2"></span>
            </button>
        </div>
        <?php
        return ob_get_clean();
    }
}

// 初始化项目模块
new WP_Architizer_Projects(); 