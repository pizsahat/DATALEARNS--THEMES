<?php
get_header();
while (have_posts()) {
    the_post(); ?>

    <div class="container container--narrow page-section">

        <div class="generic-content">
            <?php
            echo do_shortcode('[lifterlms_course_author avatar_size="48" bio="yes" course_id="' . get_the_ID() . '"]');
            the_content() ?>
        </div>
    </div>
<?php
}
get_footer();
?>