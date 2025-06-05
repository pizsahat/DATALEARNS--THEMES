<?php
get_header();
while (have_posts()) {
    the_post();

?>

    <div class="section-course">
        <div class="site-container">
            <div class="generic-content">
                <?php
                the_content() ?>
            </div>
        </div>
    </div>
<?php
}
get_footer();
?>