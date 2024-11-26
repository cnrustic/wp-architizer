<?php get_header(); ?>

<main class="site-main single-product">
    <article id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
        <!-- 面包屑导航 -->
        <div class="breadcrumb-wrapper">
            <div class="container">
                <?php architizer_breadcrumb(); ?>
            </div>
        </div>

        <!-- 产品头部信息 - 添加更多元数据 -->
        <header class="product-header">
            <div class="container">
                <div class="product-meta">
                    <?php 
                    // 获取品牌信息
                    $brands = get_the_terms(get_the_ID(), 'product_brand');
                    if ($brands): ?>
                        <div class="product-brand">
                            <?php echo esc_html($brands[0]->name); ?>
                        </div>
                    <?php endif; ?>
                    
                    <h1 class="product-title"><?php the_title(); ?></h1>
                    
                    <div class="product-meta-details">
                        <?php if (get_field('manufacturer')): ?>
                            <div class="meta-item">
                                <i class="fas fa-industry"></i>
                                <span><?php echo esc_html(get_field('manufacturer')); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (get_field('product_code')): ?>
                            <div class="meta-item">
                                <i class="fas fa-barcode"></i>
                                <span><?php echo esc_html(get_field('product_code')); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>

        <!-- 产品展示区 - 优化画廊布局 -->
        <div class="product-showcase">
            <div class="container">
                <div class="product-content-grid">
                    <div class="product-gallery">
                        <?php if (have_rows('product_gallery')): ?>
                            <div class="gallery-main">
                                <div class="swiper-container gallery-main-slider">
                                    <div class="swiper-wrapper">
                                        <?php while (have_rows('product_gallery')): the_row(); 
                                            $image = get_sub_field('image');
                                            ?>
                                            <div class="swiper-slide">
                                                <div class="gallery-image-wrapper">
                                                    <img src="<?php echo esc_url($image['url']); ?>" 
                                                         alt="<?php echo esc_attr($image['alt']); ?>"
                                                         loading="lazy">
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                    <div class="swiper-button-next"></div>
                                    <div class="swiper-button-prev"></div>
                                </div>
                            </div>
                            
                            <div class="gallery-thumbs">
                                <div class="swiper-container gallery-thumbs-slider">
                                    <div class="swiper-wrapper">
                                        <?php while (have_rows('product_gallery')): the_row(); 
                                            $image = get_sub_field('image');
                                            ?>
                                            <div class="swiper-slide">
                                                <div class="thumb-image">
                                                    <img src="<?php echo esc_url($image['sizes']['thumbnail']); ?>" 
                                                         alt="<?php echo esc_attr($image['alt']); ?>"
                                                         loading="lazy">
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="product-info">
                        <!-- 添加快速操作按钮 -->
                        <div class="product-actions">
                            <button class="action-btn download-btn">
                                <i class="fas fa-download"></i> 下载资料
                            </button>
                            <button class="action-btn share-btn">
                                <i class="fas fa-share-alt"></i> 分享
                            </button>
                            <button class="action-btn contact-btn">
                                <i class="fas fa-envelope"></i> 联系供应商
                            </button>
                        </div>

                        <!-- 产品描述 -->
                        <div class="product-description">
                            <?php the_content(); ?>
                        </div>

                        <!-- 产品规格 - 添加标签筛选 -->
                        <?php if (have_rows('product_specifications')): ?>
                            <div class="product-specifications">
                                <div class="specs-header">
                                    <h3>产品规格</h3>
                                    <div class="specs-filter">
                                        <!-- 规格标签筛选 -->
                                    </div>
                                </div>
                                <div class="specs-content">
                                    <table class="specs-table">
                                        <?php while (have_rows('product_specifications')): the_row(); ?>
                                            <tr data-category="<?php echo esc_attr(get_sub_field('spec_category')); ?>">
                                                <th><?php echo esc_html(get_sub_field('spec_name')); ?></th>
                                                <td><?php echo esc_html(get_sub_field('spec_value')); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- 技术资料 - 优化下载体验 -->
                        <?php if (have_rows('product_documents')): ?>
                            <div class="product-documents">
                                <h3>技术资料</h3>
                                <div class="document-grid">
                                    <?php while (have_rows('product_documents')): the_row(); 
                                        $document = get_sub_field('document');
                                        ?>
                                        <div class="document-card">
                                            <div class="document-icon">
                                                <i class="fas fa-file-pdf"></i>
                                            </div>
                                            <div class="document-info">
                                                <h4><?php echo esc_html($document['title']); ?></h4>
                                                <span class="document-size">
                                                    <?php echo size_format(filesize(get_attached_file($document['ID']))); ?>
                                                </span>
                                            </div>
                                            <a href="<?php echo esc_url($document['url']); ?>" 
                                               class="document-download" 
                                               target="_blank"
                                               download>
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- 相关产品 - 优化展示效果 -->
        <div class="related-products">
            <div class="container">
                <div class="section-header">
                    <h2>相关产品</h2>
                    <a href="<?php echo get_post_type_archive_link('product'); ?>" class="view-all">
                        查看全部 <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="products-slider">
                    <div class="swiper-container related-products-slider">
                        <div class="swiper-wrapper">
                            <?php
                            $related_query = architizer_get_related_products(get_the_ID(), 6);
                            if ($related_query->have_posts()): 
                                while ($related_query->have_posts()): $related_query->the_post(); ?>
                                    <div class="swiper-slide">
                                        <?php get_template_part('template-parts/content', 'product-card'); ?>
                                    </div>
                                <?php endwhile;
                                wp_reset_postdata();
                            endif; ?>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
        </div>
    </article>
</main>

<?php get_footer(); ?> 