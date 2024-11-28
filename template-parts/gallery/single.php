<?php get_header(); ?>

<main class="site-main single-gallery">
    <article id="gallery-<?php the_ID(); ?>" <?php post_class(); ?>>
        <!-- 图片展示区 -->
        <div class="gallery-hero">
            <div class="container">
                <div class="image-wrapper">
                    <?php 
                    $image = get_field('gallery_image');
                    if ($image): ?>
                        <img src="<?php echo esc_url($image['url']); ?>" 
                             alt="<?php echo esc_attr($image['alt']); ?>">
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 图片信息 -->
        <div class="gallery-content">
            <div class="container">
                <div class="content-grid">
                    <!-- 左侧信息 -->
                    <div class="main-content">
                        <h1 class="gallery-title"><?php the_title(); ?></h1>
                        
                        <?php if (get_field('image_description')): ?>
                            <div class="gallery-description">
                                <?php echo get_field('image_description'); ?>
                            </div>
                        <?php endif; ?>

                        <!-- 相关项目 -->
                        <?php 
                        $project_id = get_field('related_project');
                        if ($project_id): ?>
                            <div class="related-project">
                                <h2>所属项目</h2>
                                <?php 
                                $project = get_post($project_id);
                                if ($project): ?>
                                    <a href="<?php echo get_permalink($project); ?>" class="project-link">
                                        <h3><?php echo get_the_title($project); ?></h3>
                                        <?php if (has_post_thumbnail($project)): ?>
                                            <?php echo get_the_post_thumbnail($project, 'medium'); ?>
                                        <?php endif; ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 右侧信息 -->
                    <aside class="gallery-sidebar">
                        <div class="sidebar-section">
                            <h3>图片信息</h3>
                            <ul class="image-meta">
                                <?php if (get_field('photographer')): ?>
                                    <li>
                                        <span class="label">摄影师</span>
                                        <span class="value"><?php echo get_field('photographer'); ?></span>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if (get_field('image_date')): ?>
                                    <li>
                                        <span class="label">拍摄日期</span>
                                        <span class="value"><?php echo get_field('image_date'); ?></span>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if (get_field('image_location')): ?>
                                    <li>
                                        <span class="label">拍摄地点</span>
                                        <span class="value"><?php echo get_field('image_location'); ?></span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <div class="sidebar-section">
                            <h3>分享图片</h3>
                            <div class="share-buttons">
                                <button class="share-btn" data-platform="facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </button>
                                <button class="share-btn" data-platform="twitter">
                                    <i class="fab fa-twitter"></i>
                                </button>
                                <button class="share-btn" data-platform="pinterest">
                                    <i class="fab fa-pinterest-p"></i>
                                </button>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </article>
</main>

<?php get_footer(); ?> 