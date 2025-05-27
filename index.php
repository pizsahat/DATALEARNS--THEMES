<?php
get_header();
?>

<div class="page-header">
  <div class="container">
    <h1 class="page-title">
      <?php
      if (have_posts()) {
        echo 'Our Blog';
      } else {
        echo 'Something went wrong';
      }
      ?>
    </h1>
    <?php if (!have_posts()) : ?>
      <p class="page-subtitle">It seems we can’t find what you’re looking for.</p>
    <?php endif; ?>
  </div>
</div>

<div class="container vertical-spacing">
  <?php
  if (have_posts()) :
    while (have_posts()) : the_post();
      get_template_part('template-parts/content', get_post_type());
    endwhile;

    echo '<div class="pagination">';
    echo paginate_links();
    echo '</div>';

  else :
    echo '<p>No posts found.</p>';
  endif;
  ?>
</div>

<?php get_footer(); ?>