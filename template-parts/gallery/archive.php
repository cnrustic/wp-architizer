<?php get_header(); ?>

<main class="site-main gallery-archive">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title">项目图库</h1>
            
            <!-- 筛选器 -->
            <div class="gallery-filters">
                <form class="filters-form" method="get">
                    <div class="filter-group">
                        <select name="project_type" class="filter-select">
                            <option value="">项目类型</option>
                            <?php 
                            $types = get_terms('project_type');
                            foreach($types as $type) {
                                printf(
                                    '<option value="%s" %s>%s</option>',
                                    $type->slug,
                                    selected($_GET['project_type'] ?? '', $type->slug, false),
                                    $type->name
                                );
                            }
                            ?>
                        </select>

                        <select name="sort" class="filter-select">
                            <option value="popular">最受欢迎</option>
                            <option value="latest">最新上传</option>
                            <option value="random">随机浏览</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="filter-submit">应用筛选</button>
                </form>
            </div>
        </header>

        <?php if (have_posts()): ?>
            <!-- 瀑布流布局 -->
            <div class="gallery-masonry">
                <?php 
                while (have_posts()): the_post();
                    get_template_part('template-parts/gallery/masonry');
                endwhile; 
                ?>
            </div>
            
            <!-- 加载更多按钮 -->
            <div class="load-more">
                <button class="load-more-btn" data-page="1">
                    加载更多
                    <i class="material-icons">expand_more</i>
                </button>
            </div>
            
        <?php else: ?>
            <div class="no-results">
                <h2>暂无图片</h2>
                <p>请尝试其他筛选条件。</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?> 