<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title     = 'Galeri';
$additional_css = ['stylegallery.css'];
$additional_js  = ['gallery.js'];

include __DIR__ . '/../includes/header.php';

// Ambil album unik dan foto dari DB
$albums = db()->query("SELECT DISTINCT album FROM photos WHERE album IS NOT NULL AND album <> '' ORDER BY album ASC")->fetchAll(PDO::FETCH_COLUMN);
$photos = db()->query("SELECT id, album, url, caption, created_at FROM photos ORDER BY id DESC")->fetchAll();

function slugify_simple($s) {
    $s = strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9]+/','-', $s);
    return trim($s,'-');
}
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
        <button class="filter-btn active" data-filter="all">Semua</button>
        <?php foreach ($albums as $al): ?>
          <button class="filter-btn" data-filter="<?= slugify_simple($al) ?>"><?= htmlspecialchars($al) ?></button>
        <?php endforeach; ?>
      </div>
      <div class="search-box">
        <input type="text" id="gallery-search" placeholder="Cari kegiatan...">
        <button type="button" aria-label="Cari"><i class="fas fa-search"></i></button>
      </div>
    </div>
  </section>

  <section class="gallery-grid">
    <div class="container">
      <div class="gallery-items" id="galleryItems">
        <?php if ($photos): ?>
          <?php foreach ($photos as $p): $cat = slugify_simple($p['album'] ?: 'lainnya'); ?>
            <div class="gallery-item" data-category="<?= $cat ?>"
                 data-title="<?= htmlspecialchars($p['caption'] ?: 'Foto Galeri') ?>"
                 data-date="<?= htmlspecialchars(date('d M Y', strtotime($p['created_at']))) ?>"
                 data-description="<?= htmlspecialchars($p['album'] ?: '-') ?>">
              <div class="gallery-image">
                <img src="<?= htmlspecialchars($p['url']) ?>" alt="<?= htmlspecialchars($p['caption'] ?: 'Foto') ?>"
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