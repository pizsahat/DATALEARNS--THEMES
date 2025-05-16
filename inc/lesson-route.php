<?php

require_once  get_theme_file_path('/inc/helpers.php');

add_action('rest_api_init', 'apiLessonPost');

function apiLessonPost()
{
    register_rest_route(
        'wp/v2',
        'lessons/finish/',
        array(
            'methods'  => 'POST',
            'callback' => 'handle_finish_lessons',
            'permission_callback' => '__return_true'
        )
    );
    register_rest_route('wp/v2', 'lesson', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'apiLessonDetail',
        'args' => array(
            'id' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric($param);
                }
            ),
        )
    ));
}

function handle_finish_lessons($request)
{
    $lesson_id = sanitize_text_field($request['id']);
    $user_id = get_current_user_id();

    if (!$user_id) {
        return new WP_REST_Response(array(
            'status' => 'no_user',
            'message' => 'User not logged in',
            'code' => 401,
        ), 401);
    }

    $validation_response = validate_request_data($lesson_id, 'lesson');
    if (is_wp_error($validation_response) || $validation_response instanceof WP_REST_Response) {
        return $validation_response;
    }

    if (!llms_is_complete($user_id, $lesson_id, 'lesson')) {
        if (llms_mark_complete($user_id, $lesson_id, 'lesson')) {
            return new WP_REST_Response(array(
                'status' => 'success',
                'message' => "Lesson with ID $lesson_id marked as complete.",
                'code' => 200,
            ), 200);
        }

        return new WP_REST_Response(array(
            'status' => 'error',
            'message' => 'Failed to mark the lesson as complete.',
            'code' => 500,
        ), 500);
    }

    return new WP_REST_Response(array(
        'status' => 'failed',
        'message' => 'Lesson already completed.',
        'code' => 400,
    ), 400);
}

function apiLessonDetail($data)
{
    $post_id = (int) $data['id'];
    $post = get_post($post_id);

    if ($post && $post->post_type == 'lesson') {
        return get_post_details($post_id);
    } else {
        return new WP_Error('no_lesson', 'Lesson post not found', array('status' => 404));
    }
}
