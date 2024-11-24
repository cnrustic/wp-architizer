<?php get_header(); ?>

<main class="site-main single-firm">
    <article id="firm-<?php the_ID(); ?>" <?php post_class(); ?>>
        <!-- 公司头部信息 -->
        <header class="firm-header">
            <div class="container">
                <div class="firm-meta">
                    <?php if (has_post_thumbnail()): ?>
                        <div class="firm-logo">
                            <?php the_post_thumbnail('medium'); ?>
                        </div>
                    <?php endif; ?>
                    <h1 class="firm-title"><?php the_title(); ?></h1>
                    <?php if (get_field('firm_location')): ?>
                        <div class="firm-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo esc_html(get_field('firm_location')); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <!-- 公司简介 -->
        <div class="firm-content">
            <div class="container">
                <div class="firm-description">
                    <?php the_content(); ?>
                </div>

                <!-- 公司信息侧边栏 -->
                <aside class="firm-sidebar">
                    <?php if (get_field('firm_website')): ?>
                        <div class="firm-info-item">
                            <h3>官方网站</h3>
                            <a href="<?php echo esc_url(get_field('firm_website')); ?>" target="_blank">访问网站</a>
                        </div>
                    <?php endif; ?>

                    <?php if (get_field('firm_employees')): ?>
                        <div class="firm-info-item">
                            <h3>员工规模</h3>
                            <p><?php echo esc_html(get_field('firm_employees')); ?></p>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>
        </div>

        <!-- 公司项目展示 -->
        <div class="firm-projects">
            <div class="container">
                <h2>公司项目</h2>
                <div class="projects-grid">
                    <?php
                    $firm_projects = new WP_Query(array(
                        'post_type' => 'project',
                        'posts_per_page' => 6,
                        'meta_query' => array(
                            array(
                                'key' => 'project_firm',
                                'value' => get_the_ID(),
                                'compare' => '='
                            )
                        )
                    ));

                    if ($firm_projects->have_posts()): 
                        while ($firm_projects->have_posts()): $firm_projects->the_post(); ?>
                            <div class="project-card">
                                <?php if (has_post_thumbnail()): ?>
                                    <div class="project-thumbnail">
                                        <?php the_post_thumbnail('medium_large'); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="project-info">
                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                </div>
                            </div>
                        <?php endwhile;
                        wp_reset_postdata();
                    endif; ?>
                </div>
            </div>
        </div>
    </article>
</main>

<?php get_footer(); ?> 