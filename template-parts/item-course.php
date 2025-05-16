<div class="course-card">
    <a href="<?php the_permalink() ?>">
        <div class="course-image">
            <img src="<?php echo get_the_post_thumbnail_url() ?: get_default_image() ?>" alt="">
        </div>
    </a>
    <div class="course-content">
        <div class="course-description">
            <div class="course-list-category">
                <?php
                $course_id = get_the_ID();
                $course_type = get_field("course_type", $course_id);
                $course_type_slug = sanitize_title($course_type);

                if ($course_type) {
                    echo '<p class="course-category"><a href="' . esc_url(add_query_arg('course_type', $course_type_slug, home_url('/courses/'))) . '">' . esc_html($course_type) . '</a></p>';
                }
                ?>

            </div>
            <p class="course-title"><?php the_title() ?></p>

            <?php
            echo '<p class="course-difficulty">Skill Level: ' . esc_html(get_field("skill_level", $course_id)) . '</p>';
            ?>
        </div>
        <div class="btn-course">
            <a href="<?php the_permalink() ?>" class="course-link">Learn More</a>
        </div>
    </div>

</div>