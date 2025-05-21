<div class="tab-pane fade" id="color" role="tabpanel">
    <div class="row g-4">
        <div class="col-md-6">
            <div class="font-preview-card p-3 h-100">
                <h5 class="card-title">Links</h5>
                <div class="mb-3">
                    <label for="link-color" class="form-label d-flex justify-content-between">
                        <span>Normal</span>
                        <small class="text-muted">#0d6efd</small>
                    </label>
                    <input type="color" class="form-control form-control-color w-100" id="link-color" value="#0d6efd" title="Choose link color">
                </div>
                <div class="mb-3">
                    <label for="link-hover-color" class="form-label d-flex justify-content-between">
                        <span>Hover</span>
                        <small class="text-muted">#0a58ca</small>
                    </label>
                    <input type="color" class="form-control form-control-color w-100" id="link-hover-color" value="#0a58ca" title="Choose link hover color">
                </div>
                <div class="mt-2">
                    <div class="preview-box p-3 rounded border">
                        <p>Preview: <a href="#" class="link-preview">This is a link</a></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="font-preview-card p-3 h-100">
                <h5 class="card-title">Headings (H1-H6)</h5>
                <div class="mb-3">
                    <label for="heading-color" class="form-label d-flex justify-content-between">
                        <span>Color</span>
                        <small class="text-muted">#212529</small>
                    </label>
                    <input type="color" class="form-control form-control-color w-100" id="heading-color" value="#212529" title="Choose heading color">
                </div>
                <div class="mt-2">
                    <div class="preview-box p-3 rounded border">
                        <h2 class="heading-preview mb-1">Heading Preview</h2>
                        <h4 class="heading-preview">Subheading</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="font-preview-card p-3 h-100">
                <h5 class="card-title">Body Text & Background</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="body-text-color" class="form-label d-flex justify-content-between">
                            <span>Text Color</span>
                            <small class="text-muted">#212529</small>
                        </label>
                        <input type="color" class="form-control form-control-color w-100" id="body-text-color" value="#212529" title="Choose body text color">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="content-bg-color" class="form-label d-flex justify-content-between">
                            <span>Background</span>
                            <small class="text-muted">#ffffff</small>
                        </label>
                        <input type="color" class="form-control form-control-color w-100" id="content-bg-color" value="#ffffff" title="Choose content background color">
                    </div>
                </div>
                <div class="mt-2">
                    <div class="preview-box p-3 rounded border bg-preview">
                        <p class="body-text-preview">This is a preview of your body text and background color combination. This helps you ensure good text readability against your chosen background.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="font-preview-card p-3 h-100">
                <h5 class="card-title">Navigation Menu</h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="nav-link-color" class="form-label d-flex justify-content-between">
                            <span>Menu Link</span>
                            <small class="text-muted">#333333</small>
                        </label>
                        <input type="color" class="form-control form-control-color w-100" id="nav-link-color" value="#333333" title="Choose navigation link color">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="nav-link-hover-color" class="form-label d-flex justify-content-between">
                            <span>Menu Hover</span>
                            <small class="text-muted">#0a58ca</small>
                        </label>
                        <input type="color" class="form-control form-control-color w-100" id="nav-link-hover-color" value="#0a58ca" title="Choose navigation link hover color">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="nav-current-link-color" class="form-label d-flex justify-content-between">
                            <span>Current Menu</span>
                            <small class="text-muted">#000000</small>
                        </label>
                        <input type="color" class="form-control form-control-color w-100" id="nav-current-link-color" value="#000000" title="Choose current navigation link color">
                    </div>
                </div>
                <div class="mt-2">
                    <div class="preview-box p-3 rounded border">
                        <nav class="preview-navigation">
                            <ul class="d-flex list-unstyled gap-3">
                                <li class="current-menu-item"><a href="#">Home</a></li>
                                <li><a href="#" class="nav-link-preview">About</a></li>
                                <li><a href="#" class="nav-link-preview">Services</a></li>
                                <li><a href="#" class="nav-link-preview">Contact</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 d-flex justify-content-end mt-4">
            <button class="btn btn-modern save-colors-btn" id="save-color-settings">
                <i class="fas fa-save me-2"></i>Simpan
            </button>
        </div>
    </div>
</div>

<?php
$link_color = get_option('datalearns_custom_link_color', '#0d6efd');
$link_hover_color = get_option('datalearns_custom_link_hover_color', '#0a58ca');
$heading_color = get_option('datalearns_custom_heading_color', '#212529');
$body_text_color = get_option('datalearns_custom_body_text_color', '#212529');
$content_bg_color = get_option('datalearns_custom_content_bg_color', '#ffffff');
$nav_link_color = get_option('datalearns_custom_nav_link_color', '#333333');
$nav_link_hover_color = get_option('datalearns_custom_nav_link_hover_color', '#0a58ca');
$nav_current_link_color = get_option('datalearns_custom_nav_current_link_color', '#000000');
?>

<script>
    const savedColorSettings = {
        linkColor: '<?php echo esc_js($link_color); ?>',
        linkHoverColor: '<?php echo esc_js($link_hover_color); ?>',
        headingColor: '<?php echo esc_js($heading_color); ?>',
        bodyTextColor: '<?php echo esc_js($body_text_color); ?>',
        contentBgColor: '<?php echo esc_js($content_bg_color); ?>',
        navLinkColor: '<?php echo esc_js($nav_link_color); ?>',
        navLinkHoverColor: '<?php echo esc_js($nav_link_hover_color); ?>',
        navCurrentLinkColor: '<?php echo esc_js($nav_current_link_color); ?>'
    };

    document.getElementById('link-color').addEventListener('input', function() {
        document.querySelectorAll('.link-preview').forEach(el => {
            el.style.color = this.value;
        });
        document.querySelector('label[for="link-color"] small').textContent = this.value;
    });

    document.getElementById('link-hover-color').addEventListener('input', function() {
        document.querySelector('label[for="link-hover-color"] small').textContent = this.value;
    });

    document.getElementById('heading-color').addEventListener('input', function() {
        document.querySelectorAll('.heading-preview').forEach(el => {
            el.style.color = this.value;
        });
        document.querySelector('label[for="heading-color"] small').textContent = this.value;
    });

    document.getElementById('body-text-color').addEventListener('input', function() {
        document.querySelectorAll('.body-text-preview').forEach(el => {
            el.style.color = this.value;
        });
        document.querySelector('label[for="body-text-color"] small').textContent = this.value;
    });

    document.getElementById('content-bg-color').addEventListener('input', function() {
        document.querySelectorAll('.bg-preview').forEach(el => {
            el.style.backgroundColor = this.value;
        });
        document.querySelector('label[for="content-bg-color"] small').textContent = this.value;
    });

    document.getElementById('nav-link-color').addEventListener('input', function() {
        document.querySelectorAll('.nav-link-preview').forEach(el => {
            el.style.color = this.value;
        });
        document.querySelector('label[for="nav-link-color"] small').textContent = this.value;
    });

    document.getElementById('nav-link-hover-color').addEventListener('input', function() {
        document.querySelector('label[for="nav-link-hover-color"] small').textContent = this.value;
    });

    document.getElementById('nav-current-link-color').addEventListener('input', function() {
        document.querySelectorAll('.preview-navigation .current-menu-item a').forEach(el => {
            el.style.color = this.value;
        });
        document.querySelector('label[for="nav-current-link-color"] small').textContent = this.value;
    });

    window.addEventListener('DOMContentLoaded', function() {
        if (savedColorSettings.linkColor) {
            const linkColorInput = document.getElementById('link-color');
            linkColorInput.value = savedColorSettings.linkColor;
            document.querySelector('label[for="link-color"] small').textContent = savedColorSettings.linkColor;
            document.querySelectorAll('.link-preview').forEach(el => {
                el.style.color = savedColorSettings.linkColor;
            });
        }

        if (savedColorSettings.linkHoverColor) {
            const linkHoverColorInput = document.getElementById('link-hover-color');
            linkHoverColorInput.value = savedColorSettings.linkHoverColor;
            document.querySelector('label[for="link-hover-color"] small').textContent = savedColorSettings.linkHoverColor;
        }

        if (savedColorSettings.headingColor) {
            const headingColorInput = document.getElementById('heading-color');
            headingColorInput.value = savedColorSettings.headingColor;
            document.querySelector('label[for="heading-color"] small').textContent = savedColorSettings.headingColor;
            document.querySelectorAll('.heading-preview').forEach(el => {
                el.style.color = savedColorSettings.headingColor;
            });
        }

        if (savedColorSettings.bodyTextColor) {
            const bodyTextColorInput = document.getElementById('body-text-color');
            bodyTextColorInput.value = savedColorSettings.bodyTextColor;
            document.querySelector('label[for="body-text-color"] small').textContent = savedColorSettings.bodyTextColor;
            document.querySelectorAll('.body-text-preview').forEach(el => {
                el.style.color = savedColorSettings.bodyTextColor;
            });
        }

        if (savedColorSettings.contentBgColor) {
            const contentBgColorInput = document.getElementById('content-bg-color');
            contentBgColorInput.value = savedColorSettings.contentBgColor;
            document.querySelector('label[for="content-bg-color"] small').textContent = savedColorSettings.contentBgColor;
            document.querySelectorAll('.bg-preview').forEach(el => {
                el.style.backgroundColor = savedColorSettings.contentBgColor;
            });
        }

        if (savedColorSettings.navLinkColor) {
            const navLinkColorInput = document.getElementById('nav-link-color');
            navLinkColorInput.value = savedColorSettings.navLinkColor;
            document.querySelector('label[for="nav-link-color"] small').textContent = savedColorSettings.navLinkColor;
            document.querySelectorAll('.nav-link-preview').forEach(el => {
                el.style.color = savedColorSettings.navLinkColor;
            });
        }

        if (savedColorSettings.navLinkHoverColor) {
            const navLinkHoverColorInput = document.getElementById('nav-link-hover-color');
            navLinkHoverColorInput.value = savedColorSettings.navLinkHoverColor;
            document.querySelector('label[for="nav-link-hover-color"] small').textContent = savedColorSettings.navLinkHoverColor;
        }

        if (savedColorSettings.navCurrentLinkColor) {
            const navCurrentLinkColorInput = document.getElementById('nav-current-link-color');
            navCurrentLinkColorInput.value = savedColorSettings.navCurrentLinkColor;
            document.querySelector('label[for="nav-current-link-color"] small').textContent = savedColorSettings.navCurrentLinkColor;
            document.querySelectorAll('.preview-navigation .current-menu-item a').forEach(el => {
                el.style.color = savedColorSettings.navCurrentLinkColor;
            });
        }
    });

    document.querySelectorAll('.link-preview').forEach(link => {
        link.addEventListener('mouseover', function() {
            const hoverColor = document.getElementById('link-hover-color').value;
            this.style.color = hoverColor;
        });

        link.addEventListener('mouseout', function() {
            const normalColor = document.getElementById('link-color').value;
            this.style.color = normalColor;
        });
    });

    document.querySelectorAll('.nav-link-preview').forEach(link => {
        link.addEventListener('mouseover', function() {
            const hoverColor = document.getElementById('nav-link-hover-color').value;
            this.style.color = hoverColor;
        });

        link.addEventListener('mouseout', function() {
            const normalColor = document.getElementById('nav-link-color').value;
            this.style.color = normalColor;
        });
    });

    function saveColorSettings() {
        const colorSettings = {
            linkColor: document.getElementById('link-color').value,
            linkHoverColor: document.getElementById('link-hover-color').value,
            headingColor: document.getElementById('heading-color').value,
            bodyTextColor: document.getElementById('body-text-color').value,
            contentBgColor: document.getElementById('content-bg-color').value,

            navLinkColor: document.getElementById('nav-link-color').value,
            navLinkHoverColor: document.getElementById('nav-link-hover-color').value,
            navCurrentLinkColor: document.getElementById('nav-current-link-color').value
        };

        const colorSettingsJSON = JSON.stringify(colorSettings);

        fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `action=save_color_settings&color_settings=${encodeURIComponent(colorSettingsJSON)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Color settings saved successfully!');
                } else {
                    alert("Failed to save colors: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Error occurred while saving: " + error);
            });
    }

    // Attach to save button
    document.getElementById('save-color-settings').addEventListener('click', saveColorSettings);
</script>

<style>
    .form-control-color {
        height: 38px;
    }

    .preview-box {
        min-height: 80px;
        background-color: #fff;
    }

    .card-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .btn-modern {
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        background: var(--primary-color);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-modern:hover {
        background: var(--secondary-color);
        transform: translateY(-2px);
    }

    .preview-navigation ul {
        padding: 0;
        margin: 0;
    }

    .preview-navigation .current-menu-item a {
        font-weight: bold;
    }
</style>