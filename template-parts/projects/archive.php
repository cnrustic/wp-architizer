<div class="projects-grid">
    <div class="projects-filter">
        <?php get_template_part('template-parts/projects/filter'); ?>
    </div>
    
    <div class="projects-list">
        <?php if(have_posts()): ?>
            <?php while(have_posts()): the_post(); ?>
                <?php get_template_part('template-parts/projects/grid-item'); ?>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <div class="projects-pagination">
        <?php architizer_pagination(); ?>
    </div>
</div> 