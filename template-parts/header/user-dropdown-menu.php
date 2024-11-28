<?php
/**
 * 用户下拉菜单模板
 */
?>
<div class="row align-middle p-xs mr-0">
    <a href="<?php echo esc_url(home_url('/profile')); ?>" class="dropdown-item">
        <i class="material-icons">person</i>
        <span>Profile & Collections</span>
    </a>
</div>

<div class="row align-middle p-xs mr-0">
    <a href="<?php echo esc_url(home_url('/company/join')); ?>" class="dropdown-item">
        <i class="material-icons">business</i>
        <span>Join Company</span>
    </a>
</div>

<div class="row align-middle p-xs mr-0">
    <a href="<?php echo esc_url(home_url('/support')); ?>" class="dropdown-item">
        <i class="material-icons">contact_support</i>
        <span>Contact Support</span>
    </a>
</div>

<div class="row align-middle p-xs mr-0">
    <a href="<?php echo esc_url(home_url('/help')); ?>" class="dropdown-item">
        <i class="material-icons">help</i>
        <span>Help Center</span>
    </a>
</div>

<div class="row align-middle p-xs mr-0">
    <a href="<?php echo wp_logout_url(home_url()); ?>" class="dropdown-item">
        <i class="material-icons">exit_to_app</i>
        <span>Sign Out</span>
    </a>
</div> 