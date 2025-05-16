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
<div class="container container--narrow page-section">
  <?Php
  while (have_posts()) {
    the_post(); ?>
    <div class="post-item">
      <h2 class="headline headline--medium headline--post-title"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h2>
      <div class="metabox">
        <p>Posted by <?php the_author_posts_link() ?> on <?php the_time('F j, Y') ?> in <?php echo get_the_category_list(', ') ?></p>
      </div>
      <div class="generic-content">
        <?php the_excerpt() ?>
        <p><a class="btn btn--blue" href="<?php the_permalink(); ?>">Continue reading &raquo;</a></p>
      </div>
    </div>
  <?php
  }
  echo paginate_links();
  ?>
</div>

<?php get_footer();
?>