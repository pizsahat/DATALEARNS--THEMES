<?php
get_header();
while (have_posts()) {
  the_post(); ?>

  <div class="container container--narrow page-section">
    <div class="generic-content">
      <?php the_content() ?>
    </div>
  </div>
<?php
}
get_footer();
?>