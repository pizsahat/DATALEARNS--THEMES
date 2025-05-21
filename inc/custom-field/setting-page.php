<?php
add_action('acf/init', 'register_acf_page_display_options');
function register_acf_page_display_options()
{
    if (function_exists('acf_add_local_field_group')) {

        acf_add_local_field_group(array(
            'key' => 'group_page_display_options',
            'title' => 'Pengaturan Tampilan',
            'fields' => array(
                array(
                    'key' => 'field_show_header',
                    'label' => 'Tampilkan Header',
                    'name' => 'show_header',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                    'instructions' => 'Aktifkan untuk menampilkan header pada konten halaman.',

                ),
                array(
                    'key' => 'field_show_footer',
                    'label' => 'Tampilkan Footer',
                    'name' => 'show_footer',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                    'instructions' => 'Aktifkan untuk menampilkan footer pada konten halaman.',
                ),
                array(
                    'key' => 'field_use_container',
                    'label' => 'Container',
                    'name' => 'use_container',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                    'instructions' => 'Aktifkan untuk memberi jarak kiri dan kanan pada konten halaman.',

                ),
                array(
                    'key' => 'field_enable_vertical_spacing',
                    'label' => 'Tampilkan Spasi Vertikal',
                    'name' => 'enable_vertical_spacing',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                    'instructions' => 'Aktifkan untuk memberi jarak atas dan bawah pada konten halaman.',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'page',
                    ),
                ),
            ),
            'position' => 'side',
            'style' => 'seamless',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'active' => true,
            'description' => '',
        ));
    }
}
