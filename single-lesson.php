<?php
get_header();
while (have_posts()) {
    the_post();
    $course_id = get_post_meta(get_the_ID(), '_llms_parent_course', true);
    if (!$course_id) {
        $course_id = get_the_ID();
    }
    $object = new LLMS_Course($course_id);
    $llms_lesson = new LLMS_Lesson(get_the_ID());
    $is_lesson_complete = $llms_lesson->is_complete();
    $video_embed_url = $llms_lesson->get('video_embed');
    $video_id = '';

    if (!empty($video_embed_url)) {
        $parsed_url = parse_url($video_embed_url);
        if (isset($parsed_url['query'])) {
            parse_str($parsed_url['query'], $query_params);
            if (isset($query_params['v'])) {
                $video_id = $query_params['v'];
            }
        }
    }
    $has_video = !empty($video_id);

    $course_title = get_the_title($course_id);

?>
    <div class="section-course">
        <div class="container page-section generic-content">
            <h1 style="margin-bottom:var(--wp--preset--spacing--40);" class="wp-block-post-title"><?php the_title(); ?></h1>
            <div class="row group lesson">
                <div class="two-thirds">
                    <?php the_content(); ?>
                </div>
                <div class="one-fifth right-lesson-content">
                    <h3>Course Progress</h3>
                    <?php echo do_shortcode("[lifterlms_course_progress]"); ?>

                    <h3>Course Syllabus</h3>
                    <div class="wrapper-syllabus">
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
                                <div class="item-course-section <?php echo $section_is_active ? 'active-section' : ''; ?>">
                                    <h4><?php echo esc_html($section->get('title')); ?> <span class="toggle-icon">&#9660;</span></h4>
                                    <ul style="display: <?php echo $section_is_active ? 'block' : 'none'; ?>;">
                                        <?php
                                        $current_user_id = get_current_user_id();
                                        $student = new LLMS_Student($current_user_id);
                                        if (!empty($lessons)) {
                                            foreach ($lessons as $lesson) {
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


                                                            <?php echo esc_html($lesson->get('title')); ?>

                                                        </div>

                                                        <?php if ($is_free) { ?>
                                                            <span class="free-tag">Free</span>
                                                        <?php } ?>
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
                </div>
            </div>
        </div>
    </div>

<?php
}
get_footer();
?>


<script>
    document.addEventListener("DOMContentLoaded", () => {
        const sections = document.querySelectorAll(".item-course-section h4");

        sections.forEach((section) => {
            const content = section.nextElementSibling;
            const icon = section.querySelector(".toggle-icon");

            if (section.parentElement.classList.contains("active-section")) {
                content.style.display = "block";
                icon.style.transform = "rotate(180deg)";
            }

            section.addEventListener("click", () => {
                if (content.style.display === "block") {
                    content.style.display = "none";
                    icon.style.transform = "rotate(0deg)";
                } else {
                    content.style.display = "block";
                    icon.style.transform = "rotate(180deg)";
                }
                content.style.transition = "all 0.3s ease-in-out";
            });
        });
        const markCompleteButton = document.getElementById("llms_mark_complete");
        if (<?php echo $has_video ? 'false' : 'true'; ?> && markCompleteButton) {
            markCompleteButton.style.display = 'inline-block';
        }
    });
</script>

<script>
    const isLessonComplete = <?php echo $is_lesson_complete ? 'true' : 'false'; ?>;
    const isEnrolled = <?php echo $is_enrolled ? 'true' : 'false'; ?>;
    document.addEventListener("DOMContentLoaded", function() {
        const centerVideoDivs = document.querySelectorAll(".llms-video-wrapper");
        centerVideoDivs.forEach((div) => {
            if (!div.id) {
                div.id = "player";
            }
        });
    });

    let tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    let firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    let player;
    let alertShown = false;


    function onYouTubeIframeAPIReady() {
        const videoId = "<?php echo $video_id; ?>";
        if (videoId) {
            player = new YT.Player('player', {
                videoId: videoId,
                events: {
                    'onReady': onPlayerReady,
                    'onStateChange': onPlayerStateChange
                }
            });
        }
    }

    function onPlayerReady(event) {
        console.log('YouTube Player is ready');
    }

    function onPlayerStateChange(event) {
        if (!isEnrolled) {
            console.log('You not enroll this course');
            return;
        }
        if (isLessonComplete) {
            console.log('Lesson is already marked as complete.');
            return;
        }
        if (event.data === YT.PlayerState.PLAYING) {
            let duration = player.getDuration();

            const interval = setInterval(() => {
                let currentTime = player.getCurrentTime();
                let timeRemaining = duration - currentTime;

                const currentLesson = document.querySelector('.current-lesson');
                if (currentLesson) {
                    const checkIcon = currentLesson.querySelector('.fa-check-circle');

                    if (timeRemaining <= 20 && !alertShown) {
                        alertShown = true;
                        const lessonId = "<?php echo get_the_ID(); ?>";
                        const userId = "<?php echo get_current_user_id(); ?>";
                        markLessonComplete(userId, lessonId);

                        if (checkIcon) {
                            checkIcon.style.color = 'green';
                        }
                        const markCompleteButton = document.getElementById("llms_mark_complete");
                        if (markCompleteButton) {
                            const lessonCompleteDiv = document.createElement("div");
                            lessonCompleteDiv.classList.add("llms-lesson-button-wrapper");
                            lessonCompleteDiv.innerHTML = "Lesson Complete";

                            markCompleteButton.parentNode.replaceChild(lessonCompleteDiv, markCompleteButton);
                        }

                        clearInterval(interval);
                    }
                }

                if (player.getPlayerState() === YT.PlayerState.ENDED) {
                    clearInterval(interval);
                }
            }, 1000);
        }
    }

    function markLessonComplete(userId, lessonId) {
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'mark_lesson_complete',
                    user_id: userId,
                    lesson_id: lessonId,
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Lesson marked as complete');
                } else {
                    console.error('Failed to mark lesson as complete');
                }
            })
            .catch(error => console.error('Error:', error));
    }
</script>