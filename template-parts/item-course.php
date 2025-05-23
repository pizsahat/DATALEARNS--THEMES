<div class="course-card">
    <div class="course-image-wrapper">
        <a href="<?php the_permalink() ?>" class="image-link">
            <div class="course-image-container">
                <img src="<?php echo get_the_post_thumbnail_url() ?: get_default_image() ?>" alt="<?php the_title() ?>" class="course-image">
                <div class="image-overlay"></div>
                <div class="image-glow"></div>
            </div>
        </a>

        <?php
        $course_id = get_the_ID();
        $skill_level = get_field("skill_level", $course_id);
        if ($skill_level) {
            $level_class = strtolower($skill_level);
            echo '<div class="floating-badge ' . $level_class . '">' . esc_html($skill_level) . '</div>';
        }
        ?>
    </div>

    <div class="course-body">
        <div class="course-content">
            <div class="course-meta-top">
                <?php
                $course_type = get_field("course_type", $course_id);
                $course_type_slug = sanitize_title($course_type);

                if ($course_type) {
                    echo '<a href="' . esc_url(add_query_arg('course_type', $course_type_slug, home_url('/courses/'))) . '" class="course-tag">' . esc_html($course_type) . '</a>';
                }
                ?>

                <div class="course-duration">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12,6 12,12 16,14" />
                    </svg>
                    <span>Self-paced</span>
                </div>
            </div>

            <h3 class="course-title">
                <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
            </h3>


        </div>


        <div class="course-action">
            <?php if ($skill_level): ?>
                <div class="skill-progress">
                    <div class="skill-info">
                        <span class="skill-text">Skill Level</span>
                        <span class="skill-value"><?php echo esc_html($skill_level) ?></span>
                    </div>
                    <div class="progress-bar">
                        <?php
                        $progress_width = 33;
                        if (strtolower($skill_level) === 'beginner') $progress_width = 20;
                        if (strtolower($skill_level) === 'intermediate') $progress_width = 66;
                        if (strtolower($skill_level) === 'advance') $progress_width = 80;
                        if (strtolower($skill_level) === 'all level') $progress_width = 100;
                        ?>
                        <div class="progress-fill" style="width: <?php echo $progress_width ?>%"></div>
                    </div>
                </div>
            <?php endif; ?>
            <a href="<?php the_permalink() ?>" class="learn-btn">
                <span class="btn-text">Explore Course</span>
                <div class="btn-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </div>
            </a>
        </div>
    </div>

    <div class="card-glow"></div>
</div>