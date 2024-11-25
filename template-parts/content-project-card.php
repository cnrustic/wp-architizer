<?php
/**
 * Template part for displaying project cards
 */
?>

<article class="project-card">
    <div class="project-thumbnail">
        <a href="<?php the_permalink(); ?>">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('large'); ?>
            <?php endif; ?>
        </a>
    </div>
    <div class="project-info">
        <h3 class="project-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        <div class="project-meta">
            <?php
            $location = get_the_terms(get_the_ID(), 'project_location');
            if ($location) {
                echo '<span class="location">' . $location[0]->name . '</span>';
            }
            ?>
            <span class="firm">
                <?php
                $firm_id = get_post_meta(get_the_ID(), 'project_firm', true);
                if ($firm_id) {
                    echo '<a href="' . get_permalink($firm_id) . '">' . get_the_title($firm_id) . '</a>';
                }
                ?>
            </span>
        </div>
    </div>
</article> 