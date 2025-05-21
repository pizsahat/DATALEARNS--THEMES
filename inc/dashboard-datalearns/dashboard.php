<?php
$current_user = wp_get_current_user();
$username = $current_user->display_name;
$header_font = get_option('datalearns_custom_header_font_family', '');
$body_font = get_option('datalearns_custom_body_font_family', '');

function get_lesson_completion_engagement()
{
    global $wpdb;

    $current_month_start = date('Y-m-01');
    $current_month_end = date('Y-m-t 23:59:59');

    $current_month_completions = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) 
            FROM {$wpdb->prefix}lifterlms_user_postmeta 
            WHERE meta_key = '_is_complete' 
            AND meta_value = 'yes' 
            AND updated_date BETWEEN %s AND %s",
            $current_month_start,
            $current_month_end
        )
    );

    $prev_month_start = date('Y-m-01', strtotime('-1 month'));
    $prev_month_end = date('Y-m-t 23:59:59', strtotime('-1 month'));

    $prev_month_completions = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) 
            FROM {$wpdb->prefix}lifterlms_user_postmeta 
            WHERE meta_key = '_is_complete' 
            AND meta_value = 'yes' 
            AND updated_date BETWEEN %s AND %s",
            $prev_month_start,
            $prev_month_end
        )
    );

    if ($prev_month_completions == 0) {
        $growth_percent = $current_month_completions > 0 ? 100 : 0;
    } else {
        $growth_percent = (($current_month_completions - $prev_month_completions) / $prev_month_completions) * 100;
        $growth_percent = round($growth_percent, 1);
    }

    return array(
        'count' => $current_month_completions,
        'change' => $growth_percent,
        'period' => 'from last month'
    );
}

function get_lifterlms_dashboard_stats()
{
    $student_query = new WP_User_Query([
        'meta_query' => [
            [
                'key' => '_um_last_login',
                'value' => date('Y-m-d H:i:s', strtotime('-7 days')),
                'compare' => '>=',
                'type' => 'DATETIME',
            ]
        ]
    ]);
    $active_users_count = $student_query->get_total();

    $prev_week_query = new WP_User_Query([
        'meta_query' => [
            [
                'key' => '_um_last_login',
                'value' => [
                    date('Y-m-d H:i:s', strtotime('-14 days')),
                    date('Y-m-d H:i:s', strtotime('-7 days')),
                ],
                'compare' => 'BETWEEN',
                'type' => 'DATETIME',
            ]
        ]
    ]);
    $active_users_prev = $prev_week_query->get_total();



    if ($active_users_prev == 0) {
        $active_users_change = ($active_users_count > 0) ? 100 : 0;
    } else {
        $active_users_change = round((($active_users_count - $active_users_prev) / $active_users_prev) * 100, 1);
    }


    $courses_count = wp_count_posts('course')->publish ?? 0;

    $new_courses = new WP_Query([
        'post_type' => 'course',
        'post_status' => 'publish',
        'date_query' => [
            [
                'after' => date('Y-m-01') 
            ]
        ],
        'fields' => 'ids'
    ]);
    $new_courses_count = $new_courses->found_posts;

    $total_enrollments = 0;
    $total_completions = 0;

    if (function_exists('llms_get_enrolled_students')) {
        $courses = get_posts([
            'post_type' => 'course',
            'post_status' => 'publish',
            'numberposts' => -1,
            'fields' => 'ids'
        ]);

        foreach ($courses as $course_id) {
            if (function_exists('llms_get_enrolled_students')) {
                $enrolled_students = llms_get_enrolled_students($course_id);
                $course_enrollments = is_array($enrolled_students) ? count($enrolled_students) : 0;
                $total_enrollments += $course_enrollments;

                if ($course_enrollments > 0 && function_exists('llms')) {
                    foreach ($enrolled_students as $student_id) {
                        $student = llms_get_student($student_id);
                        if ($student && $student->is_complete($course_id, 'course')) {
                            $total_completions++;
                        }
                    }
                }
            }
        }
    }

    $engagement_data = get_lesson_completion_engagement();


    try {
        $upload_dir = wp_upload_dir();
        $storage_used = get_folder_size($upload_dir['basedir']);
        $storage_used_gb = round($storage_used / 1073741824, 1);
    } catch (Exception $e) {
        $storage_used_gb = 0;
    }

    if ($storage_used_gb <= 0) {
        $storage_used_gb = get_option('manual_storage_size', 2); 
    }

    $storage_total = get_option('total_storage_gb', 16);
    $storage_percentage = ($storage_total > 0) ? round(($storage_used_gb / $storage_total) * 100) : 0;

    return array(
        'active_users' => array(
            'count' => $active_users_count,
            'change' => $active_users_change,
            'period' => 'from last week'
        ),
        'courses' => array(
            'count' => $courses_count,
            'new' => $new_courses_count,
            'period' => 'new this month'
        ),
        'engagement' => array(
            'count' => $engagement_data['count'],
            'change' => $engagement_data['change'],
            'period' => $engagement_data['period']
        ),
        'storage' => array(
            'percentage' => $storage_percentage,
            'used' => $storage_used_gb,
            'total' => $storage_total
        )
    );
}

function get_folder_size($folder)
{
    if (!is_dir($folder)) {
        return 0;
    }

    $size = 0;
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($files as $file) {
        try {
            $size += $file->getSize();
        } catch (Exception $e) {
            continue;
        }
    }
    return $size;
}

function get_recent_activities()
{
    global $wpdb;
    ob_start();

    $notifications = $wpdb->get_results(
        "SELECT n.*, p.post_title
        FROM wp_lifterlms_notifications n
        LEFT JOIN {$wpdb->posts} p ON n.post_id = p.ID
        ORDER BY n.created DESC 
        LIMIT 5"
    );

    if (empty($notifications)) {
        echo '<div class="text-muted">No recent activity</div>';
    } else {
        foreach ($notifications as $notification) {
            $user = get_userdata($notification->user_id);
            $username = $user ? $user->display_name : 'Unknown User';
            $avatar_url = $user ? get_avatar_url($user->ID) : 'https://ui-avatars.com/api/?name=Unknown+User&background=random';

            $post_title = $notification->post_title ?: 'Untitled Content';

            $activity_message = match ($notification->trigger_id) {
                'enrollment' => sprintf('Enrolled in course "%s"', esc_html($post_title)),
                'lesson_complete' => sprintf('Completed lesson "%s"', esc_html($post_title)),
                'section_complete' => sprintf('Completed section "%s"', esc_html($post_title)),
                'quiz_passed' => sprintf('Passed quiz in "%s"', esc_html($post_title)),
                'quiz_failed' => sprintf('Needs retake for "%s" quiz', esc_html($post_title)),
                'course_complete' => sprintf('Completed course "%s"! 🎉', esc_html($post_title)),
                default => sprintf('Performed action on "%s"', esc_html($post_title))
            };

            $time_diff = human_time_diff(
                strtotime($notification->created),
                current_time('timestamp')
            ) . ' ago';

            $activity_icon = match ($notification->trigger_id) {
                'enrollment' => 'fas fa-door-open',
                'lesson_complete' => 'fas fa-book',
                'section_complete' => 'fas fa-layer-group',
                'quiz_passed' => 'fas fa-check-circle',
                'quiz_failed' => 'fas fa-exclamation-circle',
                default => 'fas fa-flag'
            };
?>
            <div class="d-flex align-items-start mb-3">
                <img src="<?php echo esc_url($avatar_url); ?>"
                    width="36"
                    height="36"
                    class="rounded-circle me-3"
                    alt="<?php echo esc_attr($username); ?>">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-1">
                        <i class="<?php echo $activity_icon; ?> text-primary me-2"></i>
                        <h6 class="mb-0"><?php echo esc_html($username); ?></h6>
                    </div>
                    <small class="text-muted d-block"><?php echo $activity_message; ?></small>
                    <small class="text-muted">
                        <i class="far fa-clock me-1"></i>
                        <?php echo $time_diff; ?>
                        <?php if ($notification->status === 'unread'): ?>
                            <span class="badge bg-info ms-2">New</span>
                        <?php endif; ?>
                    </small>
                </div>
            </div>
<?php
        }
    }

    return ob_get_clean();
}

$stats = get_lifterlms_dashboard_stats();
?>

<nav class="navbar modern-navbar p-3 navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo site_url() ?>">
            <img src="<?php echo get_template_directory_uri(); ?>/images/DL247-logo_web.png" alt="Logo" class="img-fluid">
        </a>
        <div class="d-flex align-items-center">
            <div class="me-3 position-relative">
                <i class="fas fa-bell me-3 text-muted" id="notificationBell"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger pulse-animation" style="font-size: 0.6rem;">
                    3
                </span>
            </div>
            <div class="d-flex align-items-center">
                <div class="me-2 position-relative">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($username); ?>&background=4361ee&color=fff"
                        width="32"
                        height="32"
                        class="rounded-circle"
                        alt="User Avatar">
                    <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-white rounded-circle"></span>
                </div>
                <span class="fw-medium text-dark"><?php echo esc_html($username); ?></span>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid p-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5 fw-bold">Welcome Back, <?php echo esc_html($username); ?>! 👋</h1>
            <p class="lead text-muted">Ini yang terjadi dengan platform Anda hari ini</p>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stats-card h-100" style="background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);">
                <h6 class="text-uppercase">Active Users</h6>
                <h2 class="mb-0"><?php echo number_format($stats['active_users']['count']); ?></h2>
                <small class="d-block mb-3">
                    <?php
                    $change = $stats['active_users']['change'];
                    echo ($change >= 0 ? '+' : '') . $change . '% ' . $stats['active_users']['period'];
                    ?>
                </small>
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card h-100" style="background: linear-gradient(135deg, #4895ef 0%, #4361ee 100%);">
                <h6 class="text-uppercase">Courses</h6>
                <h2 class="mb-0"><?php echo number_format($stats['courses']['count']); ?></h2>
                <small class="d-block mb-3">
                    +<?php echo $stats['courses']['new']; ?> <?php echo $stats['courses']['period']; ?>
                </small>
                <i class="fas fa-book-open"></i>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card h-100" style="background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%);">
                <h6 class="text-uppercase">Lesson Completion</h6>
                <h2 class="mb-0"><?php echo $stats['engagement']['count']; ?></h2>
                <small class="d-block mb-3">
                    <?php
                    $change = $stats['engagement']['change'];
                    echo ($change >= 0 ? '+' : '') . $change . '% ' . $stats['engagement']['period'];
                    ?>
                </small>
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card h-100" style="background: linear-gradient(135deg, #3f37c9 0%, #3a0ca3 100%);">
                <h6 class="text-uppercase">Storage</h6>
                <h2 class="mb-0"><?php echo $stats['storage']['percentage']; ?>%</h2>
                <small class="d-block mb-3">
                    <?php echo $stats['storage']['used']; ?>GB of <?php echo $stats['storage']['total']; ?>GB used
                </small>
                <i class="fas fa-database"></i>
            </div>
        </div>
    </div>

    <div class="dashboard-card">
        <ul class="nav nav-tabs px-4 pt-3" id="mainTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="greetings-tab" data-bs-toggle="tab" data-bs-target="#greetings">
                    <i class="fas fa-home me-2"></i>Dashboard
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings">
                    <i class="fas fa-cogs me-2"></i>Website Settings
                </button>
            </li>
            <!-- <li class="nav-item" role="presentation">
                <button class="nav-link" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics">
                    <i class="fas fa-chart-pie me-2"></i>Analytics
                </button>
            </li> -->
        </ul>

        <div class="tab-content p-4 container-fluid">
            <div class="tab-pane fade show active" id="greetings">
                <div class="row">
                    <div class="col-md-8">
                        <!-- Feature Highlight Card -->
                        <div class="bg-white p-4 rounded-3 shadow-sm border-0 mb-4 feature-highlight">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                            <i class="fas fa-rocket text-primary fs-2"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h4 class="mb-1">New Dashboard Experience</h4>
                                        <p class="text-muted mb-3">Dasbor Datalearns hadir untuk mempermudah pengguna mengelola LMS.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Updates Section -->
                        <div class="bg-white p-4 rounded-3 shadow-sm border-0 mb-4">
                            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0"><i class="fas fa-bullhorn text-primary me-2"></i>Recent Updates</h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="updatesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-filter me-1"></i> Filter
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="updatesDropdown">
                                        <li><a class="dropdown-item" href="#">All Updates</a></li>
                                        <li><a class="dropdown-item" href="#">Features</a></li>
                                        <li><a class="dropdown-item" href="#">Announcements</a></li>
                                        <li><a class="dropdown-item" href="#">Maintenance</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="update-list">
                                    <div class="update-item d-flex align-items-start mb-4 interactive-card p-3 rounded-3">
                                        <div class="flex-shrink-0">
                                            <span class="badge bg-success me-2 p-2"><i class="fas fa-plug fs-5"></i></span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h5 class="mb-0">New Student Management Plugin</h5>
                                                <span class="badge bg-danger">New!</span>
                                            </div>
                                            <p class="text-muted mb-2">Student management system telah diubah menjadi plugin dengan berbagai peningkatan:</p>
                                            <ul class="list-unstyled mb-2">
                                                <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>Menjadikan Plugin</li>
                                                <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>Update UI/UX yang lebih modern</li>
                                            </ul>
                                            <div class="d-flex align-items-center mt-2">
                                                <small class="text-muted me-3"><i class="fas fa-calendar-alt me-1"></i> 2 days ago</small>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="update-item d-flex align-items-start mb-4 interactive-card p-3 rounded-3">
                                        <div class="flex-shrink-0">
                                            <span class="badge bg-info me-2 p-2"><i class="fas fa-film fs-5"></i></span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h5 class="mb-0">Enhanced Reels Experience</h5>
                                                <span class="badge bg-danger">New!</span>
                                            </div>
                                            <p class="text-muted mb-2">Peningkatan fitur reels dengan berbagai kemampuan baru:</p>
                                            <ul class="list-unstyled mb-2">
                                                <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>Infinite loop scroll</li>
                                                <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>Reels analytics dashboard</li>
                                                <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>Add Description Reels</li>
                                            </ul>
                                            <div class="d-flex align-items-center mt-2">
                                                <small class="text-muted me-3"><i class="fas fa-calendar-alt me-1"></i> 1 week ago</small>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="update-item d-flex align-items-start interactive-card p-3 rounded-3">
                                        <div class="flex-shrink-0">
                                            <span class="badge bg-warning me-2 p-2"><i class="fas fa-robot fs-5"></i></span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h5 class="mb-0">AI Learning Assistant</h5>
                                                <span class="badge bg-danger">Beta</span>
                                            </div>
                                            <p class="text-muted mb-2">Chatbot baru dengan kemampuan AI canggih untuk membantu proses belajar:</p>
                                            <ul class="list-unstyled mb-2">
                                                <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>Dikembangkan dengan flowise AI dengan model Open Source</li>
                                                <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>Ditraining menggunakan PDF</li>
                                                <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>24/7 learning support</li>
                                            </ul>
                                            <div class="d-flex align-items-center mt-2">
                                                <small class="text-muted me-3"><i class="fas fa-calendar-alt me-1"></i> 2 weeks ago</small>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Quick Actions Card -->
                        <div class="card border-0 mb-4">
                            <div class="card-header bg-white border-0">
                                <h5 class="mb-0"><i class="fas fa-bolt text-warning me-2"></i>Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <?php
                                        $is_editable = current_user_can('edit_courses');
                                        $card_content = '
                                            <div class="p-3 text-center rounded-3 interactive-card bg-primary bg-opacity-10">
                                                <div class="bg-primary bg-opacity-10 p-2 rounded-circle d-inline-block mb-2">
                                                    <i class="fas fa-plus text-primary fs-4"></i>
                                                </div>
                                                <h6 class="mb-0">New <br> Course</h6>
                                            </div>
                                        ';
                                        echo $is_editable
                                            ? '<a href="' . admin_url('post-new.php?post_type=course') . '" target="_blank" class="text-decoration-none">' . $card_content . '</a>'
                                            : $card_content;
                                        ?>
                                    </div>

                                    <div class="col-6">
                                        <?php
                                        $is_editable = current_user_can('edit_courses');
                                        $card_content = '
                                            <div class="p-3 text-center rounded-3 interactive-card bg-success bg-opacity-10">
                                                <div class="bg-success bg-opacity-10 p-2 rounded-circle d-inline-block mb-2">
                                                    <i class="fas fa-users text-success fs-4"></i>
                                                </div>
                                                <h6 class="mb-0">Manage Student</h6>
                                            </div>
                                        ';
                                        echo $is_editable
                                            ? '<a href="' . admin_url('admin.php?page=student-management') . '" target="_blank" class="text-decoration-none">' . $card_content . '</a>'
                                            : $card_content;
                                        ?>
                                    </div>
                                    <div class="col-6">
                                        <?php
                                        $can_manage_reels = current_user_can('edit_posts'); // Ganti dengan capability yang sesuai
                                        $analytics_card = '
        <div class="p-3 text-center rounded-3 interactive-card bg-info bg-opacity-10">
            <div class="bg-info bg-opacity-10 p-2 rounded-circle d-inline-block mb-2">
                <i class="fas fa-chart-line text-info fs-4"></i>
            </div>
            <h6 class="mb-0">Reels Analytics</h6>
        </div>
    ';
                                        echo $can_manage_reels
                                            ? '<a href="' . admin_url('edit.php?post_type=reels&page=reels-analytics') . '" target="_blank" class="text-decoration-none">' . $analytics_card . '</a>'
                                            : $analytics_card;
                                        ?>
                                    </div>

                                    <div class="col-6">
                                        <?php
                                        $new_reels_card = '
        <div class="p-3 text-center rounded-3 interactive-card bg-warning bg-opacity-10">
            <div class="bg-warning bg-opacity-10 p-2 rounded-circle d-inline-block mb-2">
                <i class="fas fa-plus text-warning fs-4"></i>
            </div>
            <h6 class="mb-0">New <br>Reels</h6>
        </div>
    ';
                                        echo $can_manage_reels
                                            ? '<a href="' . admin_url('post-new.php?post_type=reels') . '" target="_blank" class="text-decoration-none">' . $new_reels_card . '</a>'
                                            : $new_reels_card;
                                        ?>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="card border-0">
                            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-history text-info me-2"></i>Recent Activity</h5>
                                <!-- <button class="btn btn-sm btn-outline-secondary refresh-activities">
                                    <i class="fas fa-sync-alt"></i>
                                </button> -->
                            </div>
                            <div class="card-body activity-content">
                                <?php echo get_recent_activities(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="tab-pane fade" id="settings">
                <div class="row">
                    <div class="col-md-3">
                        <div class="nav flex-column nav-pills nav-pills-modern" id="settingsSubTab">
                            <button class="nav-link active" id="typography-tab" data-bs-toggle="pill" data-bs-target="#typography">
                                <i class="fas fa-font me-2"></i>Typography
                            </button>
                            <button class="nav-link" id="color-tab" data-bs-toggle="pill" data-bs-target="#color">
                                <i class="fas fa-palette me-2"></i>Color
                            </button>
                            <button class="nav-link" id="header-tab" data-bs-toggle="pill" data-bs-target="#header">
                                <i class="fas fa-header me-2"></i>Nav Header
                            </button>
                            <button class="nav-link" id="footer-tab" data-bs-toggle="pill" data-bs-target="#footer">
                                <i class="fas fa-handshake-o me-2"></i>Footer
                            </button>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="tab-content p-3">
                            <?php include_once get_template_directory() . '/inc/dashboard-datalearns/components/tab-typography.php'; ?>
                            <?php include_once get_template_directory() . '/inc/dashboard-datalearns/components/tab-color.php'; ?>
                            <?php include_once get_template_directory() . '/inc/dashboard-datalearns/components/tab-header.php'; ?>
                            <?php include_once get_template_directory() . '/inc/dashboard-datalearns/components/tab-footer.php'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>