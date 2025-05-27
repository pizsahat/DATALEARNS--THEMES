  <footer class="modern-footer">
      <div class="container">
          <div class="footer-grid">
              <div class="footer-column footer-about">
                  <a href="https://solusi247.com/" class="footer-logo-link">
                      <img src="<?php echo get_template_directory_uri(); ?>/images/logoweb247-white.png" alt="PT. Solusi 247 Logo" class="footer-logo">
                  </a>
                  <p class="footer-description"><?php echo esc_html(get_option('datalearns_footer_description', 'Sebuah program edukasi yang disusun oleh Solusi247 untuk membangun talenta data Indonesia')); ?></p>

                  <div class="footer-contact">
                      <div class="contact-item">
                          <i class="fa fa-map-marker"></i>
                          <span><?php echo get_option('datalearns_footer_address', 'Segitiga Emas Business Park<br>Jl. Prof. Dr. Satrio KAV 6<br>Jakarta Selatan'); ?></span>
                      </div>
                      <div class="contact-item">
                          <i class="fa fa-phone"></i>
                          <span><?php echo esc_html(get_option('datalearns_footer_phone', '+62 21 579 511 32')); ?></span>
                      </div>
                      <div class="contact-item">
                          <i class="fa fa-envelope"></i>
                          <span><?php echo esc_html(get_option('datalearns_footer_email', 'info@datalearns247.com')); ?></span>
                      </div>
                  </div>
              </div>
              <div class="footer-column footer-social">
                  <h3 class="footer-heading">Connect With Us</h3>
                  <div class="social-links">
                      <a href="<?php echo esc_url(get_option('datalearns_facebook_url', 'https://www.facebook.com/datalearns247?_rdc=1&_rdr')); ?>" class="social-link facebook" aria-label="Facebook">
                          <i class="fa fa-facebook-f"></i>
                      </a>
                      <a href="<?php echo esc_url(get_option('datalearns_youtube_url', 'https://www.youtube.com/@Solusi247itsolution/')); ?>" class="social-link youtube" aria-label="YouTube">
                          <i class="fa fa-youtube"></i>
                      </a>
                      <a href="<?php echo esc_url(get_option('datalearns_linkedin_url', 'https://www.linkedin.com/company/datalearns/')); ?>" class="social-link linkedin" aria-label="LinkedIn">
                          <i class="fa fa-linkedin"></i>
                      </a>
                      <a href="<?php echo esc_url(get_option('datalearns_instagram_url', 'https://www.instagram.com/datalearns247')); ?>" class="social-link instagram" aria-label="Instagram">
                          <i class="fa fa-instagram"></i>
                      </a>
                  </div>
              </div>
          </div>
          <div class="footer-bottom">
              <div class="copyright">
                  &copy; <?php echo date('Y'); ?> PT. Solusi 247. All rights reserved.
              </div>
              <div class="footer-links">
                  <a href="<?php echo esc_url(get_option('support_link', 'https://solusi247.com/operation-support/')); ?>">Support</a>
                  <a href="<?php echo esc_url(get_option('terms_link', 'https://solusi247.com/term-of-services/')); ?>">Terms of Service</a>
                  <a href="mailto:<?php echo esc_attr(get_option('contact_link', 'corporate@solusi247.com')); ?>">Contact Us</a>
              </div>
          </div>
      </div>
  </footer>