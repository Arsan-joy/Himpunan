<?php
// Pastikan variabel tambahan JS ada
if (!isset($additional_js) || !is_array($additional_js)) $additional_js = [];

// Fallback konstanta kontak/sosmed agar tidak fatal bila belum didefinisikan
$contact_address = defined('CONTACT_ADDRESS') ? CONTACT_ADDRESS : 'Jalan Terusan Ryacudu, Way Hui, Kecamatan Jati Agung, Lampung Selatan 35365';
$contact_email   = defined('CONTACT_EMAIL')   ? CONTACT_EMAIL   : 'hmta.balakosa.itera.com';
$contact_phone   = defined('CONTACT_PHONE')   ? CONTACT_PHONE   : '0851-3611-7417';
$linkedin_url    = defined('LINKEDIN_URL')    ? LINKEDIN_URL    : 'https://id.linkedin.com/company/himpunan-mahasiswa-teknik-pertambagan-itera-hmta-itera';
$instagram_url   = defined('INSTAGRAM_URL')   ? INSTAGRAM_URL   : 'https://www.instagram.com/hmta_itera/';
$youtube_url     = defined('YOUTUBE_URL')     ? YOUTUBE_URL     : 'https://m.youtube.com/@hmtaitera';
$tiktok_url      = defined('TIKTOK_URL')      ? TIKTOK_URL      : 'https://www.tiktok.com/@hmta.balakosa.itera';
$site_name       = defined('SITE_NAME')       ? SITE_NAME       : 'HMTA ITERA';

// Helper path JS
$jsSrc = function(string $js): string {
    if (preg_match('~^https?://|^/~', $js)) return $js;
    return (defined('JS_URL') ? JS_URL : (BASE_URL . 'Resource/js/')) . ltrim($js, '/');
};
?>
<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h3>Tentang Kami</h3>
            <p><?= htmlspecialchars(defined('SITE_DESCRIPTION') ? SITE_DESCRIPTION : 'Himpunan Mahasiswa Teknik Pertambangan ITERA') ?></p>
        </div>
        <div class="footer-section">
            <h3>Kontak</h3>
            <address>
                <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($contact_address) ?></p>
                <p><i class="fas fa-envelope"></i> <a href="mailto:<?= htmlspecialchars($contact_email) ?>"><?= htmlspecialchars($contact_email) ?></a></p>
                <p><i class="fas fa-phone"></i> <a href="tel:<?= htmlspecialchars(preg_replace('~[^0-9+]~','',$contact_phone)) ?>"><?= htmlspecialchars($contact_phone) ?></a></p>
            </address>
        </div>
        <div class="footer-section">
            <h3>Sosial Media</h3>
            <div class="social-icons">
                <a href="<?= htmlspecialchars($linkedin_url) ?>" target="_blank" aria-label="LinkedIn HMTA ITERA"><i class="fab fa-linkedin-in"></i></a>
                <a href="<?= htmlspecialchars($instagram_url) ?>" target="_blank" aria-label="Instagram HMTA ITERA"><i class="fab fa-instagram"></i></a>
                <a href="<?= htmlspecialchars($youtube_url) ?>" target="_blank" aria-label="YouTube HMTA ITERA"><i class="fab fa-youtube"></i></a>
                <a href="<?= htmlspecialchars($tiktok_url) ?>" target="_blank" aria-label="TikTok HMTA ITERA"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>
    </div>
    <div class="copyright">
        <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($site_name) ?>. All rights reserved.</p>
    </div>
</footer>

<!-- index.js sudah dimuat dengan defer di header.php — tidak perlu dimuat ulang di sini -->

<!-- JS tambahan per halaman (index.js sudah dimuat dengan defer di header.php) -->
<?php
// Filter: jangan muat ulang index.js karena sudah ada di header dengan defer
$jsToLoad = array_filter($additional_js, fn($js) => !str_contains($js, 'index.js'));
foreach ($jsToLoad as $js):
?>
    <script src="<?= $jsSrc((string)$js) ?>" defer></script>
<?php endforeach; ?>
</body>
</html>