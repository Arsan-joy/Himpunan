<?php
// Guard agar header tidak dicetak dua kali jika ter-include ganda
if (defined('HMTA_HEADER_PRINTED')) return;
define('HMTA_HEADER_PRINTED', true);

require_once __DIR__ . '/functions.php';
if (!isset($page_title)) $page_title = '';
if (!isset($additional_css) || !is_array($additional_css)) $additional_css = [];
if (!isset($additional_js)  || !is_array($additional_js))  $additional_js  = [];
$site_name = defined('SITE_NAME') ? SITE_NAME : 'HMTA ITERA';
$site_desc = defined('SITE_DESCRIPTION') ? SITE_DESCRIPTION : 'Himpunan Mahasiswa Teknik Pertambangan ITERA';
$cssHref = fn($css)=> (preg_match('~^https?://|^/~',$css)?$css:(CSS_URL ?? (BASE_URL.'Resource/css/')).ltrim($css,'/'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="<?= htmlspecialchars($site_desc) ?>">
<title><?= htmlspecialchars($page_title ?: 'Halaman') ?> | <?= htmlspecialchars($site_name) ?></title>
<link rel="stylesheet" href="<?= (CSS_URL ?? (BASE_URL.'Resource/css/')) ?>style.css">
<?php foreach ($additional_css as $css): ?><link rel="stylesheet" href="<?= $cssHref((string)$css) ?>"><?php endforeach; ?>
<link rel="icon" href="<?= (IMG_URL ?? (BASE_URL.'Resource/')) ?>IMG_1381.png" type="image/png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<!-- Pastikan hamburger JS selalu termuat -->
<script defer src="<?= BASE_URL ?>Resource/js/index.js"></script>
</head>
<body>
<header>
  <a href="<?= BASE_URL ?>" class="logo"><img src="<?= (IMG_URL ?? (BASE_URL.'Resource/')) ?>IMG_1381.png" alt="Logo HMTA"></a>
  <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="menu"><i class="fas fa-bars"></i></button>
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
      <li><a href="<?= BASE_URL ?>pages/materi.php"><i class="fas fa-book"></i> MIROTA</a></li>
      <li><a href="<?= BASE_URL ?>pages/calendar.php"><i class="fas fa-calendar"></i> Kalender Akademik</a></li>
    </ul>
  </nav>
<?php // DEBUG sementara
// echo '<!-- is_logged_in='. (is_logged_in()?'YES':'NO') .' -->';
?>
  <?php if (is_logged_in()): ?>
    <div style="display:flex; gap:8px; align-items:center;">
      <button class="btn-signin" onclick="location.href='<?= BASE_URL ?>admin/logout.php'"><i class="fas fa-sign-out-alt"></i> Logout</button>
    </div>
  <?php else: ?>
    <div style="display:flex; gap:8px;">
      <button class="btn-signin" onclick="location.href='<?= BASE_URL ?>pages/login.php'"><i class="fas fa-user"></i> Login</button>
    </div>
  <?php endif; ?>
  <div class="mobile-menu-overlay" id="mobileMenuOverlay" aria-hidden="true"></div>
</header>