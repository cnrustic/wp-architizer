<div class="pagination-wrapper">
    <?php 
    $args = [
        'prev_text' => '<i class="fas fa-chevron-left"></i>上一页',
        'next_text' => '下一页<i class="fas fa-chevron-right"></i>',
        'mid_size' => 2,
        'type' => 'list'
    ];
    echo paginate_links($args); 
    ?>
</div> 