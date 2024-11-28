<section class="team-members">
    <h2 class="section-title">团队成员</h2>
    
    <?php if (have_rows('team_members')): ?>
        <div class="members-grid">
            <?php while (have_rows('team_members')): the_row(); ?>
                <div class="member-card">
                    <?php 
                    $photo = get_sub_field('member_photo');
                    if ($photo): ?>
                        <div class="member-photo">
                            <?php echo wp_get_attachment_image($photo['ID'], 'medium'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="member-info">
                        <h3 class="member-name"><?php echo get_sub_field('member_name'); ?></h3>
                        <p class="member-position"><?php echo get_sub_field('member_position'); ?></p>
                        <?php if (get_sub_field('member_bio')): ?>
                            <div class="member-bio">
                                <?php echo get_sub_field('member_bio'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (get_sub_field('member_linkedin')): ?>
                            <a href="<?php echo esc_url(get_sub_field('member_linkedin')); ?>" 
                               class="member-linkedin" 
                               target="_blank">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="no-members">暂无团队成员信息</p>
    <?php endif; ?>
</section> 