<?php get_header(); ?>

<main class="site-main">
    <!-- 英雄区域 -->
    <section class="hero">
        <div class="container">
            <h1>发现世界顶级建筑设计</h1>
            <div class="search-form">
                <?php get_search_form(); ?>
            </div>
        </div>
    </section>

    <!-- 特色项目 -->
    <section class="featured-projects">
        <div class="container">
            <h2>特色项目</h2>
            <div class="projects-grid">
                <?php
                $featured_projects = new WP_Query(array(
                    'post_type' => 'project',
                    'posts_per_page' => 6,
                    'meta_key' => 'featured_project',
                    'meta_value' => '1'
                ));

                if ($featured_projects->have_posts()) :
                    while ($featured_projects->have_posts()) : $featured_projects->the_post();
                        ?>
                        <article class="project-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="project-thumbnail">
                                    <?php the_post_thumbnail('large'); ?>
                                </div>
                            <?php endif; ?>
                            <div class="project-info">
                                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <?php the_excerpt(); ?>
                            </div>
                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
    </section>

    <!-- 最新公司 -->
    <section class="latest-firms">
        <div class="container">
            <h2>最新入驻公司</h2>
            <div class="firms-grid">
                <?php
                $latest_firms = new WP_Query(array(
                    'post_type' => 'firm',
                    'posts_per_page' => 4
                ));

                if ($latest_firms->have_posts()) :
                    while ($latest_firms->have_posts()) : $latest_firms->the_post();
                        ?>
                        <article class="firm-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="firm-logo">
                                    <?php the_post_thumbnail('medium'); ?>
                                </div>
                            <?php endif; ?>
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?> 