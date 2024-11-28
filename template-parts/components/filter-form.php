<div class="filter-wrapper">
    <form class="filter-form" method="get">
        <div class="filter-groups">
            <?php if (!empty($args['taxonomies'])): ?>
                <?php foreach ($args['taxonomies'] as $tax): ?>
                    <div class="filter-group">
                        <select name="<?php echo esc_attr($tax['name']); ?>" class="filter-select">
                            <option value=""><?php echo esc_html($tax['label']); ?></option>
                            <?php 
                            $terms = get_terms($tax['taxonomy']);
                            foreach($terms as $term) {
                                printf(
                                    '<option value="%s" %s>%s</option>',
                                    $term->slug,
                                    selected($_GET[$tax['name']] ?? '', $term->slug, false),
                                    $term->name
                                );
                            }
                            ?>
                        </select>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($args['sort'])): ?>
                <div class="filter-group">
                    <select name="sort" class="filter-select">
                        <?php foreach ($args['sort'] as $key => $label): ?>
                            <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
        </div>
        
        <button type="submit" class="filter-submit">应用筛选</button>
    </form>
</div> 