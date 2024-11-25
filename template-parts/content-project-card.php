<?php
/**
 * Template part for displaying project cards
 */
?>

<article class="project-card">
    <div class="project-image">
        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('large'); ?>
        <?php endif; ?>
    </div>
    <div class="project-info">
        <h3><?php the_title(); ?></h3>
        <?php 
        $firm_id = get_field('project_firm');
        if ($firm_id) : ?>
            <p class="project-firm"><?php echo get_the_title($firm_id); ?></p>
        <?php endif; ?>
        <?php 
        $location = get_field('project_location');
        if ($location) : ?>
            <p class="project-location"><?php echo esc_html($location['address']); ?></p>
        <?php endif; ?>
    </div>
</article> 