<?php
/**
 * 注册自定义文章类型
 */
function wp_architizer_register_post_types() {
    // 注册项目文章类型
    register_post_type('project', array(
        'labels' => array(
            'name'               => '项目',
            'singular_name'      => '项目',
            'add_new'           => '添加项目',
            'add_new_item'      => '添加新项目',
            'edit_item'         => '编辑项目',
            'new_item'          => '新项目',
            'view_item'         => '查看项目',
            'search_items'      => '搜索项目',
            'not_found'         => '未找到项目',
            'menu_name'         => '项目'
        ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true, // 支持 Gutenberg 编辑器
        'query_var'          => true,
        'rewrite'            => array('slug' => 'projects'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-building',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt')
    ));

    // 注册公司文章类型
    register_post_type('firm', array(
        'labels' => array(
            'name'               => '公司',
            'singular_name'      => '公司',
            'add_new'           => '添加公司',
            'add_new_item'      => '添加新公司',
            'edit_item'         => '编辑公司',
            'new_item'          => '新公司',
            'view_item'         => '查看公司',
            'search_items'      => '搜索公司',
            'not_found'         => '未找到公司',
            'menu_name'         => '公司'
        ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true, // 支持 Gutenberg 编辑器
        'query_var'          => true,
        'rewrite'            => array('slug' => 'firms'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-building',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt')
    ));
} 