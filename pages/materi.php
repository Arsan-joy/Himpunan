<?php
require_once __DIR__ . '/../includes/functions.php';

// Pastikan halaman Materi juga memakai header.php yang sama
$page_title     = 'Materi Pembelajaran';
$additional_css = ['materi.css']; // file ini ada di assets/css/materi.css
$additional_js  = ['../Resource/materi.js']; // file ini ada di assets/js/materi.js

include __DIR__ . '/../includes/header.php';
?>
<main class="materials-main">
    <div class="container">
        <section class="page-header">
            <h1><i class="fas fa-book"></i> Materi Pembelajaran</h1>
            <p>Koleksi materi pembelajaran dan dokumen penting untuk mahasiswa Teknik Pertambangan</p>
        </section>
        <!-- Filter Section -->
        <section class="filter-section">
            <div class="filter-container">
                <button class="filter-btn <?php echo $category === 'all' ? 'active' : ''; ?>" data-category="all">
                    <i class="fas fa-list"></i> Semua
                </button>
                <button class="filter-btn <?php echo $category === 'kuliah' ? 'active' : ''; ?>" data-category="kuliah">
                    <i class="fas fa-graduation-cap"></i> Kuliah
                </button>
                <button class="filter-btn <?php echo $category === 'praktikum' ? 'active' : ''; ?>" data-category="praktikum">
                    <i class="fas fa-flask"></i> Praktikum
                </button>
                <button class="filter-btn <?php echo $category === 'seminar' ? 'active' : ''; ?>" data-category="seminar">
                    <i class="fas fa-presentation"></i> Seminar
                </button>
                <button class="filter-btn <?php echo $category === 'workshop' ? 'active' : ''; ?>" data-category="workshop">
                    <i class="fas fa-tools"></i> Workshop
                </button>
            </div>
        </section>

        <!-- Materials Grid -->
        <section class="materials-grid">
            <?php if (!empty($materials)): ?>
                <?php foreach ($materials as $material): ?>
                    <div class="material-card" data-category="<?php echo $material['category']; ?>">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <div class="card-badge"><?php echo ucfirst($material['category']); ?></div>
                        </div>
                        <div class="card-content">
                            <h3><?php echo htmlspecialchars($material['title']); ?></h3>
                            <p class="card-description"><?php echo htmlspecialchars($material['description']); ?></p>
                            <div class="card-meta">
                                <span><i class="fas fa-calendar"></i> <?php echo $hmta->formatDateIndonesian($material['created_at']); ?></span>
                                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($material['author']); ?></span>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="btn-view" onclick="viewPDF('<?php echo getBaseUrl() . $material['file_path']; ?>')">
                                <i class="fas fa-eye"></i> Lihat
                            </button>
                            <button class="btn-download" onclick="downloadPDF('<?php echo getBaseUrl() . $material['file_path']; ?>', '<?php echo $material['title']; ?>.pdf')">
                                <i class="fas fa-download"></i> Download
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-materials">
                    <h3>Tidak ada materi tersedia</h3>
                    <p>Materi untuk kategori ini belum tersedia.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>

<!-- PDF Modal -->
<div id="pdfModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="pdfTitle">PDF Viewer</h3>
            <span class="close" onclick="closePDFModal()">&times;</span>
        </div>
        <div class="modal-body">
            <iframe id="pdfViewer" src="" width="100%" height="600px"></iframe>
        </div>
    </div>
</div>



<?php include '../includes/footer.php'; ?>