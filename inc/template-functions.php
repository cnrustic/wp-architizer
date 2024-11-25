<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Architizer
 */

if (!function_exists('architizer_body_classes')) {
    /**
     * Adds custom classes to the array of body classes.
     *
     * @param array $classes Classes for the body element.
     * @return array
     */
    function architizer_body_classes($classes) {
        // Adds a class of hfeed to non-singular pages.
        if (!is_singular()) {
            $classes[] = 'hfeed';
        }

        // Adds a class of no-sidebar when there is no sidebar present.
        if (!is_active_sidebar('sidebar-1')) {
            $classes[] = 'no-sidebar';
        }

        return $classes;
    }
}

if (!function_exists('count_firm_projects')) {
    /**
     * 统计建筑事务所的项目数量
     *
     * @param int $firm_id 建筑事务所ID
     * @return int 项目数量
     */
    function count_firm_projects($firm_id) {
        $args = array(
            'post_type' => 'project',
            'meta_query' => array(
                array(
                    'key' => 'project_firm',
                    'value' => $firm_id,
                    'compare' => '='
                )
            ),
            'posts_per_page' => -1
        );
        $query = new WP_Query($args);
        return $query->found_posts;
    }
}

// 其他自定义函数...
