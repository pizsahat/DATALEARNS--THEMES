<?php
get_header();
if (have_posts()) :
  while (have_posts()) : the_post();
    pageBanner(array(
      'title' => 'Our Blog',
      // 'subtitle' => 'It seems we can’t find what you’re looking for.'
    ));
  // get_template_part('template-parts/content', get_post_type());
  endwhile;
  echo paginate_links();
else :
  pageBanner(array(
    'title' => 'Something went wrong',
    'subtitle' => 'It seems we can’t find what you’re looking for.'
  ));
endif;
?>
<div class="container page-section">
  <?Php
  echo paginate_links();
  ?>
</div>

<?php get_footer();
?>