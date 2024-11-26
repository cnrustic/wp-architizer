<?php
function architizer_widgets_init() {
    // 主侧边栏
    register_sidebar(array(
        'name' => '主侧边栏',
        'id' => 'primary-sidebar',
        'description' => '显示在文章和页面侧边的小工具区域',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ));

    // 页脚小工具区域
    $footer_widget_areas = array(
        'footer-1' => '页脚区域一',
        'footer-2' => '页脚区域二',
        'footer-3' => '页脚区域三'
    );

    foreach ($footer_widget_areas as $id => $name) {
        register_sidebar(array(
            'name' => $name,
            'id' => $id,
            'description' => '显示在页脚的小工具区域',
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>'
        ));
    }

    // 注册自定义小工具
    register_widget('Architizer_Recent_Projects');
    register_widget('Architizer_Featured_Firms');
    register_widget('Architizer_Popular_Products');
}
add_action('widgets_init', 'architizer_widgets_init');

// 最近项目小工具
class Architizer_Recent_Projects extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'architizer_recent_projects',
            '最近项目',
            array('description' => '显示最新的建筑项目')
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = !empty($instance['title']) ? $instance['title'] : '最近项目';
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;

        echo $args['before_title'] . esc_html($title) . $args['after_title'];

        $recent_projects = new WP_Query(array(
            'post_type' => 'project',
            'posts_per_page' => $number,
            'orderby' => 'date',
            'order' => 'DESC'
        ));

        if ($recent_projects->have_posts()) :
            echo '<ul class="recent-projects-widget">';
            while ($recent_projects->have_posts()) : $recent_projects->the_post();
                echo '<li>';
                if (has_post_thumbnail()) {
                    echo '<a href="' . get_permalink() . '">' . get_the_post_thumbnail(null, 'thumbnail') . '</a>';
                }
                echo '<div class="project-info">';
                echo '<h4><a href="' . get_permalink() . '">' . get_the_title() . '</a></h4>';
                echo '<span class="project-date">' . get_the_date() . '</span>';
                echo '</div>';
                echo '</li>';
            endwhile;
            echo '</ul>';
            wp_reset_postdata();
        endif;

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '最近项目';
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">标题：</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>">显示数量：</label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>" size="3">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 5;
        return $instance;
    }
} 