<style>
    .font-preview-card {
        padding: 2rem;
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

    .form-label {
        font-weight: 500;
        color: #555;
    }

    .menu-item-group {
        border-left: 4px solid var(--primary-color);
        padding-left: 1rem;
        margin-bottom: 1.5rem;
        background-color: #f9f9f9;
        border-radius: 6px;
        padding: 1rem;
    }
</style>

<div class="tab-pane fade" id="header">
    <div class="font-preview-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="fas fa-bars me-2"></i>Header Navigation Settings</h4>
            <button class="btn btn-modern" id="save-menu-settings">
                <i class="fas fa-save me-2"></i>Simpan Menu
            </button>
        </div>

        <div id="menu-items-container">
            <!-- Dynamic Items will be inserted here -->
        </div>

        <button class="btn btn-outline-primary mt-3" id="add-menu-item">
            <i class="fas fa-plus me-1"></i> Tambah Item Menu
        </button>
    </div>
</div>

<template id="menu-item-template">
    <div class="menu-item-group">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Tipe Link</label>
                <select class="form-select nav-item-type">
                    <option value="page">Page</option>
                    <option value="post_type">Post Type</option>
                    <option value="custom">Custom URL</option>
                </select>
            </div>
            <div class="col-md-4 nav-source-field">
                <label class="form-label">Sumber</label>
                <select class="form-select nav-item-source">
                    <!-- akan diisi dinamis -->
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Label</label>
                <input type="text" class="form-control nav-item-label" placeholder="Label menu">
            </div>
            <div class="col-md-1 text-end">
                <button class="btn btn-danger btn-remove-item"><i class="fas fa-trash"></i></button>
            </div>
        </div>
    </div>
</template>

<script>
    var ajaxParams = {
        ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
        saveNonce: '<?php echo wp_create_nonce('save_header_nav_nonce'); ?>',
        getSavedNonce: '<?php echo wp_create_nonce('get_saved_nav_nonce'); ?>'
    };
    document.addEventListener("DOMContentLoaded", function() {
        const menuItemsContainer = document.getElementById("menu-items-container");
        const addItemBtn = document.getElementById("add-menu-item");
        const menuItemTemplate = document.getElementById("menu-item-template").content;

        addItemBtn.addEventListener("click", function() {
            const clone = document.importNode(menuItemTemplate, true);
            menuItemsContainer.appendChild(clone);
            updateSources(menuItemsContainer.lastElementChild);
        });

        menuItemsContainer.addEventListener("change", function(e) {
            if (e.target.classList.contains("nav-item-type")) {
                updateSources(e.target.closest(".menu-item-group"));
            }
        });

        menuItemsContainer.addEventListener("click", function(e) {
            if (e.target.closest(".btn-remove-item")) {
                e.target.closest(".menu-item-group").remove();
            }
        });

        function updateSources(group) {
            const type = group.querySelector(".nav-item-type").value;
            const sourceField = group.querySelector(".nav-item-source");
            sourceField.innerHTML = '<option value="">Loading...</option>';

            if (type === "custom") {
                sourceField.outerHTML = `<input type="url" class="form-control nav-item-source" placeholder="https://example.com">`;
                return Promise.resolve();
            } else {
                sourceField.innerHTML = '<option value="">Loading...</option>';
                return fetch(`${ajaxParams.ajaxUrl}?action=get_nav_items&type=${type}`)
                    .then(res => res.json())
                    .then(data => {
                        sourceField.innerHTML = "";
                        data.forEach(item => {
                            const opt = document.createElement("option");
                            opt.value = item.value;
                            opt.textContent = item.label;
                            sourceField.appendChild(opt);
                        });
                    });
            }
        }

        document.getElementById("save-menu-settings").addEventListener("click", function() {
            const items = [];

            if (!confirm("Apakah Anda yakin ingin menyimpan perubahan menu?")) {
                return;
            }

            document.querySelectorAll(".menu-item-group").forEach(group => {
                const type = group.querySelector(".nav-item-type").value;
                const label = group.querySelector(".nav-item-label").value;
                const source = group.querySelector(".nav-item-source").value;

                items.push({
                    type,
                    label,
                    source
                });
            });

            const formData = new URLSearchParams();
            formData.append('action', 'save_header_navigation');
            formData.append('_ajax_nonce', ajaxParams.saveNonce);
            formData.append('items', JSON.stringify(items));

            fetch(ajaxParams.ajaxUrl, {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("Menu berhasil disimpan!");
                    } else {
                        alert("Gagal menyimpan menu: " + (data.data.message || ''));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Terjadi kesalahan saat menyimpan.");
                });
        });

        function loadSavedItems() {
            fetch(`${ajaxParams.ajaxUrl}?action=get_saved_nav_items&_ajax_nonce=${ajaxParams.getSavedNonce}`)
                .then(res => res.json())
                .then(items => {
                    items.forEach(item => {
                        const clone = document.importNode(menuItemTemplate, true);
                        const group = clone.querySelector('.menu-item-group');
                        const typeSelect = group.querySelector('.nav-item-type');
                        const labelInput = group.querySelector('.nav-item-label');

                        typeSelect.value = item.type;
                        labelInput.value = item.label;

                        menuItemsContainer.appendChild(clone);

                        updateSources(group).then(() => {
                            const sourceElement = group.querySelector('.nav-item-source');
                            if (sourceElement) {
                                sourceElement.value = item.source;
                            }
                        });
                    });
                });
        }

        loadSavedItems();

    });
</script>