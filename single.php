<?php
get_header();
while (have_posts()) {
  the_post(); ?>

  <div class="site-container" style="padding: 30px 0;">
    <div class="generic-content">
      <?php the_content() ?>
    </div>
  </div>
<?php
}
get_footer();
?>