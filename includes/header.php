<?php
// Pastikan helper tersedia
require_once __DIR__ . '/functions.php';

// Normalisasi variabel halaman
if (!isset($page_title)) $page_title = '';
if (!isset($additional_css) || !is_array($additional_css)) $additional_css = [];
if (!isset($additional_js)  || !is_array($additional_js))  $additional_js  = [];

// Fallback bila konstanta meta tidak didefinisikan
$site_name = defined('SITE_NAME') ? SITE_NAME : 'HMTA ITERA';
$site_desc = defined('SITE_DESCRIPTION') ? SITE_DESCRIPTION : 'Himpunan Mahasiswa Teknik Pertambangan ITERA';

// Helper kecil untuk path asset tambahan (boleh URL absolut atau nama file di Resource/css)
$cssHref = function(string $css): string {
    if (preg_match('~^https?://|^/~', $css)) return $css;
    return (defined('CSS_URL') ? CSS_URL : (BASE_URL . 'Resource/css/')) . ltrim($css, '/');
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($site_desc) ?>">
    <title><?= htmlspecialchars($page_title ?: 'Halaman') ?> | <?= htmlspecialchars($site_name) ?></title>

    <!-- CSS utama -->
    <link rel="stylesheet" href="<?= (defined('CSS_URL') ? CSS_URL : (BASE_URL . 'Resource/css/')) ?>style.css">

    <!-- CSS tambahan per halaman -->
    <?php foreach ($additional_css as $css): ?>
        <link rel="stylesheet" href="<?= $cssHref((string)$css) ?>">
    <?php endforeach; ?>

    <link rel="icon" href="<?= (defined('IMG_URL') ? IMG_URL : (BASE_URL . 'Resource/img/')) ?>IMG_1381.png" type="image/png">

    <!-- Font Awesome v6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" referrerpolicy="no-referrer" />
</head>
<body>
<header>
    <a href="<?= BASE_URL ?>" class="logo">
        <img src="<?= (defined('IMG_URL') ? IMG_URL : (BASE_URL . 'Resource/img/')) ?>IMG_1381.png" alt="Logo HMTA">
    </a>

    <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Buka menu">
        <i class="fas fa-bars"></i>
    </button>

    <nav id="mainNav">
        <ul>
            <li><a href="<?= BASE_URL ?>pages/profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="<?= BASE_URL ?>pages/struktur.php"><i class="fas fa-sitemap"></i> Struktur</a></li>
            <li class="dropdown">
                <a href="#"><i class="fas fa-calendar-alt"></i> Event <i class="fas fa-chevron-down"></i></a>
                <div class="dropdown-content">
                    <a href="<?= BASE_URL ?>pages/upcoming.php"><i class="fas fa-clock"></i> Upcoming Events</a>
                    <a href="<?= BASE_URL ?>pages/past.php"><i class="fas fa-history"></i> Past Events</a>
                    <a href="<?= BASE_URL ?>pages/annual.php"><i class="fas fa-star"></i> Annual Programs</a>
                </div>
            </li>
            <li><a href="<?= BASE_URL ?>pages/gallery.php"><i class="fas fa-images"></i> Galeri</a></li>
            <li><a href="<?= BASE_URL ?>pages/materi.php"><i class="fas fa-book"></i> Materi</a></li>
            <li><a href="<?= BASE_URL ?>pages/calendar.php"><i class="fas fa-calendar"></i> Kalender Akademik</a></li>
        </ul>
    </nav>

    <?php if (is_logged_in()): ?>
        <div style="display:flex; gap:8px; align-items:center;">
            <?php if (is_admin()): ?>
                <button class="btn-signin" onclick="window.location.href='<?= BASE_URL ?>admin/'">
                    <i class="fas fa-gauge"></i> Dashboard
                </button>
            <?php endif; ?>
            <button class="btn-signin" onclick="window.location.href='<?= BASE_URL ?>admin/logout.php'">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </div>
    <?php else: ?>
        <button class="btn-signin" onclick="window.location.href='<?= BASE_URL ?>admin/login.php'">
            <i class="fas fa-sign-in-alt"></i> Sign In
        </button>
    <?php endif; ?>

    <div class="mobile-menu-overlay" id="mobileMenuOverlay" aria-hidden="true"></div>
</header>