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
        'transport' => 'refresh',
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

    // Fixed Header Setting
    $wp_customize->add_section('header_behavior_section', array(
        'title'    => __('Header Behavior', 'text-domain'),
        'panel'    => 'header_settings',
        'priority' => 20,
    ));

    $wp_customize->add_setting('enable_fixed_header', array(
        'default'           => false,
        'transport'         => 'refresh',
        'sanitize_callback' => 'wp_validate_boolean',
    ));

    $wp_customize->add_control('enable_fixed_header_control', array(
        'label'    => __('Enable Fixed Header', 'text-domain'),
        'section'  => 'header_behavior_section',
        'settings' => 'enable_fixed_header',
        'type'     => 'checkbox',
    ));

    // Panel untuk Main Content
    $wp_customize->add_panel('main_settings', array(
        'title'    => __('Main', 'text-domain'),
        'priority' => 40,
    ));

    // Section untuk Layout Main Content
    $wp_customize->add_section('main_layout_section', array(
        'title' => __('Main Layout', 'text-domain'),
        'panel' => 'main_settings',
        'priority' => 10,
    ));

    $wp_customize->add_setting('main_vertical_spacing', array(
        'default' => 80,
        'transport' => 'refresh',
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('main_vertical_spacing_control', array(
        'label' => __('Vertical Spacing (Top Margin in px)', 'text-domain'),
        'section' => 'main_layout_section',
        'settings' => 'main_vertical_spacing',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 0,
            'max' => 300,
            'step' => 5,
        ),
    ));

    // Vertical Padding Setting (px)
    $wp_customize->add_setting('main_vertical_padding', array(
        'default' => 32, // 32px = 2rem
        'transport' => 'refresh',
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('main_vertical_padding_control', array(
        'label' => __('Vertical Padding (Top & Bottom in px)', 'text-domain'),
        'section' => 'main_layout_section',
        'settings' => 'main_vertical_padding',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 0,
            'max' => 200,
            'step' => 4,
        ),
    ));


    // Panel Global
    $wp_customize->add_panel('global_settings', array(
        'title' => __('Global', 'text-domain'),
        'priority' => 5,
    ));

    // Section Container
    $wp_customize->add_section('container_section', array(
        'title' => __('Container', 'text-domain'),
        'panel' => 'global_settings',
        'priority' => 10,
    ));

    // Setting Max Width
    $wp_customize->add_setting('container_max_width', array(
        'default' => 1200,
        'transport' => 'refresh',
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('container_max_width_control', array(
        'label' => __('Max Width (px)', 'text-domain'),
        'section' => 'container_section',
        'settings' => 'container_max_width',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 500,
            'max' => 2000,
            'step' => 10,
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

function theme_dynamic_spacing_css()
{
    $spacing = get_theme_mod('main_vertical_spacing', 80);
    $padding = get_theme_mod('main_vertical_padding', 32);

    echo "<style>
        .vertical-spacing {
            margin-top: {$spacing}px;
            padding-top: {$padding}px;
            padding-bottom: {$padding}px;
        }
    </style>";
}

add_action('wp_head', 'theme_dynamic_spacing_css');

function theme_dynamic_container_css()
{
    $width = get_theme_mod('container_max_width', 1200);
    echo "<style>
        .container {
            max-width: {$width}px;
        }
    </style>";
}
add_action('wp_head', 'theme_dynamic_container_css');

function theme_customizer_footer_settings($wp_customize)
{
    // Panel: Footer
    $wp_customize->add_panel('footer_settings_panel', array(
        'title'    => __('Footer', 'text-domain'),
        'priority' => 160,
    ));

    // Section: Footer Template
    $wp_customize->add_section('footer_template_section', array(
        'title'    => __('Pilih Template Footer', 'text-domain'),
        'panel'    => 'footer_settings_panel',
    ));

    // Get all template parts
    $template_parts = get_posts(array(
        'post_type'      => 'template_part',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ));

    // Create choices array for the dropdown
    $choices = array('' => __('-- Pilih Template --', 'text-domain'));
    foreach ($template_parts as $part) {
        $choices[$part->ID] = $part->post_title;
    }

    // Setting
    $wp_customize->add_setting('selected_footer_template_part', array(
        'default'   => '',
        'transport' => 'refresh',
    ));

    // Control: Dropdown Template Part
    $wp_customize->add_control('selected_footer_template_part', array(
        'type'     => 'select',
        'label'    => __('Gunakan Template Footer', 'text-domain'),
        'section'  => 'footer_template_section',
        'choices'  => $choices,
    ));
}
add_action('customize_register', 'theme_customizer_footer_settings');
