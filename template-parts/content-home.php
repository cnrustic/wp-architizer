<div class="home-hero">
    <div class="container">
        <h1 class="hero-title">发现全球优秀建筑作品</h1>
        <div class="hero-search">
            <form role="search" method="get" action="<?php echo home_url('/'); ?>">
                <input type="text" placeholder="搜索项目、建筑师、产品..." name="s">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
        <div class="hero-categories">
            <div class="category-tags">
                <a href="#">住宅建筑</a>
                <a href="#">文化建筑</a>
                <a href="#">商业建筑</a>
                <a href="#">教育建筑</a>
                <a href="#">景观设计</a>
            </div>
        </div>
    </div>
</div>

<div class="trending-section">
    <div class="container">
        <div class="section-header">
            <h2>热门趋势</h2>
            <div class="trending-tabs">
                <button class="tab active" data-tab="projects">项目</button>
                <button class="tab" data-tab="firms">事务所</button>
                <button class="tab" data-tab="products">产品</button>
            </div>
        </div>
        <div class="trending-content">
            <div class="tab-content active" id="projects">
                <div class="trending-grid">
                    <?php
                    $trending_projects = new WP_Query(array(
                        'post_type' => 'project',
                        'posts_per_page' => 3,
                        'meta_key' => 'project_views',
                        'orderby' => 'meta_value_num',
                        'order' => 'DESC'
                    ));

                    while ($trending_projects->have_posts()) : $trending_projects->the_post();
                        get_template_part('template-parts/content', 'trending-project');
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
            <!-- 其他标签页内容 -->
        </div>
    </div>
</div>

<div class="categories-showcase">
    <div class="container">
        <div class="section-header">
            <h2>探索建筑类型</h2>
        </div>
        <div class="category-grid">
            <?php
            $categories = get_terms(array(
                'taxonomy' => 'project_category',
                'hide_empty' => false,
                'number' => 6
            ));

            foreach ($categories as $category) :
                $image_id = get_term_meta($category->term_id, 'category_image', true);
            ?>
                <div class="category-card">
                    <?php if ($image_id) : ?>
                        <div class="category-image">
                            <?php echo wp_get_attachment_image($image_id, 'large'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="category-info">
                        <h3><?php echo $category->name; ?></h3>
                        <span class="project-count"><?php echo $category->count; ?> 个项目</span>
                    </div>
                    <a href="<?php echo get_term_link($category); ?>" class="category-link"></a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="latest-articles">
    <div class="container">
        <div class="section-header">
            <h2>最新文章</h2>
            <a href="<?php echo get_permalink(get_option('page_for_posts')); ?>" class="view-all">查看全部</a>
        </div>
        <div class="articles-grid">
            <?php
            $latest_posts = new WP_Query(array(
                'post_type' => 'post',
                'posts_per_page' => 3
            ));

            while ($latest_posts->have_posts()) : $latest_posts->the_post();
                get_template_part('template-parts/content', 'article-card');
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
    </div>
</div> 