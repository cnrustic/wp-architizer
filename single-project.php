<?php get_header(); ?>

<main class="site-main single-project">
    <article id="project-<?php the_ID(); ?>" <?php post_class(); ?>>
        <!-- 项目头部信息 -->
        <header class="project-header">
            <div class="container">
                <div class="project-meta">
                    <h1 class="project-title"><?php the_title(); ?></h1>
                    <?php if (get_field('project_location')): ?>
                        <div class="project-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo esc_html(get_field('project_location')); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (get_field('project_year')): ?>
                        <div class="project-year">
                            <i class="far fa-calendar"></i>
                            <?php echo esc_html(get_field('project_year')); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <!-- 项目图片画廊 -->
        <div class="project-gallery">
            <div class="container">
                <?php if (have_rows('project_gallery')): ?>
                    <div class="gallery-grid">
                        <?php while (have_rows('project_gallery')): the_row(); 
                            $image = get_sub_field('image');
                            ?>
                            <div class="gallery-item">
                                <img src="<?php echo esc_url($image['url']); ?>" 
                                     alt="<?php echo esc_attr($image['alt']); ?>">
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 项目详情 -->
        <div class="project-content">
            <div class="container">
                <div class="project-description">
                    <?php the_content(); ?>
                </div>

                <!-- 项目信息侧边栏 -->
                <aside class="project-sidebar">
                    <?php if (get_field('architect')): ?>
                        <div class="project-info-item">
                            <h3>建筑师</h3>
                            <p><?php echo esc_html(get_field('architect')); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if (get_field('project_size')): ?>
                        <div class="project-info-item">
                            <h3>项目规模</h3>
                            <p><?php echo esc_html(get_field('project_size')); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php
                    $project_categories = get_the_terms(get_the_ID(), 'project_category');
                    if ($project_categories): ?>
                        <div class="project-info-item">
                            <h3>项目类别</h3>
                            <ul class="project-categories">
                                <?php foreach ($project_categories as $category): ?>
                                    <li><a href="<?php echo get_term_link($category); ?>"><?php echo $category->name; ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>
        </div>

        <!-- 相关项目 -->
        <div class="related-projects">
            <div class="container">
                <h2>相关项目</h2>
                <div class="projects-grid">
                    <?php
                    $related_projects = new WP_Query(array(
                        'post_type' => 'project',
                        'posts_per_page' => 3,
                        'post__not_in' => array(get_the_ID()),
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'project_category',
                                'terms' => wp_get_post_terms(get_the_ID(), 'project_category', array('fields' => 'ids'))
                            )
                        )
                    ));

                    if ($related_projects->have_posts()): 
                        while ($related_projects->have_posts()): $related_projects->the_post(); ?>
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