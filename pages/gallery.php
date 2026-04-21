<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title     = 'Galeri';
$additional_css = ['stylegallery.css'];
$additional_js  = ['gallery.js'];

// ── Pagination config ────────────────────────────────────────────────────────
const GALLERY_PER_PAGE = 10;

// Ambil nomor halaman dari query string; pastikan integer positif
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$offset      = ($currentPage - 1) * GALLERY_PER_PAGE;

// Filter album (opsional — diteruskan lewat ?album=xxx agar pagination tetap dalam album)
$filterAlbum = trim($_GET['album'] ?? '');

// ── Query: total item (untuk hitung jumlah halaman) ──────────────────────────
if ($filterAlbum !== '') {
    $stmtCount = db()->prepare("SELECT COUNT(*) FROM photos WHERE album = ?");
    $stmtCount->execute([$filterAlbum]);
} else {
    $stmtCount = db()->query("SELECT COUNT(*) FROM photos");
}
$totalItems = (int)$stmtCount->fetchColumn();
$totalPages = (int)ceil($totalItems / GALLERY_PER_PAGE);

// Pastikan currentPage tidak melebihi totalPages
$currentPage = min($currentPage, max(1, $totalPages));
$offset      = ($currentPage - 1) * GALLERY_PER_PAGE;

// ── Query: ambil hanya item untuk halaman ini (LIMIT + OFFSET) ───────────────
if ($filterAlbum !== '') {
    $stmtPhotos = db()->prepare(
        "SELECT id, album, url, caption, created_at
         FROM photos
         WHERE album = ?
         ORDER BY id DESC
         LIMIT ? OFFSET ?"
    );
    $stmtPhotos->execute([$filterAlbum, GALLERY_PER_PAGE, $offset]);
} else {
    $stmtPhotos = db()->prepare(
        "SELECT id, album, url, caption, created_at
         FROM photos
         ORDER BY id DESC
         LIMIT ? OFFSET ?"
    );
    $stmtPhotos->execute([GALLERY_PER_PAGE, $offset]);
}
$photos = $stmtPhotos->fetchAll();

// ── Album list untuk filter buttons (tidak perlu pagination) ─────────────────
$albums = db()->query(
    "SELECT DISTINCT album FROM photos WHERE album IS NOT NULL AND album <> '' ORDER BY album ASC"
)->fetchAll(PDO::FETCH_COLUMN);

// ── Helper: slug untuk data-category ─────────────────────────────────────────
function slugify_simple($s) {
    $s = strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    return trim($s, '-');
}

// ── Helper: bangun URL pagination dengan mempertahankan query string lain ─────
function pagination_url(int $page, string $album = ''): string {
    $params = ['page' => $page];
    if ($album !== '') $params['album'] = $album;
    return '?' . http_build_query($params);
}

include __DIR__ . '/../includes/header.php';
?>
<main>
  <section class="gallery-hero">
    <div class="container">
      <h1>Galeri Kegiatan HMTA ITERA</h1>
      <p>Foto-foto yang diunggah melalui Dashboard Admin</p>
    </div>
  </section>

  <section class="gallery-filter">
    <div class="container">
      <div class="filter-buttons">
        <!-- Filter menggunakan button + data-filter (style DADS) -->
        <!-- JS gallery.js menangani show/hide berdasarkan data-category pada item -->
        <button class="filter-btn active" data-filter="all">Semua</button>
        <?php foreach ($albums as $al): ?>
          <button class="filter-btn" data-filter="<?= slugify_simple($al) ?>">
            <?= htmlspecialchars($al) ?>
          </button>
        <?php endforeach; ?>
      </div>
      <div class="search-box">
        <input type="text" id="gallery-search" placeholder="Cari kegiatan...">
        <button type="button" aria-label="Cari"><i class="fas fa-search"></i></button>
      </div>
    </div>
  </section>

  <section class="gallery-grid" style="padding-bottom: 3rem;">
    <div class="container">
      <!-- Info jumlah item -->
      <?php if ($totalItems > 0): ?>
        <p class="gallery-info" style="color:#6b7280;margin-bottom:1rem;font-size:.9rem">
          Menampilkan <?= $offset + 1 ?>–<?= min($offset + GALLERY_PER_PAGE, $totalItems) ?>
          dari <?= $totalItems ?> foto
          <?= $filterAlbum !== '' ? '· Album: <strong>' . htmlspecialchars($filterAlbum) . '</strong>' : '' ?>
        </p>
      <?php endif; ?>

      <div class="gallery-items" id="galleryItems">
        <?php if ($photos): ?>
          <?php foreach ($photos as $p): $cat = slugify_simple($p['album'] ?: 'lainnya'); ?>
            <div class="gallery-item" data-category="<?= $cat ?>"
                 data-title="<?= htmlspecialchars($p['caption'] ?: 'Foto Galeri') ?>"
                 data-date="<?= htmlspecialchars(date('d M Y', strtotime($p['created_at']))) ?>"
                 data-description="<?= htmlspecialchars($p['album'] ?: '-') ?>">
              <div class="gallery-image">
                <img src="<?= htmlspecialchars($p['url']) ?>" alt="<?= htmlspecialchars($p['caption'] ?: 'Foto') ?>"
                     loading="lazy" width="400" height="300"
                     onerror="this.src='https://via.placeholder.com/400x300?text=HMTA'">
                <div class="overlay">
                  <div class="overlay-content">
                    <h3><?= htmlspecialchars($p['caption'] ?: 'Foto Galeri') ?></h3>
                    <p><?= htmlspecialchars(date('M Y', strtotime($p['created_at']))) ?></p>
                    <button class="view-btn" type="button"><i class="fas fa-eye"></i> Lihat</button>
                  </div>
                </div>
              </div>
              <div class="gallery-info">
                <h3><?= htmlspecialchars($p['caption'] ?: 'Foto Galeri') ?></h3>
                <p><?= htmlspecialchars($p['album'] ?: '-') ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="no-items">Belum ada foto. Tambahkan dari Dashboard.</div>
        <?php endif; ?>
      </div>

      <!-- ── Pagination ──────────────────────────────────────────────────── -->
      <?php if ($totalPages > 1): ?>
        <nav class="pagination" aria-label="Navigasi halaman galeri">

          <!-- Tombol Previous -->
          <?php if ($currentPage > 1): ?>
            <a href="<?= pagination_url($currentPage - 1, $filterAlbum) ?>" class="page-btn page-prev" aria-label="Halaman sebelumnya">
              <i class="fas fa-chevron-left"></i> Prev
            </a>
          <?php else: ?>
            <span class="page-btn page-prev disabled" aria-disabled="true">
              <i class="fas fa-chevron-left"></i> Prev
            </span>
          <?php endif; ?>

          <!-- Nomor halaman dengan ellipsis untuk halaman banyak -->
          <?php
          // Tampilkan maks 5 nomor halaman di sekitar halaman aktif
          $range  = 2; // halaman kiri & kanan dari current
          $start  = max(1, $currentPage - $range);
          $end    = min($totalPages, $currentPage + $range);
          ?>

          <?php if ($start > 1): ?>
            <a href="<?= pagination_url(1, $filterAlbum) ?>" class="page-btn">1</a>
            <?php if ($start > 2): ?><span class="page-ellipsis">…</span><?php endif; ?>
          <?php endif; ?>

          <?php for ($p = $start; $p <= $end; $p++): ?>
            <?php if ($p === $currentPage): ?>
              <span class="page-btn active" aria-current="page"><?= $p ?></span>
            <?php else: ?>
              <a href="<?= pagination_url($p, $filterAlbum) ?>" class="page-btn"><?= $p ?></a>
            <?php endif; ?>
          <?php endfor; ?>

          <?php if ($end < $totalPages): ?>
            <?php if ($end < $totalPages - 1): ?><span class="page-ellipsis">…</span><?php endif; ?>
            <a href="<?= pagination_url($totalPages, $filterAlbum) ?>" class="page-btn"><?= $totalPages ?></a>
          <?php endif; ?>

          <!-- Tombol Next -->
          <?php if ($currentPage < $totalPages): ?>
            <a href="<?= pagination_url($currentPage + 1, $filterAlbum) ?>" class="page-btn page-next" aria-label="Halaman berikutnya">
              Next <i class="fas fa-chevron-right"></i>
            </a>
          <?php else: ?>
            <span class="page-btn page-next disabled" aria-disabled="true">
              Next <i class="fas fa-chevron-right"></i>
            </span>
          <?php endif; ?>

        </nav>
      <?php endif; ?>
      <!-- ── End Pagination ─────────────────────────────────────────────── -->

    </div>
  </section>

  <!-- Lightbox Modal -->
  <div class="modal" id="galleryModal" aria-hidden="true">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="modal-title">
      <button class="close-modal" type="button" aria-label="Tutup">&times;</button>
      <div class="modal-image-container">
        <img id="modal-image" src="" alt="">
        <button class="nav-btn prev-btn" type="button" aria-label="Sebelumnya"><i class="fas fa-chevron-left"></i></button>
        <button class="nav-btn next-btn" type="button" aria-label="Berikutnya"><i class="fas fa-chevron-right"></i></button>
      </div>
      <div class="modal-info">
        <h2 id="modal-title"></h2>
        <p id="modal-date"></p>
        <p id="modal-description"></p>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>