<?php
function course_image_mobile_acf_fields()
{
    if (function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(array(
            'key' => 'course_setting',
            'title' => 'Course Setting',
            'position' => 'acf_after_title',
            'fields' => array(
                array(
                    'key' => 'field_featured_course_image_mobile',
                    'label' => 'Featured Course Image Mobile',
                    'name' => 'featured_course_image_mobile',
                    'type' => 'image',
                    'return_format' => 'url',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'course',
                    ),
                ),
            ),
        ));
    }
}

add_action('acf/init', 'course_image_mobile_acf_fields');
