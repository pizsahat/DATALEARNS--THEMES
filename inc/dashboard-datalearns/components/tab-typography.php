<style>
    .btn-modern {
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        transition: all 0.3s ease;
        background: var(--primary-color);
        border: none;
        color: white;
    }

    .btn-modern:hover {
        background: var(--secondary-color);
        transform: translateY(-2px);
        color: white;
    }
</style>
<!-- Font Settings Tab -->
<div class="tab-pane fade show active" id="typography" role="tabpanel">
    <div class="row g-4">
        <!-- Header Font -->
        <div class="col-12">
            <div class="font-preview-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0"><i class="fas fa-heading me-2"></i>Header Settings</h4>
                    <button class="btn btn-modern btn-save" data-type="header">
                        <i class="fas fa-save me-2"></i>Save Settings
                    </button>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted mb-3">Font Family</label>
                    <select class="form-select font-selector shadow-sm w-100" id="header-font-family" style="width: 100%"></select>
                </div>

                <div class="border-start border-3 border-primary ps-4 py-3 bg-light rounded">
                    <div id="header-font-preview" class="h2 mb-0">
                        Header Preview Text
                    </div>
                </div>
            </div>
        </div>

        <!-- Body Font -->
        <div class="col-12">
            <div class="font-preview-card p-4 mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0"><i class="fas fa-paragraph me-2"></i>Body Settings</h4>
                    <button class="btn btn-modern btn-save" data-type="body">
                        <i class="fas fa-save me-2"></i>Save Settings
                    </button>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted mb-3">Font Family</label>
                    <select class="form-select font-selector shadow-sm w-100" id="body-font-family" style="width: 100%"></select>
                </div>

                <div class="border-start border-3 border-primary ps-4 py-3 bg-light rounded">
                    <div id="body-font-preview" class="lead mb-0">
                        Body preview text with longer example to see how paragraphs will look.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Update preview saat memilih font
        document.getElementById("header-font-family").addEventListener("change", function() {
            const selectedFont = this.value;
            document.getElementById("header-font-preview").style.fontFamily = selectedFont;
        });

        document.getElementById("body-font-family").addEventListener("change", function() {
            const selectedFont = this.value;
            document.getElementById("body-font-preview").style.fontFamily = selectedFont;
        });

        // Jika sudah ada font yang tersimpan, tampilkan di preview
        if (savedHeaderFont) {
            document.getElementById("header-font-preview").style.fontFamily = savedHeaderFont;
        }
        if (savedBodyFont) {
            document.getElementById("body-font-preview").style.fontFamily = savedBodyFont;
        }

        // Event listener untuk tombol save
        document.querySelectorAll(".btn-save").forEach(button => {
            button.addEventListener("click", function() {
                const fontType = this.getAttribute("data-type");
                let fontFamily = '';

                if (fontType === "header") {
                    fontFamily = document.getElementById("header-font-family").value;
                } else if (fontType === "body") {
                    fontFamily = document.getElementById("body-font-family").value;
                }

                if (confirm(`Apakah Anda yakin ingin menyimpan ${fontType} font: ${fontFamily}?`)) {
                    saveFontSetting(fontType, fontFamily);
                }
            });
        });
    });

    const savedHeaderFont = "<?php echo esc_js($header_font); ?>";
    const savedBodyFont = "<?php echo esc_js($body_font); ?>";
    const googleFontsAPI = "<?php echo esc_url('https://www.googleapis.com/webfonts/v1/webfonts?key=' . GOOGLE_FONTS_API_KEY); ?>";
    const otherFonts = ["Helvetica", "Verdana", "Arial", "Times New Roman", "Georgia", "Courier New"];

    async function fetchGoogleFonts() {
        try {
            const response = await fetch(googleFontsAPI);
            const data = await response.json();
            const googleFonts = data.items.map(font => font.family);

            populateFontSelect("#header-font-family", googleFonts);
            populateFontSelect("#body-font-family", googleFonts);

            $(".font-selector").select2({
                placeholder: "Pilih atau ketik font...",
                allowClear: true
            });

            // Event handler untuk perubahan font
            $('#header-font-family').on('change', function() {
                const font = $(this).val();
                $('#header-font-preview').css('font-family', font);
                loadGoogleFont(font);
            });

            $('#body-font-family').on('change', function() {
                const font = $(this).val();
                $('#body-font-preview').css('font-family', font);
                loadGoogleFont(font);
            });

            // Set nilai awal jika ada
            if (savedHeaderFont) {
                $('#header-font-family').val(savedHeaderFont).trigger('change');
            }
            if (savedBodyFont) {
                $('#body-font-family').val(savedBodyFont).trigger('change');
            }

        } catch (error) {
            console.error("Gagal mengambil data font:", error);
        }
    }

    function loadGoogleFont(fontFamily) {
        if (!otherFonts.includes(fontFamily)) {
            const fontId = `google-font-${fontFamily.replace(/\s+/g, '-').toLowerCase()}`;
            if (!document.getElementById(fontId)) {
                const link = document.createElement('link');
                link.id = fontId;
                link.href = `https://fonts.googleapis.com/css?family=${encodeURIComponent(fontFamily)}`;
                link.rel = 'stylesheet';
                document.head.appendChild(link);
            }
        }
    }

    function populateFontSelect(selectId, googleFonts) {
        const select = $(selectId);
        select.empty();

        select.append('<optgroup label="System Fonts">');
        otherFonts.forEach(font => {
            select.append(`<option value="${font}" data-source="system" style="font-family: ${font};">${font}</option>`);
        });
        select.append('</optgroup>');

        select.append('<optgroup label="Google Fonts">');
        googleFonts.forEach(font => {
            select.append(`<option value="${font}" data-source="google" style="font-family: ${font};">${font}</option>`);
        });
        select.append('</optgroup>');
    }

    function saveFontSetting(fontType, fontFamily) {
        fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `action=save_font_settings&font_type=${fontType}&font_family=${fontFamily}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(fontType.toUpperCase() + " Font berhasil disimpan: " + fontFamily);
                } else {
                    alert("Gagal menyimpan font: " + data);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Terjadi kesalahan saat menyimpan: " + error);
            });
    }

    // Initialize when document is loaded
    document.addEventListener("DOMContentLoaded", function() {
        fetchGoogleFonts();
    });
</script>