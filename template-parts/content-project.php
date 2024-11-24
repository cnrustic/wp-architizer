<article <?php post_class('project-card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="project-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('large'); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="project-info">
        <h2 class="project-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>

        <div class="project-meta">
            <?php if (get_field('project_location')) : ?>
                <span class="project-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <?php echo esc_html(get_field('project_location')); ?>
                </span>
            <?php endif; ?>

            <?php if (get_field('project_year')) : ?>
                <span class="project-year">
                    <i class="far fa-calendar"></i>
                    <?php echo esc_html(get_field('project_year')); ?>
                </span>
            <?php endif; ?>
        </div>

        <div class="project-excerpt">
            <?php the_excerpt(); ?>
        </div>
    </div>

    <div class="project-actions">
        <button class="share-trigger">
            <i class="fas fa-share-alt"></i> 分享
        </button>
    </div>
</article> 