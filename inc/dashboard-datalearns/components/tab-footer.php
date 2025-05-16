<div class="tab-pane fade" id="footer">
    <div class="font-preview-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <h4 class="mb-0 text-dark fw-semibold">
                <i class="fas fa-shoe-prints me-2 text-primary"></i>Footer Settings
            </h4>
            <button class="btn btn-primary px-4 py-2 rounded-3 d-flex align-items-center" id="save-footer-settings">
                <i class="fas fa-save me-2"></i>Save Settings
            </button>
        </div>

        <form id="footer-settings-form" class="settings-form">
            <div class="footer-section mb-5 p-4 bg-light rounded-4">
                <h5 class="d-flex align-items-center text-dark mb-4">
                    <i class="fas fa-info-circle me-2 text-primary"></i> About Section
                </h5>

                <div class="mb-3">
                    <label for="footer-description" class="form-label fw-medium text-secondary">Description</label>
                    <textarea class="form-control border-2 rounded-3 p-3" id="footer-description" name="footer_description" rows="3"><?php echo esc_textarea(get_option('datalearns_footer_description', 'Sebuah program edukasi yang disusun oleh Solusi247 untuk membangun talenta data Indonesia')); ?></textarea>
                </div>
            </div>

            <div class="footer-section mb-5 p-4 bg-light rounded-4">
                <h5 class="d-flex align-items-center text-dark mb-4">
                    <i class="fas fa-address-book me-2 text-primary"></i> Contact Information
                </h5>

                <div class="mb-3">
                    <label for="footer-address" class="form-label fw-medium text-secondary">Address</label>
                    <textarea class="form-control border-2 rounded-3 p-3" id="footer-address" name="footer_address" rows="2"><?php echo esc_textarea(get_option('datalearns_footer_address', 'Segitiga Emas Business Park<br>Jl. Prof. Dr. Satrio KAV 6<br>Jakarta Selatan')); ?></textarea>
                    <small class="text-muted mt-1 d-block">Use &lt;br&gt; for line breaks</small>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="footer-phone" class="form-label fw-medium text-secondary">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text border-2 bg-white"><i class="fas fa-phone text-muted"></i></span>
                            <input type="text" class="form-control border-2 rounded-end-3" id="footer-phone" name="footer_phone" value="<?php echo esc_attr(get_option('datalearns_footer_phone', '+62 21 579 511 32')); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="footer-email" class="form-label fw-medium text-secondary">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text border-2 bg-white"><i class="fas fa-envelope text-muted"></i></span>
                            <input type="email" class="form-control border-2 rounded-end-3" id="footer-email" name="footer_email" value="<?php echo esc_attr(get_option('datalearns_footer_email', 'info@datalearns247.com')); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer-section mb-5 p-4 bg-light rounded-4">
                <h5 class="d-flex align-items-center text-dark mb-4">
                    <i class="fas fa-share-alt me-2 text-primary"></i> Social Media Links
                </h5>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="facebook-url" class="form-label fw-medium text-secondary">Facebook URL</label>
                        <div class="input-group">
                            <input type="url" class="form-control border-2 rounded-end-3" id="facebook-url" name="facebook_url" value="<?php echo esc_url(get_option('datalearns_facebook_url', 'https://www.facebook.com/datalearns247?_rdc=1&_rdr')); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="youtube-url" class="form-label fw-medium text-secondary">YouTube URL</label>
                        <div class="input-group">
                            <input type="url" class="form-control border-2 rounded-end-3" id="youtube-url" name="youtube_url" value="<?php echo esc_url(get_option('datalearns_youtube-url', 'https://www.youtube.com/@Solusi247itsolution/')); ?>">
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="linkedin-url" class="form-label fw-medium text-secondary">LinkedIn URL</label>
                        <div class="input-group">
                            <input type="url" class="form-control border-2 rounded-end-3" id="linkedin-url" name="linkedin_url" value="<?php echo esc_url(get_option('datalearns_linkedin_url', 'https://www.linkedin.com/company/datalearns/')); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="instagram-url" class="form-label fw-medium text-secondary">Instagram URL</label>
                        <div class="input-group">
                            <input type="url" class="form-control border-2 rounded-end-3" id="instagram-url" name="instagram_url" value="<?php echo esc_url(get_option('datalearns_instagram_url', 'https://www.instagram.com/datalearns247')); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer-section p-4 bg-light rounded-4">
                <h5 class="d-flex align-items-center text-dark mb-4">
                    <i class="fas fa-link me-2 text-primary"></i> Bottom Links
                </h5>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="support-link" class="form-label fw-medium text-secondary">Support Link</label>
                        <div class="input-group">
                            <span class="input-group-text border-2 bg-white"><i class="fas fa-question-circle text-muted"></i></span>
                            <input type="url" class="form-control border-2 rounded-end-3" id="support-link" name="support_link" value="<?php echo esc_url(get_option('datalearns_support_link', 'https://solusi247.com/operation-support/')); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="terms-link" class="form-label fw-medium text-secondary">Terms of Service</label>
                        <div class="input-group">
                            <span class="input-group-text border-2 bg-white"><i class="fas fa-file-contract text-muted"></i></span>
                            <input type="url" class="form-control border-2 rounded-end-3" id="terms-link" name="terms_link" value="<?php echo esc_url(get_option('datalearns_terms_link', 'https://solusi247.com/term-of-services/')); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="contact-link" class="form-label fw-medium text-secondary">Contact Email</label>
                        <div class="input-group">
                            <span class="input-group-text border-2 bg-white"><i class="fas fa-at text-muted"></i></span>
                            <input type="email" class="form-control border-2 rounded-end-3" id="contact-link" name="contact_link" value="<?php echo esc_attr(get_option('datalearns_contact_link', 'corporate@solusi247.com')); ?>">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>



<script>
    jQuery(document).ready(function($) {
        $('#save-footer-settings').click(function(e) {
            e.preventDefault();

            if (!confirm('Apakah Anda yakin ingin menyimpan pengaturan footer?')) {
                return;
            }

            var $btn = $(this);
            var originalHtml = $btn.html();
            var formData = $('#footer-settings-form').serialize();
            formData += '&action=save_footer_settings';
            formData += '&security=<?php echo wp_create_nonce("save_footer_settings_nonce"); ?>';

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: formData,
                beforeSend: function() {
                    $btn.html('<i class="fas fa-spinner fa-spin me-2"></i> Saving...').prop('disabled', true);
                    $btn.addClass('btn-saving');
                },
                success: function(response) {
                    if (response.success) {
                        $btn.html('<i class="fas fa-check me-2"></i> Saved!');
                        $btn.removeClass('btn-primary').addClass('btn-success');

                        setTimeout(function() {
                            $btn.html(originalHtml).prop('disabled', false);
                            $btn.removeClass('btn-success').addClass('btn-primary');
                        }, 2000);
                    } else {
                        showElegantAlert('Error', 'Error saving settings: ' + response.data, 'error');
                        $btn.html(originalHtml).prop('disabled', false);
                    }
                },
                error: function() {
                    showElegantAlert('Error', 'An error occurred while saving settings.', 'error');
                    $btn.html(originalHtml).prop('disabled', false);
                }
            });
        });

        function showElegantAlert(title, message, type) {
            alert(title + ": " + message);
        }
    });
</script>