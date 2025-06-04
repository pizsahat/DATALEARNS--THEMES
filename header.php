<!DOCTYPE html>
<html <?php language_attributes() ?>>

<head>
  <?php wp_head(); ?>
  <meta charset="<?php bloginfo('charset') ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="<?php bloginfo('description'); ?>">
</head>

<body <?php body_class() ?>>
  <?php
  $show_header = get_field('show_header');
  if (($show_header || $show_header === null)) {
  ?> <header class="site-header <?php echo get_theme_mod('enable_fixed_header') ? 'fixed-header' : ''; ?>">
      <div class="container container--header">
        <h1 class="school-logo-text float-left">
          <a class="site-footer__link" href="<?php echo site_url() ?>">
            <?php
            $logo_id = get_theme_mod('website_logo', '');
            $logo_width = get_theme_mod('logo_width', 180);
            $logo_width_mobile = get_theme_mod('logo_width_mobile', 120);

            if ($logo_id) {
              echo wp_get_attachment_image($logo_id, 'medium', false, array(
                'alt' => get_bloginfo('name'),
                'class' => 'site-logo',
                'data-desktop-width' => esc_attr($logo_width),
                'data-mobile-width' => esc_attr($logo_width_mobile)
              ));
            } else {
              echo '<img src="' . get_template_directory_uri() . '/images/DL247-logo_web.png" alt="' . get_bloginfo('name') . '" class="site-logo" data-desktop-width="' . esc_attr($logo_width) . '" data-mobile-width="' . esc_attr($logo_width_mobile) . '">';
            }
            ?> </a>
        </h1>
        <span class="js-search-trigger site-header__search-trigger"><i class="fa fa-search" aria-hidden="true"></i></span>
        <i class="site-header__menu-trigger fa fa-bars" aria-hidden="true"></i>
        <div class="site-header__menu group">
          <nav class="main-navigation">
            <ul>
              <?php
              $saved_menu = get_option('custom_header_nav', []);
              foreach ($saved_menu as $menu_item) {
                $url = $menu_item['source'];
                $label = $menu_item['label'];
                $type = $menu_item['type'];

                $current_class = '';

                if ($type === 'page') {
                  $page_id = url_to_postid($url);
                  if (is_page($page_id) || wp_get_post_parent_id(0) == $page_id) {
                    $current_class = 'class="current-menu-item"';
                  }
                } elseif ($type === 'post_type') {
                  $post_type = get_post_type_from_archive_link($url);
                  if (get_post_type() === $post_type || (is_post_type_archive($post_type))) {
                    $current_class = 'class="current-menu-item"';
                  }
                } elseif ($type === 'custom') {
                  if (($_SERVER['REQUEST_URI'] === parse_url($url, PHP_URL_PATH))) {
                    $current_class = 'class="current-menu-item"';
                  }
                }

                echo sprintf(
                  '<li %s><a href="%s">%s</a></li>',
                  $current_class,
                  esc_url($url),
                  esc_html($label)
                );
              }

              if (!is_user_logged_in()) {
                echo '<li><a href="' . esc_url(site_url('login')) . '">Login</a></li>';
                echo '<li><a href="' . esc_url(site_url('register')) . '">Register</a></li>';
              } else {
                echo '<li';
                if (is_page('profile') || wp_get_post_parent_id(get_the_ID()) == 12) {
                  echo ' class="current-menu-item"';
                }
                echo '><a href="' . esc_url(site_url('/profile')) . '">Profile</a></li>';

                echo '<li class="menu-item-has-children' . (is_page('my-dashboard') ? ' current-menu-item' : '') . '">';
                echo '<a href="' . esc_url(site_url('/my-dashboard')) . '">My Dashboard <i class="fa fa-angle-down"></i></a>';

                echo '<ul class="submenu">';

                echo '<li' . (is_page('my-courses') ? ' class="current-menu-item"' : '') . '>';
                echo '<a href="' . esc_url(site_url('/my-courses')) . '">My Courses</a></li>';

                echo '<li' . (is_page('my-certificates') ? ' class="current-menu-item"' : '') . '>';
                echo '<a href="' . esc_url(site_url('/my-certificates')) . '">My Certificates</a></li>';

                echo '</ul></li>';
              }
              ?>
            </ul>
          </nav>
          <div class="site-header__util">
            <?php
            if (is_user_logged_in()) { ?>
              <a href="<?php echo wp_logout_url(site_url('/login'))   ?>" class="btn btn--small float-left btn--with-photo">
                <span class="site-header__avatar"><?php echo get_avatar(get_current_user_id(), 60) ?></span>
                <span class="btn__text">Log Out</span>
              </a>
            <?php } else { ?>
              <!-- <a href="<?php echo esc_url(site_url('login'))   ?>" class="btn btn--small btn-login float-left push-right">Login</a>
              <a href="<?php echo esc_url(site_url('register'))   ?>" class="btn btn--small btn--dark-orange float-left">Sign Up</a> -->
            <?php }
            ?> <span class="search-trigger js-search-trigger"><i class="fa fa-search" aria-hidden="true"></i></span>
          </div>
        </div>
      </div>
    </header>
  <?php }
  ?>