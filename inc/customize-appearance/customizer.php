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

        self::register_footer_settings($wp_customize);
        self::register_404_settings($wp_customize);
        self::single_lesson_settings($wp_customize);
        self::register_archive_settings($wp_customize);
        self::register_time_based_images($wp_customize);
    }

    /**
     * Register footer settings
     */
    private static function register_footer_settings($wp_customize)
    {
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
     * Register single lesson page settings
     */
    private static function single_lesson_settings($wp_customize)
    {
        // 404 Template Section
        self::add_template_part_section(
            $wp_customize,
            'datalearns_panel',
            'single_lesson_template_section',
            __('Pilih Template Single Lesson', 'text-domain'),
            'selected_single_lesson_template_part',
            __('Gunakan Template Single Lesson', 'text-domain')
        );
    }

    /**
     * Register archive template settings for custom post types
     */
    private static function register_archive_settings($wp_customize)
    {
        // Content Archive Template Section
        self::add_template_part_section(
            $wp_customize,
            'datalearns_panel',
            'content_archive_template_section',
            __('Pilih Template Archive Content', 'text-domain'),
            'selected_content_archive_template_part',
            __('Gunakan Template Archive Content', 'text-domain')
        );

        // Course Archive Template Section
        self::add_template_part_section(
            $wp_customize,
            'datalearns_panel',
            'course_archive_template_section',
            __('Pilih Template Archive Course', 'text-domain'),
            'selected_course_archive_template_part',
            __('Gunakan Template Archive Course', 'text-domain')
        );
    }

    /**
     * Register image mobile settings 
     */
    private static function register_time_based_images($wp_customize)
    {
        $wp_customize->add_section('time_based_images_section', [
            'title' => __('Pengaturan Gambar & Teks Berdasarkan Waktu', 'text-domain'),
            'panel' => 'datalearns_panel',
        ]);

        $waktu = [
            'pagi' => 'Pagi',
            'siang' => 'Siang',
            'sore' => 'Sore',
            'malam' => 'Malam',
        ];

        foreach ($waktu as $key => $label) {
            $image_setting_id = "image_{$key}";
            $text_setting_id = "text_{$key}";

            // Gambar
            $wp_customize->add_setting($image_setting_id, [
                'default' => '',
                'transport' => 'refresh',
            ]);

            $wp_customize->add_control(new WP_Customize_Image_Control(
                $wp_customize,
                $image_setting_id,
                [
                    'label' => __("Gambar {$label}", 'text-domain'),
                    'section' => 'time_based_images_section',
                    'settings' => $image_setting_id,
                ]
            ));

            // Teks
            $wp_customize->add_setting($text_setting_id, [
                'default' => '',
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',
            ]);

            $wp_customize->add_control($text_setting_id, [
                'label' => __("Teks {$label}", 'text-domain'),
                'type' => 'text',
                'section' => 'time_based_images_section',
                'settings' => $text_setting_id,
            ]);
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
}

// Initialize the customizer
Theme_Customizer::init();
