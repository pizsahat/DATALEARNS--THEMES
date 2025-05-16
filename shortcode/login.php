<?php

add_shortcode('ultimatemember-datalearns-login', 'DatalearnsLogin');

function DatalearnsLogin($atts)
{
    $atts = shortcode_atts(
        array(
            'form_id' => '',
        ),
        $atts,
        'ultimatemember-datalearns-login'
    );
    $form_id = sanitize_text_field($atts['form_id']);

    ob_start();
?>

    <div class="login">
        <div class="image-login-container">
            <div class="image-login">
                <img src="<?php echo esc_url(get_theme_file_uri('/images/img-login-dl247.png')); ?>" class="image" alt="Login Image" />
            </div>
        </div>
        <div class="login-form-wrapper">
            <h5>Login</h5>
            <?php echo do_shortcode('[ultimatemember form_id="' . esc_attr($form_id) . '"]'); ?>
        </div>
    </div>

<?php
    return ob_get_clean();
}
