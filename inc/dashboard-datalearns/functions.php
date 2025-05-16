<?php

function save_font_settings()
{
    if (!isset($_POST['font_type']) || !isset($_POST['font_family'])) {
        wp_send_json_error("Data tidak lengkap!");
    }

    $font_type = sanitize_text_field($_POST['font_type']);
    $font_family = sanitize_text_field($_POST['font_family']);

    update_option("datalearns_custom_{$font_type}_font_family", $font_family);

    wp_send_json_success("Font berhasil disimpan!");
}

add_action('wp_ajax_save_font_settings', 'save_font_settings');
add_action('wp_ajax_nopriv_save_font_settings', 'save_font_settings');

function save_color_settings()
{
    if (!isset($_POST['color_settings'])) {
        wp_send_json_error("Color data is missing!");
    }

    $color_settings = json_decode(stripslashes($_POST['color_settings']), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error("Invalid color data format!");
    }

    // Sanitize and save each color
    update_option('datalearns_custom_link_color', sanitize_hex_color($color_settings['linkColor']));
    update_option('datalearns_custom_link_hover_color', sanitize_hex_color($color_settings['linkHoverColor']));
    update_option('datalearns_custom_heading_color', sanitize_hex_color($color_settings['headingColor']));
    update_option('datalearns_custom_body_text_color', sanitize_hex_color($color_settings['bodyTextColor']));
    update_option('datalearns_custom_content_bg_color', sanitize_hex_color($color_settings['contentBgColor']));

    // Add these lines to your existing function
    update_option('datalearns_custom_nav_link_color', sanitize_hex_color($color_settings['navLinkColor']));
    update_option('datalearns_custom_nav_link_hover_color', sanitize_hex_color($color_settings['navLinkHoverColor']));
    update_option('datalearns_custom_nav_current_link_color', sanitize_hex_color($color_settings['navCurrentLinkColor']));

    wp_send_json_success("Color settings saved successfully!");
}

add_action('wp_ajax_save_color_settings', 'save_color_settings');
add_action('wp_ajax_nopriv_save_color_settings', 'save_color_settings');

function apply_custom_fonts()
{
    $header_font = get_option('datalearns_custom_header_font_family', 'Roboto');
    $body_font = get_option('datalearns_custom_body_font_family', 'Roboto');

    echo "<style>
        h1, h2, h3, h4, h5, h6 { font-family: '{$header_font}', roboto !important; }
        body { font-family: '{$body_font}', roboto !important; }
    </style>";
}
add_action('wp_head', 'apply_custom_fonts');

function apply_custom_colors()
{
    $link_color = get_option('datalearns_custom_link_color', '#0d6efd');
    $link_hover_color = get_option('datalearns_custom_link_hover_color', '#0a58ca');
    $heading_color = get_option('datalearns_custom_heading_color', '#212529');
    $body_text_color = get_option('datalearns_custom_body_text_color', '#212529');
    $content_bg_color = get_option('datalearns_custom_content_bg_color', '#ffffff');

    // Add these lines
    $nav_link_color = get_option('datalearns_custom_nav_link_color', '#333333');
    $nav_link_hover_color = get_option('datalearns_custom_nav_link_hover_color', '#0a58ca');
    $nav_current_link_color = get_option('datalearns_custom_nav_current_link_color', '#000000');

    echo "<style>
        a { color: {$link_color}; }
        a:hover { color: {$link_hover_color}; }
        h1, h2, h3, h4, h5, h6 { color: {$heading_color}; }
        body { color: {$body_text_color}; background-color: {$content_bg_color}; }
        .content-area { background-color: {$content_bg_color}; }

        .main-navigation ul li a { color: {$nav_link_color}; }
        .main-navigation ul li a:hover { color: {$nav_link_hover_color}; }
        .main-navigation ul li.current-menu-item a { color: {$nav_current_link_color}; }
    </style>";
}
add_action('wp_head', 'apply_custom_colors');

function datalearns_enqueue_assets()
{
    // Cek jika kita di halaman admin DataLearns
    $screen = get_current_screen();
    if ($screen->id !== 'toplevel_page_datalearns') return;

    // Path ke file CSS di folder assets
    $css_path = '/inc/dashboard-datalearns/asset/style.css';

    // CSS Eksternal dari folder assets
    wp_enqueue_style(
        'datalearns-admin-style',
        get_template_directory_uri() . $css_path,
        array()
    );

    // CSS Dinamis untuk font
    $header_font = get_option('datalearns_custom_header_font_family', '');
    $body_font = get_option('datalearns_custom_body_font_family', '');

    $dynamic_css = ":root {
        --header-font: {$header_font};
        --body-font: {$body_font};
    }";

    wp_add_inline_style('datalearns-admin-style', $dynamic_css);

    // Library eksternal
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
    wp_enqueue_style('select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css');
    wp_enqueue_style('animate-css', 'https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css');

    // JavaScript
    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', array(), null, true);
    wp_enqueue_script('select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js', array('jquery'), null, true);

    // Localize script untuk AJAX
    wp_localize_script('jquery', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('admin_enqueue_scripts', 'datalearns_enqueue_assets');

function custom_admin_menu_icon_datalearns()
{
    $site_icon = get_template_directory_uri() . '/images/cropped-faviconV2.png';
    if ($site_icon) {
        echo '
        <style>
            #toplevel_page_datalearns .wp-menu-image {
                width: 20px;
                height: 20px;
                background: url("' . esc_url($site_icon) . '") no-repeat center !important;
                background-size: 20px 20px !important;
                opacity: 0.8;
            }
            #toplevel_page_datalearns .wp-menu-image:before {
                display: none;
            }
            #toplevel_page_datalearns .wp-menu-image img {
                display: none;
            }
            #toplevel_page_datalearns:hover .wp-menu-image,
            #toplevel_page_datalearns.current .wp-menu-image,
            #toplevel_page_datalearns.wp-has-current-submenu .wp-menu-image {
                opacity: 1;
            }
        </style>';
    }
}
add_action('admin_head', 'custom_admin_menu_icon_datalearns');

function custom_admin_footer_text_datalearns()
{
    $screen = get_current_screen();

    if ($screen && $screen->id === 'toplevel_page_datalearns') {
        return 'Powered by <strong>DataLearns</strong>. Created by <a href="https://solusi247.com" target="_blank">PT Solusi 247</a>.';
    }

    return 'Thank you for creating with WordPress.';
}

add_filter('admin_footer_text', 'custom_admin_footer_text_datalearns');

add_action('wp_ajax_get_nav_items', function () {
    $type = $_GET['type'];

    if ($type === 'page') {
        $pages = get_pages();
        $items = array_map(function ($p) {
            return ['value' => get_permalink($p->ID), 'label' => $p->post_title];
        }, $pages);
    } elseif ($type === 'post_type') {
        $post_types = get_post_types(['public' => true, '_builtin' => false], 'objects');
        $items = [];

        foreach ($post_types as $post_type) {
            $items[] = ['value' => get_post_type_archive_link($post_type->name), 'label' => $post_type->labels->name];
        }
    } else {
        $items = [];
    }

    wp_send_json($items);
});

function get_post_type_from_archive_link($url)
{
    $path = parse_url($url, PHP_URL_PATH);
    foreach (get_post_types(['public' => true], 'objects') as $post_type) {
        $archive_url = get_post_type_archive_link($post_type->name);
        if ($archive_url && parse_url($archive_url, PHP_URL_PATH) === $path) {
            return $post_type->name;
        }
    }
    return null;
}
function get_nav_options($type)
{
    if ($type === 'page') {
        return array_map(function ($p) {
            return ['value' => get_permalink($p->ID), 'label' => $p->post_title];
        }, get_pages());
    }

    if ($type === 'post_type') {
        return array_map(function ($pt) {
            return ['value' => get_post_type_archive_link($pt->name), 'label' => $pt->labels->name];
        }, get_post_types(['public' => true, '_builtin' => false], 'objects'));
    }

    return [];
}
add_action('wp_ajax_get_saved_nav_items', function () {
    check_ajax_referer('get_saved_nav_nonce');

    if (!current_user_can('edit_theme_options')) {
        wp_send_json_error();
    }

    $items = get_option('custom_header_nav', []);
    wp_send_json($items);
});

add_action('wp_ajax_save_header_navigation', function () {
    check_ajax_referer('save_header_nav_nonce');

    if (!current_user_can('edit_theme_options')) {
        wp_send_json_error('Unauthorized', 403);
    }

    $items = json_decode(stripslashes($_POST['items']), true);

    $clean_items = [];
    foreach ($items as $item) {
        $clean_items[] = [
            'type' => sanitize_text_field($item['type']),
            'label' => sanitize_text_field($item['label']),
            'source' => esc_url_raw($item['source'])
        ];
    }

    update_option('custom_header_nav', $clean_items);
    wp_send_json_success();
});

// Register footer settings
function register_footer_settings()
{
    // Register all footer settings
    register_setting('footer_settings_group', 'footer_logo');
    register_setting('footer_settings_group', 'footer_description');
    register_setting('footer_settings_group', 'footer_address');
    register_setting('footer_settings_group', 'footer_phone');
    register_setting('footer_settings_group', 'footer_email');
    register_setting('footer_settings_group', 'facebook_url');
    register_setting('footer_settings_group', 'youtube_url');
    register_setting('footer_settings_group', 'linkedin_url');
    register_setting('footer_settings_group', 'instagram_url');
    register_setting('footer_settings_group', 'support_link');
    register_setting('footer_settings_group', 'terms_link');
    register_setting('footer_settings_group', 'contact_link');
}
add_action('admin_init', 'register_footer_settings');

add_action('wp_ajax_save_footer_settings', 'save_footer_settings');
function save_footer_settings()
{
    check_ajax_referer('save_footer_settings_nonce', 'security');

    $settings = array(
        'datalearns_footer_description' => sanitize_textarea_field($_POST['footer_description']),
        'datalearns_footer_address' => wp_kses_post($_POST['footer_address']),
        'datalearns_footer_phone' => sanitize_text_field($_POST['footer_phone']),
        'datalearns_footer_email' => sanitize_email($_POST['footer_email']),
        'datalearns_facebook_url' => esc_url_raw($_POST['facebook_url']),
        'datalearns_youtube_url' => esc_url_raw($_POST['youtube_url']),
        'datalearns_linkedin_url' => esc_url_raw($_POST['linkedin_url']),
        'datalearns_instagram_url' => esc_url_raw($_POST['instagram_url']),
        'datalearns_support_link' => esc_url_raw($_POST['support_link']),
        'datalearns_terms_link' => esc_url_raw($_POST['terms_link']),
        'datalearns_contact_link' => sanitize_email($_POST['contact_link'])
    );

    foreach ($settings as $key => $value) {
        update_option($key, $value);
    }

    wp_send_json_success('Footer settings saved successfully.');
}
