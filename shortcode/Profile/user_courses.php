<?php
function us_render_course_item($course_id, $user_id)
{
    $course = get_post($course_id);
    if (!$course) {
        return '';
    }

    $student = llms_get_student($user_id);
    $progress = $student->get_progress($course_id);
    $progress_percent = round($progress, 1);

    // Get course data
    $course_title = esc_html($course->post_title);
    $course_url = get_permalink($course_id);
    $course_excerpt = wp_trim_words($course->post_excerpt, 15, '...');

    // Get enrollment date
    $enrollment_date = $student->get_enrollment_date($course_id);
    $enrollment_formatted = $enrollment_date ? date('M Y', strtotime($enrollment_date)) : 'Unknown';

    // Get course status
    $status = $student->get_enrollment_status($course_id);
    $status_text = '';
    $status_class = '';

    switch ($status) {
        case 'enrolled':
            if ($progress_percent >= 100) {
                $status_text = 'Completed';
                $status_class = 'status-completed';
            } else {
                $status_text = 'In Progress';
                $status_class = 'status-progress';
            }
            break;
        case 'cancelled':
            $status_text = 'Cancelled';
            $status_class = 'status-cancelled';
            break;
        case 'expired':
            $status_text = 'Expired';
            $status_class = 'status-expired';
            break;
        default:
            $status_text = 'Enrolled';
            $status_class = 'status-enrolled';
    }

    // Get course thumbnail
    $thumbnail = get_the_post_thumbnail($course_id, 'medium');
    if (!$thumbnail) {
        $thumbnail = '<div class="course-placeholder-img">
            <svg width="80" height="60" viewBox="0 0 80 60" fill="none">
                <rect width="80" height="60" rx="8" fill="#f0f0f0"/>
                <path d="M25 20h30v4H25v-4z" fill="#ddd"/>
                <path d="M25 28h20v2H25v-2z" fill="#ddd"/>
                <path d="M25 32h25v2H25v-2z" fill="#ddd"/>
                <circle cx="40" cy="40" r="8" fill="#ddd"/>
                <path d="M37 37l6 3-6 3v-6z" fill="white"/>
            </svg>
        </div>';
    }

    // Progress bar color based on percentage
    $progress_color = '';
    if ($progress_percent < 25) {
        $progress_color = '#ff4757'; // Red
    } elseif ($progress_percent < 50) {
        $progress_color = '#ff9f43'; // Orange
    } elseif ($progress_percent < 75) {
        $progress_color = '#ffa502'; // Yellow
    } else {
        $progress_color = '#2ed573'; // Green
    }

    return "
        <div class='course-item'>
            <div class='course-content'>
                <h4 class='course-title'>
                    <a href='{$course_url}'>{$course_title}</a>
                </h4>
                <div class='course-meta'>
                    <span class='enrollment-date'>📅 Enrolled: {$enrollment_formatted}</span>
                </div>
                <div class='course-progress'>
                    <div class='progress-header'>
                        <span class='progress-label'>Progress</span>
                        <span class='progress-percentage'>{$progress_percent}%</span>
                    </div>
                    <div class='progress-bar'>
                        <div class='progress-fill' style='width: {$progress_percent}%; background-color: {$progress_color};'></div>
                    </div>
                </div>
              
            </div>
        </div>
    ";
}

function user_courses_shortcode($atts)
{
    $atts = shortcode_atts([
        'limit' => 5,
        'show_completed' => 'true',
        'show_in_progress' => 'true'
    ], $atts);

    $author_login = us_get_profile_username();

    if (is_admin() || empty($author_login)) {
        return '';
    }

    $user_obj = us_get_user_by_username($author_login);
    if (!$user_obj) {
        return '<p>Pengguna tidak ditemukan: ' . esc_html($author_login) . '</p>';
    }

    $profile_user_id = $user_obj->ID;
    $current_user = wp_get_current_user();
    $is_owner = is_user_logged_in() && $current_user->ID === $profile_user_id;

    // Check if LifterLMS is active
    if (!function_exists('llms_get_student')) {
        return '<p>LifterLMS plugin tidak aktif.</p>';
    }

    $student = llms_get_student($profile_user_id);
    if (!$student) {
        return '<p>Data student tidak ditemukan.</p>';
    }

    // Get enrolled courses
    $enrolled_courses = $student->get_courses([
        'limit' => $atts['limit'],
        'status' => 'enrolled'
    ]);

    if (empty($enrolled_courses['results'])) {
        return "
            <div class='user-courses-wrapper'>
                <div class='no-courses-message'>
                    <div class='no-courses-icon'>📚</div>
                    <p>Belum ada course yang dienroll" . ($is_owner ? "." : " oleh pengguna ini.") . "</p>
                    " . ($is_owner ? "<a href='" . site_url('/courses') . "' class='browse-courses-btn'>🔍 Browse Courses</a>" : "") . "
                </div>
            </div>
        ";
    }

    // Filter courses based on settings
    $filtered_courses = [];
    foreach ($enrolled_courses['results'] as $course_id) {
        $progress = $student->get_progress($course_id);

        if ($progress >= 100 && $atts['show_completed'] === 'false') {
            continue;
        }

        if ($progress < 100 && $atts['show_in_progress'] === 'false') {
            continue;
        }

        $filtered_courses[] = $course_id;
    }

    if (empty($filtered_courses)) {
        return "
            <div class='user-courses-wrapper'>
                <div class='no-courses-message'>
                    <p>Tidak ada course yang sesuai dengan filter.</p>
                </div>
            </div>
        ";
    }

    // Calculate overall stats
    $total_courses = count($filtered_courses);
    $completed_courses = 0;
    $total_progress = 0;

    foreach ($filtered_courses as $course_id) {
        $progress = $student->get_progress($course_id);
        $total_progress += $progress;
        if ($progress >= 100) {
            $completed_courses++;
        }
    }

    $average_progress = $total_courses > 0 ? round($total_progress / $total_courses, 1) : 0;

    ob_start();
?>
    <div class="user-courses-wrapper">
        <div class="courses-header">
            <h3>📚 User Courses</h3>
            <div class="courses-stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo $total_courses; ?></span>
                    <span class="stat-label">Total</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $completed_courses; ?></span>
                    <span class="stat-label">Completed</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $average_progress; ?>%</span>
                    <span class="stat-label">Avg</span>
                </div>
            </div>
        </div>

        <div class="courses-list">
            <?php foreach ($filtered_courses as $course_id): ?>
                <?php echo us_render_course_item($course_id, $profile_user_id); ?>
            <?php endforeach; ?>
        </div>
    </div>

    <style>
        .user-courses-wrapper {
            background: #fff;
            border: 1px solid #e1e5e9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .courses-header {
            margin-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }

        .courses-header h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 18px;
            font-weight: 600;
        }

        .courses-stats {
            display: flex;
            gap: 15px;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 8px;
            width: calc(100%/3);
        }

        .stat-number {
            font-size: 16px;
            font-weight: 700;
            color: #2c3e50;
        }

        .stat-label {
            font-size: 11px;
            color: #666;
            margin-top: 2px;
        }

        .courses-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .course-item {
            display: flex;
            gap: 12px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #e9ecef;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .course-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .course-placeholder-img {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .status-completed {
            background: #2ed573;
            color: white;
        }

        .status-progress {
            background: #ffa502;
            color: white;
        }

        .status-cancelled {
            background: #ff4757;
            color: white;
        }

        .status-expired {
            background: #747d8c;
            color: white;
        }

        .course-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .course-title {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.3;
        }

        .course-title a {
            text-decoration: none;
            color: #2c3e50;
        }

        .course-title a:hover {
            color: #3498db;
        }

        .course-meta {
            font-size: 11px;
            color: #666;
        }

        .course-progress {
            margin: 4px 0;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .progress-label {
            font-size: 11px;
            color: #666;
            font-weight: 500;
        }

        .progress-percentage {
            font-size: 11px;
            font-weight: 600;
            color: #2c3e50;
        }

        .progress-bar {
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s ease;
        }


        .no-courses-message {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }

        .no-courses-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .browse-courses-btn,
        .browse-more-btn {
            display: inline-block;
            padding: 8px 16px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 10px;
            transition: background 0.2s ease;
        }

        .browse-courses-btn:hover,
        .browse-more-btn:hover {
            background: #2980b9;
            color: white;
        }


        /* Responsive */
        @media (max-width: 768px) {
            .course-item {
                flex-direction: column;
                gap: 10px;
            }

            .courses-stats {
                justify-content: center;
            }
        }
    </style>

<?php
    return ob_get_clean();
}
add_shortcode('user_courses', 'user_courses_shortcode');
