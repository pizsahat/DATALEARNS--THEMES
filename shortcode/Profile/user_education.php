<?php
function us_render_education_item($education)
{
    $school = esc_html($education['school']);
    $degree = esc_html($education['degree']);
    $major = esc_html($education['major']);
    $start_date = esc_html($education['start_date']);
    $end_date = $education['is_current'] ? 'Expected' : esc_html($education['end_date']);
    $description = esc_html($education['description']);

    // Format dates for display
    $start_formatted = !empty($start_date) ? date('Y', strtotime($start_date)) : '';
    $end_formatted = $education['is_current'] ? 'Expected' : (!empty($end_date) ? date('Y', strtotime($end_date)) : '');
    $date_range = $start_formatted . ' - ' . $end_formatted;

    // Generate unique ID for this education item
    $unique_id = 'edu_' . uniqid();

    // Check if description is long (more than 150 characters as rough estimate for 3 lines)
    $is_long_description = strlen($description) > 150;
    $description_class = $is_long_description ? 'education-description collapsed' : 'education-description';

    $description_html = '';
    if (!empty($description)) {
        $description_html = "<div class='{$description_class}' id='{$unique_id}'><p>{$description}</p></div>";
        if ($is_long_description) {
            $description_html .= "<button class='see-more-btn' onclick='toggleEducationDescription(\"{$unique_id}\", this)'>Lihat selengkapnya</button>";
        }
    }

    return "
        <div class='education-item'>
            <div class='education-header'>
                <div class='school-logo'>
                    <img src='data:image/svg+xml;base64," . base64_encode('
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="48" height="48" rx="8" fill="#1976D2"/>
                            <path d="M24 8L8 16v8c0 10 7 19 16 19s16-9 16-19v-8L24 8z" fill="white"/>
                            <path d="M24 12L14 18v6c0 6 4.5 12 10 12s10-6 10-12v-6L24 12z" fill="#1976D2"/>
                            <circle cx="24" cy="20" r="2" fill="white"/>
                            <path d="M20 24h8v2h-8v-2z" fill="white"/>
                            <path d="M18 28h12v2H18v-2z" fill="white"/>
                        </svg>
                    ') . "' alt='School Logo' />
                </div>
                <div class='education-details'>
                    <h3 class='school-name'>{$school}</h3>
                    <div class='degree-info'>
                        <span class='degree-name'>{$degree}, {$major}</span>
                    </div>
                    <div class='date-range'>
                        <span class='education-dates'>{$date_range}</span>
                    </div>
                </div>
            </div>
            {$description_html}
        </div>
    ";
}

function us_render_education_form_row($education = null)
{
    $school = $education ? esc_attr($education['school']) : '';
    $degree = $education ? esc_attr($education['degree']) : '';
    $major = $education ? esc_attr($education['major']) : '';
    $start_date = $education ? esc_attr($education['start_date']) : '';
    $end_date = $education ? esc_attr($education['end_date']) : '';
    $description = $education ? esc_attr($education['description']) : '';


    return "
        <div class='education-form-row'>
            <div class='form-group'>
                <label>Nama Sekolah/Universitas *</label>
                <input type='text' name='edu_school[]' placeholder='Contoh: Universitas Indonesia' value='{$school}' required />
            </div>
            
            <div class='form-group'>
                <label>Tingkat Pendidikan *</label>
                <select name='edu_degree[]' required>
                    <option value=''>Pilih Tingkat</option>
                    <option value='SMA/SMK'" . ($degree === 'SMA/SMK' ? ' selected' : '') . ">SMA/SMK</option>
                    <option value='Diploma'" . ($degree === 'Diploma' ? ' selected' : '') . ">Diploma</option>
                    <option value='Sarjana'" . ($degree === 'Sarjana' ? ' selected' : '') . ">Sarjana (S1)</option>
                    <option value='Magister'" . ($degree === 'Magister' ? ' selected' : '') . ">Magister (S2)</option>
                    <option value='Doktor'" . ($degree === 'Doktor' ? ' selected' : '') . ">Doktor (S3)</option>
                </select>
            </div>
            
            <div class='form-group'>
                <label>Jurusan/Bidang Studi *</label>
                <input type='text' name='edu_major[]' placeholder='Contoh: Teknik Informatika' value='{$major}' required />
            </div>
            
            <div class='form-group'>
                <label>Tahun Mulai *</label>
                <input type='month' name='edu_start_date[]' value='{$start_date}' required />
            </div>
            
          
            
            <div class='form-group end-date-group'>
                <label>Tahun Selesai/Ekspektasi</label>
                <input type='month' name='edu_end_date[]' value='{$end_date}' />
            </div>
            
            <div class='form-group'>
                <label>Deskripsi (Opsional)</label>
                <textarea name='edu_description[]' placeholder='Jelaskan aktivitas, prestasi, atau hal penting selama pendidikan...' rows='4'>{$description}</textarea>
            </div>
            
            <button type='button' class='remove-education-btn'>🗑️ Hapus</button>
        </div>
    ";
}

function user_education_shortcode()
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
    if ($is_edit_mode && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_education'])) {
        $schools = array_map('sanitize_text_field', $_POST['edu_school'] ?? []);
        $degrees = array_map('sanitize_text_field', $_POST['edu_degree'] ?? []);
        $majors = array_map('sanitize_text_field', $_POST['edu_major'] ?? []);
        $start_dates = array_map('sanitize_text_field', $_POST['edu_start_date'] ?? []);
        $end_dates = array_map('sanitize_text_field', $_POST['edu_end_date'] ?? []);
        $is_currents = $_POST['edu_is_current'] ?? [];
        $descriptions = array_map('sanitize_textarea_field', $_POST['edu_description'] ?? []);

        $new_educations = [];

        for ($i = 0; $i < count($schools); $i++) {
            $school = trim($schools[$i]);
            $degree = trim($degrees[$i]);
            $major = trim($majors[$i]);
            $start_date = trim($start_dates[$i]);
            $end_date = trim($end_dates[$i] ?? '');
            $is_current = isset($is_currents[$i]) && $is_currents[$i] === '1';
            $description = trim($descriptions[$i] ?? '');

            if (!empty($school) && !empty($degree) && !empty($major) && !empty($start_date)) {
                $new_educations[] = [
                    'school' => $school,
                    'degree' => $degree,
                    'major' => $major,
                    'start_date' => $start_date,
                    'end_date' => $is_current ? '' : $end_date,
                    'is_current' => $is_current,
                    'description' => $description,
                ];
            }
        }

        // Sort educations by start date (newest first)
        usort($new_educations, function ($a, $b) {
            return strtotime($b['start_date']) - strtotime($a['start_date']);
        });

        update_user_meta($profile_user_id, 'user_education', $new_educations);

        wp_redirect(remove_query_arg('um_action'));
        exit;
    }

    // Get educations
    $educations = get_user_meta($profile_user_id, 'user_education', true);
    $educations = is_array($educations) ? $educations : [];

    ob_start();
?>
    <div class="user-education-wrapper">


        <?php if (!$is_edit_mode): ?>
            <!-- View Mode -->
            <?php if (!empty($educations)): ?>
                <?php foreach ($educations as $education): ?>
                    <?php echo us_render_education_item($education); ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-education-message">
                    <p>Belum ada riwayat pendidikan yang ditambahkan<?php echo $is_owner ? '. Klik tombol edit untuk menambahkan riwayat pendidikan.' : ' oleh pengguna ini.'; ?></p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Edit Mode -->
            <div class="education-form-wrapper">
                <h4>Edit Education</h4>
                <form method="post" id="education-form">
                    <div id="education-form-container">
                        <?php if (!empty($educations)): ?>
                            <?php foreach ($educations as $education): ?>
                                <?php echo us_render_education_form_row($education); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php echo us_render_education_form_row(); ?>
                        <?php endif; ?>
                    </div>

                    <button type="button" class="add-education-btn" id="add-education-btn">
                        ➕ Tambah Pendidikan
                    </button>

                    <div class="form-actions">
                        <button type="submit" name="submit_education" class="save-btn">💾 Simpan</button>
                        <a href="<?php echo esc_url(remove_query_arg('um_action')); ?>" class="cancel-btn">❌ Batal</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!$is_edit_mode): ?>
        <script>
            function toggleEducationDescription(id, button) {
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
                const container = document.getElementById('education-form-container');
                const addBtn = document.getElementById('add-education-btn');

                // Add new education row
                addBtn.addEventListener('click', function() {
                    const row = document.createElement('div');
                    row.className = 'education-form-row';
                    row.innerHTML = `
                        <div class='form-group'>
                            <label>Nama Sekolah/Universitas *</label>
                            <input type='text' name='edu_school[]' placeholder='Contoh: Universitas Indonesia' required />
                        </div>
                        
                        <div class='form-group'>
                            <label>Tingkat Pendidikan *</label>
                            <select name='edu_degree[]' required>
                                <option value=''>Pilih Tingkat</option>
                                <option value='SMA/SMK'>SMA/SMK</option>
                                <option value='Diploma'>Diploma</option>
                                <option value='Sarjana'>Sarjana (S1)</option>
                                <option value='Magister'>Magister (S2)</option>
                                <option value='Doktor'>Doktor (S3)</option>
                            </select>
                        </div>
                        
                        <div class='form-group'>
                            <label>Jurusan/Bidang Studi *</label>
                            <input type='text' name='edu_major[]' placeholder='Contoh: Teknik Informatika' required />
                        </div>
                        
                        <div class='form-group'>
                            <label>Tahun Mulai *</label>
                            <input type='month' name='edu_start_date[]' required />
                        </div>
                        
                        <div class='form-group'>
                            <label>
                                <input type='checkbox' name='edu_is_current[]' value='1' class='current-education-checkbox' />
                                Saya masih bersekolah di sini
                            </label>
                        </div>
                        
                        <div class='form-group end-date-group'>
                            <label>Tahun Selesai/Ekspektasi</label>
                            <input type='month' name='edu_end_date[]' />
                        </div>
                        
                        <div class='form-group'>
                            <label>Deskripsi (Opsional)</label>
                            <textarea name='edu_description[]' placeholder='Jelaskan aktivitas, prestasi, atau hal penting selama pendidikan...' rows='4'></textarea>
                        </div>
                        
                        <button type='button' class='remove-education-btn'>🗑️ Hapus</button>
                    `;
                    container.appendChild(row);

                    // Add event listeners to new elements
                    addEducationRowListeners(row);
                });

                function addEducationRowListeners(row) {
                    // Remove button
                    row.querySelector('.remove-education-btn').addEventListener('click', function() {
                        removeEducationRow(this);
                    });

                    // Current education checkbox
                    row.querySelector('.current-education-checkbox').addEventListener('change', function() {
                        toggleEndDateField(this);
                    });
                }

                function removeEducationRow(button) {
                    const rows = container.querySelectorAll('.education-form-row');
                    if (rows.length > 1) {
                        button.closest('.education-form-row').remove();
                    } else {
                        alert('Minimal harus ada satu riwayat pendidikan!');
                    }
                }

                function toggleEndDateField(checkbox) {
                    const endDateGroup = checkbox.closest('.education-form-row').querySelector('.end-date-group');
                    const endDateInput = endDateGroup.querySelector('input[name="edu_end_date[]"]');

                    if (checkbox.checked) {
                        endDateGroup.style.display = 'none';
                        endDateInput.value = '';
                        endDateInput.required = false;
                    } else {
                        endDateGroup.style.display = 'block';
                        endDateInput.required = false; // End date is optional
                    }
                }


                document.querySelectorAll('.education-form-row').forEach(row => {
                    addEducationRowListeners(row);
                });
            });
        </script>
    <?php endif; ?>

<?php
    return ob_get_clean();
}
add_shortcode('user_education', 'user_education_shortcode');