<?php
get_header();

while (have_posts()) {
    the_post();
    if (!get_field('show_header', get_the_ID())) {
        echo '<style>#masthead { display: none !important; }</style>';
    }
?>
    <div class="generic-content">
        <?php the_content(); ?>
    </div>

<?php

}

get_footer();
?>