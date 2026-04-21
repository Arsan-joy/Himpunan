<?php
require_once __DIR__ . '/../includes/functions.php';
require_login(); // materi hanya untuk user login

$page_title     = 'Materi Pembelajaran';
$additional_css = ['materi.css'];
$additional_js  = ['materi.js', 'index.js'];

// ── Cek kolom 'category' di tabel materials ──────────────────────────────────
// Kolom ini mungkin belum ada di hosting lama — cek dulu sebelum SELECT
function materials_has_category_col(): bool {
    try {
        $stmt = db()->query("SHOW COLUMNS FROM materials LIKE 'category'");
        return (bool)$stmt->fetch();
    } catch (Throwable $e) {
        // Fallback: DESCRIBE jika SHOW COLUMNS tidak tersedia
        try {
            $desc = db()->query("DESCRIBE materials")->fetchAll();
            foreach ($desc as $col) {
                if (isset($col['Field']) && $col['Field'] === 'category') return true;
            }
        } catch (Throwable $e2) {}
    }
    return false;
}

$hasCategoryCol = materials_has_category_col();

// ── Filter kategori dari query string (?category=kuliah) ─────────────────────
// Whitelist nilai yang diizinkan — cegah nilai sembarang masuk ke query
$validCategories = ['kuliah', 'praktikum', 'laporan_kp', 'laporan_ta'];
$filterCategory  = $_GET['category'] ?? 'all';
if ($filterCategory !== 'all' && !in_array($filterCategory, $validCategories, true)) {
    $filterCategory = 'all';
}

// ── Pagination config ────────────────────────────────────────────────────────
const MATERI_PER_PAGE = 20;

$currentPage = max(1, (int)($_GET['page'] ?? 1));

// ── Query: total item (dengan filter kategori jika aktif) ────────────────────
// Jika kolom category belum ada di DB, abaikan filter dan tampilkan semua
if ($hasCategoryCol && $filterCategory !== 'all') {
    $stmtCount = db()->prepare("SELECT COUNT(*) FROM materials WHERE category = ?");
    $stmtCount->execute([$filterCategory]);
} else {
    $stmtCount = db()->query("SELECT COUNT(*) FROM materials");
}
$totalItems = (int)$stmtCount->fetchColumn();
$totalPages = (int)ceil($totalItems / MATERI_PER_PAGE);

// Pastikan currentPage tidak melebihi totalPages
$currentPage = min($currentPage, max(1, $totalPages));
$offset      = ($currentPage - 1) * MATERI_PER_PAGE;

// ── Query: ambil item untuk halaman ini + kolom category jika ada ─────────────
$selectCols = $hasCategoryCol
    ? "id, title, file_url, is_public, category, created_at"
    : "id, title, file_url, is_public, created_at";

if ($hasCategoryCol && $filterCategory !== 'all') {
    // Filter by category + pagination
    $stmt = db()->prepare(
        "SELECT $selectCols FROM materials
         WHERE category = ?
         ORDER BY id DESC
         LIMIT ? OFFSET ?"
    );
    $stmt->execute([$filterCategory, MATERI_PER_PAGE, $offset]);
} else {
    // Semua kategori + pagination
    $stmt = db()->prepare(
        "SELECT $selectCols FROM materials
         ORDER BY id DESC
         LIMIT ? OFFSET ?"
    );
    $stmt->execute([MATERI_PER_PAGE, $offset]);
}
$materials = $stmt->fetchAll();

// ── Normalisasi kategori → slug standar ──────────────────────────────────────
// Dipakai saat kolom category belum ada: tebak dari judul/path file
function normalize_category(?string $raw, array $row): string {
    $r = strtolower(trim((string)$raw));
    // Nilai valid langsung dikembalikan
    if (in_array($r, ['kuliah', 'praktikum', 'laporan_kp', 'laporan_ta'], true)) return $r;
    if ($r === 'laporan ta') return 'laporan_ta';

    // Heuristik dari judul dan path file
    $t = strtolower((string)($row['title']    ?? ''));
    $p = strtolower((string)($row['file_url'] ?? ''));
    if (str_contains($t, 'praktikum') || str_contains($p, 'praktikum'))                    return 'praktikum';
    if (str_contains($t, 'kerja praktik') || str_contains($t, ' kp') || str_contains($p, '/kp/')) return 'laporan_kp';
    if (str_contains($t, 'tugas akhir') || str_contains($t, ' ta') || str_contains($p, 'tugas-akhir')) return 'laporan_ta';
    return 'kuliah'; // default
}

// ── Label tampilan per kategori ───────────────────────────────────────────────
function category_label(string $cat): string {
    return match($cat) {
        'kuliah'     => 'Materi Kuliah',
        'praktikum'  => 'Materi Praktikum',
        'laporan_kp' => 'Laporan KP',
        'laporan_ta' => 'Laporan TA',
        default      => ucfirst(str_replace('_', ' ', $cat)),
    };
}

// ── Helper: icon berdasarkan ekstensi file ────────────────────────────────────
function ext_icon(string $url): string {
    $e = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
    return match($e) {
        'pdf'        => 'fa-file-pdf',
        'doc','docx' => 'fa-file-word',
        'ppt','pptx' => 'fa-file-powerpoint',
        default      => 'fa-file',
    };
}

// ── Helper: bangun URL pagination dengan mempertahankan filter kategori ───────
function materi_page_url(int $page, string $category = 'all'): string {
    $params = ['page' => $page];
    if ($category !== 'all') $params['category'] = $category;
    return '?' . http_build_query($params);
}

include __DIR__ . '/../includes/header.php';
?>
<main class="materials-main" style="padding-bottom: 1rem;">
  <div class="container">
    <section class="page-header">
      <h1><i class="fas fa-book"></i> Materi Pembelajaran</h1>
      <p>Dokumen yang diunggah melalui Dashboard Admin</p>
    </section>

    <!-- ── Filter Kategori ───────────────────────────────────────────────── -->
    <!-- Filter menggunakan <a href> agar pagination tetap bekerja saat filter aktif -->
    <section class="filter-section">
      <div class="filter-container">
        <a href="?category=all"
           class="filter-btn <?= $filterCategory === 'all' ? 'active' : '' ?>">
          <i class="fas fa-layer-group"></i> Semua
        </a>
        <a href="<?= materi_page_url(1, 'kuliah') ?>"
           class="filter-btn <?= $filterCategory === 'kuliah' ? 'active' : '' ?>">
          <i class="fas fa-chalkboard-teacher"></i> Materi Kuliah
        </a>
        <a href="<?= materi_page_url(1, 'praktikum') ?>"
           class="filter-btn <?= $filterCategory === 'praktikum' ? 'active' : '' ?>">
          <i class="fas fa-tools"></i> Materi Praktikum
        </a>
        <a href="<?= materi_page_url(1, 'laporan_kp') ?>"
           class="filter-btn <?= $filterCategory === 'laporan_kp' ? 'active' : '' ?>">
          <i class="fas fa-briefcase"></i> Laporan KP
        </a>
        <a href="<?= materi_page_url(1, 'laporan_ta') ?>"
           class="filter-btn <?= $filterCategory === 'laporan_ta' ? 'active' : '' ?>">
          <i class="fas fa-graduation-cap"></i> Laporan TA
        </a>
      </div>
    </section>

    <!-- ── Info jumlah item ──────────────────────────────────────────────── -->
    <?php if ($totalItems > 0): ?>
      <p style="color:#6b7280;margin-bottom:1rem;font-size:.9rem">
        Menampilkan <?= $offset + 1 ?>–<?= min($offset + MATERI_PER_PAGE, $totalItems) ?>
        dari <?= $totalItems ?> materi
        <?php if ($filterCategory !== 'all'): ?>
          · Kategori: <strong><?= htmlspecialchars(category_label($filterCategory)) ?></strong>
        <?php endif; ?>
      </p>
    <?php endif; ?>

    <!-- ── Grid Materi ───────────────────────────────────────────────────── -->
    <section class="materials-grid" id="materialsGrid">
      <?php if ($materials): ?>
        <?php foreach ($materials as $m):
          // Tentukan kategori: dari kolom DB jika ada, fallback heuristik
          $catRaw = $hasCategoryCol ? ($m['category'] ?? null) : null;
          $cat    = normalize_category($catRaw, $m);
        ?>
          <div class="material-card" data-category="<?= htmlspecialchars($cat) ?>">
            <div class="card-header">
              <div class="card-icon"><i class="fas <?= ext_icon($m['file_url']) ?>"></i></div>
              <?php if (!(int)$m['is_public']): ?><div class="card-badge">Private</div><?php endif; ?>
            </div>
            <div class="card-content">
              <h3><?= htmlspecialchars($m['title']) ?></h3>
              <div class="card-meta">
                <span><i class="fas fa-calendar"></i> <?= htmlspecialchars(date('d M Y', strtotime($m['created_at']))) ?></span>
                <span><i class="fas fa-tag"></i> <?= htmlspecialchars(category_label($cat)) ?></span>
              </div>
            </div>
            <div class="card-actions">
              <?php if (preg_match('~\.pdf$~i', $m['file_url'])): ?>
                <button class="btn-view"
                        onclick="viewPDF('<?= htmlspecialchars($m['file_url'], ENT_QUOTES) ?>')">
                  <i class="fas fa-eye"></i> Lihat
                </button>
              <?php endif; ?>
              <button class="btn-download"
                      onclick="downloadPDF('<?= htmlspecialchars($m['file_url'], ENT_QUOTES) ?>','<?= htmlspecialchars($m['title'], ENT_QUOTES) ?>')">
                <i class="fas fa-download"></i> Download
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-materials">
          <h3>Tidak ada materi</h3>
          <p>
            <?= $filterCategory !== 'all'
              ? 'Belum ada materi untuk kategori ' . htmlspecialchars(category_label($filterCategory)) . '.'
              : 'Silakan tambahkan dari Dashboard.' ?>
          </p>
        </div>
      <?php endif; ?>
    </section>

    <!-- ── Pagination ────────────────────────────────────────────────────── -->
    <!-- URL pagination menyertakan filter kategori agar tidak hilang saat ganti halaman -->
    <?php if ($totalPages > 1): ?>
      <nav class="pagination" aria-label="Navigasi halaman materi">

        <!-- Tombol Previous -->
        <?php if ($currentPage > 1): ?>
          <a href="<?= materi_page_url($currentPage - 1, $filterCategory) ?>"
             class="page-btn page-prev" aria-label="Halaman sebelumnya">
            <i class="fas fa-chevron-left"></i> Prev
          </a>
        <?php else: ?>
          <span class="page-btn page-prev disabled" aria-disabled="true">
            <i class="fas fa-chevron-left"></i> Prev
          </span>
        <?php endif; ?>

        <!-- Nomor halaman dengan ellipsis -->
        <?php
        $range = 2;
        $start = max(1, $currentPage - $range);
        $end   = min($totalPages, $currentPage + $range);
        ?>

        <?php if ($start > 1): ?>
          <a href="<?= materi_page_url(1, $filterCategory) ?>" class="page-btn">1</a>
          <?php if ($start > 2): ?><span class="page-ellipsis">…</span><?php endif; ?>
        <?php endif; ?>

        <?php for ($p = $start; $p <= $end; $p++): ?>
          <?php if ($p === $currentPage): ?>
            <span class="page-btn active" aria-current="page"><?= $p ?></span>
          <?php else: ?>
            <a href="<?= materi_page_url($p, $filterCategory) ?>" class="page-btn"><?= $p ?></a>
          <?php endif; ?>
        <?php endfor; ?>

        <?php if ($end < $totalPages): ?>
          <?php if ($end < $totalPages - 1): ?><span class="page-ellipsis">…</span><?php endif; ?>
          <a href="<?= materi_page_url($totalPages, $filterCategory) ?>" class="page-btn"><?= $totalPages ?></a>
        <?php endif; ?>

        <!-- Tombol Next -->
        <?php if ($currentPage < $totalPages): ?>
          <a href="<?= materi_page_url($currentPage + 1, $filterCategory) ?>"
             class="page-btn page-next" aria-label="Halaman berikutnya">
            Next <i class="fas fa-chevron-right"></i>
          </a>
        <?php else: ?>
          <span class="page-btn page-next disabled" aria-disabled="true">
            Next <i class="fas fa-chevron-right"></i>
          </span>
        <?php endif; ?>

      </nav>
    <?php endif; ?>
    <!-- ── End Pagination ────────────────────────────────────────────────── -->

  </div>
</main>

<!-- PDF Modal -->
<div id="pdfModal" class="modal" style="display:none;opacity:0">
  <div class="modal-content">
    <div class="modal-header">
      <h3 id="pdfTitle">PDF Viewer</h3>
      <span class="close" onclick="closePDFModal()">&times;</span>
    </div>
    <div class="modal-body">
      <iframe id="pdfViewer" src="" width="100%" height="600"></iframe>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
