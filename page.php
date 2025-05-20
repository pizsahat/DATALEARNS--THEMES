<?php
get_header();
while (have_posts()) {
    the_post();
    $content = get_the_content();
    $has_shortcode_login = has_shortcode($content, 'ultimatemember-datalearns-login');
    $has_shortcode_register = has_shortcode($content, 'ultimatemember-datalearns-register');

    if (!$has_shortcode_login && !$has_shortcode_register) {
?>
        <div class="container page-section">
        <?php
    }
        ?>
        <div class="generic-content">
            <?php the_content(); ?>
        </div>
        <?php
        if (!$has_shortcode_login && !$has_shortcode_register) {
        ?>
        </div>
<?php
        }
    }
    get_footer();
?>