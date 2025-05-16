<?php

// ShortCode
require get_theme_file_path('/shortcode/homepage-content.php');
require get_theme_file_path('/shortcode/my-certificates.php');
require get_theme_file_path('/shortcode/my-courses.php');
require get_theme_file_path('/shortcode/my-dashboard.php');
require get_theme_file_path('/shortcode/login.php');
require get_theme_file_path('/shortcode/register.php');

// Routing
require get_theme_file_path('/inc/course-route.php');
require get_theme_file_path('/inc/search-route.php');
require get_theme_file_path('/inc/lesson-route.php');
require get_theme_file_path('/inc/course-shortcode.php');

function university_custom_rest()
{
  register_rest_field('post', 'authorName', array(
    'get_callback' => function () {
      return get_the_author();
    }
  ));
}

add_action('rest_api_init', 'university_custom_rest');

function pageBanner($args = NULL)
{

  if (!isset($args['title'])) {
    $args['title'] = get_the_title();
  }
  if (!isset($args['subtitle'])) {
    $args['subtitle'] = get_field('page_banner_subtittle');
  }
  if (!isset($args['photo'])) {
    if (get_field('page_banner_background_image') and !is_archive() and !is_home()) {
      $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
    } else {
      $args['photo'] = get_theme_file_uri('/images/datalearns_cover1.jpg');
    }
  }
?>
  <div class="page-banner">
    <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo'] ?>)"></div>
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title"><?php echo $args['title'] ?></h1>
      <div class="page-banner__intro">
        <p><?php echo $args['subtitle'] ?></p>
      </div>
    </div>
  </div>
<?php
}

function university_files()
{
  wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
  wp_enqueue_style('custom-google-font', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
  wp_enqueue_style('font-awesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
  wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
  wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
  wp_enqueue_style('custom-style', get_template_directory_uri() . '/build/style.css');
  wp_localize_script('main-university-js', 'universityData', array(
    'root_url' => get_site_url(),

  ));
  wp_enqueue_script('theme-script', get_template_directory_uri() . '/js/scripts.js', array(), '1.0', true);

  wp_localize_script('theme-script', 'themeVars', array(
    'scrollLogo' => get_template_directory_uri() . '/images/DL247-logo_web.png',
    'defaultLogo' => get_template_directory_uri() . '/images/datalearns247-logo-white-small-notagline-navbar.png'
  ));
}

add_action('wp_enqueue_scripts', 'university_files');

function university_features()
{
  register_nav_menu('headerMenuLocation', "Header Menu Location");
  register_nav_menu('footerLocationOne', "Footer Location One");
  register_nav_menu('footerLocationTwo', "Footer Location Two");
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_image_size('thumbnailLandscape', 400, 260, true);
  add_image_size('thumbnailPortrait', 480, 650, true);
  add_image_size('pageBanner', 1500, 350, true);
}

add_action('after_setup_theme', 'university_features');

function university_adjust_queries($query)
{
  if (!is_admin() and is_post_type_archive('program') and is_main_query()) {
    $query->set('orderby', 'title');
    $query->set('order', 'ASC');
    $query->set('post_per_page', -1);
  }
  if (!is_admin() and is_post_type_archive('event') and is_main_query()) {
    $today = date('Ymd');
    $query->set('meta_key', 'event_date');
    $query->set('orderby', 'meta_value_num');
    $query->set('order', 'ASC');
    $query->set('meta_query', array(
      array(
        'key' => 'event_date',
        'compare' => '>=',
        'value' => $today,
        'type' => 'numeric'
      )
    ));
  }
}

add_action('pre_get_posts', 'university_adjust_queries');

function redirectSubsToFrontend()
{
  $ourCurrentUser = wp_get_current_user();
  if (count($ourCurrentUser->roles) == 1 and $ourCurrentUser->roles[0] == 'subscriber') {
    wp_redirect(site_url('/'));
    exit;
  }
};

add_action('admin_init', 'redirectSubsToFrontend');

function noSubsAdminBar()
{
  $ourCurrentUser = wp_get_current_user();
  if (count($ourCurrentUser->roles) == 1 and $ourCurrentUser->roles[0] == 'subscriber') {
    show_admin_bar(false);
  }
};

add_action('wp_loaded', 'noSubsAdminBar');

// customize login screen
function ourHeaderUrl()
{
  return esc_url(site_url('/'));
}
add_filter('login_headerurl', 'ourHeaderUrl');

function track_last_accessed_lesson()
{
  if (is_singular('lesson') && is_user_logged_in()) {
    $current_user_id = get_current_user_id();
    $current_lesson_id = get_the_ID();

    update_user_meta($current_user_id, 'last_accessed_lesson', $current_lesson_id);
  }
}
add_action('template_redirect', 'track_last_accessed_lesson');


function mark_lesson_complete()
{
  if (isset($_POST['user_id']) && isset($_POST['lesson_id'])) {
    $user_id = intval($_POST['user_id']);
    $lesson_id = intval($_POST['lesson_id']);

    if (llms_is_user_enrolled($user_id, $lesson_id)) {
      $student = new LLMS_Student($user_id);
      $student->mark_complete($lesson_id, 'lesson', 'lesson_video');

      wp_send_json_success();
    }
  }

  wp_send_json_error();
}

add_action('wp_ajax_mark_lesson_complete', 'mark_lesson_complete');


function custom_admin_menu_datalearns()
{
  add_menu_page(
    'DataLearns',
    'DataLearns',
    'manage_options',
    'datalearns',
    'datalearns_page_callback',
    ' ',
    25
  );
}

function datalearns_page_callback()
{
  include_once get_template_directory() . '/inc/dashboard-datalearns/dashboard.php';
}

add_action('admin_menu', 'custom_admin_menu_datalearns');

include_once get_template_directory() . '/inc/dashboard-datalearns/functions.php';

// CUSTOMIZE APPEARANCE
require get_theme_file_path('/inc/customize-appearance/header.php');
