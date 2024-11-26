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

    // 添加产品文章类型
    register_post_type('product', array(
        'labels' => array(
            'name' => '产品',
            'singular_name' => '产品',
            'add_new' => '添加产品',
            'add_new_item' => '添加新产品',
            'edit_item' => '编辑产品',
            'view_item' => '查看产品',
            'search_items' => '搜索产品',
            'not_found' => '未找到产品',
            'menu_name' => '产品'
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'menu_icon' => 'dashicons-cart',
        'rewrite' => array('slug' => 'products'),
        'show_in_rest' => true
    ));

    // 添加产品分类法
    register_taxonomy('product_category', 'product', array(
        'hierarchical' => true,
        'labels' => array(
            'name' => '产品分类',
            'singular_name' => '产品分类',
            'add_new_item' => '添加新产品分类',
            'edit_item' => '编辑产品分类',
            'view_item' => '查看产品分类',
            'update_item' => '更新产品分类',
            'add_or_remove_items' => '添加或移除产品分类',
            'choose_from_most_used' => '从常用产品分类中选择',
            'not_found' => '未找到产品分类'
        ),
        'show_ui' => true,
        'show_admin_column' => true,
        'rewrite' => array('slug' => 'product-category')
    ));

    // 添加产品品牌分类法
    register_taxonomy('product_brand', 'product', array(
        'hierarchical' => false,
        'labels' => array(
            'name' => '品牌',
            'singular_name' => '品牌',
            'add_new_item' => '添加新品牌',
            'edit_item' => '编辑品牌',
            'view_item' => '查看品牌',
            'update_item' => '更新品牌',
            'add_or_remove_items' => '添加或移除品牌',
            'choose_from_most_used' => '从常用品牌中选择',
            'not_found' => '未找到品牌'
        ),
        'show_ui' => true,
        'show_admin_column' => true,
        'rewrite' => array('slug' => 'brand')
    ));
}
add_action('init', 'architizer_register_post_types'); 