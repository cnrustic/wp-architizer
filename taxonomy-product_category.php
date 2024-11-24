<?php get_header(); ?>

<main class="site-main taxonomy-archive">
    <div class="container">
        <header class="archive-header">
            <h1 class="archive-title">
                <?php single_term_title('产品分类：'); ?>
            </h1>
            <?php
            $term_description = term_description();
            if (!empty($term_description)) :
                ?>
                <div class="archive-description">
                    <?php echo $term_description; ?>
                </div>
            <?php endif; ?>
            
            <div class="category-stats">
                共 <?php echo $wp_query->found_posts; ?> 个产品
            </div>
        </header>

        <!-- 筛选器 -->
        <div class="archive-filters">
            <form class="filters-form" method="get">
                <div class="filters-group">
                    <!-- 制造商筛选 -->
                    <input type="text" 
                           name="manufacturer" 
                           class="filter-input" 
                           placeholder="输入制造商"
                           value="<?php echo esc_attr($_GET['manufacturer'] ?? ''); ?>" />

                    <!-- 价格区间 -->
                    <select name="price_range" class="filter-select">
                        <option value="">选择价格区间</option>
                        <option value="0-1000" <?php selected($_GET['price_range'] ?? '', '0-1000'); ?>>￥0 - ￥1000</option>
                        <option value="1000-5000" <?php selected($_GET['price_range'] ?? '', '1000-5000'); ?>>￥1000 - ￥5000</option>
                        <option value="5000+" <?php selected($_GET['price_range'] ?? '', '5000+'); ?>>￥5000以上</option>
                    </select>

                    <!-- 排序方式 -->
                    <select name="orderby" class="filter-select">
                        <option value="date" <?php selected($_GET['orderby'] ?? '', 'date'); ?>>最新发布</option>
                        <option value="title" <?php selected($_GET['orderby'] ?? '', 'title'); ?>>按标题</option>
                        <option value="price" <?php selected($_GET['orderby'] ?? '', 'price'); ?>>按价格</option>
                    </select>
                </div>

                <button type="submit" class="filter-submit">应用筛选</button>
            </form>
        </div>

        <?php if (have_posts()) : ?>
            <div class="products-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article <?php post_class('product-card'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="product-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('large'); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="product-info">
                            <h2 class="product-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>

                            <div class="product-meta">
                                <?php if (get_field('manufacturer')) : ?>
                                    <span class="product-manufacturer">
                                        <i class="fas fa-industry"></i>
                                        <?php echo esc_html(get_field('manufacturer')); ?>
                                    </span>
                                <?php endif; ?>

                                <?php if (get_field('price')) : ?>
                                    <span class="product-price">
                                        <i class="fas fa-tag"></i>
                                        ￥<?php echo number_format(get_field('price'), 2); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="product-excerpt">
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
                <h2>暂无产品</h2>
                <p>该分类下还没有产品，请尝试其他分类或清除筛选条件。</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?> 