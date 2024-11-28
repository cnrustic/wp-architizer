<?php get_header(); ?>

<main class="site-main products-archive">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title">产品目录</h1>
            
            <!-- 筛选器 -->
            <div class="products-filters">
                <form class="filters-form" method="get">
                    <div class="filter-group">
                        <!-- 品牌筛选 -->
                        <select name="brand" class="filter-select">
                            <option value="">选择品牌</option>
                            <?php 
                            $brands = get_terms('product_brand');
                            foreach($brands as $brand) {
                                printf(
                                    '<option value="%s" %s>%s</option>',
                                    $brand->slug,
                                    selected($_GET['brand'] ?? '', $brand->slug, false),
                                    $brand->name
                                );
                            }
                            ?>
                        </select>

                        <!-- 分类筛选 -->
                        <select name="category" class="filter-select">
                            <option value="">选择分类</option>
                            <?php 
                            $categories = get_terms('product_category');
                            foreach($categories as $category) {
                                printf(
                                    '<option value="%s" %s>%s</option>',
                                    $category->slug,
                                    selected($_GET['category'] ?? '', $category->slug, false),
                                    $category->name
                                );
                            }
                            ?>
                        </select>

                        <!-- 排序方式 -->
                        <select name="orderby" class="filter-select">
                            <option value="date">最新产品</option>
                            <option value="popular">最受欢迎</option>
                            <option value="price-low">价格从低到高</option>
                            <option value="price-high">价格从高到低</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="filter-submit">应用筛选</button>
                </form>
            </div>
        </header>

        <?php if (have_posts()): ?>
            <div class="products-grid">
                <?php 
                while (have_posts()): the_post();
                    get_template_part('template-parts/content', 'product-card');
                endwhile; 
                ?>
            </div>
            
            <?php architizer_pagination(); ?>
            
        <?php else: ?>
            <div class="no-results">
                <h2>暂无产品</h2>
                <p>请尝试其他筛选条件。</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?> 