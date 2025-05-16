<?php
function theme_customizer_settings($wp_customize)
{
    $wp_customize->add_panel('header_settings', array(
        'title' => __('Header', 'text-domain'),
        'priority' => 30,
    ));

    $wp_customize->add_section('website_logo_section', array(
        'title' => __('Website Logo', 'text-domain'),
        'panel' => 'header_settings',
        'priority' => 10,
    ));

    $wp_customize->add_setting('website_logo', array(
        'default' => '',
        'transport' => 'refresh',
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'website_logo_control', array(
        'label' => __('Upload Website Logo', 'text-domain'),
        'section' => 'website_logo_section',
        'settings' => 'website_logo',
        'mime_type' => 'image',
        'description' => __('Recommended size: 180x50 px (PNG/JPG format)', 'text-domain'),
    )));

    $wp_customize->add_setting('logo_width', array(
        'default' => '180',
        'transport' => 'postMessage',
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('logo_width_control', array(
        'label' => __('Logo Width (px)', 'text-domain'),
        'section' => 'website_logo_section',
        'settings' => 'logo_width',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 50,
            'max' => 500,
            'step' => 5,
        ),
    ));
}
add_action('customize_register', 'theme_customizer_settings');


function theme_localize_scripts()
{
    wp_localize_script('theme-customizer-preview', 'themeData', array(
        'defaultLogo' => get_template_directory_uri() . '/images/DL247-logo_web.png'
    ));
}
add_action('wp_enqueue_scripts', 'theme_localize_scripts');
