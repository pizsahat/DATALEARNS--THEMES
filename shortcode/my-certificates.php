<?php
add_shortcode('datalearns-my-certificates', 'MyCertificates');

function MyCertificates($atts)
{
    $atts = shortcode_atts([
        'layout' => '1',
        'owner'  => '',
    ], $atts, 'datalearns-my-certificates');

    ob_start();


    $target_user_id = null;


    if ($atts['owner'] === 'self') {
        $target_user_id = get_current_user_id();
    }


    if (!$target_user_id) {
        global $wp;
        $current_url_path = $wp->request;
        $path_parts = explode('/', $current_url_path);


        $username = end($path_parts);
        $user = get_user_by('login', $username);

        if ($user) {
            $target_user_id = $user->ID;
        }
    }


    if (!$target_user_id) {
        echo '<p>User not found.</p>';
        return ob_get_clean();
    }


    $certificates = get_posts([
        'post_type'   => 'certificate',
        'numberposts' => -1,
        'meta_key'    => 'certificate_user',
        'meta_value'  => $target_user_id,
        'order'       => 'ASC'
    ]);

    if (empty($certificates)) {
        echo '<p>This user has not received any certificates yet.</p>';
        return ob_get_clean();
    }


    if ($atts['layout'] == '2') { ?>
        <div class="certificate-container linkedin-style">
            <?php foreach ($certificates as $certificate) :
                $certificate_image = get_field("certificate_image", $certificate->ID);
                $course_id = get_post_meta($certificate->ID, 'course_name', true);
                $course_name = 'Unknown Course';

                if (is_array($course_id)) {
                    $course_id = $course_id[0] ?? '';
                    $course_name = $course_id ? html_entity_decode(get_the_title($course_id)) : 'Unknown Course';
                } elseif ($course_id) {
                    $course_name = html_entity_decode(get_the_title($course_id));
                }

                $certificate_type = get_post_meta($certificate->ID, 'certificate_type', true) ?: 'Certificate';
                $issued_on = get_post_meta($certificate->ID, 'issued_on', true) ?: get_the_date('M Y', $certificate->ID);
                $certificate_id = get_post_meta($certificate->ID, 'certificate_id', true) ?: 'ID-' . $certificate->ID;
                $certificate_url = get_permalink($certificate->ID);
            ?>
                <div class="certificate-item linkedin-item">
                    <div class="certificate-image linkedin-image">
                        <?php if ($certificate_image) :
                            $certificate_image_url = is_array($certificate_image) ? $certificate_image['url'] : $certificate_image;
                        ?>
                            <img src="<?php echo esc_url($certificate_image_url); ?>" alt="<?php echo esc_attr(get_the_title($certificate->ID)); ?>">
                        <?php else : ?>
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/images/default-thumbnail.jpg'); ?>" alt="Default Thumbnail">
                        <?php endif; ?>
                    </div>
                    <div class="certificate-content linkedin-content">
                        <span class="certificate-type-badge"><?php echo esc_html($certificate_type); ?></span>
                        <h4 class="certificate-title linkedin-title">
                            <a href="<?php echo esc_url($certificate_url); ?>"><?php echo esc_html(get_the_title($certificate->ID)); ?></a>
                        </h4>
                        <p class="certificate-course"><?php echo esc_html($course_name); ?></p>
                        <p class="certificate-date linkedin-date">
                            Issued <?php echo esc_html($issued_on); ?>
                            <?php if ($certificate_id) : ?>
                                · Credential ID <?php echo esc_html($certificate_id); ?>
                            <?php endif; ?>
                        </p>
                        <div class="certificate-actions">
                            <a href="<?php echo esc_url($certificate_url); ?>" class="certificate-link">View details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div><?php
            } else { ?>
        <div class="certificate-grid">
            <?php foreach ($certificates as $certificate) :
                    $certificate_image = get_field("certificate_image", $certificate->ID);
                    $course_id = get_post_meta($certificate->ID, 'course_name', true);
                    $course_name = 'Unknown Course';

                    if (is_array($course_id)) {
                        $course_id = $course_id[0] ?? '';
                        $course_name = $course_id ? html_entity_decode(get_the_title($course_id)) : 'Unknown Course';
                    } elseif ($course_id) {
                        $course_name = html_entity_decode(get_the_title($course_id));
                    }

                    $certificate_type = get_post_meta($certificate->ID, 'certificate_type', true) ?: 'Certificate';
                    $certificate_url = get_permalink($certificate->ID);
                    $certificate_file_url = get_post_meta($certificate->ID, 'certificate_url', true);
            ?>
                <div class="certificate-card">
                    <a href="<?php echo esc_url($certificate_url); ?>">
                        <div class="certificate-image">
                            <?php if ($certificate_image) :
                                $certificate_image_url = is_array($certificate_image) ? $certificate_image['url'] : $certificate_image;
                            ?>
                                <img src="<?php echo esc_url($certificate_image_url); ?>" alt="<?php echo esc_attr(get_the_title($certificate->ID)); ?>">
                            <?php else : ?>
                                <img src="<?php echo esc_url(get_template_directory_uri() . '/images/default-thumbnail.jpg'); ?>" alt="Default Thumbnail">
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="certificate-content">
                        <span class="certificate-type-badge"><?php echo esc_html($certificate_type); ?></span>
                        <h3 class="certificate-title">
                            <a href="<?php echo esc_url($certificate_url); ?>"><?php echo esc_html(get_the_title($certificate->ID)); ?></a>
                        </h3>
                        <p class="certificate-course"><?php echo esc_html($course_name); ?></p>
                        <p class="certificate-date">
                            Issued on: <?php echo esc_html(get_the_date('F j, Y', $certificate->ID)); ?>
                        </p>
                        <?php if ($certificate_file_url) : ?>
                            <a href="<?php echo esc_url($certificate_file_url); ?>" class="certificate-link" target="_blank">Download Certificate</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div><?php
            }

            return ob_get_clean();
        }
