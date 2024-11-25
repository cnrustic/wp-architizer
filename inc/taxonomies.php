<?php
/**
 * 注册自定义分类法
 */
function architizer_register_taxonomies() {
    // 项目分类
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
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'project-category')
    ));
    
    // 项目位置
    register_taxonomy('project_location', 'project', array(
        'labels' => array(
            'name' => '项目位置',
            'singular_name' => '项目位置',
            'search_items' => '搜索位置',
            'all_items' => '所有位置',
            'edit_item' => '编辑位置',
            'update_item' => '更新位置',
            'add_new_item' => '添加新位置',
            'new_item_name' => '新位置名称'
        ),
        'hierarchical' => false,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'project-location')
    ));

    // 新增：项目风格分类
    register_taxonomy('project_style', 'project', array(
        'labels' => array(
            'name' => '项目风格',
            'singular_name' => '项目风格',
            'search_items' => '搜索风格',
            'all_items' => '所有风格',
            'edit_item' => '编辑风格',
            'update_item' => '更新风格',
            'add_new_item' => '添加新风格',
            'new_item_name' => '新风格名称'
        ),
        'hierarchical' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'project-style')
    ));
}
add_action('init', 'architizer_register_taxonomies');

// 刷新固定链接
function architizer_rewrite_flush() {
    architizer_register_post_types();
    architizer_register_taxonomies();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'architizer_rewrite_flush'); 