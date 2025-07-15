<?php
add_action('rest_api_init', function () {
    register_rest_route('wp/v2', '/greeting-image', [
        'methods'  => 'GET',
        'callback' => 'get_gambar_waktu_api',
        'permission_callback' => '__return_true',
    ]);
});

function get_gambar_waktu_api()
{
    $data = [
        'pagi'  => get_theme_mod('image_pagi') ?: '',
        'siang' => get_theme_mod('image_siang') ?: '',
        'sore'  => get_theme_mod('image_sore') ?: '',
        'malam' => get_theme_mod('image_malam') ?: '',
    ];

    return rest_ensure_response($data);
}
