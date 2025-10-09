<?php
require_once __DIR__ . '/../includes/functions.php';

// Normalisasi supaya $additional_css/$additional_js boleh string atau array
$additional_css = isset($additional_css) ? (array)$additional_css : [];
$additional_js  = isset($additional_js)  ? (array)$additional_js  : [];
$page_title     = isset($page_title) ? $page_title : SITE_NAME;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo SITE_DESCRIPTION; ?>">
    <title><?php echo htmlspecialchars($page_title); ?> | <?php echo SITE_NAME; ?></title>

    <!-- CSS utama (pastikan file ini ada di assets/css/style.css) -->
    <link rel="stylesheet" href="<?php echo asset_url('style.css'); ?>">

    <!-- CSS tambahan per halaman (cukup isi nama file, contoh: ['materials.css']) -->
    <?php foreach ($additional_css as $css): ?>
        <link rel="stylesheet" href="<?php echo asset_url($css); ?>">
    <?php endforeach; ?>

    <link rel="icon" href="<?php echo IMG_URL; ?>IMG_1381.png" type="image/png">

    <!-- Gunakan satu versi Font Awesome (v6) di semua halaman -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-9LpZb..." crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<header>
    <a href="<?php echo BASE_URL; ?>" class="logo">
        <img src="<?php echo IMG_URL; ?>IMG_1381.png" alt="Logo HMTA">
    </a>
    <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="fas fa-bars"></i>
    </button>
    <nav id="mainNav">
        <ul>
            <li><a href="<?php echo BASE_URL; ?>pages/profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="<?php echo BASE_URL; ?>pages/struktur.php"><i class="fas fa-sitemap"></i> Struktur</a></li>
            <li class="dropdown">
                <a href="#"><i class="fas fa-calendar-alt"></i> Event <i class="fas fa-chevron-down"></i></a>
                <div class="dropdown-content">
                    <a href="<?php echo BASE_URL; ?>pages/upcoming.php"><i class="fas fa-clock"></i> Upcoming Events</a>
                    <a href="<?php echo BASE_URL; ?>pages/past.php"><i class="fas fa-history"></i> Past Events</a>
                    <a href="<?php echo BASE_URL; ?>pages/annual.php"><i class="fas fa-star"></i> Annual Programs</a>
                </div>
            </li>
            <li><a href="<?php echo BASE_URL; ?>pages/gallery.php"><i class="fas fa-images"></i> Galeri</a></li>
            <li><a href="<?php echo BASE_URL; ?>pages/materi.php"><i class="fas fa-book"></i> Materi</a></li>
            <li><a href="<?php echo BASE_URL; ?>pages/calendar.php"><i class="fas fa-calendar"></i> Kalender Akademik</a></li>
        </ul>
    </nav>
    <button class="btn-signin" onclick="window.location.href='<?php echo BASE_URL; ?>pages/login.php'">
        <i class="fas fa-sign-in-alt"></i> Sign In
    </button>
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>
</header>