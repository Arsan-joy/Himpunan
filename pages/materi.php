<?php
require_once __DIR__ . '/../includes/functions.php';
require_login(); // materi hanya untuk user login

$page_title     = 'Materi Pembelajaran';
$additional_css = ['materi.css'];
$additional_js  = ['materi.js', 'index.js'];

include __DIR__ . '/../includes/header.php';

// Ambil materi dari DB (via Dashboard -> Kelola Materi)
$materials = db()->query("
    SELECT id, title, file_url, is_public, created_at
    FROM materials
    ORDER BY id DESC
")->fetchAll();

// Helper icon berdasarkan ekstensi
function ext_icon($url) {
    $e = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
    return match($e) {
        'pdf' => 'fa-file-pdf',
        'doc','docx' => 'fa-file-word',
        'ppt','pptx' => 'fa-file-powerpoint',
        default => 'fa-file'
    };
}
?>
<main class="materials-main">
  <div class="container">
    <section class="page-header">
      <h1><i class="fas fa-book"></i> Materi Pembelajaran</h1>
      <p>Dokumen yang diunggah melalui Dashboard Admin</p>
    </section>

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