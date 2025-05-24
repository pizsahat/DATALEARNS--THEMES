<?php
get_header();
function format_course_type($text)
{
    $text = str_replace('-', ' ', $text);
    return ucwords($text);
}

?>

<div class="container vertical-spacing">
    <div class="archive-course-container">
        <?php
        $search_query = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
        $course_type_filter = isset($_GET['course_type']) ? sanitize_text_field($_GET['course_type']) : '';
        $skill_level_filter = isset($_GET['course-skill']) ? sanitize_text_field($_GET['course-skill']) : '';

        $shortcode_params = array(
            'item' => '-1',
            'random' => 'false'
        );

        if (!empty($course_type_filter)) {
            $shortcode_params['course_type'] = $course_type_filter;
        }

        if (!empty($skill_level_filter)) {
            $shortcode_params['skill_level'] = $skill_level_filter;
        }

        if (!empty($search_query)) {
            $shortcode_params['search'] = $search_query;
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

<?php get_footer(); ?>