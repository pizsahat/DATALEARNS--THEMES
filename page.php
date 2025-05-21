<?php
get_header();

while (have_posts()) {
    the_post();

    $show_header = get_field('show_header');
    $use_container = get_field('use_container');
    $enable_spacing = get_field('enable_vertical_spacing');

    $classes = [];

    if ($use_container) {
        $classes[] = 'container';
    }

    if ($enable_spacing || $enable_spacing === null) {
        $classes[] = 'vertical-spacing'; // class ini mengatur padding atas & bawah
    }

    $wrapper_attr = !empty($classes) ? ' class="' . esc_attr(implode(' ', $classes)) . '"' : '';

    if (!empty($classes)) {
        echo '<div' . $wrapper_attr . '>';
    }
?>

    <div class="generic-content">
        <?php the_content(); ?>
    </div>

<?php
    if (!empty($classes)) {
        echo '</div>';
    }
}

get_footer();
?>