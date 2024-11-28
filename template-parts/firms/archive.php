<?php get_header(); ?>

<main class="site-main firms-archive">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title">建筑事务所</h1>
            
            <!-- 筛选器 -->
            <div class="firms-filters">
                <form class="filters-form" method="get">
                    <div class="filter-group">
                        <input type="text" 
                               name="location" 
                               placeholder="位置"
                               value="<?php echo esc_attr($_GET['location'] ?? ''); ?>">
                               
                        <select name="size">
                            <option value="">公司规模</option>
                            <option value="small">小型 (1-50人)</option>
                            <option value="medium">中型 (51-200人)</option>
                            <option value="large">大型 (200人以上)</option>
                        </select>
                        
                        <select name="orderby">
                            <option value="name">按名称</option>
                            <option value="date">最新加入</option>
                            <option value="projects">项目数量</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="filter-submit">应用筛选</button>
                </form>
            </div>
        </header>

        <?php if (have_posts()): ?>
            <div class="firms-grid">
                <?php 
                while (have_posts()): the_post();
                    get_template_part('template-parts/content', 'firm-card');
                endwhile; 
                ?>
            </div>
            
            <?php the_posts_pagination(); ?>
            
        <?php else: ?>
            <div class="no-results">
                <h2>未找到事务所</h2>
                <p>请尝试其他筛选条件。</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?> 