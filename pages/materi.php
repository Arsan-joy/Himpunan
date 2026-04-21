<?php
require_once __DIR__ . '/../includes/functions.php';
require_login(); // materi hanya untuk user login

$page_title     = 'Materi Pembelajaran';
$additional_css = ['materi.css'];
$additional_js  = ['materi.js', 'index.js'];

// ── Pagination config ────────────────────────────────────────────────────────
const MATERI_PER_PAGE = 20;

$currentPage = max(1, (int)($_GET['page'] ?? 1));

// ── Query: total item ────────────────────────────────────────────────────────
$totalItems = (int)db()->query("SELECT COUNT(*) FROM materials")->fetchColumn();
$totalPages = (int)ceil($totalItems / MATERI_PER_PAGE);

// Pastikan currentPage tidak melebihi totalPages
$currentPage = min($currentPage, max(1, $totalPages));
$offset      = ($currentPage - 1) * MATERI_PER_PAGE;

// ── Query: ambil hanya item untuk halaman ini ────────────────────────────────
$stmt = db()->prepare(
    "SELECT id, title, file_url, is_public, created_at
     FROM materials
     ORDER BY id DESC
     LIMIT ? OFFSET ?"
);
$stmt->execute([MATERI_PER_PAGE, $offset]);
$materials = $stmt->fetchAll();

// ── Helper: icon berdasarkan ekstensi file ───────────────────────────────────
function ext_icon($url) {
    $e = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
    return match($e) {
        'pdf'        => 'fa-file-pdf',
        'doc','docx' => 'fa-file-word',
        'ppt','pptx' => 'fa-file-powerpoint',
        default      => 'fa-file'
    };
}

include __DIR__ . '/../includes/header.php';
?>
<main class="materials-main">
  <div class="container">
    <section class="page-header">
      <h1><i class="fas fa-book"></i> Materi Pembelajaran</h1>
      <p>Dokumen yang diunggah melalui Dashboard Admin</p>
    </section>

    <!-- Info jumlah item -->
    <?php if ($totalItems > 0): ?>
      <p style="color:#6b7280;margin-bottom:1rem;font-size:.9rem">
        Menampilkan <?= $offset + 1 ?>–<?= min($offset + MATERI_PER_PAGE, $totalItems) ?>
        dari <?= $totalItems ?> materi
      </p>
    <?php endif; ?>

    <section class="materials-grid">
      <?php if ($materials): ?>
        <?php foreach ($materials as $m): ?>
          <div class="material-card">
            <div class="card-header">
              <div class="card-icon"><i class="fas <?= ext_icon($m['file_url']) ?>"></i></div>
              <?php if (!$m['is_public']): ?><div class="card-badge">Private</div><?php endif; ?>
            </div>
            <div class="card-content">
              <h3><?= htmlspecialchars($m['title']) ?></h3>
              <div class="card-meta">
                <span><i class="fas fa-calendar"></i> <?= htmlspecialchars(date('d M Y', strtotime($m['created_at']))) ?></span>
              </div>
            </div>
            <div class="card-actions">
              <?php if (preg_match('~\.pdf$~i', $m['file_url'])): ?>
                <button class="btn-view" onclick="viewPDF('<?= htmlspecialchars($m['file_url']) ?>')"><i class="fas fa-eye"></i> Lihat</button>
              <?php endif; ?>
              <button class="btn-download" onclick="downloadPDF('<?= htmlspecialchars($m['file_url']) ?>','<?= htmlspecialchars($m['title']) ?>')"><i class="fas fa-download"></i> Download</button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-materials">
          <h3>Tidak ada materi</h3>
          <p>Silakan tambahkan dari Dashboard.</p>
        </div>
      <?php endif; ?>
    </section>

    <!-- ── Pagination ──────────────────────────────────────────────────────── -->
    <?php if ($totalPages > 1): ?>
      <nav class="pagination" aria-label="Navigasi halaman materi">

        <!-- Tombol Previous -->
        <?php if ($currentPage > 1): ?>
          <a href="?page=<?= $currentPage - 1 ?>" class="page-btn page-prev" aria-label="Halaman sebelumnya">
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
          <a href="?page=1" class="page-btn">1</a>
          <?php if ($start > 2): ?><span class="page-ellipsis">…</span><?php endif; ?>
        <?php endif; ?>

        <?php for ($p = $start; $p <= $end; $p++): ?>
          <?php if ($p === $currentPage): ?>
            <span class="page-btn active" aria-current="page"><?= $p ?></span>
          <?php else: ?>
            <a href="?page=<?= $p ?>" class="page-btn"><?= $p ?></a>
          <?php endif; ?>
        <?php endfor; ?>

        <?php if ($end < $totalPages): ?>
          <?php if ($end < $totalPages - 1): ?><span class="page-ellipsis">…</span><?php endif; ?>
          <a href="?page=<?= $totalPages ?>" class="page-btn"><?= $totalPages ?></a>
        <?php endif; ?>

        <!-- Tombol Next -->
        <?php if ($currentPage < $totalPages): ?>
          <a href="?page=<?= $currentPage + 1 ?>" class="page-btn page-next" aria-label="Halaman berikutnya">
            Next <i class="fas fa-chevron-right"></i>
          </a>
        <?php else: ?>
          <span class="page-btn page-next disabled" aria-disabled="true">
            Next <i class="fas fa-chevron-right"></i>
          </span>
        <?php endif; ?>

      </nav>
    <?php endif; ?>
    <!-- ── End Pagination ─────────────────────────────────────────────────── -->

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