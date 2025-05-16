<?php

require_once  get_theme_file_path('/inc/helpers.php');

add_action('rest_api_init', 'apiCoursePost');

function apiCoursePost()
{
    register_rest_route(
        'wp/v2',
        'courses/enroll/',
        array(
            'methods'  => 'POST',
            'callback' => 'handle_course_enroll',
            'permission_callback' => '__return_true'
        )
    );
    register_rest_route('wp/v2', 'list-courses', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'listCourseResults'
    ));

    register_rest_route('wp/v2', 'my-courses', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'listMyCourseResults'
    ));


    register_rest_route('wp/v2', 'course', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'apiCourseDetail',
        'args' => array(
            'id' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric($param);
                }
            ),
        )
    ));
}

function listMyCourseResults()
{
    if (!is_user_logged_in()) {
        return create_rest_response(401);
    }

    $current_user_id = get_current_user_id();

    $all_courses = get_posts(array(
        'post_type' => 'course',
        'numberposts' => -1
    ));

    $enrolled_courses = array();
    if (!empty($all_courses)) {
        foreach ($all_courses as $course) {
            if (llms_is_user_enrolled($current_user_id, $course->ID)) {
                $enrolled_courses[] = $course->ID;
            }
        }
    }

    if (empty($enrolled_courses)) {
        return create_rest_response(400, "No enrolled courses found.");
    }

    $courses = array();
    foreach ($enrolled_courses as $course_id) {
        $courses[] = get_post_list($course_id);
    }

    return $courses;
}


function handle_course_enroll($request)
{
    $course_id = sanitize_text_field($request['id']);

    $user_id = get_current_user_id();
    if (!$user_id) {
        return create_rest_response(401);
    }

    $validation_response = validate_request_data($course_id, 'course');
    if (is_wp_error($validation_response) || $validation_response instanceof WP_REST_Response) {
        return $validation_response;
    }

    if (!llms_is_user_enrolled($user_id, $course_id)) {
        if (llms_enroll_student($user_id, $course_id, 'user_' . $user_id)) {
            return create_rest_response(200, "User enrolled successfully.");
        }
        return create_rest_response(500, "Enrollment failed.");
    }
    return create_rest_response(400, "User already enrolled.");
}

function listCourseResults()
{
    $posts = new WP_Query(array('post_type' => 'course'));
    $postResults = array();

    while ($posts->have_posts()) {
        $posts->the_post();
        $postResults[] = get_post_list();
    }

    wp_reset_postdata();
    return $postResults;
}

function apiCourseDetail($data)
{
    $post_id = (int) $data['id'];
    $post = get_post($post_id);

    if ($post && $post->post_type == 'course') {
        return get_post_details($post_id);
    } else {
        return create_rest_response(404, "Course post not found");
    }
}
