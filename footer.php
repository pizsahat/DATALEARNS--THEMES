<?php
$show_footer = get_field('show_footer');
if (($show_footer || $show_footer === null)) :

  $selected_template_id = get_theme_mod('selected_footer_template_part');

  if (!empty($selected_template_id)) {
    $template_part = get_post($selected_template_id);

    if ($template_part && $template_part->post_type === 'template_part') {
      echo '<footer>';

      if (function_exists('do_blocks') && has_blocks($template_part->post_content)) {
        echo do_blocks($template_part->post_content);
      } else {
        echo wpautop($template_part->post_content);
      }

      echo '</footer>';
    } else {
      get_template_part('template-parts/footer');
    }
  } else {
    get_template_part('template-parts/footer');
  }
endif;

wp_footer(); ?>
</body>

</html>