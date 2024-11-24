<?php
/**
 * 注册自定义分类法
 */
function wp_architizer_register_taxonomies() {
    // 项目分类
    register_taxonomy('project_category', 'project', array(
        'labels' => array(
            'name'              => '项目分类',
            'singular_name'     => '项目分类',
            'search_items'      => '搜索项目分类',
            'all_items'         => '所有项目分类',
            'parent_item'       => '父级分类',
            'parent_item_colon' => '父级分类:',
            'edit_item'         => '编辑分类',
            'update_item'       => '更新分类',
            'add_new_item'      => '添加新分类',
            'new_item_name'     => '新分类名称',
            'menu_name'         => '项目分类'
        ),
        'hierarchical'      => true,
        'show_ui'          => true,
        'show_admin_column' => true,
        'query_var'        => true,
        'rewrite'          => array('slug' => 'project-category'),
        'show_in_rest'     => true
    ));

    // 项目标签
    register_taxonomy('project_tag', 'project', array(
        'labels' => array(
            'name'              => '项目标签',
            'singular_name'     => '项目标签',
            'search_items'      => '搜索标签',
            'all_items'         => '所有标签',
            'edit_item'         => '编辑标签',
            'update_item'       => '更新标签',
            'add_new_item'      => '添加新标签',
            'new_item_name'     => '新标签名称',
            'menu_name'         => '项目标签'
        ),
        'hierarchical'      => false,
        'show_ui'          => true,
        'show_admin_column' => true,
        'query_var'        => true,
        'rewrite'          => array('slug' => 'project-tag'),
        'show_in_rest'     => true
    ));
}
add_action('init', 'wp_architizer_register_taxonomies'); 