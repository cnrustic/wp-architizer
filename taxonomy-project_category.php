<?php get_header(); ?>

<main class="site-main taxonomy-archive">
    <div class="container">
        <header class="archive-header">
            <h1 class="archive-title">
                <?php single_term_title('项目分类：'); ?>
            </h1>
            <?php
            $term_description = term_description();
            if (!empty($term_description)) :
                ?>
                <div class="archive-description">
                    <?php echo $term_description; ?>
                </div>
            <?php endif; ?>
            
            <!-- 分类统计 -->
            <div class="category-stats">
                共 <?php echo $wp_query->found_posts; ?> 个项目
            </div>
        </header>

        <!-- 筛选器 -->
        <div class="archive-filters">
            <form class="filters-form" method="get">
                <div class="filters-group">
                    <!-- 年份筛选 -->
                    <select name="project_year" class="filter-select">
                        <option value="">选择年份</option>
                        <?php
                        $years = range(date('Y'), 2000);
                        foreach ($years as $year) {
                            printf(
                                '<option value="%s" %s>%s年</option>',
                                $year,
                                selected($_GET['project_year'] ?? '', $year, false),
                                $year
                            );
                        }
                        ?>
                    </select>

                    <!-- 位置筛选 -->
                    <input type="text" 
                           name="project_location" 
                           class="filter-input" 
                           placeholder="输入位置"
                           value="<?php echo esc_attr($_GET['project_location'] ?? ''); ?>" />

                    <!-- 排序方式 -->
                    <select name="orderby" class="filter-select">
                        <option value="date" <?php selected($_GET['orderby'] ?? '', 'date'); ?>>最新发布</option>
                        <option value="title" <?php selected($_GET['orderby'] ?? '', 'title'); ?>>按标题</option>
                        <option value="menu_order" <?php selected($_GET['orderby'] ?? '', 'menu_order'); ?>>推荐项目</option>
                    </select>
                </div>

                <button type="submit" class="filter-submit">应用筛选</button>
            </form>
        </div>

        <?php if (have_posts()) : ?>
            <div class="projects-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article <?php post_class('project-card'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="project-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('large'); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="project-info">
                            <h2 class="project-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>

                            <div class="project-meta">
                                <?php if (get_field('project_location')) : ?>
                                    <span class="project-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo esc_html(get_field('project_location')); ?>
                                    </span>
                                <?php endif; ?>

                                <?php if (get_field('project_year')) : ?>
                                    <span class="project-year">
                                        <i class="far fa-calendar"></i>
                                        <?php echo esc_html(get_field('project_year')); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="project-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <?php
            // 分页导航
            the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => '<i class="fas fa-chevron-left"></i>',
                'next_text' => '<i class="fas fa-chevron-right"></i>',
            ));
            ?>

        <?php else : ?>
            <div class="no-results">
                <h2>暂无项目</h2>
                <p>该分类下还没有项目，请尝试其他分类或清除筛选条件。</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>