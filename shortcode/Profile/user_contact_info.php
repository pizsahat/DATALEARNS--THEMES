<?php
function user_contact_info_shortcode()
{
    $author_login = us_get_profile_username();

    // Kalau sedang di admin atau tidak ada username
    if (is_admin() || empty($author_login)) {
        return '';
    }

    $user_obj = us_get_user_by_username($author_login);
    if (!$user_obj) {
        return '<p>Pengguna tidak ditemukan: ' . esc_html($author_login) . '</p>';
    }

    $profile_user_id = $user_obj->ID;

    // Ambil meta user
    $city = get_user_meta($profile_user_id, 'user_city', true);
    $org = get_user_meta($profile_user_id, 'user_organization', true);
    $birth_year = get_user_meta($profile_user_id, 'user_year_of_birth', true);

    // Buat tampilan contact info
    ob_start();
?>
    <div class="user-contact-info">


        <?php if ($city): ?>
            <div class="contact-row">
                <span class="contact-icon">📍</span>
                <span><strong>Kota:</strong> <?= esc_html($city) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($org): ?>
            <div class="contact-row">
                <span class="contact-icon">🏢</span>
                <span><strong>Organisasi:</strong> <?= esc_html($org) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($birth_year): ?>
            <div class="contact-row">
                <span class="contact-icon">🎂</span>
                <span><strong>Tahun Lahir:</strong> <?= esc_html($birth_year) ?></span>
            </div>
        <?php endif; ?>
    </div>
<?php
    return ob_get_clean();
}
add_shortcode('user_contact_info', 'user_contact_info_shortcode');
