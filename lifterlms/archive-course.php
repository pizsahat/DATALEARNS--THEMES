<?php
get_header();
if (isset($_GET['q']) && !empty($_GET['q']) && isset($_GET['course_type']) && !empty($_GET['course_type'])) {
    $subtitle = 'Search Results for: ' . sanitize_text_field($_GET['q']) . ' & Filtering Course Type: ' . sanitize_text_field($_GET['course_type']);
} elseif (isset($_GET['q']) && !empty($_GET['q'])) {
    $subtitle = 'Search Results for: ' . sanitize_text_field($_GET['q']);
} elseif (isset($_GET['course_type']) && !empty($_GET['course_type'])) {
    $subtitle = 'Filtering Course Type: ' . sanitize_text_field($_GET['course_type']);
} else {
    $subtitle = 'Unlock your potential with knowledge—start learning today!';
}

function format_course_type($text)
{
    $text = str_replace('-', ' ', $text);
    return ucwords($text);
}

?>

<div class="container page-section mt40">
    <div class="archive-course-container">
        <?php
        $search_query = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
        $course_type_filter = isset($_GET['course_type']) ? sanitize_text_field($_GET['course_type']) : '';
        $skill_level_filter = isset($_GET['course-skill']) ? sanitize_text_field($_GET['course-skill']) : '';
        $formatted_course_type = format_course_type($course_type_filter);

        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'course',
        );

        if (!empty($course_type_filter)) {
            $args['meta_query'][] = array(
                'key' => 'course_type',
                'value' => $formatted_course_type,
                'compare' => 'LIKE',
            );
        }

        if (!empty($skill_level_filter)) {
            $args['meta_query'][] = array(
                'key' => 'skill_level',
                'value' => $skill_level_filter,
                'compare' => 'LIKE',
            );
        }

        if (!empty($search_query)) {
            add_filter('posts_where', function ($where) use ($search_query) {
                global $wpdb;
                $where .= $wpdb->prepare(" AND {$wpdb->posts}.post_title LIKE %s", '%' . $wpdb->esc_like($search_query) . '%');
                return $where;
            });
        }

        $courseQuery = new WP_Query($args);

        remove_filter('posts_where', function ($where) use ($search_query) {
            global $wpdb;
            $where .= $wpdb->prepare(" AND {$wpdb->posts}.post_title LIKE %s", '%' . $wpdb->esc_like($search_query) . '%');
            return $where;
        });

        if ($courseQuery->have_posts()) :
            while ($courseQuery->have_posts()) :
                $courseQuery->the_post();
                get_template_part('template-parts/item', 'course');
            endwhile;
        else :
            echo "<h3>Sorry, we couldn't find any results for '" . sanitize_text_field($_GET['q']) . "'" . "</h3>";
        endif;

        wp_reset_postdata();
        ?>
    </div>
</div>

<?php get_footer(); ?>

<script>
    function filterBySkillLevel() {
        var skillLevel = document.getElementById("skill_level").value;
        var url = new URL(window.location.href);
        url.searchParams.set('course-skill', skillLevel);
        window.location.href = url.toString();
    }
</script>