<?php

/**
 * Single Access Plan Button.
 *
 * @property LLMS_Access_Plan $plan Instance of the LLMS_Access_Plan.
 * @package LifterLMS/Templates
 * @since 3.23.0
 * @since 4.2.0 Added `llms_display_free_enroll_form` filter hook.
 * @version 4.2.0
 */
defined('ABSPATH') || exit;
?>

<?php
if (apply_filters('llms_display_free_enroll_form', get_current_user_id() && $plan->has_free_checkout() && $plan->is_available_to_user(), $plan)) :
	// Tampilkan form pendaftaran gratis jika memenuhi syarat
	llms_get_template('product/free-enroll-form.php', compact('plan'));
else :
	// Cek apakah user sudah login
	if (is_user_logged_in()) :
?>
		<a class="llms-button-action button" href="<?php echo esc_url($plan->get_checkout_url()); ?>">
			<?php echo esc_html($plan->get_enroll_text()); ?>
		</a>
	<?php else : ?>
		<!-- Jika belum login, arahkan ke halaman login -->
		<a class="llms-button-action button" href="<?php echo esc_url(site_url('/login?redirect_to=' . urlencode(get_permalink()))); ?>">
			<?php echo esc_html($plan->get_enroll_text()); ?>
		</a>
	<?php endif; ?>
<?php endif; ?>