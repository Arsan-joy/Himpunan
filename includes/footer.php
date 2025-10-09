    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Tentang Kami</h3>
                <p><?php echo SITE_DESCRIPTION; ?></p>
            </div>
            <div class="footer-section">
                <h3>Kontak</h3>
                <address>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo CONTACT_ADDRESS; ?></p>
                    <p><i class="fas fa-envelope"></i> <a href="mailto:<?php echo CONTACT_EMAIL; ?>"><?php echo CONTACT_EMAIL; ?></a></p>
                    <p><i class="fas fa-phone"></i> <a href="tel:<?php echo str_replace('-', '', CONTACT_PHONE); ?>"><?php echo CONTACT_PHONE; ?></a></p>
                </address>
            </div>
            <div class="footer-section">
                <h3>Sosial Media</h3>
                <div class="social-icons">
                    <a href="<?php echo LINKEDIN_URL; ?>" target="_blank" aria-label="LinkedIn HMTA ITERA"><i class="fab fa-linkedin-in"></i></a>
                    <a href="<?php echo INSTAGRAM_URL; ?>" target="_blank" aria-label="Instagram HMTA ITERA"><i class="fab fa-instagram"></i></a>
                    <a href="<?php echo YOUTUBE_URL; ?>" target="_blank" aria-label="YouTube HMTA ITERA"><i class="fab fa-youtube"></i></a>
                    <a href="<?php echo TIKTOK_URL; ?>" target="_blank" aria-label="TikTok HMTA ITERA"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
        </div>
    </footer>

    <!-- JS utama -->
    <script src="<?php echo asset_url('index.js'); ?>"></script>

    <!-- JS tambahan per halaman -->
    <?php foreach ($additional_js as $js): ?>
        <script src="<?php echo asset_url($js); ?>"></script>
    <?php endforeach; ?>
</body>
</html>