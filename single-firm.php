<?php get_header(); ?>

<main class="site-main single-firm">
    <article id="firm-<?php the_ID(); ?>" <?php post_class(); ?>>
        <!-- 公司头部信息 -->
        <header class="firm-header">
            <div class="container">
                <nav class="breadcrumb">
                    <a href="<?php echo home_url('/firms/'); ?>">建筑事务所</a>
                    <span class="current"><?php the_title(); ?></span>
                </nav>
                
                <div class="firm-meta">
                    <div class="firm-logo">
                        <?php 
                        $logo = get_field('firm_logo');
                        if ($logo) {
                            echo wp_get_attachment_image($logo['ID'], 'medium');
                        } elseif (has_post_thumbnail()) {
                            the_post_thumbnail('medium');
                        }
                        ?>
                    </div>
                    
                    <div class="firm-info">
                        <h1 class="firm-title"><?php the_title(); ?></h1>
                        <?php if (get_field('firm_tagline')): ?>
                            <p class="firm-tagline"><?php echo esc_html(get_field('firm_tagline')); ?></p>
                        <?php endif; ?>
                        
                        <div class="firm-stats">
                            <div class="stat-item">
                                <i class="material-icons">location_on</i>
                                <?php echo esc_html(get_field('firm_location')); ?>
                            </div>
                            <div class="stat-item">
                                <i class="material-icons">business</i>
                                <?php echo esc_html(get_field('firm_employees')); ?> 员工
                            </div>
                            <div class="stat-item">
                                <i class="material-icons">architecture</i>
                                <?php 
                                $projects_count = count_user_posts(get_the_author_meta('ID'), 'project');
                                echo $projects_count . ' 个项目';
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- 主要内容区 -->
        <div class="firm-content">
            <div class="container">
                <div class="content-grid">
                    <!-- 左侧内容 -->
                    <div class="main-content">
                        <div class="firm-description">
                            <?php the_content(); ?>
                        </div>

                        <!-- 团队成员 -->
                        <?php if (have_rows('team_members')): ?>
                            <section class="team-section">
                                <h2>团队成员</h2>
                                <div class="team-grid">
                                    <?php while(have_rows('team_members')): the_row(); ?>
                                        <div class="team-member">
                                            <?php 
                                            $photo = get_sub_field('member_photo');
                                            if ($photo): ?>
                                                <div class="member-photo">
                                                    <?php echo wp_get_attachment_image($photo['ID'], 'thumbnail'); ?>
                                                </div>
                                            <?php endif; ?>
                                            <h3><?php echo get_sub_field('member_name'); ?></h3>
                                            <p class="member-position"><?php echo get_sub_field('member_position'); ?></p>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </section>
                        <?php endif; ?>

                        <!-- 公司项目 -->
                        <section class="projects-section">
                            <h2>代表项目</h2>
                            <div class="projects-grid">
                                <?php
                                $projects = new WP_Query([
                                    'post_type' => 'project',
                                    'posts_per_page' => 6,
                                    'meta_query' => [
                                        [
                                            'key' => 'project_firm',
                                            'value' => get_the_ID(),
                                            'compare' => '='
                                        ]
                                    ]
                                ]);

                                while ($projects->have_posts()): $projects->the_post();
                                    get_template_part('template-parts/content', 'project-card');
                                endwhile;
                                wp_reset_postdata();
                                ?>
                            </div>
                        </section>
                    </div>

                    <!-- 右侧信息 -->
                    <aside class="firm-sidebar">
                        <div class="sidebar-section">
                            <h3>联系方式</h3>
                            <?php if (get_field('firm_website')): ?>
                                <div class="contact-item">
                                    <i class="material-icons">language</i>
                                    <a href="<?php echo esc_url(get_field('firm_website')); ?>" target="_blank">
                                        访问官网
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (get_field('firm_email')): ?>
                                <div class="contact-item">
                                    <i class="material-icons">email</i>
                                    <a href="mailto:<?php echo esc_attr(get_field('firm_email')); ?>">
                                        <?php echo esc_html(get_field('firm_email')); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="sidebar-section">
                            <h3>分享</h3>
                            <div class="share-buttons">
                                <!-- 分享按钮 -->
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </article>
</main>

<?php get_footer(); ?> 