function architizer_register_project_post_type() {
    $labels = array(
        'name'               => '项目',
        'singular_name'      => '项目',
        'menu_name'          => '项目',
        'add_new'           => '添加项目',
        'add_new_item'      => '添加新项目',
        'edit_item'         => '编辑项目',
        'new_item'          => '新项目',
        'view_item'         => '查看项目',
        'search_items'      => '搜索项目',
        'not_found'         => '未找到项目',
        'not_found_in_trash'=> '回收站中未找到项目'
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'show_in_rest'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-building',
        'hierarchical'        => false,
        'supports'            => array(
            'title',
            'editor',
            'thumbnail',
            'excerpt',
            'custom-fields',
            'revisions',
            'author'
        ),
        'taxonomies'          => array('project_category', 'project_tag'),
        'rewrite'             => array(
            'slug'            => 'projects',
            'with_front'      => false
        ),
        'capabilities'        => array(
            'edit_post'       => 'edit_project',
            'edit_posts'      => 'edit_projects',
            'edit_others_posts' => 'edit_other_projects'
        )
    );

    register_post_type('project', $args);

    // 注册项目分类法
    register_taxonomy('project_category', 'project', array(
        'label'              => '项目分类',
        'hierarchical'       => true,
        'show_in_rest'       => true,
        'show_admin_column'  => true,
        'rewrite'            => array('slug' => 'project-category')
    ));

    register_taxonomy('project_tag', 'project', array(
        'label'              => '项目标签',
        'hierarchical'       => false,
        'show_in_rest'       => true,
        'show_admin_column'  => true,
        'rewrite'            => array('slug' => 'project-tag')
    ));
}
add_action('init', 'architizer_register_project_post_type');

// 添加项目自定义字段
function architizer_register_project_fields() {
    if(function_exists('acf_add_local_field_group')):

    acf_add_local_field_group(array(
        'key' => 'group_project_details',
        'title' => '项目详情',
        'fields' => array(
            array(
                'key' => 'field_project_location',
                'label' => '项目位置',
                'name' => 'project_location',
                'type' => 'text'
            ),
            array(
                'key' => 'field_project_year',
                'label' => '完工年份',
                'name' => 'project_year',
                'type' => 'number'
            ),
            array(
                'key' => 'field_project_size',
                'label' => '项目规模',
                'name' => 'project_size',
                'type' => 'text'
            ),
            array(
                'key' => 'field_architect',
                'label' => '建筑师',
                'name' => 'architect',
                'type' => 'text'
            ),
            array(
                'key' => 'field_project_gallery',
                'label' => '项目图片',
                'name' => 'project_gallery',
                'type' => 'gallery',
                'return_format' => 'array',
                'min' => 1,
                'max' => 30,
                'library' => 'all'
            ),
            array(
                'key' => 'field_project_specs',
                'label' => '项目规格',
                'name' => 'project_specs',
                'type' => 'repeater',
                'layout' => 'table',
                'sub_fields' => array(
                    array(
                        'key' => 'field_spec_label',
                        'label' => '规格名称',
                        'name' => 'spec_label',
                        'type' => 'text'
                    ),
                    array(
                        'key' => 'field_spec_value',
                        'label' => '规格值',
                        'name' => 'spec_value',
                        'type' => 'text'
                    )
                )
            )
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'project'
                )
            )
        )
    ));

    endif;
}
add_action('acf/init', 'architizer_register_project_fields'); 