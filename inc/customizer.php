<?php
/**
 * Architizer 主题自定义设置
 *
 * @package Architizer
 */

class Architizer_Customizer {
    private $wp_customize;
    
    public function __construct() {
        add_action('customize_register', array($this, 'register'));
        add_action('customize_preview_init', array($this, 'preview_js'));
    }
    
    public function register($wp_customize) {
        $this->wp_customize = $wp_customize;
        
        $this->setup_basic_settings();
        $this->add_social_media_section();
        $this->add_contact_section();
        $this->add_homepage_section();
        $this->add_projects_section();
    }
    
    private function setup_basic_settings() {
        // 基础设置的实时预览
        $this->wp_customize->get_setting('blogname')->transport = 'postMessage';
        $this->wp_customize->get_setting('blogdescription')->transport = 'postMessage';
        
        if (isset($this->wp_customize->selective_refresh)) {
            $this->wp_customize->selective_refresh->add_partial('blogname', array(
                'selector' => '.site-title a',
                'render_callback' => array($this, 'render_blogname'),
            ));
            
            $this->wp_customize->selective_refresh->add_partial('blogdescription', array(
                'selector' => '.site-description',
                'render_callback' => array($this, 'render_blogdescription'),
            ));
        }
    }
    
    private function add_social_media_section() {
        $this->wp_customize->add_section('social_links', array(
            'title' => '社交媒体链接',
            'priority' => 30,
        ));
        
        $social_platforms = array(
            'weibo' => '微博',
            'wechat' => '微信',
            'qq' => 'QQ',
            'linkedin' => '领英',
            'twitter' => '推特',
            'facebook' => '脸书'
        );
        
        foreach ($social_platforms as $platform => $label) {
            $this->add_setting_and_control(
                "social_links[$platform]",
                array(
                    'label' => $label,
                    'section' => 'social_links',
                    'type' => 'url',
                    'sanitize_callback' => 'esc_url_raw'
                )
            );
        }
    }
    
    private function add_contact_section() {
        $this->wp_customize->add_section('contact_info', array(
            'title' => '联系信息',
            'priority' => 31,
        ));
        
        $contact_fields = array(
            'contact_email' => array(
                'label' => '邮箱地址',
                'type' => 'email',
                'sanitize_callback' => 'sanitize_email'
            ),
            'contact_phone' => array(
                'label' => '联系电话',
                'type' => 'tel',
                'sanitize_callback' => 'sanitize_text_field'
            ),
            'contact_address' => array(
                'label' => '公司地址',
                'type' => 'textarea',
                'sanitize_callback' => 'sanitize_textarea_field'
            )
        );
        
        foreach ($contact_fields as $key => $field) {
            $this->add_setting_and_control($key, array_merge(
                $field,
                array('section' => 'contact_info')
            ));
        }
    }
    
    private function add_setting_and_control($id, $args) {
        $this->wp_customize->add_setting($id, array(
            'default' => $args['default'] ?? '',
            'sanitize_callback' => $args['sanitize_callback'] ?? 'sanitize_text_field'
        ));
        
        $this->wp_customize->add_control($id, array(
            'label' => $args['label'],
            'section' => $args['section'],
            'type' => $args['type']
        ));
    }
    
    public function render_blogname() {
        bloginfo('name');
    }
    
    public function render_blogdescription() {
        bloginfo('description');
    }
    
    public function preview_js() {
        wp_enqueue_script(
            'architizer-customizer',
            get_template_directory_uri() . '/js/customizer.js',
            array('customize-preview'),
            THEME_VERSION,
            true
        );
    }
}

// 初始化
new Architizer_Customizer();
