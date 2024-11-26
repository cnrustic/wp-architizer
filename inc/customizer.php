<?php
/**
 * architizer Theme Customizer
 *
 * @package architizer
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function architizer_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-title a',
				'render_callback' => 'architizer_customize_partial_blogname',
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blogdescription',
			array(
				'selector'        => '.site-description',
				'render_callback' => 'architizer_customize_partial_blogdescription',
			)
		);
	}

	// 社交媒体链接
	$wp_customize->add_section('social_links', array(
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
		$wp_customize->add_setting("social_links[$platform]", array(
			'default' => '',
			'sanitize_callback' => 'esc_url_raw'
		));

		$wp_customize->add_control("social_links[$platform]", array(
			'label' => $label,
			'section' => 'social_links',
			'type' => 'url'
		));
	}

	// 联系信息
	$wp_customize->add_section('contact_info', array(
		'title' => '联系信息',
		'priority' => 31,
	));

	$contact_fields = array(
		'contact_email' => '邮箱地址',
		'contact_phone' => '联系电话',
		'contact_address' => '公司地址'
	);

	foreach ($contact_fields as $field => $label) {
		$wp_customize->add_setting($field, array(
			'default' => '',
			'sanitize_callback' => 'sanitize_text_field'
		));

		$wp_customize->add_control($field, array(
			'label' => $label,
			'section' => 'contact_info',
			'type' => 'text'
		));
	}

	// 首页设置
	$wp_customize->add_section('homepage_settings', array(
		'title' => '首页设置',
		'priority' => 32,
	));

	// 首页幻灯片
	$wp_customize->add_setting('hero_slides', array(
		'default' => '',
		'sanitize_callback' => 'absint'
	));

	$wp_customize->add_control('hero_slides', array(
		'label' => '首页幻灯片',
		'section' => 'homepage_settings',
		'type' => 'media'
	));
}
add_action( 'customize_register', 'architizer_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function architizer_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function architizer_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function architizer_customize_preview_js() {
	wp_enqueue_script( 'architizer-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), _S_VERSION, true );
}
add_action( 'customize_preview_init', 'architizer_customize_preview_js' );
