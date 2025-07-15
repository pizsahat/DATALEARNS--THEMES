<?php

add_shortcode('course-syllabus', 'CourseSyllabus');

function CourseSyllabus($atts = [])
{
    ob_start();


    $atts = shortcode_atts([
        'only_active' => 'no',
    ], $atts);


    $course_id = get_post_meta(get_the_ID(), '_llms_parent_course', true);
    if (!$course_id) {
        $course_id = get_the_ID();
    }

    $object = new LLMS_Course($course_id);
    $sections = $object->get_sections();

    if (!empty($sections)) {
        echo '<div class="wrapper-syllabus-course">';
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


            if ($atts['only_active'] === 'yes' && !$section_is_active) {
                continue;
            }

            echo '<div class="item-course-section">';
            echo '<div class="section-header">';
            echo '<span class="section-title">' . esc_html($section->get('title')) . '</span>';
            echo '<span class="section-arrow">▼</span>';
            echo '</div>';
            echo '<ul class="section-content ' . ($section_is_active ? '' : 'collapsed') . '">';

            $current_user_id = get_current_user_id();
            $student = new LLMS_Student($current_user_id);

            if (!empty($lessons)) {
                foreach ($lessons as $lesson) {
                    $post_id = $lesson->get("id");
                    $is_complete = $student->is_complete($post_id);
                    $is_enrolled = llms_is_user_enrolled($current_user_id, $post_id);
                    $is_free = $lesson->is_free();
                    $is_current = (get_permalink($post_id) === get_permalink());
                    $is_disabled = (!$is_enrolled && !$is_free && !current_user_can('administrator'));

                    echo '<li class="' . ($is_current ? 'current-lesson' : '') . ' ' . ($is_disabled ? 'disable' : '') . '">';
                    echo '<a href="' . esc_url(get_permalink($post_id)) . '" class="' . ($is_disabled ? 'disabled-link' : '') . '">';
                    echo '<div class="lesson-content">';
                    echo '<span class="icon">';
                    if ($is_enrolled) {
                        echo '<i class="fa fa-check-circle" style="color:' . ($is_complete ? 'green' : 'gray') . ';"></i>';
                    } elseif ($is_free || current_user_can('administrator')) {
                        echo '<i class="fa fa-unlock"></i>';
                    } else {
                        echo '<i class="fa fa-lock" style="color: gray;"></i>';
                    }
                    echo '</span>';
                    echo '<span class="title">' . esc_html($lesson->get('title')) . '</span>';
                    echo '</div>';

                    echo '</a>';
                    echo '</li>';
                }
            } else {
                echo '<li>No lessons available in this section.</li>';
            }

            echo '</ul>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>No sections found for this course.</p>';
    }


?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const headers = document.querySelectorAll(".wrapper-syllabus-course .section-header");
            headers.forEach(header => {
                header.addEventListener("click", function() {
                    const content = this.nextElementSibling;
                    this.classList.toggle("active");
                    content.classList.toggle("collapsed");
                });
            });
        });
    </script>
<?php

    return ob_get_clean();
}


add_shortcode('course-info', 'CourseInfo');

function CourseInfo($atts = [])
{
    ob_start();

    $atts = shortcode_atts([
        'show_icons' => 'yes',
        'fields'     => '',
    ], $atts);

    $show_icons = $atts['show_icons'] === 'yes';
    $selected_fields = array_filter(array_map('trim', explode(',', $atts['fields'])));

    $course_id = get_post_meta(get_the_ID(), '_llms_parent_course', true);
    if (!$course_id) {
        $course_id = get_the_ID();
    }

    $acf_fields = get_fields($course_id);
    if (!$acf_fields) {
        return '<p>No course info available.</p>';
    }

    $output_fields = [];

    $blacklist_fields = ['chat_flow_id'];

    foreach ($acf_fields as $key => $value) {
        if (in_array($key, $blacklist_fields)) continue;
        if (str_ends_with($key, '_icon')) continue;

        if (!empty($selected_fields) && !in_array($key, $selected_fields)) {
            continue;
        }

        $icon_key = $key . '_icon';
        $icon = isset($acf_fields[$icon_key]) ? $acf_fields[$icon_key] : '';
        $label = ucwords(str_replace('_', ' ', $key));

        $output_fields[] = [
            'label' => $label,
            'value' => $value,
            'icon'  => $icon
        ];
    }


    if (empty($output_fields)) {
        return '<p>No matching course info found.</p>';
    }
?>

    <table class="course-info">
        <?php foreach ($output_fields as $info): ?>
            <tr>
                <td>
                    <?php if ($show_icons && $info['icon']): ?>
                        <span style="margin-right: 6px;"><?php echo $info['icon']; ?></span>
                    <?php endif; ?>
                    <?php echo esc_html($info['label']); ?>:
                </td>
                <td><?php echo esc_html($info['value']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php
    if (function_exists('llms_is_user_enrolled') && llms_is_user_enrolled(get_current_user_id(), $course_id)) {
        echo do_shortcode('[lifterlms_course_continue_button course_id="' . $course_id . '"]');
    } else {
        echo do_shortcode('[lifterlms_pricing_table product="' . $course_id . '"]');
    }

    return ob_get_clean();
}


add_shortcode('llms_lesson_content', function () {
    $lesson = llms_get_post(get_the_ID());
    return $lesson ? apply_filters('the_content', $lesson->get('content')) : '';
});

add_shortcode('llms_lesson_video', function ($atts) {
    $atts = shortcode_atts([
        'auto_complete' => 'true',
        'complete_seconds' => 20,
        'debug' => 'false'
    ], $atts);

    $lesson = llms_get_post(get_the_ID());

    if (!$lesson || !$lesson->get('video_embed')) {
        return '';
    }

    $video_embed = wp_oembed_get($lesson->get('video_embed'));


    if ($atts['auto_complete'] === 'true') {
        add_action('wp_footer', function () {
            echo '<style>#llms_mark_complete { display: none !important; }</style>';
        });

        $script = generate_auto_complete_script($atts['complete_seconds'], $atts['debug']);
        $video_embed .= $script;
    }

    return $video_embed;
});

function generate_auto_complete_script($complete_seconds = 20, $debug = 'false')
{
    $lesson_id = get_the_ID();
    $user_id = get_current_user_id();
    $lesson = llms_get_post($lesson_id);

    if (!$lesson) {
        return '';
    }

    $video_embed = $lesson->get('video_embed');
    $video_id = extract_youtube_video_id($video_embed);


    $student = llms_get_student($user_id);
    $course_id = $lesson->get('parent_course');
    $is_enrolled = $student && $course_id ? $student->is_enrolled($course_id) : false;
    $is_lesson_complete = $student ? $student->is_complete($lesson_id, 'lesson') : false;

    ob_start();
    ?>
    <script>
        (function() {
            'use strict';


            const CONFIG = {
                isLessonComplete: <?php echo $is_lesson_complete ? 'true' : 'false'; ?>,
                isEnrolled: <?php echo $is_enrolled ? 'true' : 'false'; ?>,
                videoId: "<?php echo esc_js($video_id); ?>",
                lessonId: <?php echo intval($lesson_id); ?>,
                userId: <?php echo intval($user_id); ?>,
                completeSeconds: <?php echo intval($complete_seconds); ?>,
                debug: <?php echo $debug === 'true' ? 'true' : 'false'; ?>,
                ajaxUrl: "<?php echo admin_url('admin-ajax.php'); ?>"
            };


            let player = null;
            let alertShown = false;
            let completionInterval = null;
            let playerReady = false;


            function debugLog(message, data = null) {
                if (CONFIG.debug) {
                    console.log('[LLMS Auto-Complete]', message, data || '');
                }
            }


            document.addEventListener("DOMContentLoaded", function() {
                debugLog('DOM loaded, initializing...');
                initializeVideoPlayer();
            });

            function initializeVideoPlayer() {
                debugLog('Initializing video player...');


                setTimeout(() => {
                    setupVideoContainers();
                }, 500);
            }

            function setupVideoContainers() {

                const selectors = [
                    '.llms-video-wrapper',
                    '.wp-block-embed__wrapper',
                    '.wp-embed-responsive',
                    '.fluid-width-video-wrapper',
                    '.video-container',
                    '[class*="video"]',
                    'iframe[src*="youtube"]'
                ];

                let videoContainer = null;
                let iframe = null;


                for (const selector of selectors) {
                    const elements = document.querySelectorAll(selector);
                    if (elements.length > 0) {
                        videoContainer = elements[0];
                        iframe = videoContainer.querySelector('iframe[src*="youtube"]') ||
                            (videoContainer.tagName === 'IFRAME' && videoContainer.src.includes('youtube') ? videoContainer : null);

                        if (iframe) {
                            debugLog('Found video container:', {
                                selector,
                                container: videoContainer,
                                iframe: iframe
                            });
                            break;
                        }
                    }
                }

                if (!iframe) {
                    debugLog('No YouTube iframe found');
                    return;
                }


                const currentSrc = iframe.src;
                if (!currentSrc.includes('enablejsapi=1')) {
                    const separator = currentSrc.includes('?') ? '&' : '?';
                    iframe.src = currentSrc + separator + 'enablejsapi=1&origin=' + encodeURIComponent(window.location.origin);
                }


                if (!iframe.id) {
                    iframe.id = 'llms-youtube-player';
                }


                loadYouTubeAPI();
            }

            function loadYouTubeAPI() {
                debugLog('Loading YouTube API...');

                if (document.querySelector('script[src*="youtube.com/iframe_api"]')) {
                    debugLog('YouTube API already loading');
                    return;
                }

                const tag = document.createElement('script');
                tag.src = "https://www.youtube.com/iframe_api";
                tag.async = true;

                const firstScriptTag = document.getElementsByTagName('script')[0];
                firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);


                window.onYouTubeIframeAPIReady = onYouTubeIframeAPIReady;
            }

            function onYouTubeIframeAPIReady() {
                debugLog('YouTube API ready');
                initYouTubePlayer();
            }

            function initYouTubePlayer() {
                if (!CONFIG.videoId) {
                    debugLog('No video ID found');
                    return;
                }


                const iframe = document.querySelector('iframe[src*="youtube"]');
                if (!iframe) {
                    debugLog('No YouTube iframe found for player initialization');
                    return;
                }

                const playerId = iframe.id || 'llms-youtube-player';
                iframe.id = playerId;

                try {

                    if (window.player && typeof window.player.destroy === 'function') {
                        window.player.destroy();
                    }


                    player = new YT.Player(playerId, {
                        events: {
                            'onReady': onPlayerReady,
                            'onStateChange': onPlayerStateChange,
                            'onError': onPlayerError
                        }
                    });


                    window.player = player;

                    debugLog('YouTube player initialized successfully', {
                        playerId: playerId,
                        videoId: CONFIG.videoId,
                        iframe: iframe.src
                    });

                } catch (error) {
                    debugLog('Error initializing YouTube player:', error);


                    setTimeout(() => {
                        retryPlayerInit(playerId);
                    }, 2000);
                }
            }

            function retryPlayerInit(playerId) {
                try {
                    player = new YT.Player(playerId, {
                        events: {
                            'onReady': onPlayerReady,
                            'onStateChange': onPlayerStateChange,
                            'onError': onPlayerError
                        }
                    });
                    debugLog('Player initialized on retry');
                } catch (error) {
                    debugLog('Failed to initialize player on retry:', error);
                }
            }

            function onPlayerReady(event) {
                playerReady = true;
                debugLog('Player ready');


                if (!CONFIG.isEnrolled) {
                    debugLog('User not enrolled, auto-complete disabled');
                    return;
                }

                if (CONFIG.isLessonComplete) {
                    debugLog('Lesson already complete');
                    updateUIComplete();
                    return;
                }
            }

            function onPlayerStateChange(event) {
                if (!playerReady || !CONFIG.isEnrolled || CONFIG.isLessonComplete) {
                    return;
                }

                debugLog('Player state changed:', event.data);

                if (event.data === YT.PlayerState.PLAYING) {
                    startCompletionTracking();
                } else if (event.data === YT.PlayerState.PAUSED || event.data === YT.PlayerState.ENDED) {
                    stopCompletionTracking();
                }
            }

            function onPlayerError(event) {
                debugLog('Player error:', event.data);
                stopCompletionTracking();
            }

            function startCompletionTracking() {
                if (completionInterval) {
                    clearInterval(completionInterval);
                }

                debugLog('Starting completion tracking...');

                completionInterval = setInterval(() => {
                    try {
                        const duration = player.getDuration();
                        const currentTime = player.getCurrentTime();
                        const timeRemaining = duration - currentTime;

                        if (CONFIG.debug && Math.floor(currentTime) % 10 === 0) {
                            debugLog(`Progress: ${Math.floor(currentTime)}/${Math.floor(duration)} seconds, ${Math.floor(timeRemaining)} remaining`);
                        }

                        if (timeRemaining <= CONFIG.completeSeconds && !alertShown) {
                            completeLesson();
                        }

                        if (player.getPlayerState() === YT.PlayerState.ENDED) {
                            stopCompletionTracking();
                        }
                    } catch (error) {
                        debugLog('Error in completion tracking:', error);
                        stopCompletionTracking();
                    }
                }, 1000);
            }

            function stopCompletionTracking() {
                if (completionInterval) {
                    clearInterval(completionInterval);
                    completionInterval = null;
                    debugLog('Stopped completion tracking');
                }
            }

            function completeLesson() {
                if (alertShown) return;

                alertShown = true;
                debugLog('Completing lesson...');


                updateUIComplete();


                markLessonComplete();

                stopCompletionTracking();
            }

            function updateUIComplete() {
                debugLog('Updating UI to complete state');


                const currentLesson = document.querySelector('.current-lesson, .llms-lesson-complete');
                if (currentLesson) {
                    const checkIcon = currentLesson.querySelector('.fa-check-circle, .fa-check');
                    if (checkIcon) {
                        checkIcon.style.color = 'green';
                        checkIcon.classList.add('completed');
                    }
                }


                const markCompleteButton = document.getElementById("llms_mark_complete");
                if (markCompleteButton && !markCompleteButton.disabled) {
                    markCompleteButton.click();


                    setTimeout(() => {
                        const completedDiv = document.createElement("div");
                        completedDiv.classList.add("llms-lesson-button-wrapper", "completed");
                        completedDiv.innerHTML = '<span class="llms-lesson-complete-text">✓ Lesson Complete</span>';

                        if (markCompleteButton.parentNode) {
                            markCompleteButton.parentNode.replaceChild(completedDiv, markCompleteButton);
                        }
                    }, 500);
                }
            }

            function markLessonComplete() {
                debugLog('Sending completion request to server...');


                const formData = new URLSearchParams({
                    action: 'mark_lesson_complete',
                    user_id: CONFIG.userId,
                    lesson_id: CONFIG.lessonId
                });

                fetch(CONFIG.ajaxUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: formData,
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        debugLog('Server response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        debugLog('Server response data:', data);
                        if (data.success) {
                            debugLog('Lesson marked as complete successfully');


                            CONFIG.isLessonComplete = true;


                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);


                            const event = new CustomEvent('llms_lesson_completed', {
                                detail: {
                                    lessonId: CONFIG.lessonId,
                                    userId: CONFIG.userId
                                }
                            });
                            document.dispatchEvent(event);
                        } else {
                            debugLog('Failed to mark lesson as complete:', data);
                        }
                    })
                    .catch(error => {
                        debugLog('Error marking lesson complete:', error);
                    });
            }


            window.addEventListener('beforeunload', function() {
                stopCompletionTracking();
            });


            if (CONFIG.debug) {
                window.llmsAutoComplete = {
                    player: () => player,
                    config: CONFIG,
                    completeNow: () => completeLesson(),
                    debugLog: debugLog
                };
            }

        })();
    </script>

    <style>
        .llms-lesson-complete-text {
            color: #28a745;
            font-weight: bold;
        }

        .fa-check-circle.completed,
        .fa-check.completed {
            color: #28a745 !important;
        }

        .llms-lesson-button-wrapper.completed {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 10px 15px;
            border-radius: 4px;
            text-align: center;
        }
    </style>
<?php
    return ob_get_clean();
}


function extract_youtube_video_id($url)
{
    if (empty($url)) return '';

    $patterns = [
        '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
        '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
        '/youtu\.be\/([a-zA-Z0-9_-]+)/',
        '/youtube\.com\/v\/([a-zA-Z0-9_-]+)/'
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
    }

    return '';
}
