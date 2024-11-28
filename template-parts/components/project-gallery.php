<div class="project-gallery">
    <div class="gallery-main swiper">
        <div class="swiper-wrapper">
            <?php foreach ($gallery as $image): ?>
                <div class="swiper-slide">
                    <figure class="gallery-image">
                        <img src="<?php echo esc_url($image['url']); ?>" 
                             alt="<?php echo esc_attr($image['alt']); ?>"
                             loading="lazy">
                        <?php if ($image['caption']): ?>
                            <figcaption><?php echo esc_html($image['caption']); ?></figcaption>
                        <?php endif; ?>
                    </figure>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
    
    <div class="gallery-thumbs swiper">
        <div class="swiper-wrapper">
            <?php foreach ($gallery as $image): ?>
                <div class="swiper-slide">
                    <img src="<?php echo esc_url($image['sizes']['thumbnail']); ?>" 
                         alt="<?php echo esc_attr($image['alt']); ?>">
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div> 