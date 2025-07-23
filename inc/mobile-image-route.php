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
    $waktu = ['pagi', 'siang', 'sore', 'malam'];
    $data = [];

    foreach ($waktu as $key) {
        $data[$key] = [
            'image' => get_theme_mod("image_{$key}") ?: '',
            'text'  => get_theme_mod("text_{$key}") ?: '',
        ];
    }

    return rest_ensure_response($data);
}
