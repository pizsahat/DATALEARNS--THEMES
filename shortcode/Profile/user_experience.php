<?php
function us_render_experience_item($experience)
{
    $position = esc_html($experience['position']);
    $company = esc_html($experience['company']);
    $type = esc_html($experience['type']);
    $start_date = esc_html($experience['start_date']);
    $end_date = $experience['is_current'] ? 'Present' : esc_html($experience['end_date']);
    $location = esc_html($experience['location']);
    $description = esc_html($experience['description']);

    // Calculate duration
    $duration = '';
    if (!empty($start_date)) {
        $start = new DateTime($start_date);
        $end = $experience['is_current'] ? new DateTime() : new DateTime($end_date);
        $diff = $start->diff($end);

        $years = $diff->y;
        $months = $diff->m;

        if ($years > 0) {
            $duration = $years . ' year' . ($years > 1 ? 's' : '');
            if ($months > 0) {
                $duration .= ' ' . $months . ' month' . ($months > 1 ? 's' : '');
            }
        } else if ($months > 0) {
            $duration = $months . ' month' . ($months > 1 ? 's' : '');
        } else {
            $duration = '1 month';
        }
    }

    // Format dates for display
    $start_formatted = !empty($start_date) ? date('M Y', strtotime($start_date)) : '';
    $end_formatted = $experience['is_current'] ? 'Present' : (!empty($end_date) ? date('M Y', strtotime($end_date)) : '');
    $date_range = $start_formatted . ' - ' . $end_formatted;
    if (!empty($duration)) {
        $date_range .= ' · ' . $duration;
    }

    // Generate unique ID for this experience item
    $unique_id = 'exp_' . uniqid();

    // Check if description is long (more than 150 characters as rough estimate for 3 lines)
    $is_long_description = strlen($description) > 150;
    $description_class = $is_long_description ? 'experience-description collapsed' : 'experience-description';

    $description_html = '';
    if (!empty($description)) {
        $description_html = "<div class='{$description_class}' id='{$unique_id}'><p>{$description}</p></div>";
        if ($is_long_description) {
            $description_html .= "<button class='see-more-btn' onclick='toggleDescription(\"{$unique_id}\", this)'>Lihat selengkapnya</button>";
        }
    }

    return "
        <div class='experience-item'>
            <div class='experience-header'>
                <div class='company-logo'>
                    <img src='data:image/svg+xml;base64," . base64_encode('
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="48" height="48" rx="8" fill="#0077B5"/>
                            <path d="M12 16h24v16H12V16z" fill="white" opacity="0.8"/>
                            <path d="M14 18h4v2h-4v-2z" fill="#0077B5"/>
                            <path d="M20 18h12v2H20v-2z" fill="#0077B5"/>
                            <path d="M14 22h18v2H14v-2z" fill="#0077B5"/>
                            <path d="M14 26h16v2H14v-2z" fill="#0077B5"/>
                            <path d="M14 30h12v2H14v-2z" fill="#0077B5"/>
                        </svg>
                    ') . "' alt='Company Logo' />
                </div>
                <div class='experience-details'>
                    <h3 class='position-title'>{$position}</h3>
                    <div class='company-info'>
                        <span class='company-name'>{$company}</span>
                        <span class='employment-type'> · {$type}</span>
                    </div>
                    <div class='date-location'>
                        <span class='date-range'>{$date_range}</span>
                        <span class='work-location'> · {$location}</span>
                    </div>
                </div>
            </div>
            {$description_html}
        </div>
    ";
}

function us_render_experience_form_row($experience = null)
{
    $position = $experience ? esc_attr($experience['position']) : '';
    $company = $experience ? esc_attr($experience['company']) : '';
    $type = $experience ? esc_attr($experience['type']) : '';
    $start_date = $experience ? esc_attr($experience['start_date']) : '';
    $end_date = $experience ? esc_attr($experience['end_date']) : '';
    $is_current = $experience ? $experience['is_current'] : false;
    $location = $experience ? esc_attr($experience['location']) : '';
    $description = $experience ? esc_attr($experience['description']) : '';

    $current_checked = $is_current ? 'checked' : '';
    $end_date_style = $is_current ? 'style="display:none;"' : '';

    return "
        <div class='experience-form-row'>
            <div class='form-group'>
                <label>Posisi *</label>
                <input type='text' name='exp_position[]' placeholder='Contoh: Backend Developer' value='{$position}' required />
            </div>
            
            <div class='form-group'>
                <label>Perusahaan *</label>
                <input type='text' name='exp_company[]' placeholder='Contoh: PT Solusi247' value='{$company}' required />
            </div>
            
            <div class='form-group'>
                <label>Tipe Pekerjaan *</label>
                <select name='exp_type[]' required>
                    <option value=''>Pilih Tipe</option>
                    <option value='Full-time'" . ($type === 'Full-time' ? ' selected' : '') . ">Full-time</option>
                    <option value='Part-time'" . ($type === 'Part-time' ? ' selected' : '') . ">Part-time</option>
                    <option value='Freelance'" . ($type === 'Freelance' ? ' selected' : '') . ">Freelance</option>
                    <option value='Contract'" . ($type === 'Contract' ? ' selected' : '') . ">Contract</option>
                    <option value='Internship'" . ($type === 'Internship' ? ' selected' : '') . ">Internship</option>
                </select>
            </div>
            
            <div class='form-group'>
                <label>Lokasi Kerja *</label>
                <select name='exp_location[]' required>
                    <option value=''>Pilih Lokasi</option>
                    <option value='On-site'" . ($location === 'On-site' ? ' selected' : '') . ">On-site</option>
                    <option value='Remote'" . ($location === 'Remote' ? ' selected' : '') . ">Remote</option>
                    <option value='Hybrid'" . ($location === 'Hybrid' ? ' selected' : '') . ">Hybrid</option>
                </select>
            </div>
            
            <div class='form-group'>
                <label>Mulai Bekerja *</label>
                <input type='month' name='exp_start_date[]' value='{$start_date}' required />
            </div>
            
            <div class='form-group'>
                <label>
                    <input type='checkbox' name='exp_is_current[]' value='1' {$current_checked} class='current-job-checkbox' />
                    Saya masih bekerja di sini
                </label>
            </div>
            
            <div class='form-group end-date-group' {$end_date_style}>
                <label>Selesai Bekerja</label>
                <input type='month' name='exp_end_date[]' value='{$end_date}' />
            </div>
            
            <div class='form-group'>
                <label>Deskripsi Pekerjaan</label>
                <textarea name='exp_description[]' placeholder='Jelaskan tugas dan tanggung jawab Anda...' rows='4'>{$description}</textarea>
            </div>
            
            <button type='button' class='remove-experience-btn'>🗑️ Hapus</button>
        </div>
    ";
}

function user_experience_shortcode()
{
    $author_login = us_get_profile_username();

    if (is_admin() || empty($author_login)) {
        return '';
    }

    $current_user = wp_get_current_user();
    $is_logged_in = is_user_logged_in();
    $user_obj = us_get_user_by_username($author_login);

    if (!$user_obj) {
        return '<p>Pengguna tidak ditemukan: ' . esc_html($author_login) . '</p>';
    }

    $profile_user_id = $user_obj->ID;
    $is_owner = $is_logged_in && $current_user->ID === $profile_user_id;
    $is_edit_mode = $is_owner && isset($_GET['um_action']) && $_GET['um_action'] === 'edit';

    // Handle form submission
    if ($is_edit_mode && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_experience'])) {
        $positions = array_map('sanitize_text_field', $_POST['exp_position'] ?? []);
        $companies = array_map('sanitize_text_field', $_POST['exp_company'] ?? []);
        $types = array_map('sanitize_text_field', $_POST['exp_type'] ?? []);
        $locations = array_map('sanitize_text_field', $_POST['exp_location'] ?? []);
        $start_dates = array_map('sanitize_text_field', $_POST['exp_start_date'] ?? []);
        $end_dates = array_map('sanitize_text_field', $_POST['exp_end_date'] ?? []);
        $is_currents = $_POST['exp_is_current'] ?? [];
        $descriptions = array_map('sanitize_textarea_field', $_POST['exp_description'] ?? []);

        $new_experiences = [];

        for ($i = 0; $i < count($positions); $i++) {
            $position = trim($positions[$i]);
            $company = trim($companies[$i]);
            $type = trim($types[$i]);
            $location = trim($locations[$i]);
            $start_date = trim($start_dates[$i]);
            $end_date = trim($end_dates[$i] ?? '');
            $is_current = isset($is_currents[$i]) && $is_currents[$i] === '1';
            $description = trim($descriptions[$i] ?? '');

            if (!empty($position) && !empty($company) && !empty($type) && !empty($location) && !empty($start_date)) {
                $new_experiences[] = [
                    'position' => $position,
                    'company' => $company,
                    'type' => $type,
                    'location' => $location,
                    'start_date' => $start_date,
                    'end_date' => $is_current ? '' : $end_date,
                    'is_current' => $is_current,
                    'description' => $description,
                ];
            }
        }

        // Sort experiences by start date (newest first)
        usort($new_experiences, function ($a, $b) {
            return strtotime($b['start_date']) - strtotime($a['start_date']);
        });

        update_user_meta($profile_user_id, 'user_experience', $new_experiences);

        wp_redirect(remove_query_arg('um_action'));
        exit;
    }

    // Get experiences
    $experiences = get_user_meta($profile_user_id, 'user_experience', true);
    $experiences = is_array($experiences) ? $experiences : [];

    ob_start();
?>
    <div class="user-experience-wrapper">


        <?php if (!$is_edit_mode): ?>
            <!-- View Mode -->
            <?php if (!empty($experiences)): ?>
                <?php foreach ($experiences as $experience): ?>
                    <?php echo us_render_experience_item($experience); ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-experience-message">
                    <p>Belum ada pengalaman kerja yang ditambahkan<?php echo $is_owner ? '. Klik tombol edit untuk menambahkan pengalaman kerja.' : ' oleh pengguna ini.'; ?></p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Edit Mode -->
            <div class="experience-form-wrapper">
                <h4>Edit Experience</h4>
                <form method="post" id="experience-form">
                    <div id="experience-form-container">
                        <?php if (!empty($experiences)): ?>
                            <?php foreach ($experiences as $experience): ?>
                                <?php echo us_render_experience_form_row($experience); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php echo us_render_experience_form_row(); ?>
                        <?php endif; ?>
                    </div>

                    <button type="button" class="add-experience-btn" id="add-experience-btn">
                        ➕ Tambah Pengalaman
                    </button>

                    <div class="form-actions">
                        <button type="submit" name="submit_experience" class="save-btn">💾 Simpan</button>
                        <a href="<?php echo esc_url(remove_query_arg('um_action')); ?>" class="cancel-btn">❌ Batal</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!$is_edit_mode): ?>
        <script>
            function toggleDescription(id, button) {
                const descElement = document.getElementById(id);
                const isCollapsed = descElement.classList.contains('collapsed');

                if (isCollapsed) {
                    descElement.classList.remove('collapsed');
                    button.textContent = 'Lihat lebih sedikit';
                } else {
                    descElement.classList.add('collapsed');
                    button.textContent = 'Lihat selengkapnya';
                }
            }
        </script>
    <?php endif; ?>

    <?php if ($is_edit_mode): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('experience-form-container');
                const addBtn = document.getElementById('add-experience-btn');

                // Add new experience row
                addBtn.addEventListener('click', function() {
                    const row = document.createElement('div');
                    row.className = 'experience-form-row';
                    row.innerHTML = `
                        <div class='form-group'>
                            <label>Posisi *</label>
                            <input type='text' name='exp_position[]' placeholder='Contoh: Backend Developer' required />
                        </div>
                        
                        <div class='form-group'>
                            <label>Perusahaan *</label>
                            <input type='text' name='exp_company[]' placeholder='Contoh: PT Solusi247' required />
                        </div>
                        
                        <div class='form-group'>
                            <label>Tipe Pekerjaan *</label>
                            <select name='exp_type[]' required>
                                <option value=''>Pilih Tipe</option>
                                <option value='Full-time'>Full-time</option>
                                <option value='Part-time'>Part-time</option>
                                <option value='Freelance'>Freelance</option>
                                <option value='Contract'>Contract</option>
                                <option value='Internship'>Internship</option>
                            </select>
                        </div>
                        
                        <div class='form-group'>
                            <label>Lokasi Kerja *</label>
                            <select name='exp_location[]' required>
                                <option value=''>Pilih Lokasi</option>
                                <option value='On-site'>On-site</option>
                                <option value='Remote'>Remote</option>
                                <option value='Hybrid'>Hybrid</option>
                            </select>
                        </div>
                        
                        <div class='form-group'>
                            <label>Mulai Bekerja *</label>
                            <input type='month' name='exp_start_date[]' required />
                        </div>
                        
                        <div class='form-group'>
                            <label>
                                <input type='checkbox' name='exp_is_current[]' value='1' class='current-job-checkbox' />
                                Saya masih bekerja di sini
                            </label>
                        </div>
                        
                        <div class='form-group end-date-group'>
                            <label>Selesai Bekerja</label>
                            <input type='month' name='exp_end_date[]' />
                        </div>
                        
                        <div class='form-group'>
                            <label>Deskripsi Pekerjaan</label>
                            <textarea name='exp_description[]' placeholder='Jelaskan tugas dan tanggung jawab Anda...' rows='4'></textarea>
                        </div>
                        
                        <button type='button' class='remove-experience-btn'>🗑️ Hapus</button>
                    `;
                    container.appendChild(row);

                    // Add event listeners to new elements
                    addExperienceRowListeners(row);
                });

                function addExperienceRowListeners(row) {
                    // Remove button
                    row.querySelector('.remove-experience-btn').addEventListener('click', function() {
                        removeExperienceRow(this);
                    });

                    // Current job checkbox
                    row.querySelector('.current-job-checkbox').addEventListener('change', function() {
                        toggleEndDateField(this);
                    });
                }

                function removeExperienceRow(button) {
                    const rows = container.querySelectorAll('.experience-form-row');
                    if (rows.length > 1) {
                        button.closest('.experience-form-row').remove();
                    } else {
                        alert('Minimal harus ada satu pengalaman kerja!');
                    }
                }

                function toggleEndDateField(checkbox) {
                    const endDateGroup = checkbox.closest('.experience-form-row').querySelector('.end-date-group');
                    const endDateInput = endDateGroup.querySelector('input[name="exp_end_date[]"]');

                    if (checkbox.checked) {
                        endDateGroup.style.display = 'none';
                        endDateInput.value = '';
                        endDateInput.required = false;
                    } else {
                        endDateGroup.style.display = 'block';
                        endDateInput.required = false; // End date is optional
                    }
                }

                // Add event listeners to existing elements
                document.querySelectorAll('.experience-form-row').forEach(row => {
                    addExperienceRowListeners(row);
                });
            });
        </script>
    <?php endif; ?>

<?php
    return ob_get_clean();
}
add_shortcode('user_experience', 'user_experience_shortcode');
