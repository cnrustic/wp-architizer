<?php get_header(); ?>

<main class="site-main brands-archive">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title">品牌目录</h1>
            
            <!-- 品牌搜索 -->
            <div class="brands-search">
                <form role="search" method="get" class="search-form">
                    <input type="search" 
                           class="search-field" 
                           placeholder="搜索品牌..."
                           value="<?php echo get_search_query(); ?>" 
                           name="s" />
                    <input type="hidden" name="post_type" value="brand" />
                    <button type="submit" class="search-submit">
                        <i class="material-icons">search</i>
                    </button>
                </form>
            </div>
        </header>

        <!-- 品牌字母导航 -->
        <nav class="brands-alphabet">
            <?php
            $letters = range('A', 'Z');
            foreach ($letters as $letter) {
                printf(
                    '<a href="#letter-%s" class="letter-link">%s</a>',
                    $letter,
                    $letter
                );
            }
            ?>
        </nav>

        <!-- 品牌列表 -->
        <div class="brands-grid">
            <?php
            $brands = get_terms([
                'taxonomy' => 'product_brand',
                'hide_empty' => false,
            ]);

            if ($brands):
                $grouped_brands = [];
                foreach ($brands as $brand) {
                    $first_letter = strtoupper(substr($brand->name, 0, 1));
                    $grouped_brands[$first_letter][] = $brand;
                }
                ksort($grouped_brands);

                foreach ($grouped_brands as $letter => $letter_brands): ?>
                    <div id="letter-<?php echo $letter; ?>" class="brand-group">
                        <h2 class="letter-heading"><?php echo $letter; ?></h2>
                        <div class="brand-cards">
                            <?php foreach ($letter_brands as $brand): ?>
                                <a href="<?php echo get_term_link($brand); ?>" class="brand-card">
                                    <?php 
                                    $logo = get_field('brand_logo', $brand);
                                    if ($logo): ?>
                                        <div class="brand-logo">
                                            <img src="<?php echo esc_url($logo['url']); ?>" 
                                                 alt="<?php echo esc_attr($brand->name); ?>">
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="brand-info">
                                        <h3 class="brand-name"><?php echo $brand->name; ?></h3>
                                        <span class="product-count">
                                            <?php echo $brand->count; ?> 个产品
                                        </span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach;
            endif; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?> 