<?php get_header(); ?>

<main class="site-main single-project">
    <article id="project-<?php the_ID(); ?>" <?php post_class(); ?>>
        <!-- 项目头部信息 -->
        <header class="project-header">
            <div class="container">
                <nav class="breadcrumb">
                    <a href="<?php echo home_url('/projects/'); ?>">项目</a>
                    <?php 
                    $categories = get_the_terms(get_the_ID(), 'project_category');
                    if ($categories) {
                        echo ' / <a href="' . get_term_link($categories[0]) . '">' . $categories[0]->name . '</a>';
                    }
                    ?>
                    <span class="current"><?php the_title(); ?></span>
                </nav>

                <div class="project-meta">
                    <h1 class="project-title"><?php the_title(); ?></h1>
                    <div class="project-info">
                        <?php if (get_field('project_location')): ?>
                            <div class="info-item">
                                <i class="material-icons">location_on</i>
                                <?php echo esc_html(get_field('project_location')); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (get_field('project_year')): ?>
                            <div class="info-item">
                                <i class="material-icons">calendar_today</i>
                                <?php echo esc_html(get_field('project_year')); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (get_field('project_size')): ?>
                            <div class="info-item">
                                <i class="material-icons">square_foot</i>
                                <?php echo esc_html(get_field('project_size')); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>

        <!-- 项目主图 -->
        <div class="project-hero">
            <?php 
            $gallery = get_field('project_gallery');
            if ($gallery): ?>
                <div class="hero-image">
                    <img src="<?php echo esc_url($gallery[0]['url']); ?>" 
                    alt="<?php echo esc_attr($gallery[0]['alt']); ?>">
</div>
<?php if (count($gallery) > 1): ?>
<button class="view-gallery">
<i class="material-icons">collections</i>
查看全部图片 (<?php echo count($gallery); ?>)
</button>
<?php endif; ?>
<?php endif; ?>
</div>
<!-- 项目内容区 -->
<div class="project-content">
<div class="container">
<div class="content-grid">
<!-- 左侧内容 -->
<div class="main-content">
<div class="project-description">
<?php the_content(); ?>
</div>
<?php if (get_field('project_specs')): ?>
<div class="project-specs">
<h2>项目规格</h2>
<div class="specs-grid">
<?php while(have_rows('project_specs')): the_row(); ?>
<div class="spec-item">
<div class="spec-label"><?php echo get_sub_field('spec_label'); ?></div>
<div class="spec-value"><?php echo get_sub_field('spec_value'); ?></div>
</div>
<?php endwhile; ?>
</div>
</div>
<?php endif; ?>
</div>
<!-- 右侧信息 -->
<aside class="project-sidebar">
<?php if (get_field('architect')): ?>
<div class="sidebar-section">
<h3>建筑师</h3>
<div class="architect-info">
<a href="<?php echo get_field('architect_link'); ?>" class="architect-link">
<?php echo get_field('architect'); ?>
</a>
</div>
</div>
<?php endif; ?>
<?php if ($categories): ?>
<div class="sidebar-section">
<h3>项目类别</h3>
<div class="category-tags">
<?php foreach ($categories as $category): ?>
<a href="<?php echo get_term_link($category); ?>" class="category-tag">
<?php echo $category->name; ?>
</a>
<?php endforeach; ?>
</div>
</div>
<?php endif; ?>
<div class="sidebar-section">
<h3>分享项目</h3>
<div class="share-buttons">
<button class="share-btn" data-platform="facebook">
<i class="fab fa-facebook-f"></i>
</button>
<button class="share-btn" data-platform="twitter">
<i class="fab fa-twitter"></i>
</button>
<button class="share-btn" data-platform="linkedin">
<i class="fab fa-linkedin-in"></i>
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
<!-- 相关项目 -->
<section class="related-projects">
<div class="container">
<h2>相关项目</h2>
<div class="projects-grid">
<?php
$related = new WP_Query(array(
'post_type' => 'project',
'posts_per_page' => 3,
'postnot_in' => array(get_the_ID()),
'tax_query' => array(
array(
'taxonomy' => 'project_category',
'terms' => wp_get_post_terms(get_the_ID(), 'project_category', array('fields' => 'ids'))
)
)
));
while ($related->have_posts()): $related->the_post();
get_template_part('template-parts/content', 'project-card');
endwhile;
wp_reset_postdata();
?>
</div>
</div>
</section>
</article>
<!-- 图片画廊弹窗 -->
<div class="gallery-modal" style="display: none;">
<div class="gallery-slider">
<?php
if ($gallery):
foreach ($gallery as $image): ?>
<div class="gallery-slide">
<img src="<?php echo esc_url($image['url']); ?>"
alt="<?php echo esc_attr($image['alt']); ?>">
</div>
<?php endforeach;
endif;
?>
</div>
<button class="close-gallery">
<i class="material-icons">close</i>
</button>
</div>
</main>
<?php get_footer(); ?>