<?php
/**
 * The front page template file
 */

get_header();
?>

<main id="primary" class="site-main">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1>探索全球优秀建筑设计</h1>
            <div class="search-bar">
                <form role="search" method="get" class="search-form" action="<?php echo home_url('/'); ?>">
                    <input type="search" class="search-field" placeholder="搜索项目、建筑师或产品..." value="<?php echo get_search_query(); ?>" name="s" />
                    <button type="submit" class="search-submit">搜索</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Featured Projects -->
    <section class="featured-projects">
        <div class="container">
            <div class="section-title">
                <h2>精选项目</h2>
            </div>
            <div class="projects-grid">
                <?php
                $args = array(
                    'post_type' => 'project',
                    'posts_per_page' => 3,
                    'orderby' => 'date',
                    'order' => 'DESC'
                );
                $projects_query = new WP_Query($args);
                
                if ($projects_query->have_posts()) :
                    while ($projects_query->have_posts()) : $projects_query->the_post();
                        get_template_part('template-parts/content', 'project-card');
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
            <div class="view-all">
                <a href="<?php echo get_post_type_archive_link('project'); ?>" class="button">查看更多项目</a>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
?> 