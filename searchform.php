<form role="search" method="get" class="search-form" action="<?php echo home_url('/'); ?>">
    <div class="search-wrapper">
        <div class="search-input-group">
            <input type="search" 
                   class="search-field" 
                   placeholder="搜索项目、公司或产品..." 
                   value="<?php echo get_search_query(); ?>" 
                   name="s" />
            
            <select name="post_type" class="search-type">
                <option value="all" <?php selected(get_query_var('post_type'), 'all'); ?>>所有内容</option>
                <option value="project" <?php selected(get_query_var('post_type'), 'project'); ?>>项目</option>
                <option value="firm" <?php selected(get_query_var('post_type'), 'firm'); ?>>公司</option>
                <option value="product" <?php selected(get_query_var('post_type'), 'product'); ?>>产品</option>
            </select>
        </div>
        
        <div class="advanced-search-toggle">
            <button type="button" class="toggle-button">高级搜索</button>
        </div>
        
        <div class="advanced-search-fields">
            <div class="search-filters">
                <!-- 项目筛选 -->
                <div class="filter-group project-filters" style="display: none;">
                    <?php 
                    $project_categories = get_terms('project_category');
                    if ($project_categories) : ?>
                        <select name="project_category">
                            <option value="">选择项目类别</option>
                            <?php foreach ($project_categories as $category) : ?>
                                <option value="<?php echo esc_attr($category->slug); ?>">
                                    <?php echo esc_html($category->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                    
                    <input type="text" name="project_location" placeholder="项目位置" />
                </div>
                
                <!-- 公司筛选 -->
                <div class="filter-group firm-filters" style="display: none;">
                    <input type="text" name="firm_location" placeholder="公司位置" />
                </div>
                
                <!-- 产品筛选 -->
                <div class="filter-group product-filters" style="display: none;">
                    <?php 
                    $product_categories = get_terms('product_category');
                    if ($product_categories) : ?>
                        <select name="product_category">
                            <option value="">选择产品类别</option>
                            <?php foreach ($product_categories as $category) : ?>
                                <option value="<?php echo esc_attr($category->slug); ?>">
                                    <?php echo esc_html($category->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <button type="submit" class="search-submit">
            <span class="screen-reader-text">搜索</span>
            <i class="fas fa-search"></i>
        </button>
    </div>
</form> 