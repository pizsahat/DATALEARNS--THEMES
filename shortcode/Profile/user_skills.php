<?php
function us_get_profile_username()
{
    $author_login = get_query_var('author_name');

    if (empty($author_login)) {
        $request_uri = $_SERVER['REQUEST_URI'];
        $parsed_url = parse_url($request_uri);
        $path = trim($parsed_url['path'] ?? '', '/');
        $path_parts = explode('/', $path);

        if (($key = array_search('profile', $path_parts)) !== false && isset($path_parts[$key + 1])) {
            $author_login = $path_parts[$key + 1];
        }
    }

    if (empty($author_login)) {
        $author_login = sanitize_text_field($_GET['user'] ?? $_GET['author_name'] ?? '');
    }

    return $author_login;
}

function us_get_user_by_username($username)
{
    $user_obj = get_user_by('login', $username);
    if (!$user_obj) {
        $user_obj = get_user_by('slug', $username);
    }
    return $user_obj;
}

function us_render_skill_item($skill)
{
    $name = esc_html($skill['name']);
    $percent = intval($skill['percent']);
    return "
      <div class='skill-item'>
        <div class='skill-header'>
          <span class='skill-name'>{$name}</span>
          <span class='skill-percentage'>{$percent}%</span>
        </div>
        <div class='skill-bar'>
          <div class='skill-fill' data-percent='{$percent}' style='width: {$percent}%;'></div>
        </div>
      </div>
    ";
}

function us_render_skill_form_row($skill = null)
{
    $name = $skill ? esc_attr($skill['name']) : '';
    $percent = $skill ? intval($skill['percent']) : '';
    return "
      <div class='skill-form-row'>
        <input type='text' name='skill_name[]' placeholder='Nama skill' value='{$name}' required />
        <input type='number' name='skill_percent[]' placeholder='%' min='0' max='100' value='{$percent}' required />
        <button type='button' class='remove-skill-btn'>🗑️</button>
      </div>
    ";
}

function user_skills_shortcode()
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
    $profile_display_name = $user_obj->display_name;
    $is_owner = $is_logged_in && $current_user->ID === $profile_user_id;
    $is_edit_mode = $is_owner && isset($_GET['um_action']) && $_GET['um_action'] === 'edit';


    if ($is_edit_mode && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_skills'])) {
        $names = array_map('sanitize_text_field', $_POST['skill_name'] ?? []);
        $percents = array_map('intval', $_POST['skill_percent'] ?? []);
        $new_skills = [];

        for ($i = 0; $i < count($names); $i++) {
            $name = trim($names[$i]);
            $percent = $percents[$i];

            if (!empty($name) && $percent >= 0 && $percent <= 100) {
                $new_skills[] = [
                    'name' => $name,
                    'percent' => $percent,
                ];
            }
        }

        update_user_meta($profile_user_id, 'user_skills', $new_skills);


        wp_redirect(remove_query_arg('um_action'));
        exit;
    }


    $skills = get_user_meta($profile_user_id, 'user_skills', true);
    $skills = is_array($skills) ? $skills : [];

    ob_start();
?>
    <div class="user-skills-wrapper">

        <?php if (!$is_edit_mode): ?>
            <!-- View Mode -->
            <?php if (!empty($skills)): ?>
                <?php foreach ($skills as $skill): ?>
                    <?php echo us_render_skill_item($skill); ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-skills-message">
                    <p>Belum ada skill yang ditambahkan<?php echo $is_owner ? '. Klik tombol edit untuk menambahkan skill.' : ' oleh pengguna ini.'; ?></p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Edit Mode -->
            <div class="skill-form-wrapper">
                <h4>Edit Skills</h4>
                <form method="post" id="skills-form">
                    <div id="skill-form-container">
                        <?php if (!empty($skills)): ?>
                            <?php foreach ($skills as $skill): ?>
                                <?php echo us_render_skill_form_row($skill); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php echo us_render_skill_form_row(); ?>
                        <?php endif; ?>
                    </div>

                    <button type="button" class="add-skill-btn" id="add-skill-btn">
                        ➕ Tambah Skill
                    </button>

                    <div class="form-actions">
                        <button type="submit" name="submit_skills" class="save-btn">💾 Simpan</button>
                        <a href="<?php echo esc_url(remove_query_arg('um_action')); ?>" class="cancel-btn">❌ Batal</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($is_edit_mode): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('skill-form-container');
                const addBtn = document.getElementById('add-skill-btn');

                // Add new skill row
                addBtn.addEventListener('click', function() {
                    const row = document.createElement('div');
                    row.className = 'skill-form-row';
                    row.innerHTML = `
                    <input type="text" name="skill_name[]" placeholder="Nama skill" required />
                    <input type="number" name="skill_percent[]" placeholder="%" min="0" max="100" required />
                    <button type="button" class="remove-skill-btn">🗑️</button>
                `;
                    container.appendChild(row);

                    // Add event to new remove button
                    row.querySelector('.remove-skill-btn').addEventListener('click', function() {
                        removeSkillRow(this);
                    });
                });

                // Remove skill row
                function removeSkillRow(button) {
                    const rows = container.querySelectorAll('.skill-form-row');
                    if (rows.length > 1) {
                        button.closest('.skill-form-row').remove();
                    } else {
                        alert('Minimal harus ada satu skill!');
                    }
                }

                // Add event to existing remove buttons
                document.querySelectorAll('.remove-skill-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        removeSkillRow(this);
                    });
                });
            });
        </script>
    <?php endif; ?>

<?php
    return ob_get_clean();
}
add_shortcode('user_skills', 'user_skills_shortcode');
