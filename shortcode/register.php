<?php

add_shortcode('ultimatemember-datalearns-register', 'DatalearnsRegister');

function DatalearnsRegister($atts)
{
    $atts = shortcode_atts(
        array(
            'form_id' => '',
        ),
        $atts,
        'ultimatemember-datalearns-register'
    );
    $form_id = sanitize_text_field($atts['form_id']);

    ob_start();
?>

    <div class="login">
        <div class="login-form-wrapper">
            <h2>Register</h2>
            <div class="content">
                <?php echo do_shortcode('[ultimatemember form_id="' . esc_attr($form_id) . '"]'); ?>
            </div>
        </div>
        <div class="image-login-container">
            <div class="image-login">
                <img src="<?php echo get_theme_file_uri('/images/img-register-dl247.png') ?>" class="image" alt="" />
            </div>
        </div>
    </div>
<?php
    return ob_get_clean();
}
