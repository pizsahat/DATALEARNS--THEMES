<?php

add_shortcode('datalearns-my-courses', 'MyCourses');

function MyCourses()
{
    $current_user_id = get_current_user_id();

    $enrolled_courses = array();

    if ($current_user_id) {
        $all_courses = get_posts(array(
            'post_type' => 'course',
            'numberposts' => -1,
            'order' => 'ASC'
        ));

        foreach ($all_courses as $course) {
            if (llms_is_user_enrolled($current_user_id, $course->ID)) {
                $enrolled_courses[] = $course;
            }
        }
    }
?>

    <?php if (!empty($enrolled_courses)) : ?>
        <div class="course-grid">
            <?php foreach ($enrolled_courses as $course) : ?>
                <div class="course-card">
                    <a href="<?php echo get_permalink($course->ID); ?>">
                        <div class="course-image">
                            <?php if (has_post_thumbnail($course->ID)) : ?>
                                <img src="<?php echo get_the_post_thumbnail_url($course->ID); ?>" alt="<?php echo esc_attr(get_the_title($course->ID)); ?>">
                            <?php else : ?>
                                <img src="<?php echo esc_url(get_template_directory_uri() . '/images/default-thumbnail.jpg'); ?>" alt="Default Thumbnail">
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="course-content">
                        <p class="course-title"><?php echo esc_html(get_the_title($course->ID)); ?></p>
                        <p class="course-difficulty">
                            Skill Level: <?php echo esc_html(get_field("skill_level", $course->ID)); ?>
                        </p>
                        <div class="progress-bar-course">
                            <?php
                            if (class_exists('LLMS_Course')) {
                                $course_dashboard = new LLMS_Course($course->ID);
                                $percent_complete = $course_dashboard->get_percent_complete() ?? 0; // Default ke 0 jika null
                            } else {
                                $percent_complete = 0;
                            }
                            ?>
                            <div class="progress-line">
                                <div class="progress-fill" style="width: <?php echo esc_attr($percent_complete); ?>%;"></div>
                            </div>
                            <p class="progress-text"><?php echo esc_html($percent_complete); ?>% Complete</p>
                        </div>
                        <div class="btn-course">
                            <?php

                            lifterlms_course_continue_button($course->ID) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>You have not enrolled in any courses yet.</p>
        <?php endif; ?>
        </div>
    <?php
    return ob_get_clean();
}
