<?php
/**
 * 翻译相关功能
 *
 * @package architizer
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 加载主题和插件翻译
 */
function architizer_load_translations() {
    // 主题翻译
    load_theme_textdomain(
        'architizer', 
        get_template_directory() . '/languages'
    );
}
// 使用更晚的优先级（15）来确保在其他插件之后加载
add_action('init', 'architizer_load_translations', 15);

/**
 * WP Rocket 翻译处理
 */
function architizer_load_rocket_translations() {
    if (defined('WP_ROCKET_PATH')) {
        load_plugin_textdomain(
            'rocket', 
            false, 
            dirname(plugin_basename(WP_ROCKET_PATH)) . '/languages/'
        );
    }
}
// 使用更晚的优先级（20）来确保在主题翻译之后加载
add_action('init', 'architizer_load_rocket_translations', 20);