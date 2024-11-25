<article class="firm-card">
    <div class="firm-logo">
        <?php 
        $logo = get_field('firm_logo');
        if ($logo) {
            echo wp_get_attachment_image($logo['ID'], 'medium');
        }
        ?>
    </div>
    <div class="firm-info">
        <h3><?php the_title(); ?></h3>
        <?php 
        $address = get_field('firm_address');
        if ($address) : ?>
            <p class="firm-location"><?php echo esc_html($address['address']); ?></p>
        <?php endif; ?>
    </div>
</article> 