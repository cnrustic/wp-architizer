<?php
class WP_Architizer_Team {
    public function __construct() {
        add_action('init', array($this, 'register_team_post_type'));
        add_action('init', array($this, 'register_team_taxonomies'));
        add_action('add_meta_boxes', array($this, 'add_team_meta_boxes'));
        add_action('save_post_team_member', array($this, 'save_team_meta'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_team_scripts'));
        add_shortcode('team_grid', array($this, 'render_team_grid'));
        add_shortcode('team_carousel', array($this, 'render_team_carousel'));
    }

    public function register_team_post_type() {
        $labels = array(
            'name' => '团队成员',
            'singular_name' => '团队成员',
            'menu_name' => '团队管理',
            'add_new' => '添加成员',
            'add_new_item' => '添加新成员',
            'edit_item' => '编辑成员',
            'new_item' => '新成员',
            'view_item' => '查看成员',
            'search_items' => '搜索成员',
            'not_found' => '未找到成员',
            'not_found_in_trash' => '回收站中未找到成员'
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-groups',
            'supports' => array('title', 'editor', 'thumbnail'),
            'rewrite' => array('slug' => 'team'),
            'show_in_rest' => true,
            'menu_position' => 6
        );

        register_post_type('team_member', $args);
    }

    public function register_team_taxonomies() {
        // 注册部门分类
        register_taxonomy('department', 'team_member', array(
            'labels' => array(
                'name' => '部门',
                'singular_name' => '部门',
                'search_items' => '搜索部门',
                'all_items' => '所有部门',
                'edit_item' => '编辑部门',
                'update_item' => '更新部门',
                'add_new_item' => '添加新部门',
                'new_item_name' => '新部门名称'
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'department')
        ));

        // 注册专业技能标签
        register_taxonomy('skill', 'team_member', array(
            'labels' => array(
                'name' => '专业技能',
                'singular_name' => '技能',
                'search_items' => '搜索技能',
                'all_items' => '所有技能',
                'edit_item' => '编辑技能',
                'update_item' => '更新技能',
                'add_new_item' => '添加新技能',
                'new_item_name' => '新技能名称'
            ),
            'hierarchical' => false,
            'show_ui' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'skill')
        ));
    }

    public function add_team_meta_boxes() {
        add_meta_box(
            'team_member_details',
            '成员详情',
            array($this, 'render_team_details_meta_box'),
            'team_member',
            'normal',
            'high'
        );

        add_meta_box(
            'team_member_social',
            '社交媒体',
            array($this, 'render_team_social_meta_box'),
            'team_member',
            'side',
            'default'
        );
    }

    public function render_team_details_meta_box($post) {
        wp_nonce_field('team_details_nonce', 'team_details_nonce');
        
        $member_meta = get_post_meta($post->ID);
        ?>
        <div class="team-member-details">
            <p>
                <label>职位:</label>
                <input type="text" name="member_position" 
                       value="<?php echo esc_attr($member_meta['member_position'][0] ?? ''); ?>" class="widefat">
            </p>
            
            <p>
                <label>专业资质:</label>
                <input type="text" name="member_qualification" 
                       value="<?php echo esc_attr($member_meta['member_qualification'][0] ?? ''); ?>" class="widefat">
            </p>
            
            <p>
                <label>工作年限:</label>
                <input type="number" name="member_experience" 
                       value="<?php echo esc_attr($member_meta['member_experience'][0] ?? ''); ?>">
            </p>
            
            <p>
                <label>电子邮箱:</label>
                <input type="email" name="member_email" 
                       value="<?php echo esc_attr($member_meta['member_email'][0] ?? ''); ?>" class="widefat">
            </p>
            
            <p>
                <label>专业特长:</label>
                <textarea name="member_expertise" rows="3" class="widefat"><?php 
                    echo esc_textarea($member_meta['member_expertise'][0] ?? ''); 
                ?></textarea>
            </p>
            
            <div class="member-achievements">
                <label>主要成就:</label>
                <div class="achievements-list">
                    <?php
                    $achievements = isset($member_meta['member_achievements']) ? 
                        unserialize($member_meta['member_achievements'][0]) : array('');
                    foreach ($achievements as $achievement) {
                        echo '<div class="achievement-item">';
                        echo '<input type="text" name="member_achievements[]" value="' . esc_attr($achievement) . '" class="widefat">';
                        echo '<button type="button" class="remove-achievement">删除</button>';
                        echo '</div>';
                    }
                    ?>
                </div>
                <button type="button" class="add-achievement button">添加成就</button>
            </div>
        </div>
        <?php
    }

    public function render_team_social_meta_box($post) {
        $member_meta = get_post_meta($post->ID);
        $social_platforms = array(
            'weixin' => '微信',
            'weibo' => '微博',
            'linkedin' => 'LinkedIn',
            'zhihu' => '知乎'
        );
        
        foreach ($social_platforms as $platform => $label) {
            $field_name = 'member_social_' . $platform;
            $field_value = $member_meta[$field_name][0] ?? '';
            ?>
            <p>
                <label><?php echo esc_html($label); ?>:</label>
                <input type="text" name="<?php echo esc_attr($field_name); ?>" 
                       value="<?php echo esc_attr($field_value); ?>" class="widefat">
            </p>
            <?php
        }
    }

    public function save_team_meta($post_id) {
        if (!isset($_POST['team_details_nonce']) || 
            !wp_verify_nonce($_POST['team_details_nonce'], 'team_details_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // 保存基本信息
        $fields = array(
            'member_position',
            'member_qualification',
            'member_experience',
            'member_email',
            'member_expertise'
        );

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta(
                    $post_id,
                    $field,
                    sanitize_text_field($_POST[$field])
                );
            }
        }

        // 保存成就列表
        if (isset($_POST['member_achievements'])) {
            $achievements = array_filter(array_map('sanitize_text_field', $_POST['member_achievements']));
            update_post_meta($post_id, 'member_achievements', serialize($achievements));
        }

        // 保存社交媒体链接
        $social_platforms = array('weixin', 'weibo', 'linkedin', 'zhihu');
        foreach ($social_platforms as $platform) {
            $field_name = 'member_social_' . $platform;
            if (isset($_POST[$field_name])) {
                update_post_meta(
                    $post_id,
                    $field_name,
                    esc_url_raw($_POST[$field_name])
                );
            }
        }
    }

    public function enqueue_team_scripts() {
        if (is_singular('team_member') || has_shortcode(get_the_content(), 'team_grid')) {
            wp_enqueue_style(
                'team-styles',
                get_template_directory_uri() . '/assets/css/team.css',
                array(),
                '1.0.0'
            );
            
            wp_enqueue_script(
                'team-scripts',
                get_template_directory_uri() . '/assets/js/team.js',
                array('jquery'),
                '1.0.0',
                true
            );
        }
    }

    public function render_team_grid($atts) {
        $atts = shortcode_atts(array(
            'department' => '',
            'columns' => 3,
            'posts_per_page' => -1,
            'orderby' => 'menu_order',
            'order' => 'ASC'
        ), $atts);

        $query_args = array(
            'post_type' => 'team_member',
            'posts_per_page' => $atts['posts_per_page'],
            'orderby' => $atts['orderby'],
            'order' => $atts['order']
        );

        if ($atts['department']) {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'department',
                    'field' => 'slug',
                    'terms' => explode(',', $atts['department'])
                )
            );
        }

        $team_members = new WP_Query($query_args);
        
        ob_start();
        ?>
        <div class="team-grid columns-<?php echo esc_attr($atts['columns']); ?>">
            <?php
            while ($team_members->have_posts()) : $team_members->the_post();
                $position = get_post_meta(get_the_ID(), 'member_position', true);
                ?>
                <div class="team-member">
                    <div class="member-photo">
                        <?php 
                        if (has_post_thumbnail()) {
                            the_post_thumbnail('team-member');
                        } else {
                            echo '<img src="' . get_template_directory_uri() . '/assets/images/default-avatar.png" alt="默认头像">';
                        }
                        ?>
                    </div>
                    <div class="member-info">
                        <h3 class="member-name"><?php the_title(); ?></h3>
                        <?php if ($position) : ?>
                            <div class="member-position"><?php echo esc_html($position); ?></div>
                        <?php endif; ?>
                        <div class="member-excerpt">
                            <?php echo wp_trim_words(get_the_content(), 20); ?>
                        </div>
                        <div class="member-social">
                            <?php
                            $social_platforms = array('weixin', 'weibo', 'linkedin', 'zhihu');
                            foreach ($social_platforms as $platform) {
                                $url = get_post_meta(get_the_ID(), 'member_social_' . $platform, true);
                                if ($url) {
                                    echo '<a href="' . esc_url($url) . '" class="social-icon ' . esc_attr($platform) . '">';
                                    echo '<i class="fab fa-' . esc_attr($platform) . '"></i>';
                                    echo '</a>';
                                }
                            }
                            ?>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="member-link">查看详情</a>
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

    public function render_team_carousel($atts) {
        $atts = shortcode_atts(array(
            'posts_per_page' => 5,
            'department' => ''
        ), $atts);

        $query_args = array(
            'post_type' => 'team_member',
            'posts_per_page' => $atts['posts_per_page']
        );

        if ($atts['department']) {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'department',
                    'field' => 'slug',
                    'terms' => explode(',', $atts['department'])
                )
            );
        }

        $team_members = new WP_Query($query_args);
        
        ob_start();
        ?>
        <div class="team-carousel">
            <div class="carousel-wrapper">
                <?php
                while ($team_members->have_posts()) : $team_members->the_post();
                    $position = get_post_meta(get_the_ID(), 'member_position', true);
                    $expertise = get_post_meta(get_the_ID(), 'member_expertise', true);
                    ?>
                    <div class="carousel-item">
                        <div class="member-card">
                            <div class="member-photo">
                                <?php the_post_thumbnail('team-member-large'); ?>
                            </div>
                            <div class="member-info">
                                <h3><?php the_title(); ?></h3>
                                <?php if ($position) : ?>
                                    <div class="member-position"><?php echo esc_html($position); ?></div>
                                <?php endif; ?>
                                <?php if ($expertise) : ?>
                                    <div class="member-expertise"><?php echo esc_html($expertise); ?></div>
                                <?php endif; ?>
                                <a href="<?php the_permalink(); ?>" class="button">了解更多</a>
                            </div>
                        </div>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
            <button class="carousel-nav prev">
                <span class="dashicons dashicons-arrow-left-alt2"></span>
            </button>
            <button class="carousel-nav next">
                <span class="dashicons dashicons-arrow-right-alt2"></span>
            </button>
        </div>
        <?php
        return ob_get_clean();
    }
}

// 初始化团队成员模块
new WP_Architizer_Team(); 