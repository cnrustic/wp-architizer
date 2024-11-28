<?php
/**
 * 主题模板标签函数
 * 
 * @package Architizer
 */

class Architizer_Template_Tags {
    /**
     * 显示项目元数据
     */
    public static function project_meta($post_id = null) {
        $post_id = $post_id ?: get_the_ID();
        $meta_fields = array(
            'location' => array(
                'icon' => 'location_on',
                'label' => '位置'
            ),
            'area' => array(
                'icon' => 'square_foot',
                'label' => '面积'
            ),
            'year' => array(
                'icon' => 'event',
                'label' => '年份'
            ),
            'architect' => array(
                'icon' => 'person',
                'label' => '建筑师'
            )
        );
        
        echo '<div class="project-meta">';
        foreach ($meta_fields as $key => $field) {
            $value = get_post_meta($post_id, "project_{$key}", true);
            if ($value) {
                printf(
                    '<span class="%1$s"><i class="material-icons">%2$s</i><span class="label">%3$s:</span> %4$s</span>',
                    esc_attr($key),
                    esc_html($field['icon']),
                    esc_html($field['label']),
                    esc_html($value)
                );
            }
        }
        echo '</div>';
    }

    /**
     * 显示项目分类
     */
    public static function project_categories($post_id = null) {
        $post_id = $post_id ?: get_the_ID();
        $taxonomies = array(
            'project_category' => '项目类型',
            'project_style' => '建筑风格',
            'project_location' => '地理位置'
        );
        
        echo '<div class="project-taxonomies">';
        foreach ($taxonomies as $tax => $label) {
            $terms = get_the_terms($post_id, $tax);
            if ($terms && !is_wp_error($terms)) {
                printf('<div class="taxonomy-group %s">', esc_attr($tax));
                printf('<span class="taxonomy-label">%s:</span>', esc_html($label));
                echo '<div class="term-list">';
                foreach ($terms as $term) {
                    printf(
                        '<a href="%s" class="term-link">%s</a>',
                        esc_url(get_term_link($term)),
                        esc_html($term->name)
                    );
                }
                echo '</div></div>';
            }
        }
        echo '</div>';
    }

    /**
     * 显示发布时间
     */
    public static function posted_on() {
        $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
        
        printf(
            $time_string,
            esc_attr(get_the_date(DATE_W3C)),
            esc_html(get_the_date())
        );
    }

    /**
     * 显示作者信息
     */
    public static function posted_by() {
        printf(
            '<span class="author">%s <a href="%s">%s</a></span>',
            esc_html__('作者:', 'architizer'),
            esc_url(get_author_posts_url(get_the_author_meta('ID'))),
            esc_html(get_the_author())
        );
    }
}

// 为了保持向后兼容，添加函数别名
function architizer_project_meta($post_id = null) {
    Architizer_Template_Tags::project_meta($post_id);
}

function architizer_project_categories($post_id = null) {
    Architizer_Template_Tags::project_categories($post_id);
}
