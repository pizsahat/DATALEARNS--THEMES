<?php
get_header();
?>

<?php
$selected_single_lesson_template = get_theme_mod('selected_single_lesson_template_part');

if ($selected_single_lesson_template) {
    $template_part = get_post($selected_single_lesson_template);

    if ($template_part && $template_part->post_type === 'template_part') {
        echo '<section class="the-lesson-page">';
        echo apply_filters('the_content', $template_part->post_content);
        echo '</section>';
    } else {
        get_template_part('template-parts/404');
    }
} else {
    get_template_part('template-parts/404');
}

if (!function_exists('llms_get_post')) {
    get_footer();
    return;
}

$current_lesson_id = get_the_ID();
$course_url = '';

if ($current_lesson_id) {
    $lesson = llms_get_post($current_lesson_id);
    if ($lesson) {
        $course_id = $lesson->get('parent_course');
        if ($course_id) {
            $course_url = get_permalink($course_id);
        }
    }
}

if ($course_url && $course_url !== '#'): ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const backButtons = document.querySelectorAll('.llms-parent-course-permalink');

            if (backButtons.length === 0) return;

            const courseUrl = <?php echo json_encode($course_url); ?>;

            for (let i = 0; i < backButtons.length; i++) {
                const button = backButtons[i];

                if (button.tagName.toLowerCase() === 'a') {
                    button.href = courseUrl;
                } else {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        window.location.href = courseUrl;
                    });
                }
            }
        }, {
            once: true
        });
    </script>

<?php endif; ?>

<?php
get_footer();
?>