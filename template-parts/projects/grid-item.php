<article <?php post_class('project-card animate-on-scroll'); ?> data-aos="fade-up">
    <div class="project-media">
        <?php if (has_post_thumbnail()): ?>
            <a href="<?php the_permalink(); ?>" class="project-thumbnail">
                <?php 
                the_post_thumbnail('project-thumb', [
                    'loading' => 'lazy',
                    'class' => 'project-image'
                ]); 
                ?>
            </a>
        <?php endif; ?>
        
        <div class="project-overlay">
            <div class="project-actions">
                <?php get_template_part('template-parts/components/save-button'); ?>
                <?php get_template_part('template-parts/components/share-button'); ?>
            </div>
        </div>
    </div>

    <div class="project-content">
        <!-- 原有的项目信息内容 -->
    </div>
</article> 