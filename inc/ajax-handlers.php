<?php
/**
 * architizer functions and definitions
 */

function architizer_ajax_search() {
    // 安全检查
    if (!check_ajax_referer('architizer_search_nonce', 'nonce', false)) {
        wp_send_json_error(['message' => '安全验证失败']);
        return;
    }

    // 获取并验证搜索参数
    $search_query = sanitize_text_field($_GET['query'] ?? '');
    if (empty($search_query)) {
        wp_send_json_error(['message' => '搜索关键词不能为空']);
        return;
    }

    // 获取文章类型
    $post_type = sanitize_text_field($_GET['post_type'] ?? 'all');
    
    // 构建查询参数
    $args = [
        's' => $search_query,
        'posts_per_page' => 5,
        'post_status' => 'publish',
        'orderby' => 'relevance',
        'order' => 'DESC'
    ];

    // 设置文章类型
    if ($post_type !== 'all') {
        $args['post_type'] = $post_type;
    } else {
        $args['post_type'] = ['post', 'project', 'firm', 'product']; // 指定所有可搜索的文章类型
    }

    // 执行查询
    $results = new WP_Query($args);
    $suggestions = [];

    if ($results->have_posts()) {
        while ($results->have_posts()) {
            $results->the_post();
            $thumbnail = get_the_post_thumbnail_url(null, 'thumbnail');
            
            $suggestions[] = [
                'title' => get_the_title(),
                'url' => get_permalink(),
                'type' => get_post_type(),
                'thumbnail' => $thumbnail ?: '', // 确保缩略图为空时返回空字符串
                'excerpt' => wp_trim_words(get_the_excerpt(), 20) // 添加摘要
            ];
        }
        wp_reset_postdata();
    }

    wp_send_json_success([
        'results' => $suggestions,
        'total' => $results->found_posts
    ]);
}

// 注册 AJAX 动作
add_action('wp_ajax_architizer_search', 'architizer_ajax_search');
add_action('wp_ajax_nopriv_architizer_search', 'architizer_ajax_search'); 