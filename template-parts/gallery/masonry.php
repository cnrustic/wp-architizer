<div class="masonry-item">
    <a href="<?php the_permalink(); ?>" class="gallery-card">
        <?php 
        $image = get_field('gallery_image');
        if ($image): ?>
            <div class="card-image">
                <img src="<?php echo esc_url($image['sizes']['medium_large']); ?>" 
                     alt="<?php echo esc_attr($image['alt']); ?>"
                     loading="lazy">
            </div>
        <?php endif; ?>
        
        <div class="card-overlay">
            <div class="overlay-content">
                <h3 class="image-title"><?php the_title(); ?></h3>
                
                <?php 
                $project_id = get_field('related_project');
                if ($project_id): ?>
                    <p class="project-name">
                        <?php echo get_the_title($project_id); ?>
                    </p>
                <?php endif; ?>
                
                <div class="image-meta">
                    <span class="views">
                        <i class="material-icons">visibility</i>
                        <?php echo get_post_meta(get_the_ID(), 'image_views', true); ?>
                    </span>
                    <span class="likes">
                        <i class="material-icons">favorite</i>
                        <?php echo get_post_meta(get_the_ID(), 'image_likes', true); ?>
                    </span>
                </div>
            </div>
        </div>
    </a>
</div> 