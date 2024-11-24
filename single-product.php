<?php get_header(); ?>

<main class="site-main single-product">
    <article id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
        <!-- 产品头部信息 -->
        <header class="product-header">
            <div class="container">
                <div class="product-meta">
                    <h1 class="product-title"><?php the_title(); ?></h1>
                    <?php if (get_field('manufacturer')): ?>
                        <div class="product-manufacturer">
                            <span>制造商：</span>
                            <?php echo esc_html(get_field('manufacturer')); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <!-- 产品展示区 -->
        <div class="product-showcase">
            <div class="container">
                <div class="product-gallery">
                    <?php if (have_rows('product_gallery')): ?>
                        <div class="gallery-main">
                            <?php 
                            $first_image = true;
                            while (have_rows('product_gallery')): the_row(); 
                                $image = get_sub_field('image');
                                ?>
                                <div class="gallery-main-item <?php echo $first_image ? 'active' : ''; ?>">
                                    <img src="<?php echo esc_url($image['url']); ?>" 
                                         alt="<?php echo esc_attr($image['alt']); ?>">
                                </div>
                                <?php 
                                $first_image = false;
                            endwhile; ?>
                        </div>
                        <div class="gallery-thumbs">
                            <?php while (have_rows('product_gallery')): the_row(); 
                                $image = get_sub_field('image');
                                ?>
                                <div class="gallery-thumb">
                                    <img src="<?php echo esc_url($image['sizes']['thumbnail']); ?>" 
                                         alt="<?php echo esc_attr($image['alt']); ?>">
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="product-info">
                    <!-- 产品描述 -->
                    <div class="product-description">
                        <?php the_content(); ?>
                    </div>

                    <!-- 产品规格 -->
                    <?php if (have_rows('product_specifications')): ?>
                        <div class="product-specifications">
                            <h3>产品规格</h3>
                            <table class="specs-table">
                                <?php while (have_rows('product_specifications')): the_row(); ?>
                                    <tr>
                                        <th><?php echo esc_html(get_sub_field('spec_name')); ?></th>
                                        <td><?php echo esc_html(get_sub_field('spec_value')); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </table>
                        </div>
                    <?php endif; ?>

                    <!-- 下载资料 -->
                    <?php if (have_rows('product_documents')): ?>
                        <div class="product-documents">
                            <h3>技术资料</h3>
                            <ul class="document-list">
                                <?php while (have_rows('product_documents')): the_row(); 
                                    $document = get_sub_field('document');
                                    ?>
                                    <li>
                                        <a href="<?php echo esc_url($document['url']); ?>" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                            <?php echo esc_html($document['title']); ?>
                                        </a>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 相关产品 -->
        <div class="related-products">
            <div class="container">
                <h2>相关产品</h2>
                <div class="products-grid">
                    <?php
                    $related_products = new WP_Query(array(
                        'post_type' => 'product',
                        'posts_per_page' => 3,
                        'post__not_in' => array(get_the_ID()),
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'product_category',
                                'terms' => wp_get_post_terms(get_the_ID(), 'product_category', array('fields' => 'ids'))
                            )
                        )
                    ));

                    if ($related_products->have_posts()): 
                        while ($related_products->have_posts()): $related_products->the_post(); ?>
                            <div class="product-card">
                                <?php if (has_post_thumbnail()): ?>
                                    <div class="product-thumbnail">
                                        <?php the_post_thumbnail('medium_large'); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="product-card-info">
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