<?php
class WP_Architizer_Case_Studies {
    public function __construct() {
        add_action('init', array($this, 'register_case_study_post_type'));
        add_action('init', array($this, 'register_case_study_taxonomies'));
        add_action('add_meta_boxes', array($this, 'add_case_study_meta_boxes'));
        add_action('save_post_case_study', array($this, 'save_case_study_meta'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_case_study_scripts'));
        add_shortcode('case_studies_grid', array($this, 'render_case_studies_grid'));
        add_shortcode('case_study_showcase', array($this, 'render_case_study_showcase'));
        add_shortcode('case_study_comparison', array($this, 'render_case_study_comparison'));
    }

    public function register_case_study_post_type() {
        $labels = array(
            'name' => '案例研究',
            'singular_name' => '案例研究',
            'menu_name' => '案例研究',
            'add_new' => '添加案例',
            'add_new_item' => '添加新案例',
            'edit_item' => '编辑案例',
            'new_item' => '新案例',
            'view_item' => '查看案例',
            'search_items' => '搜索案例',
            'not_found' => '未找到案例',
            'not_found_in_trash' => '回收站中未找到案例'
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-analytics',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'rewrite' => array('slug' => 'case-studies'),
            'show_in_rest' => true,
            'menu_position' => 8
        );

        register_post_type('case_study', $args);
    }

    public function register_case_study_taxonomies() {
        // 注册行业分类
        register_taxonomy('industry', 'case_study', array(
            'labels' => array(
                'name' => '行业',
                'singular_name' => '行业',
                'search_items' => '搜索行业',
                'all_items' => '所有行业',
                'edit_item' => '编辑行业',
                'update_item' => '更新行业',
                'add_new_item' => '添加新行业',
                'new_item_name' => '新行业名称'
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'industry')
        ));

        // 注册解决方案标签
        register_taxonomy('solution', 'case_study', array(
            'labels' => array(
                'name' => '解决方案',
                'singular_name' => '解决方案',
                'search_items' => '搜索解决方案',
                'all_items' => '所有解决方案',
                'edit_item' => '编辑解决方案',
                'update_item' => '更新解决方案',
                'add_new_item' => '添加新解决方案',
                'new_item_name' => '新解决方案名称'
            ),
            'hierarchical' => false,
            'show_ui' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'solution')
        ));
    }

    public function add_case_study_meta_boxes() {
        add_meta_box(
            'case_study_details',
            '案例详情',
            array($this, 'render_case_study_details_meta_box'),
            'case_study',
            'normal',
            'high'
        );

        add_meta_box(
            'case_study_results',
            '案例成果',
            array($this, 'render_case_study_results_meta_box'),
            'case_study',
            'normal',
            'high'
        );
    }

    public function render_case_study_details_meta_box($post) {
        wp_nonce_field('case_study_details_nonce', 'case_study_details_nonce');
        
        $meta = get_post_meta($post->ID);
        ?>
        <div class="case-study-details">
            <p>
                <label>客户名称:</label>
                <input type="text" name="client_name" 
                       value="<?php echo esc_attr($meta['client_name'][0] ?? ''); ?>" class="widefat">
            </p>
            
            <p>
                <label>项目时长:</label>
                <input type="text" name="project_duration" 
                       value="<?php echo esc_attr($meta['project_duration'][0] ?? ''); ?>" class="widefat">
            </p>
            
            <p>
                <label>项目预算:</label>
                <input type="text" name="project_budget" 
                       value="<?php echo esc_attr($meta['project_budget'][0] ?? ''); ?>" class="widefat">
            </p>
            
            <p>
                <label>挑战描述:</label>
                <textarea name="challenges" rows="4" class="widefat"><?php 
                    echo esc_textarea($meta['challenges'][0] ?? ''); 
                ?></textarea>
            </p>
            
            <p>
                <label>解决方案概述:</label>
                <textarea name="solution_overview" rows="4" class="widefat"><?php 
                    echo esc_textarea($meta['solution_overview'][0] ?? ''); 
                ?></textarea>
            </p>
            
            <p>
                <label>使用技术:</label>
                <input type="text" name="technologies_used" 
                       value="<?php echo esc_attr($meta['technologies_used'][0] ?? ''); ?>" class="widefat">
                <span class="description">用逗号分隔多个技术</span>
            </p>
        </div>
        <?php
    }

    public function render_case_study_results_meta_box($post) {
        $meta = get_post_meta($post->ID);
        ?>
        <div class="case-study-results">
            <p>
                <label>关键成果:</label>
                <textarea name="key_results" rows="4" class="widefat"><?php 
                    echo esc_textarea($meta['key_results'][0] ?? ''); 
                ?></textarea>
            </p>
            
            <div class="metrics-group">
                <h4>性能指标</h4>
                <div class="metric-fields">
                    <?php
                    $metrics = isset($meta['metrics']) ? unserialize($meta['metrics'][0]) : array();
                    if (!empty($metrics)) {
                        foreach ($metrics as $index => $metric) {
                            $this->render_metric_field($index, $metric);
                        }
                    } else {
                        $this->render_metric_field(0);
                    }
                    ?>
                </div>
                <button type="button" class="button add-metric">添加指标</button>
            </div>
            
            <p>
                <label>客户反馈:</label>
                <textarea name="client_feedback" rows="4" class="widefat"><?php 
                    echo esc_textarea($meta['client_feedback'][0] ?? ''); 
                ?></textarea>
            </p>
            
            <p>
                <label>ROI分析:</label>
                <textarea name="roi_analysis" rows="4" class="widefat"><?php 
                    echo esc_textarea($meta['roi_analysis'][0] ?? ''); 
                ?></textarea>
            </p>
        </div>
        <?php
    }

    private function render_metric_field($index, $metric = array()) {
        ?>
        <div class="metric-field">
            <input type="text" name="metrics[<?php echo $index; ?>][label]" 
                   value="<?php echo esc_attr($metric['label'] ?? ''); ?>" 
                   placeholder="指标名称" style="width: 30%;">
            
            <input type="text" name="metrics[<?php echo $index; ?>][value]" 
                   value="<?php echo esc_attr($metric['value'] ?? ''); ?>" 
                   placeholder="指标值" style="width: 30%;">
            
            <input type="text" name="metrics[<?php echo $index; ?>][unit]" 
                   value="<?php echo esc_attr($metric['unit'] ?? ''); ?>" 
                   placeholder="单位" style="width: 20%;">
            
            <button type="button" class="button remove-metric">删除</button>
        </div>
        <?php
    }

    public function save_case_study_meta($post_id) {
        if (!isset($_POST['case_study_details_nonce']) || 
            !wp_verify_nonce($_POST['case_study_details_nonce'], 'case_study_details_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        $fields = array(
            'client_name',
            'project_duration',
            'project_budget',
            'challenges',
            'solution_overview',
            'technologies_used',
            'key_results',
            'client_feedback',
            'roi_analysis'
        );

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }

        // 保存性能指标
        if (isset($_POST['metrics'])) {
            $metrics = array_map(function($metric) {
                return array(
                    'label' => sanitize_text_field($metric['label']),
                    'value' => sanitize_text_field($metric['value']),
                    'unit' => sanitize_text_field($metric['unit'])
                );
            }, $_POST['metrics']);
            
            update_post_meta($post_id, 'metrics', $metrics);
        }
    }

    public function enqueue_case_study_scripts() {
        if (is_singular('case_study') || has_shortcode(get_the_content(), 'case_studies_grid')) {
            wp_enqueue_style(
                'case-studies-style',
                get_template_directory_uri() . '/assets/css/case-studies.css',
                array(),
                '1.0.0'
            );
            
            wp_enqueue_script(
                'case-studies-script',
                get_template_directory_uri() . '/assets/js/case-studies.js',
                array('jquery'),
                '1.0.0',
                true
            );
        }
    }

    public function render_case_studies_grid($atts) {
        $atts = shortcode_atts(array(
            'columns' => 3,
            'posts_per_page' => 9,
            'industry' => '',
            'solution' => '',
            'orderby' => 'date',
            'order' => 'DESC'
        ), $atts);

        $query_args = array(
            'post_type' => 'case_study',
            'posts_per_page' => $atts['posts_per_page'],
            'orderby' => $atts['orderby'],
            'order' => $atts['order']
        );

        if ($atts['industry']) {
            $query_args['tax_query'][] = array(
                'taxonomy' => 'industry',
                'field' => 'slug',
                'terms' => explode(',', $atts['industry'])
            );
        }

        if ($atts['solution']) {
            $query_args['tax_query'][] = array(
                'taxonomy' => 'solution',
                'field' => 'slug',
                'terms' => explode(',', $atts['solution'])
            );
        }

        $case_studies = new WP_Query($query_args);
        
        ob_start();
        ?>
        <div class="case-studies-grid columns-<?php echo esc_attr($atts['columns']); ?>">
            <?php
            while ($case_studies->have_posts()) : $case_studies->the_post();
                $client_name = get_post_meta(get_the_ID(), 'client_name', true);
                $industry_terms = get_the_terms(get_the_ID(), 'industry');
                ?>
                <div class="case-study-item">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="case-study-image">
                            <?php the_post_thumbnail('large'); ?>
                            <div class="case-study-overlay">
                                <a href="<?php the_permalink(); ?>" class="read-more">查看详情</a>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="case-study-content">
                        <h3 class="case-study-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        
                        <?php if ($client_name) : ?>
                            <div class="client-name"><?php echo esc_html($client_name); ?></div>
                        <?php endif; ?>
                        
                        <?php if ($industry_terms) : ?>
                            <div class="industry-tags">
                                <?php
                                foreach ($industry_terms as $term) {
                                    echo '<span class="industry-tag">' . esc_html($term->name) . '</span>';
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="case-study-excerpt">
                            <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
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

    public function render_case_study_showcase($atts) {
        $atts = shortcode_atts(array(
            'id' => get_the_ID()
        ), $atts);

        $meta = get_post_meta($atts['id']);
        $metrics = isset($meta['metrics']) ? unserialize($meta['metrics'][0]) : array();
        
        ob_start();
        ?>
        <div class="case-study-showcase">
            <div class="showcase-header">
                <?php if (has_post_thumbnail($atts['id'])) : ?>
                    <div class="showcase-image">
                        <?php echo get_the_post_thumbnail($atts['id'], 'full'); ?>
                    </div>
                <?php endif; ?>
                
                <div class="showcase-intro">
                    <h2><?php echo get_the_title($atts['id']); ?></h2>
                    <?php if (isset($meta['client_name'][0])) : ?>
                        <div class="client-name"><?php echo esc_html($meta['client_name'][0]); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="showcase-content">
                <div class="project-details">
                    <?php if (isset($meta['project_duration'][0])) : ?>
                        <div class="detail-item">
                            <span class="label">项目时长:</span>
                            <span class="value"><?php echo esc_html($meta['project_duration'][0]); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($meta['project_budget'][0])) : ?>
                        <div class="detail-item">
                            <span class="label">项目预算:</span>
                            <span class="value"><?php echo esc_html($meta['project_budget'][0]); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if (isset($meta['challenges'][0])) : ?>
                    <div class="section challenges">
                        <h3>项目挑战</h3>
                        <div class="content">
                            <?php echo wp_kses_post($meta['challenges'][0]); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($meta['solution_overview'][0])) : ?>
                    <div class="section solution">
                        <h3>解决方案</h3>
                        <div class="content">
                            <?php echo wp_kses_post($meta['solution_overview'][0]); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($metrics)) : ?>
                    <div class="section metrics">
                        <h3>性能指标</h3>
                        <div class="metrics-grid">
                            <?php foreach ($metrics as $metric) : ?>
                                <div class="metric-item">
                                    <div class="metric-value">
                                        <?php echo esc_html($metric['value']); ?>
                                        <?php if (isset($metric['unit'])) : ?>
                                            <span class="unit"><?php echo esc_html($metric['unit']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="metric-label"><?php echo esc_html($metric['label']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($meta['key_results'][0])) : ?>
                    <div class="section results">
                        <h3>关键成果</h3>
                        <div class="content">
                            <?php echo wp_kses_post($meta['key_results'][0]); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($meta['client_feedback'][0])) : ?>
                    <div class="section feedback">
                        <h3>客户反馈</h3>
                        <div class="content testimonial">
                            <?php echo wp_kses_post($meta['client_feedback'][0]); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

// 初始化案例研究模块
new WP_Architizer_Case_Studies(); 