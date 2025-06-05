<?php
get_header();

function format_course_type($text)
{
    $text = str_replace('-', ' ', $text);
    return ucwords($text);
}

$has_relevant_params = isset($_GET['course_type']) || isset($_GET['course-skill']) || isset($_GET['q']);

if ($has_relevant_params) {
?>
    <div class="site-container">
        <div class="archive-course-container">
            <?php
            $shortcode_params = array(
                'item' => '-1',
                'random' => 'false'
            );

            if (isset($_GET['course_type'])) {
                $shortcode_params['course_type'] = sanitize_text_field($_GET['course_type']);
            }

            if (isset($_GET['course-skill'])) {
                $shortcode_params['skill_level'] = sanitize_text_field($_GET['course-skill']);
            }

            if (isset($_GET['q'])) {
                $shortcode_params['search'] = sanitize_text_field($_GET['q']);
            }

            $shortcode_string = '[list-course';
            foreach ($shortcode_params as $key => $value) {
                $shortcode_string .= ' ' . $key . '="' . $value . '"';
            }
            $shortcode_string .= ']';

            echo do_shortcode($shortcode_string);
            ?>
        </div>
    </div>

    <script>
        function filterBySkillLevel() {
            var skillLevel = document.getElementById("skill_level").value;
            var url = new URL(window.location.href);
            url.searchParams.set('course-skill', skillLevel);
            window.location.href = url.toString();
        }
    </script>
    <?php
} else {
    $selected_course_archive_template = get_theme_mod('selected_course_archive_template_part');

    if ($selected_course_archive_template) {
        $template_part = get_post($selected_course_archive_template);
        if ($template_part && $template_part->post_type === 'template_part') {
            echo '<section class="course-archive">';
            echo apply_filters('the_content', $template_part->post_content);
            echo '</section>';
        } else {
    ?>
            <div class="site-container">
                <?php echo do_shortcode('[list-course item="-1" random="false"]'); ?>
            </div>
        <?php
        }
    } else {
        ?>
        <div class="site-container">
            <?php echo do_shortcode('[list-course item="-1" random="false"]'); ?>
        </div>
<?php
    }
}

get_footer();
?>