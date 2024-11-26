<?php
/**
 * The front page template file
 */

get_header();
?>

<main class="site-main home">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-slider swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="<?php echo get_theme_file_uri('assets/images/hero-1.jpg'); ?>" alt="Hero Image">
                    <div class="slide-content">
                        <h1 class="hero-title">发现全球顶尖建筑设计</h1>
                        <div class="hero-search">
                            <?php get_template_part('template-parts/components/search-box'); ?>
                        </div>
                    </div>
                </div>
                <!-- 添加更多幻灯片 -->
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </section>

    <!-- Featured Projects -->
    <section class="featured-projects">
        <div class="container">
            <div class="section-header">
                <h2>精选项目</h2>
                <div class="filter-tabs">
                    <button class="filter-btn active" data-filter="all">全部</button>
                    <button class="filter-btn" data-filter="residential">住宅</button>
                    <button class="filter-btn" data-filter="commercial">商业</button>
                    <button class="filter-btn" data-filter="cultural">文化</button>
                </div>
            </div>
            <div class="projects-grid">
                <?php
                $featured_projects = new WP_Query([
                    'post_type' => 'project',
                    'posts_per_page' => 6,
                    'meta_key' => 'featured_project',
                    'meta_value' => '1'
                ]);
                
                if ($featured_projects->have_posts()):
                    while ($featured_projects->have_posts()): $featured_projects->the_post();
                        get_template_part('template-parts/content', 'project-card');
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
            <div class="section-footer">
                <a href="<?php echo get_post_type_archive_link('project'); ?>" class="btn btn-primary">查看更多项目</a>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-products">
        <div class="container">
            <div class="section-header">
                <h2>热门产品</h2>
                <a href="<?php echo get_post_type_archive_link('product'); ?>" class="view-all">查看全部</a>
            </div>
            <div class="products-slider">
                <?php
                $featured_products = new WP_Query([
                    'post_type' => 'product',
                    'posts_per_page' => 8,
                    'meta_key' => 'featured_product',
                    'meta_value' => '1'
                ]);
                
                if ($featured_products->have_posts()):
                    while ($featured_products->have_posts()): $featured_products->the_post();
                        get_template_part('template-parts/content', 'product-card');
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
    </section>

    <!-- Featured Firms -->
    <section class="featured-firms">
        <div class="container">
            <div class="section-header">
                <h2>优秀建筑事务所</h2>
                <a href="<?php echo get_post_type_archive_link('firm'); ?>" class="view-all">查看全部</a>
            </div>
            <div class="firms-grid">
                <?php
                $featured_firms = new WP_Query([
                    'post_type' => 'firm',
                    'posts_per_page' => 4,
                    'meta_key' => 'featured_firm',
                    'meta_value' => '1'
                ]);
                
                if ($featured_firms->have_posts()):
                    while ($featured_firms->have_posts()): $featured_firms->the_post();
                        get_template_part('template-parts/content', 'firm-card');
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
?> 