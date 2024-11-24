<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package architizer
 */

get_header();
?>

<main class="site-main search-results">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title">
                <?php
                $search_type = isset($_GET['post_type']) ? $_GET['post_type'] : 'all';
                $type_label = array(
                    'all' => '所有内容',
                    'project' => '项目',
                    'firm' => '公司',
                    'product' => '产品'
                );
                printf(
                    '搜索"%s"的%s结果',
                    '<span>' . get_search_query() . '</span>',
                    $type_label[$search_type]
                );
                ?>
            </h1>
            <div class="search-stats">
                找到 <?php echo $wp_query->found_posts; ?> 个结果
            </div>
        </header>

        <!-- 搜索筛选器 -->
        <div class="search-filters-bar">
            <form class="filters-form" method="get" action="<?php echo home_url('/'); ?>">
                <input type="hidden" name="s" value="<?php echo get_search_query(); ?>" />
                
                <div class="filter-controls">
                    <!-- 排序选项 -->
                    <select name="orderby" class="orderby-select">
                        <option value="date" <?php selected(get_query_var('orderby'), 'date'); ?>>最新发布</option>
                        <option value="title" <?php selected(get_query_var('orderby'), 'title'); ?>>按标题</option>
                        <option value="relevance" <?php selected(get_query_var('orderby'), 'relevance'); ?>>相关度</option>
                    </select>

                    <!-- 每页显示数量 -->
                    <select name="posts_per_page" class="per-page-select">
                        <option value="12" <?php selected(get_query_var('posts_per_page'), 12); ?>>每页12个</option>
                        <option value="24" <?php selected(get_query_var('posts_per_page'), 24); ?>>每页24个</option>
                        <option value="48" <?php selected(get_query_var('posts_per_page'), 48); ?>>每页48个</option>
                    </select>
                </div>
            </form>
        </div>

        <?php if (have_posts()) : ?>
            <div class="search-results-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article <?php post_class('search-item'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="search-item-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="search-item-content">
                            <!-- 文章类型标签 -->
                            <div class="post-type-label <?php echo get_post_type(); ?>">
                                <?php 
                                $post_type_obj = get_post_type_object(get_post_type());
                                echo $post_type_obj->labels->singular_name;
                                ?>
                            </div>

                            <h2 class="entry-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>

                            <div class="entry-meta">
                                <?php
                                // 根据不同文章类型显示不同的元信息
                                switch (get_post_type()) {
                                    case 'project':
                                        $location = get_field('project_location');
                                        if ($location) {
                                            echo '<span class="location"><i class="fas fa-map-marker-alt"></i> ' . esc_html($location) . '</span>';
                                        }
                                        break;
                                    case 'firm':
                                        $firm_location = get_field('firm_location');
                                        if ($firm_location) {
                                            echo '<span class="location"><i class="fas fa-building"></i> ' . esc_html($firm_location) . '</span>';
                                        }
                                        break;
                                    case 'product':
                                        $manufacturer = get_field('manufacturer');
                                        if ($manufacturer) {
                                            echo '<span class="manufacturer"><i class="fas fa-industry"></i> ' . esc_html($manufacturer) . '</span>';
                                        }
                                        break;
                                }
                                ?>
                            </div>

                            <div class="entry-summary">
                                <?php the_excerpt(); ?>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <?php
            // 分页导航
            the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => '<i class="fas fa-chevron-left"></i>',
                'next_text' => '<i class="fas fa-chevron-right"></i>',
            ));
            ?>

        <?php else : ?>
            <div class="no-results">
                <h2>未找到结果</h2>
                <p>抱歉，没有找到符合您搜索条件的内容。请尝试其他关键词或筛选条件。</p>
                
                <div class="search-suggestions">
                    <h3>搜索建议：</h3>
                    <ul>
                        <li>检查关键词拼写是否正确</li>
                        <li>尝试使用更通用的关键词</li>
                        <li>减少筛选条件</li>
                        <li>尝试不同的内容类型</li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
