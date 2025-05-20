<?php
get_header();
while (have_posts()) {
    the_post();

?>

    <div class="section-course">
        <div class="container page-section generic-content">
            <div class="full-width">
                <?php
                the_content() ?>
            </div>
        </div>
    </div>
<?php
}
get_footer();
?>