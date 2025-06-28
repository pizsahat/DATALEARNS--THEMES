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
?>

<?php
get_footer();
