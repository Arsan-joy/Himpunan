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

// Maintenance: aktifkan overlay jika global atau untuk halaman yang ditargetkan (parsial)
$is_maintenance = function_exists('is_maintenance_active') ? is_maintenance_active() : false;
// Untuk meta informasi (optional)
$maintenance = function_exists('get_maintenance_status') ? get_maintenance_status() : ['enabled' => false, 'pages' => [], 'updated_by' => '', 'updated_at' => ''];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="<?= htmlspecialchars($site_desc) ?>">
<title><?= htmlspecialchars($page_title ?: 'Halaman') ?> | <?= htmlspecialchars($site_name) ?></title>
<?php
// Cache busting: gunakan hash dari file jika ada, fallback ke filemtime
$_cssFile = defined('ROOT_PATH') ? ROOT_PATH . '/Resource/css/style.css' : (dirname(__DIR__) . '/Resource/css/style.css');
$_jsFile  = defined('ROOT_PATH') ? ROOT_PATH . '/Resource/js/index.js'   : (dirname(__DIR__) . '/Resource/js/index.js');
$_cssVer  = file_exists($_cssFile) ? substr(md5_file($_cssFile), 0, 8) : '1';
$_jsVer   = file_exists($_jsFile)  ? substr(md5_file($_jsFile),  0, 8) : '1';
?>
<!-- Preconnect untuk CDN Font Awesome -->
<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
<!-- Preload LCP image (banner pertama) -->
<link rel="preload" as="image" href="<?= (IMG_URL ?? (BASE_URL.'Resource/img/')) ?>banner1.png" fetchpriority="high">
<link rel="stylesheet" href="<?= (CSS_URL ?? (BASE_URL.'Resource/css/')) ?>style.css?v=<?= $_cssVer ?>">
<?php foreach ($additional_css as $css): ?><link rel="stylesheet" href="<?= $cssHref((string)$css) ?>"><?php endforeach; ?>
<link rel="icon" href="<?= (IMG_URL ?? (BASE_URL.'Resource/')) ?>IMG_1381.png" type="image/png">
<!-- Font Awesome: media=print trick agar tidak blokir render, lalu swap ke all -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
      media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"></noscript>
<!-- JS utama — defer agar tidak blokir rendering, dimuat SATU kali di sini -->
<script defer src="<?= BASE_URL ?>Resource/js/index.js?v=<?= $_jsVer ?>"></script>

<style>
  /* ── Critical CSS inline: render above-the-fold tanpa blokir ── */
  /* Hanya style yang dibutuhkan sebelum JS/CSS eksternal selesai dimuat */
  :root{--primary-color:#2c3e50;--secondary-color:#3498db;--accent-color:#e74c3c}
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;color:#333;line-height:1.6;overflow-x:hidden;min-height:100vh;display:flex;flex-direction:column}
  main{flex:1}
  header{background-color:var(--primary-color);color:#fff;padding:.8rem 2rem;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:100;box-shadow:0 2px 10px rgba(0,0,0,.2);width:100%;min-height:80px}
  .logo{display:flex;align-items:center;text-decoration:none;flex-shrink:0}
  .logo img{height:60px;width:auto;object-fit:contain}
  .mobile-menu-btn{display:none;background:none;border:none;font-size:1.5rem;color:#fff;cursor:pointer;padding:.5rem}
  @media(max-width:768px){.mobile-menu-btn{display:block;z-index:1100}}

  .maintenance-overlay {
    position: fixed; inset: 0; background: rgba(20,27,37,0.92); color: #ecf0f1;
    display: none; align-items: center; justify-content: center; z-index: 9999;
    text-align: center; padding: 2rem;
  }
  .maintenance-overlay.active { display: flex; }
  .maintenance-box { display: flex; flex-direction: column; align-items: center; gap: 1.25rem; max-width: 720px; }
  .gear {
    width: 110px; height: 110px; border: 10px solid #3498db; border-radius: 50%;
    position: relative; animation: spin 2.5s linear infinite;
    box-shadow: 0 0 25px rgba(52,152,219,0.4), inset 0 0 12px rgba(52,152,219,0.2);
  }
  .gear:before, .gear:after {
    content: ''; position: absolute; left: 50%; top: 50%; transform: translate(-50%,-50%);
    width: 6px; height: 6px; background: #3498db; border-radius: 50%;
    box-shadow:
      -55px 0 0 0 #3498db, 55px 0 0 0 #3498db,
      0 -55px 0 0 #3498db, 0 55px 0 0 #3498db,
      -39px -39px 0 0 #3498db, 39px -39px 0 0 #3498db,
      -39px 39px 0 0 #3498db, 39px 39px 0 0 #3498db;
  }
  @keyframes spin { to { transform: rotate(360deg); } }
  .maintenance-title { font-size: 1.8rem; font-weight: 700; color: #ecf0f1; }
  .maintenance-desc { max-width: 640px; color: #bdc3c7; }
  .maintenance-meta { font-size: .9rem; color: #95a5a6; margin-top: .5rem; }
  body.maintenance-no-scroll { overflow: hidden !important; height: 100vh; }
</style>
</head>
<body class="<?= $is_maintenance ? 'maintenance-no-scroll' : '' ?>">
<header>
  <a href="<?= BASE_URL ?>" class="logo"><img src="<?= (IMG_URL ?? (BASE_URL.'Resource/')) ?>IMG_1381.png" alt="Logo HMTA" width="60" height="60" fetchpriority="high"></a>
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

<div class="maintenance-overlay <?= $is_maintenance ? 'active' : '' ?>" role="dialog" aria-live="polite" aria-label="Sedang Maintenance">
  <div class="maintenance-box">
    <div class="gear" aria-hidden="true"></div>
    <div class="maintenance-title">Sedang Maintenance</div>
    <div class="maintenance-desc">Website HMTA ITERA sedang dalam proses pemeliharaan. Harap kembali beberapa saat lagi.</div>
    <?php if (!empty($maintenance['updated_at']) || !empty($maintenance['updated_by'])): ?>
      <div class="maintenance-meta">
        Diperbarui: <?= htmlspecialchars($maintenance['updated_at'] ?: '-') ?>
        <?php if (!empty($maintenance['updated_by'])): ?> oleh <?= htmlspecialchars($maintenance['updated_by']) ?><?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</div>