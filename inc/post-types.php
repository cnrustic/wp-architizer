<?php
/**
 * 注册自定义文章类型
 */
function architizer_register_post_types() {
    // 建筑项目
    register_post_type('project', array(
        'labels' => array(
            'name' => '项目',
            'singular_name' => '项目',
            'add_new' => '添加项目',
            'add_new_item' => '添加新项目',
            'edit_item' => '编辑项目',
            'view_item' => '查看项目',
            'search_items' => '搜索项目',
            'not_found' => '未找到项目',
            'menu_name' => '建筑项目'
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'menu_icon' => 'dashicons-building',
        'rewrite' => array('slug' => 'projects'),
        'show_in_rest' => true, // 支持古腾堡编辑器
        'menu_position' => 5
    ));
    
    // 建筑事务所
    register_post_type('firm', array(
        'labels' => array(
            'name' => '事务所',
            'singular_name' => '事务所',
            'add_new' => '添加事务所',
            'add_new_item' => '添加新事务所',
            'edit_item' => '编辑事务所',
            'view_item' => '查看事务所',
            'search_items' => '搜索事务所',
            'not_found' => '未找到事务所',
            'menu_name' => '建筑事务所'
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'menu_icon' => 'dashicons-groups',
        'rewrite' => array('slug' => 'firms'),
        'show_in_rest' => true,
        'menu_position' => 6
    ));
}
add_action('init', 'architizer_register_post_types'); 