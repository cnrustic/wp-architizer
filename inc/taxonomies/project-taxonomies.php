function architizer_register_project_taxonomies() {
    // 项目类型
    register_taxonomy('project_type', 'project', array(
        'label' => '项目类型',
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'project-type')
    ));

    // 项目位置
    register_taxonomy('project_location', 'project', array(
        'label' => '项目位置',
        'hierarchical' => false,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'project-location')
    ));
}
add_action('init', 'architizer_register_project_taxonomies'); 