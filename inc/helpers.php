<?php
function get_default_image()
{
    global $wpdb;

    $filename = 'default_image_datalearns';

    $attachment_id = $wpdb->get_var(
        $wpdb->prepare(
            "
            SELECT ID 
            FROM {$wpdb->posts} 
            WHERE post_type = 'attachment' 
            AND post_name = %s
            LIMIT 1
            ",
            $filename
        )
    );

    if ($attachment_id) {
        return wp_get_attachment_url($attachment_id);
    }

    return get_template_directory_uri() . '/images/default_image_datalearns.png';
}



function check_has_video($content)
{
    return strpos($content, '<iframe') !== false;
}

function title_link($post_id)
{
    $post_permalink = wp_make_link_relative(get_permalink($post_id));
    $post_title = html_entity_decode(get_the_title($post_id));
    $post_hreflang = 'en';

    return '<a href="' . esc_url($post_permalink) . '" hreflang="' . esc_attr($post_hreflang) . '">' . esc_html($post_title) . '</a>';
}

function get_lesson_type($lesson)
{
    $lesson_content = is_object($lesson) && method_exists($lesson, 'get')
        ? $lesson->get('content')
        : (isset($lesson->post_content) ? $lesson->post_content : '');

    if (strpos($lesson_content, 'class="pdfemb-viewer"') !== false || strpos($lesson_content, '<!-- wp:pdfemb/pdf-embedder-viewer') !== false) {
        return 'pdf';
    } elseif (method_exists($lesson, 'get_video') && $lesson->get_video()) {
        return 'youtube';
    } elseif (method_exists($lesson, 'has_quiz') && $lesson->has_quiz()) {
        return 'quiz';
    } else {
        return 'article';
    }
}

function create_rest_response($status = 200, $message = null)
{
    $default_responses = [
        200 => ['status_key' => 'success', 'message' => 'Request successful.'],
        201 => ['status_key' => 'created', 'message' => 'Resource created successfully.'],
        400 => ['status_key' => 'bad_request', 'message' => 'Bad request.'],
        401 => ['status_key' => 'unauthorized', 'message' => 'Unauthorized access.'],
        403 => ['status_key' => 'forbidden', 'message' => 'Forbidden.'],
        404 => ['status_key' => 'not_found', 'message' => 'Resource not found.'],
        500 => ['status_key' => 'server_error', 'message' => 'Internal server error.'],
    ];

    $response = $default_responses[$status] ?? ['status_key' => 'unknown', 'message' => 'Unknown status.'];

    return new WP_REST_Response(array(
        'status'  => $response['status_key'],
        'message' => $message ?: $response['message'],
        'code'    => $status,
    ), $status);
}


function get_post_details($post_id)
{
    $time_diff = human_time_diff(get_the_time('U', $post_id), current_time('timestamp')) . ' ago';
    $post_type = get_post_type($post_id);
    $post = get_post($post_id);
    if ($post_type === 'course') {
        $object = new LLMS_Course($post_id);
        $access_plan = new LLMS_Access_Plan($post_id);
        $content = apply_filters('the_content', $object->get('content', true));

        $price = $access_plan->get("price");

        $content = preg_replace('/<div class="llms-access-plan-description">.*?<\/div>/s', '', $content);
        $categories = $object->get_categories();

        $category_names = array_map(function ($category) {
            return $category->name;
        }, $categories);

        $sections = array();
        foreach ($object->get_sections() as $section) {
            $section_data = array(
                'section_ID' => $section->get('id'),
                'section_title' => $section->get('title'),
                'lessons' => array()
            );

            foreach ($section->get_lessons() as $lesson) {
                $is_complete = $lesson->is_complete(get_current_user_id());
                $lesson_type = get_lesson_type($lesson);

                $section_data['lessons'][] = array(
                    'lesson_ID' => $lesson->get('id'),
                    'lesson_title' => $lesson->get('title'),
                    'lesson_type' => $lesson_type,
                    'is_complete' => $is_complete
                );
            }

            $sections[] = $section_data;
        }

        $is_enrolled = llms_is_user_enrolled(get_current_user_id(), $post_id);

        $reels = get_posts(array(
            'post_type' => 'reels',
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'related_course',
                    'value' => $post_id,
                    'compare' => '='
                )
            ),
            'posts_per_page' => -1,
        ));

        $list_reels = array_map(function ($reel) {
            $video_field = get_field('reels_video', $reel->ID);
            $video_url = is_array($video_field) ? $video_field['url'] : $video_field;
            return array(
                'title' => html_entity_decode(get_the_title($reel->ID)),
                'video_url'    => $video_url,
                'id'           => $reel->ID,
            );
        }, $reels);

        $post_data = array(
            'is_enrolled' => $is_enrolled,
            'title' => title_link($post_id),
            'field_tags' => get_post_type($post_id),
            'created' => $time_diff,
            'field_display_name' => get_the_author_meta('display_name', get_post_field('post_author', $post_id)),
            'user_picture' => get_avatar_url(get_the_author_meta('ID', get_post_field('post_author', $post_id))),
            'field_image' => get_field('featured_course_image_mobile', $post_id) ?: get_default_image(),
            'categories' => $category_names,
            'progress' => $object->get_percent_complete(),
            'price' => (int) $price,
            'course_code' => get_field("course_code", $post_id),
            'course_type' => get_field("course_type", $post_id),
            'duration' => get_field("duration", $post_id),
            'difficulty' => get_field("skill_level", $post_id),
            'course_format' => get_field("course_format", $post_id),
            'sections' => $sections,
            'body' => $content,
            'list_reels' => $list_reels,
            'id' => $post_id
        );
        if (function_exists('chatbot_datalearns_replace_placeholders')) {
            $post_data['chatbotId'] = get_field('chat_flow_id', $post_id);
        }
    } else if ($post_type === 'lesson') {
        $lesson = new LLMS_Lesson($post_id);
        $is_free = $lesson->is_free();
        $parent_course_id = $lesson->get('parent_course');
        $parent_course = new LLMS_Course($parent_course_id);
        $is_enrolled = llms_is_user_enrolled(get_current_user_id(), $parent_course_id);
        $content_restricted_message = $parent_course->get('content_restricted_message');
        if (!$is_free && !$is_enrolled) {
            $post_data = new WP_REST_Response(array(
                'status' => 'error',
                'message' => $content_restricted_message,
                'code' => 403,
            ), 403);
        } else {
            $content = llms_get_post($post_id);
            $lesson_type = get_lesson_type($content);
            $is_complete = $lesson->is_complete(get_current_user_id());


            $video = $lesson->get_video();
            $video = !empty($video) ? $video : null;

            $body_content = apply_filters('the_content', $post->post_content);
            if ($lesson_type === 'quiz') {
                $quiz_id = $lesson->get('quiz');
                $body_content = $quiz_id ? (string) $quiz_id : null;
            } else {
                $body_content = apply_filters('the_content', $post->post_content);
                if ($video) {
                    $body_content .= "\n\n" . $video;
                }
            }

            $post_data = array(
                'title' => title_link($post_id),
                'previous_lesson' => $lesson->get_previous_lesson() === false ? 0 : $lesson->get_previous_lesson(),
                'next_lesson' => $lesson->get_next_lesson() === false ? 0 : $lesson->get_next_lesson(),
                'lesson_type' => $lesson_type,
                'is_complete' => $is_complete,
                'body' => $body_content,
                'id' => $post_id
            );
        }
    } else {
        $post_data = array(
            'title' => title_link($post_id),
            'field_tags' => get_post_type($post_id),
            'created' => $time_diff,
            'field_display_name' => get_the_author_meta('display_name', get_post_field('post_author', $post_id)),
            'user_picture' => get_avatar_url(get_the_author_meta('ID', get_post_field('post_author', $post_id))),
            'field_image' => get_field('featured_course_image_mobile', $post_id) ?: get_default_image(),
            'body' => apply_filters('the_content', $post->post_content),
            'id' => $post_id
        );
    }

    if ($post_type === 'content') {
        $list_links = get_field('list_link', $post_id);
        $links = array();

        if (!empty($list_links) && is_array($list_links)) {
            foreach ($list_links as $link) {
                $content = get_post_field('post_content', $link->ID);

                $has_video_list_value = check_has_video($content);

                $links[] = array(
                    'id' => $link->ID,
                    'title' => html_entity_decode(get_the_title($link->ID)),
                    'has_video' => $has_video_list_value,
                    'image' => get_field('featured_course_image_mobile', $link->ID) ?: get_default_image()
                );
            }
        }

        $post_data['list_link'] = $links;
    }

    return $post_data;
}

function get_post_list($post_id = null)
{
    $id = $post_id ?: get_the_ID();
    $time_diff = human_time_diff(get_post_time('U', false, $id), current_time('timestamp')) . ' ago';
    $post_type = get_post_type($id);

    $post_data = array(
        'title'        => html_entity_decode(get_the_title($id)),
        'image'        => get_field('featured_course_image_mobile', $id) ?: get_default_image($id),
        'tag'          => $post_type,
        'author'       => get_the_author_meta('display_name', get_post_field('post_author', $id)),
        'author_photo' => get_avatar_url(get_post_field('post_author', $id)),
        'date_created' => $time_diff,
        'id'           => $id,
    );

    if ($post_type === 'content') {
        $group = get_field('content_group', $id);

        if ($group) {
            if (!isset($grouped_posts[$group])) {
                $grouped_posts[$group] = array();
            }

            $block_group_title = $group;

            $content = get_post_field('post_content', $id);

            $has_video = check_has_video($content);

            $post_data['block-group'] = ucwords(str_replace('-', ' ', $block_group_title));
            $post_data['has_video'] = $has_video;
        } else {
            $post_data['block-group'] = 'No group assigned';
            $post_data['has_video'] = false;
        }
    }


    if ($post_type === 'course') {
        $object = new LLMS_Course($id);
        $post_data['progress'] = $object->get_percent_complete();
    }


    return $post_data;
}

function validate_request_data($id, $post_type)
{
    if (empty($id) || !is_numeric($id)) {
        return new WP_REST_Response(array(
            'status' => "invalid_{$post_type}_id",
            'message' => ucfirst($post_type) . ' ID is not valid',
            'code' => 400,
        ), 400);
    }

    $post = get_post($id);
    if (!$post || $post->post_type !== $post_type) {
        return new WP_REST_Response(array(
            'status' => 'error',
            'message' => ucfirst($post_type) . ' ID not found',
            'code' => 404,
        ), 404);
    }

    return $post;
}
