<?php
/**
 * Template part for displaying project cards
 */
?>

php:template-parts/content-project-card.php
<article <?php post_class('project-card'); ?>>
<div class="card-media">
<?php if (has_post_thumbnail()): ?>
<a href="<?php the_permalink(); ?>" class="card-image">
<?php the_post_thumbnail('project-card'); ?>
</a>
<?php endif; ?>
<div class="card-overlay">
<div class="overlay-content">
<?php
$categories = get_the_terms(get_the_ID(), 'project_category');
if ($categories): ?>
<div class="card-categories">
<?php foreach (array_slice($categories, 0, 2) as $category): ?>
<a href="<?php echo get_term_link($category); ?>" class="category-tag">
<?php echo $category->name; ?>
</a>
<?php endforeach; ?>
</div>
<?php endif; ?>
<div class="card-actions">
<button class="action-btn like-btn" data-project="<?php the_ID(); ?>">
<i class="material-icons">favorite_border</i>
</button>
<button class="action-btn share-btn">
<i class="material-icons">share</i>
</button>
</div>
</div>
</div>
</div>
<div class="card-content">
<h3 class="card-title">
<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
</h3>
<div class="card-meta">
<?php if (get_field('architect')): ?>
<a href="<?php echo get_field('architect_link'); ?>" class="architect-link">
<?php echo get_field('architect'); ?>
</a>
<?php endif; ?>
<?php if (get_field('project_location')): ?>
<span class="location">
<i class="material-icons">location_on</i>
<?php echo get_field('project_location'); ?>
</span>
<?php endif; ?>
</div>
</div>
</article>