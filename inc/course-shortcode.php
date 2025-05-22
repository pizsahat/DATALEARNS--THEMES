<?php

add_shortcode('course-syllabus', 'CourseSyllabus');

function CourseSyllabus()
{
    ob_start();
    $course_id = get_post_meta(get_the_ID(), '_llms_parent_course', true);
    if (!$course_id) {
        $course_id = get_the_ID();
    }
    $object = new LLMS_Course($course_id); ?>
    <br>
    <h3>Course Syllabus</h3>
    <div class="wrapper-syllabus-course">
        <?php
        $sections = $object->get_sections();
        if (!empty($sections)) {
            foreach ($sections as $section) {
                $lessons = $section->get_lessons();
                $section_is_active = false;

                if (!empty($lessons)) {
                    foreach ($lessons as $lesson) {
                        if (get_permalink($lesson->get("id")) === get_permalink()) {
                            $section_is_active = true;
                            break;
                        }
                    }
                }
        ?>
                <div class="item-course-section">
                    <h4><?php echo esc_html($section->get('title')); ?></h4>
                    <ul>
                        <?php
                        $current_user_id = get_current_user_id();
                        $student = new LLMS_Student($current_user_id);
                        $lesson_count = count($lessons);
                        $lesson_index = 0;

                        if (!empty($lessons)) {
                            foreach ($lessons as $lesson) {
                                $lesson_index++;
                                $post_id = $lesson->get("id");
                                $is_complete = $student->is_complete($post_id);
                                $is_enrolled = llms_is_user_enrolled(get_current_user_id(), $post_id);
                                $is_free = $lesson->is_free();
                        ?>
                                <a href="<?php echo get_permalink($post_id); ?>"
                                    class="<?php echo (!$is_enrolled && !$is_free && !current_user_can('administrator')) ? 'disabled-link' : ''; ?>">
                                    <li class="<?php echo (get_permalink($post_id) === get_permalink()) ? 'current-lesson' : ''; ?> <?php echo (!$is_enrolled && !$is_free && !current_user_can('administrator')) ? 'disable' : ''; ?>">
                                        <div class="lesson-content">
                                            <span class="icon">
                                                <?php if ($is_enrolled) {
                                                    if ($is_complete) { ?>
                                                        <i class="fa fa-check-circle" style="color: green;"></i>
                                                    <?php } else { ?>
                                                        <i class="fa fa-check-circle" style="color: gray;"></i>
                                                    <?php }
                                                } else if ($is_free || current_user_can('administrator')) { ?>
                                                    <i class="fa fa-unlock"></i>
                                                <?php } else { ?>
                                                    <i class="fa fa-lock" style="color: gray;"></i>
                                                <?php } ?>
                                            </span>

                                            <span class="title"><?php echo esc_html($lesson->get('title')); ?></span>
                                        </div>

                                        <div class="lesson-meta">
                                            <?php if ($is_free && !$is_enrolled) { ?>
                                                <span class="free-tag">Free</span>
                                            <?php } ?>
                                            <?php if ($is_enrolled) { ?>
                                                <span class="lesson-progress"><?php echo $lesson_index . ' of ' . $lesson_count; ?></span>
                                            <?php } ?>
                                        </div>
                                    </li>
                                </a>
                        <?php
                            }
                        } else {
                            echo '<li>No lessons available in this section.</li>';
                        }
                        ?>
                    </ul>
                </div>
        <?php
            }
        } else {
            echo '<p>No sections found for this course.</p>';
        }
        ?>
    </div>
<?php
    return ob_get_clean();
}

add_shortcode('course-info', 'CourseInfo');

function CourseInfo()
{
    ob_start();
    $course_id = get_post_meta(get_the_ID(), '_llms_parent_course', true);
    if (!$course_id) {
        $course_id = get_the_ID();
    }
    $object = new LLMS_Course($course_id); ?>
    <table class="course-info">
        <tr>
            <td>Course code:</td>
            <td><?php echo esc_html(get_field('course_code')); ?></td>
        </tr>
        <tr>
            <td>Course type:</td>
            <td><?php echo esc_html(get_field('course_type')); ?></td>
        </tr>
        <tr>
            <td>Duration:</td>
            <td><?php echo esc_html(get_field('duration')); ?></td>
        </tr>
        <tr>
            <td>Skill level:</td>
            <td><?php echo esc_html(get_field('skill_level')); ?></td>
        </tr>
        <tr>
            <td>Course Format:</td>
            <td><?php echo esc_html(get_field('course_format')); ?></td>
        </tr>
    </table>
    <?php
    if (llms_is_user_enrolled(get_current_user_id(), get_the_ID())) {
        echo do_shortcode('[lifterlms_course_continue_button course_id="' . get_the_ID() . '"]');
    } else {
        echo do_shortcode('[lifterlms_pricing_table product="' . get_the_ID() . '"]');
    } ?>
<?php
    return ob_get_clean();
}
