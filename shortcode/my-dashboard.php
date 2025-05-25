<?php

add_shortcode('datalearns-my-dashboard', 'MyDashboard');

function MyDashboard()
{
    ob_start();

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

        $enrolled_courses_view = array_slice($enrolled_courses, 0, 3);
    }
?>
    <div class="dashboard-course">
        <div class="last-accessed-section">
            <h3 style="margin-top: 0;">Last Accessed Course</h3>
            <?php
            $last_accessed_lesson_id = get_user_meta($current_user_id, 'last_accessed_lesson', true);

            if ($last_accessed_lesson_id) {
                $last_accessed_lesson_title = get_the_title($last_accessed_lesson_id);
                $last_accessed_lesson_url = get_permalink($last_accessed_lesson_id);
                $Lesson = new LLMS_Lesson($last_accessed_lesson_id);

                $parent_course_id = $Lesson->get("parent_course");
                $next_lesson = $Lesson->get_next_lesson();

                if ($parent_course_id) {
            ?>
                    <div class="card-course-last-access">
                        <a href="<?php echo get_permalink($parent_course_id); ?>">
                            <div class="course-image">
                                <?php if (has_post_thumbnail($parent_course_id)) : ?>
                                    <img src="<?php echo get_the_post_thumbnail_url($parent_course_id); ?>" alt="<?php echo esc_attr(get_the_title($parent_course_id)); ?>">
                                <?php else : ?>
                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/images/default-thumbnail.jpg'); ?>" alt="Default Thumbnail">
                                <?php endif; ?>
                            </div>
                        </a>
                        <div class="course-content">
                            <p class="course-title"><?php echo esc_html(get_the_title($parent_course_id)); ?></p>
                            <p class="course-difficulty">
                                Skill Level: <?php echo esc_html(get_field("skill_level", $parent_course_id)); ?>
                            </p>
                            <div class="last-accessed-details">
                                <a href="<?php echo esc_url($last_accessed_lesson_url); ?>"> <span class="play-icon"></span></a>
                                <p>Last Accessed Lesson:
                                    <a href="<?php echo esc_url($last_accessed_lesson_url); ?>">
                                        <?php echo esc_html($last_accessed_lesson_title); ?>
                                    </a>
                                </p>
                            </div>
                            <div class="btn-course">
                                <a href="<?php echo get_permalink($next_lesson); ?>" class="course-link">Continue</a>
                            </div>
                        </div>
                    </div>
            <?php

                } else {
                    echo '<p>Lesson: <a href="' . esc_url($last_accessed_lesson_url) . '">' . esc_html($last_accessed_lesson_title) . '</a></p>';
                    echo '<p>Course information is not available.</p>';
                }
            } else {
                echo '<p>You have not accessed any lessons yet.</p>';
            }
            ?>
        </div>
        <hr class="vertical-divider">
        <div class="list-course-section">
            <h3 style="margin-top: 0;">My Courses</h3>
            <?php if (!empty($enrolled_courses)) : ?>
                <ul class="course-list-dashboard">
                    <?php foreach ($enrolled_courses_view as $course) : ?>
                        <li class="course-list-item">
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
                                <div class="progress-bar">
                                    <?php
                                    if (class_exists('LLMS_Course')) {
                                        $course_dashboard = new LLMS_Course($course->ID);
                                        $percent_complete = $course_dashboard->get_percent_complete() ?? 0;
                                    } else {
                                        $percent_complete = 0;
                                    }
                                    ?>
                                    <div class="progress-line">
                                        <div class="progress-fill" style="width: <?php echo esc_attr($percent_complete); ?>%;"></div>
                                    </div>
                                    <p class="progress-text"><?php echo esc_html($percent_complete); ?>% Complete</p>
                                </div>
                            </div>
                        </li>
                    <?php endforeach;
                    if (count($enrolled_courses) > 3) : ?>
                        <a href="<?php echo esc_url(site_url('my-courses'))   ?>" class="view-all-link">View All My Course >></a>
                    <?php endif; ?>
                </ul>

            <?php else : ?>
                <p>You have not enrolled in any courses yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="dashboard-certificates">
        <h2>My Certificates</h2>
        <?php
        $certificates = get_posts(array(
            'post_type' => 'certificate',
            'meta_key' => 'certificate_user',
            'meta_value' => $current_user_id,
            'numberposts' => -1
        ));

        if (!empty($certificates)) : ?>
            <ul class="certificate-list">
                <?php foreach ($certificates as $certificate) :
                    $certificate_file = get_field('certificate_file', $certificate->ID);
                    $certificate_file_url = get_post_meta($certificate->ID, 'certificate_url', true);
                    $certificate_image = get_field("certificate_image", $certificate->ID); ?>
                    <li class="certificate-card">
                        <a href="<?php echo get_permalink($certificate->ID) ?>">
                            <div class="certificate-image">
                                <?php
                                if ($certificate_image) :
                                    $certificate_image_url = is_array($certificate_image) ? $certificate_image['url'] : $certificate_image;
                                ?>
                                    <img src="<?php echo esc_url($certificate_image_url); ?>" alt="<?php echo esc_attr(get_the_title($certificate->ID)); ?>">
                                <?php else : ?>
                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/images/default-thumbnail.jpg'); ?>" alt="Default Thumbnail">
                                <?php endif; ?>
                            </div>
                            <div class="certificate-content">
                                <p class="certificate-title"><?php echo esc_html(get_the_title($certificate->ID)); ?></p>
                                <p class="certificate-date">
                                    Date Awarded: <?php echo esc_html(get_the_date('F j, Y', $certificate->ID)); ?>
                                </p>
                            </div>
                        </a>
                    </li>

                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p>You have not earned any certificates yet.</p>
        <?php endif; ?>
    </div>


<?php
    return ob_get_clean();
}
