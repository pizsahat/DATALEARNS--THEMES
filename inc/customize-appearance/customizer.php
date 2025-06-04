<?php

/**
 * Theme Customizer with clean code practices
 */

class Theme_Customizer
{
    /**
     * Initialize customizer settings
     */
    public static function init()
    {
        add_action('customize_register', [__CLASS__, 'register_settings']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'localize_scripts']);
        add_action('wp_head', [__CLASS__, 'output_dynamic_css']);
    }

    /**
     * Register all customizer settings
     * 
     * @param WP_Customize_Manager $wp_customize
     */
    public static function register_settings($wp_customize)
    {
        $wp_customize->add_panel('datalearns_panel', [
            'title' => __('DataLearns Settings', 'datalearns'),
            'priority' => 3,
            'description' => __('Custom settings for DataLearns theme', 'datalearns'),
        ]);

        self::register_header_settings($wp_customize);
        self::register_main_settings($wp_customize);
        self::register_global_settings($wp_customize);
        self::register_footer_settings($wp_customize);
        self::register_404_settings($wp_customize);
    }

    /**
     * Register header settings
     */
    private static function register_header_settings($wp_customize)
    {
        // // Header Panel
        // $wp_customize->add_panel('header_settings', [
        //     'title' => __('Header', 'text-domain'),
        //     'priority' => 30,
        // ]);

        // Logo Section
        self::add_section_with_settings($wp_customize, [
            'panel' => 'datalearns_panel',
            'section_id' => 'website_logo_section',
            'section_title' => __('Website Logo', 'text-domain'),
            'settings' => [
                [
                    'id' => 'website_logo',
                    'default' => '',
                    'control' => [
                        'type' => 'WP_Customize_Media_Control',
                        'label' => __('Upload Website Logo', 'text-domain'),
                        'args' => [
                            'mime_type' => 'image',
                            'description' => __('Recommended size: 180x50 px (PNG/JPG format)', 'text-domain'),
                        ]
                    ]
                ],
                [
                    'id' => 'logo_width',
                    'default' => '180',
                    'control' => [
                        'label' => __('Logo Width Desktop (px)', 'text-domain'),
                        'description' => __('Logo width for desktop and tablet screens', 'text-domain'),
                        'type' => 'number',
                        'input_attrs' => [
                            'min' => 50,
                            'max' => 500,
                            'step' => 5,
                        ]
                    ]
                ],
                [
                    'id' => 'logo_width_mobile',
                    'default' => '120',
                    'control' => [
                        'label' => __('Logo Width Mobile (px)', 'text-domain'),
                        'description' => __('Logo width for mobile screens (768px and below)', 'text-domain'),
                        'type' => 'number',
                        'input_attrs' => [
                            'min' => 30,
                            'max' => 300,
                            'step' => 5,
                        ]
                    ]
                ]
            ]
        ]);

        // Header Behavior Section
        self::add_section_with_settings($wp_customize, [
            'panel' => 'header_settings',
            'section_id' => 'header_behavior_section',
            'section_title' => __('Header Behavior', 'text-domain'),
            'priority' => 20,
            'settings' => [
                [
                    'id' => 'enable_fixed_header',
                    'default' => false,
                    'sanitize_callback' => 'wp_validate_boolean',
                    'control' => [
                        'label' => __('Enable Fixed Header', 'text-domain'),
                        'type' => 'checkbox'
                    ]
                ]
            ]
        ]);
    }

    /**
     * Register main content settings
     */
    private static function register_main_settings($wp_customize)
    {
        // // Main Panel
        // $wp_customize->add_panel('main_settings', [
        //     'title' => __('Main', 'text-domain'),
        //     'priority' => 40,
        // ]);

        // Main Layout Section
        self::add_section_with_settings($wp_customize, [
            'panel' => 'datalearns_panel',
            'section_id' => 'main_layout_section',
            'section_title' => __('Main Layout', 'text-domain'),
            'settings' => [
                [
                    'id' => 'main_vertical_spacing',
                    'default' => 80,
                    'control' => [
                        'label' => __('Vertical Spacing (Top Margin in px)', 'text-domain'),
                        'type' => 'number',
                        'input_attrs' => [
                            'min' => 0,
                            'max' => 300,
                            'step' => 5,
                        ]
                    ]
                ],
                [
                    'id' => 'main_vertical_padding',
                    'default' => 32,
                    'control' => [
                        'label' => __('Vertical Padding (Top & Bottom in px)', 'text-domain'),
                        'type' => 'number',
                        'input_attrs' => [
                            'min' => 0,
                            'max' => 200,
                            'step' => 4,
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * Register global settings
     */
    private static function register_global_settings($wp_customize)
    {
        // // Global Panel
        // $wp_customize->add_panel('global_settings', [
        //     'title' => __('Global', 'text-domain'),
        //     'priority' => 5,
        // ]);

        // Container Section
        self::add_section_with_settings($wp_customize, [
            'panel' => 'datalearns_panel',
            'section_id' => 'container_section',
            'section_title' => __('Container', 'text-domain'),
            'settings' => [
                [
                    'id' => 'container_max_width',
                    'default' => 1200,
                    'control' => [
                        'label' => __('Max Width (px)', 'text-domain'),
                        'type' => 'number',
                        'input_attrs' => [
                            'min' => 500,
                            'max' => 2000,
                            'step' => 10,
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * Register footer settings
     */
    private static function register_footer_settings($wp_customize)
    {
        // // Footer Panel
        // $wp_customize->add_panel('footer_settings_panel', [
        //     'title' => __('Footer', 'text-domain'),
        //     'priority' => 160,
        // ]);

        // Footer Template Section
        self::add_template_part_section(
            $wp_customize,
            'datalearns_panel',
            'footer_template_section',
            __('Pilih Template Footer', 'text-domain'),
            'selected_footer_template_part',
            __('Gunakan Template Footer', 'text-domain')
        );
    }

    /**
     * Register 404 page settings
     */
    private static function register_404_settings($wp_customize)
    {
        // // 404 Panel
        // $wp_customize->add_panel('page_404_settings_panel', [
        //     'title' => __('404 Page', 'text-domain'),
        //     'priority' => 170,
        // ]);

        // 404 Template Section
        self::add_template_part_section(
            $wp_customize,
            'datalearns_panel',
            'page_404_template_section',
            __('Pilih Template 404', 'text-domain'),
            'selected_404_template_part',
            __('Gunakan Template 404', 'text-domain')
        );
    }

    /**
     * Helper method to add a section with multiple settings
     */
    private static function add_section_with_settings($wp_customize, $args)
    {
        $section_args = [
            'title' => $args['section_title'],
            'panel' => $args['panel'] ?? '',
            'priority' => $args['priority'] ?? 10,
        ];

        $wp_customize->add_section($args['section_id'], $section_args);

        foreach ($args['settings'] as $setting) {
            $wp_customize->add_setting($setting['id'], [
                'default' => $setting['default'],
                'transport' => $setting['transport'] ?? 'refresh',
                'sanitize_callback' => $setting['sanitize_callback'] ?? 'absint',
            ]);

            $control_args = [
                'label' => $setting['control']['label'],
                'section' => $args['section_id'],
                'settings' => $setting['id'],
            ];

            if (isset($setting['control']['type']) && $setting['control']['type'] === 'WP_Customize_Media_Control') {
                $media_args = array_merge($control_args, $setting['control']['args'] ?? []);
                $wp_customize->add_control(new WP_Customize_Media_Control(
                    $wp_customize,
                    $setting['id'] . '_control',
                    $media_args
                ));
            } else {
                $control_args['type'] = $setting['control']['type'] ?? 'text';

                if (isset($setting['control']['input_attrs'])) {
                    $control_args['input_attrs'] = $setting['control']['input_attrs'];
                }

                $wp_customize->add_control($setting['id'] . '_control', $control_args);
            }
        }
    }

    /**
     * Helper method to add template part sections
     */
    private static function add_template_part_section($wp_customize, $panel, $section_id, $section_title, $setting_id, $control_label)
    {
        $wp_customize->add_section($section_id, [
            'title' => $section_title,
            'panel' => $panel,
        ]);

        $template_parts = get_posts([
            'post_type' => 'template_part',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ]);

        $choices = ['' => __('-- Pilih Template --', 'text-domain')];
        foreach ($template_parts as $part) {
            $choices[$part->ID] = $part->post_title;
        }

        $wp_customize->add_setting($setting_id, [
            'default' => '',
            'transport' => 'refresh',
        ]);

        $wp_customize->add_control($setting_id, [
            'type' => 'select',
            'label' => $control_label,
            'section' => $section_id,
            'choices' => $choices,
        ]);
    }

    /**
     * Localize scripts for front-end
     */
    public static function localize_scripts()
    {
        wp_localize_script('theme-customizer-preview', 'themeData', [
            'defaultLogo' => get_template_directory_uri() . '/images/DL247-logo_web.png'
        ]);
    }

    /**
     * Output dynamic CSS based on customizer settings
     */
    public static function output_dynamic_css()
    {
        $css = '';

        // Vertical spacing and padding
        $spacing = get_theme_mod('main_vertical_spacing', 80);
        $padding = get_theme_mod('main_vertical_padding', 32);
        $css .= ".vertical-spacing {
            margin-top: {$spacing}px;
            padding-top: {$padding}px;
            padding-bottom: {$padding}px;
        }";

        // Container width
        $width = get_theme_mod('container_max_width', 1200);
        $css .= ".container {
            max-width: {$width}px;
        }";

        // Logo width for desktop
        $logo_width = get_theme_mod('logo_width', 180);
        $css .= ".site-logo {
            max-width: {$logo_width}px !important;
            height: auto;
        }";

        // Logo width for mobile
        $logo_width_mobile = get_theme_mod('logo_width_mobile', 120);
        $css .= "@media (max-width: 768px) {
            .site-logo {
                max-width: {$logo_width_mobile}px !important;
                height: auto;
            }
        }";

        if (!empty($css)) {
            echo "<style>{$css}</style>";
        }
    }
}

// Initialize the customizer
Theme_Customizer::init();
