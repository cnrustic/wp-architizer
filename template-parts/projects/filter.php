<form class="filter-form" action="<?php echo esc_url(home_url('/projects/')); ?>" method="get">
    <!-- 项目类型 -->
    <div class="filter-group">
        <label>项目类型</label>
        <?php 
        wp_dropdown_categories(array(
            'taxonomy' => 'project_type',
            'name' => 'project_type',
            'show_option_all' => '所有类型',
            'selected' => get_query_var('project_type')
        ));
        ?>
    </div>

    <!-- 项目位置 -->
    <div class="filter-group">
        <label>项目位置</label>
        <?php 
        wp_dropdown_categories(array(
            'taxonomy' => 'project_location',
            'name' => 'project_location',
            'show_option_all' => '所有位置',
            'selected' => get_query_var('project_location')
        ));
        ?>
    </div>

    <button type="submit" class="filter-submit">应用筛选</button>
</form> 