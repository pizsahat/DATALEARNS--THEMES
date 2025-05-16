<?php

add_shortcode('datalearns-my-certificates', 'MyCertificates');

function MyCertificates()
{
    ob_start();
    $current_user_id = get_current_user_id();

    $certificates = array();

    if ($current_user_id) {
        $certificates = get_posts(array(
            'post_type' => 'certificate',
            'numberposts' => -1,
            'meta_key' => 'certificate_user',
            'meta_value' => $current_user_id,
            'order' => 'ASC'
        ));
    }
?>

    <?php if (!empty($certificates)) : ?>
        <div class="certificate-grid">
            <?php foreach ($certificates as $certificate) :
                $certificate_file = get_field('certificate_file', $certificate->ID);
                $certificate_file_url = get_post_meta($certificate->ID, 'certificate_url', true);
                $certificate_image = get_field("certificate_image", $certificate->ID); ?>
                <div class="certificate-card">
                    <a href="<?php echo get_permalink($certificate->ID); ?>">
                        <div class="certificate-image">
                            <?php
                            if ($certificate_image) :
                                $certificate_image_url = is_array($certificate_image) ? $certificate_image['url'] : $certificate_image;
                            ?>
                                <img src="<?php echo esc_url($certificate_image_url); ?>" alt="<?php echo esc_attr(get_the_title($certificate->ID)); ?>">
                            <?php else : ?>
                                <img src="<?php echo esc_url(get_template_directory_uri() . '/images/default-thumbnail.jpg'); ?>" alt="Default Thumbnail">
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="certificate-content">
                        <p class="certificate-title"><?php echo esc_html(get_the_title($certificate->ID)); ?></p>
                        <p class="certificate-date">
                            Issued on: <?php echo esc_html(get_the_date('F j, Y', $certificate->ID)); ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>You have not received any certificates yet.</p>
        <?php endif; ?>
        </div>
    <?php
    return ob_get_clean();
}
