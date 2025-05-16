<?php

add_action('rest_api_init', 'apiRegisterSearch');

function apiRegisterSearch()
{
    register_rest_route('wp/v2', 'search', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'apiSearchResults'
    ));
}

function apiSearchResults($data)
{
    $mainQuery = new WP_Query(array(
        'post_type' => array('content', 'course'),
        's' => sanitize_text_field($data['q'])
    ));

    $results = array(
        'contents' => array(),
        'courses' => array(),
    );

    while ($mainQuery->have_posts()) {
        $mainQuery->the_post();

        if (get_post_type() == 'content') {
            $content = get_the_content();
            $has_video_list_value = check_has_video($content);

            $content_group = get_field('content_group');
            $categories_names = [];
            $categories_slugs = [];

            if (!empty($content_group)) {
                foreach ($content_group as $group_id) {
                    $term = get_term($group_id);
                    if ($term && !is_wp_error($term)) {
                        $categories_names[] = $term->name;
                        $categories_slugs[] = $term->slug;
                    }
                }
            }

            if (!empty($content_group_slug) && empty(array_intersect([$content_group_slug], $categories_slugs))) {
                continue;
            }

            // Ambil kategori
            $categories = get_the_category(); // Tambahkan ini untuk memastikan variabel $categories terisi
            $category_name = !empty($categories) ? esc_html($categories[0]->name) : '';

            array_push($results['contents'], array(
                'id' => get_the_ID(),
                'title' => html_entity_decode(get_the_title()),
                'permalink' => get_the_permalink(),
                'thumbnail' => get_the_post_thumbnail_url() ?: get_default_image(),
                'has_video' => $has_video_list_value,
                'excerpt' => get_the_excerpt(),
                'author' => get_the_author(),
                'content_group' => implode(', ', $categories_names),
                'author_avatar' => get_avatar_url(get_the_author_meta('ID')),
                'time_ago' => human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago',
                'time_created' => get_the_date('d F Y'),
                'category' => $category_name,

            ));
        }
        if (get_post_type() == 'course') {
            $course_id = get_the_ID();
            $course_type = get_field("course_type", $course_id);
            $course_type_slug = sanitize_title($course_type);

            array_push($results['courses'], array(
                'id' => get_the_ID(),
                'title' => html_entity_decode(get_the_title()),
                'permalink' => get_the_permalink(),
                'skill_level' => esc_html(get_field("skill_level", $course_id)),
                'thumbnail' => get_the_post_thumbnail_url() ?: get_default_image(),
                'course_type' => $course_type,
                'course_type_permalink' => esc_url(add_query_arg('course_type', $course_type_slug, home_url('/courses/'))),
            ));
        }
    }

    return $results;
}
