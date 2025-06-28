<?php

// ShortCode
require get_theme_file_path('/shortcode/list.php');
require get_theme_file_path('/shortcode/my-certificates.php');
require get_theme_file_path('/shortcode/my-dashboard.php');
require get_theme_file_path('/shortcode/course-shortcode.php');

// Routing
require get_theme_file_path('/inc/course-route.php');
require get_theme_file_path('/inc/search-route.php');
require get_theme_file_path('/inc/lesson-route.php');

// Custom Field
require get_theme_file_path('/inc/custom-field/setting-page.php');


function datalearns_custom_rest()
{
  register_rest_field('post', 'authorName', array(
    'get_callback' => function () {
      return get_the_author();
    }
  ));
}

add_action('rest_api_init', 'datalearns_custom_rest');

function datalearns_files()
{
  wp_enqueue_script('main-datalearns-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
  wp_enqueue_style('custom-google-font', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
  wp_enqueue_style('font-awesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
  // wp_enqueue_style('datalearns_main_styles', get_theme_file_uri('/build/style-index.css'));
  wp_enqueue_style('datalearns_main_styles', get_theme_file_uri('/css/style.css'));
  wp_enqueue_script('datalearns_main_script', get_theme_file_uri('/src/index.js'));
  wp_localize_script('main-datalearns-js', 'datalearnsData', array(
    'root_url' => get_site_url(),

  ));
}

add_action('wp_enqueue_scripts', 'datalearns_files');

function datalearns_features()
{
  register_nav_menu('headerMenuLocation', "Header Menu Location");
  register_nav_menu('footerLocationOne', "Footer Location One");
  register_nav_menu('footerLocationTwo', "Footer Location Two");
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_image_size('thumbnailLandscape', 400, 260, true);
  add_image_size('thumbnailPortrait', 480, 650, true);
}

add_action('after_setup_theme', 'datalearns_features');

function datalearns_adjust_queries($query)
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

add_action('pre_get_posts', 'datalearns_adjust_queries');

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

// CUSTOMIZE APPEARANCE
require get_theme_file_path('/inc/customize-appearance/customizer.php');

add_filter('register_post_type_args', function ($args, $post_type) {
  if ('course' === $post_type) {
    $args['template'] = array(
      array('core/post-title', array(
        'style' => array(
          'spacing' => array(
            'margin' => array(
              'bottom' => 'var:preset|spacing|40'
            )
          )
        )
      )),
      array('core/columns', array(), array(
        array(
          'core/column',
          array(
            'width' => '66.66%',
            'style' => array(
              'border' => array('radius' => '4px'),
              'spacing' => array(
                'padding' => array(
                  'top' => 'var:preset|spacing|40',
                  'right' => 'var:preset|spacing|40',
                  'bottom' => 'var:preset|spacing|40',
                  'left' => 'var:preset|spacing|40'
                )
              ),
              'color' => array(
                'background' => '#ffffff' // <-- warna putih hardcoded
              )
            )
          ),

          array(
            array('core/heading', array(
              'level' => 3,
              'content' => 'Course Overview'
            )),
            array('core/paragraph', array(
              'placeholder' => 'masukkan deskripsi course ini',
            )),

            array('core/heading', array(
              'level' => 3,
              'content' => 'Audience'
            )),
            array('core/list', array(), array(
              array('core/list-item', array('placeholder' => 'masukkan audience course ini')),
            )),

            array('core/heading', array(
              'level' => 3,
              'content' => 'Prerequisites'
            )),
            array('core/list', array(), array(
              array('core/list-item', array(
                'placeholder' => 'masukkan syarat / requirement untuk mengikuti course ini'
              )),
            )),

            array('core/heading', array(
              'level' => 3,
              'content' => 'What\'s Covered in This Course'
            )),
            array('core/paragraph', array(
              'content' => 'At the end of this training, participants will have the knowledge and skills to :'
            )),
            array('core/list', array(), array(
              array('core/list-item', array('placeholder' => 'masukkan cakupan dalam Kursus Ini')),
            )),

            array('core/heading', array(
              'level' => 3,
              'content' => 'Certification'
            )),
            array('core/paragraph', array(
              'placeholder' => 'jelaskan sertifikat yang didapat dari course ini'
            )),

            array('core/shortcode', array('text' => '[course-syllabus]')),
            array('core/separator'),
            array('core/shortcode', array(
              'text' => '[reels related_course_id= heading="Microlearning"]'
            ))
          )
        ),

        array('core/column', array(
          'width' => '33.33%',
          'style' => array(
            'spacing' => array(
              'padding' => array(
                'top' => 'var:preset|spacing|40',
                'right' => 'var:preset|spacing|40',
                'bottom' => 'var:preset|spacing|40',
                'left' => 'var:preset|spacing|40'
              )
            ),
            'border' => array('radius' => '4px'),
            'color' => array(
              'background' => '#ffffff' // <-- warna putih hardcoded
            )
          )
        ), array(
          array('core/post-featured-image', array(
            'style' => array(
              'border' => array('radius' => '4px')
            )
          )),
          array('core/shortcode', array('text' => '[course-info]'))
        ))
      ))
    );
  }
  return $args;
}, 20, 2);

function register_template_parts_post_type()
{
  $labels = array(
    'name'               => 'Template Parts',
    'singular_name'      => 'Template Part',
    'menu_name'          => 'Template Parts',
    'name_admin_bar'     => 'Template Part',
    'add_new'            => 'Tambah Baru',
    'add_new_item'       => 'Tambah Template Part',
    'new_item'           => 'Template Part Baru',
    'edit_item'          => 'Edit Template Part',
    'view_item'          => 'Lihat Template Part',
    'all_items'          => 'Semua Template Parts',
    'search_items'       => 'Cari Template Part',
    'not_found'          => 'Tidak ditemukan.',
    'not_found_in_trash' => 'Tidak ditemukan di tong sampah.'
  );

  $args = array(
    'labels'             => $labels,
    'public'             => true,
    'publicly_queryable' => false,
    'exclude_from_search' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => false,
    'capability_type'    => 'post',
    'has_archive'        => false,
    'hierarchical'       => false,
    'menu_position'      => 20,
    'menu_icon'          => 'dashicons-layout',
    'supports'           => array('title', 'editor'),
    'show_in_rest'       => true,
  );

  register_post_type('template_part', $args);
}
add_action('init', 'register_template_parts_post_type');

add_filter('wp_nav_menu_args', 'custom_conditional_menu_by_login');
function custom_conditional_menu_by_login($args)
{
  if ($args['theme_location'] === 'primary') {
    if (is_user_logged_in()) {
      $args['menu'] = 'menu-logged-in';
    } else {
      $args['menu'] = 'menu-guest';
    }
  }
  return $args;
}

// OPTIMAZION SEO
function custom_meta_description()
{
  if (is_singular()) {
    global $post;
    $excerpt = strip_tags($post->post_excerpt ?: wp_trim_words($post->post_content, 25));
    echo '<meta name="description" content="' . esc_attr($excerpt) . '">' . "\n";
  } else {
    echo '<meta name="description" content="Selamat datang di Datalearns247">' . "\n";
  }
}
add_action('wp_head', 'custom_meta_description');

// OPTIMAZION ACCESIBILITY
function remove_skip_link()
{
  ob_start(function ($html) {
    return preg_replace('/<a class="skip-link screen-reader-text scroll-ignore" href="#main">Skip to content<\/a>/', '', $html);
  });
}
add_action('wp_loaded', 'remove_skip_link');

// FUNCTION UNTUK MEMATIKAN AUTO EMBED YOUTUBE LESSON
add_action('wp', function () {
  if (is_singular('lesson')) {
    remove_action('lifterlms_single_lesson_before_summary', 'lifterlms_template_single_lesson_video', 20);
  }
});
