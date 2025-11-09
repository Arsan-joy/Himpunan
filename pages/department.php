<?php
require_once __DIR__ . '/../includes/functions.php';

// Defensive include: if helper file missing, show a clear error
$helpersPath = __DIR__ . '/../includes/department_helpers.php';
if (!file_exists($helpersPath)) {
    http_response_code(500);
    echo "Missing file: includes/department_helpers.php. Please create it as instructed.";
    exit;
}
require_once $helpersPath;

// Slug from query or wrapper
$slugParam = $_GET['slug'] ?? '';
$slug = $slugParam !== '' ? strtolower($slugParam) : (isset($slug) ? strtolower($slug) : 'internal');

// Fetch data
$dept = get_department_by_slug($slug);
$dept_id = (int)($dept['id'] ?? 0);

$leaders   = $dept_id ? get_department_leaders($dept_id) : [];
$divisions = $dept_id ? get_divisions($dept_id) : [];
$programs  = $dept_id ? get_programs($dept_id) : [];

$page_title     = $dept ? ('Departemen ' . $dept['name']) : 'Departemen HMTA';
$additional_css = ['department.css'];
$additional_js  = ['department.js'];

include __DIR__ . '/../includes/header.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function ph($w=600,$h=400,$t='HMTA'){ return "https://via.placeholder.com/{$w}x{$h}?text=".rawurlencode($t); }
?>
<main>
  <section class="department-header">
    <div class="container">
      <div class="header-content">
        <img src="<?= BASE_URL ?>Resource/default-dept.png" alt="Logo Departemen" class="dept-logo"
             onerror="this.src='<?= ph(120,120,$dept['name']??'DEPT') ?>'">
        <div>
          <h1><?= h($dept['name'] ?? 'Departemen HMTA') ?></h1>
          <p><?= h($dept['description'] ?? 'Deskripsi departemen belum tersedia.') ?></p>
        </div>
      </div>
    </div>
  </section>

  <section class="department-mission">
    <div class="container">
      <h2><i class="fas fa-bullseye"></i> Visi & Misi</h2>
      <div class="mission-content">
        <div class="mission-box">
          <h3><i class="fas fa-eye"></i> Visi</h3>
          <p><?= h($dept['description'] ?? 'Mewujudkan departemen yang profesional, kolaboratif, dan berdampak nyata.') ?></p>
        </div>
        <div class="mission-box">
          <h3><i class="fas fa-tasks"></i> Misi</h3>
          <ul>
            <li>Mengembangkan kompetensi dan peran strategis anggota.</li>
            <li>Menyelenggarakan program yang relevan dan berkelanjutan.</li>
            <li>Memperkuat koordinasi lintas divisi dan kolaborasi eksternal.</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <?php if (!empty($programs)): ?>
  <section class="programs-section">
    <div class="container">
      <h2><i class="fas fa-rocket"></i> Program Kerja</h2>
      <div class="programs-grid">
        <?php foreach ($programs as $pg): ?>
          <div class="program-card">
            <div class="program-icon"><i class="fas fa-check-circle"></i></div>
            <h3><?= h($pg['name']) ?></h3>
            <?php if (!empty($pg['description'])): ?><p><?= h($pg['description']) ?></p><?php endif; ?>
            <?php if (!empty($pg['frequency'])): ?><span class="program-frequency"><?= h(ucfirst($pg['frequency'])) ?></span><?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($leaders)): ?>
  <section class="team-section">
    <div class="container">
      <h2><i class="fas fa-users"></i> Pimpinan Inti</h2>
      <div class="team-grid">
        <?php foreach ($leaders as $ld): ?>
          <div class="team-card" data-aos="zoom-in">
            <div class="team-image">
              <img src="<?= h($ld['photo_url'] ?: (BASE_URL.'Resource/default-profile.jpg')) ?>"
                   alt="<?= h($ld['name']) ?>"
                   onerror="this.src='<?= BASE_URL ?>Resource/default-profile.jpg'">
              <div class="team-overlay">
                <div class="social-links">
                  <?php if (!empty($ld['linkedin_url'])): ?><a href="<?= h($ld['linkedin_url']) ?>" target="_blank"><i class="fab fa-linkedin"></i></a><?php endif; ?>
                  <?php if (!empty($ld['email'])): ?><a href="mailto:<?= h($ld['email']) ?>"><i class="fas fa-envelope"></i></a><?php endif; ?>
                </div>
              </div>
            </div>
            <div class="team-info">
              <h3><?= h($ld['name']) ?></h3>
              <p class="position"><?= h($ld['role'] ?? '') ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <section class="department-divisions">
    <div class="section-division">
      <h2><i class="fa-solid fa-people-group"></i> Divisi & Anggota</h2>
    </div>

    <?php if (!empty($divisions)): ?>
      <div class="division-filter">
        <button class="filter-btn active" data-filter="all">Semua Divisi</button>
        <?php foreach ($divisions as $dv): ?>
          <button class="filter-btn" data-filter="<?= h($dv['slug'] ?: 'divisi') ?>"><?= h($dv['name']) ?></button>
        <?php endforeach; ?>
      </div>

      <div class="divisions-container">
        <?php foreach ($divisions as $dv): ?>
          <?php $members = get_division_members((int)$dv['id']); ?>
          <div class="division-card" data-category="<?= h($dv['slug'] ?: 'divisi') ?>">
            <div class="division-header">
              <div class="division-icon"><i class="fas fa-puzzle-piece"></i></div>
              <div class="division-title">
                <h3><?= h($dv['name']) ?></h3>
                <?php $countDisp = !empty($dv['member_count']) ? (int)$dv['member_count'] : (is_array($members) ? count($members) : 0); ?>
                <span class="member-count"><?= $countDisp ?> Anggota</span>
              </div>
              <button class="toggle-btn" data-target="dv-<?= (int)$dv['id'] ?>"><i class="fas fa-chevron-down"></i></button>
            </div>

            <div class="division-content" id="dv-<?= (int)$dv['id'] ?>" data-aos="fade-up">
              <?php if (!empty($dv['description'])): ?><p class="division-description"><?= h($dv['description']) ?></p><?php endif; ?>

              <?php if (!empty($members)): ?>
                <div class="staff-section">
                  <h4>Anggota</h4>
                  <div class="staff-grid">
                    <?php foreach ($members as $m): ?>
                      <div class="staff-member">
                        <img src="<?= h($m['photo_url'] ?: (BASE_URL.'Resource/default-profile.jpg')) ?>"
                             alt="<?= h($m['name']) ?>"
                             onerror="this.src='<?= BASE_URL ?>Resource/default-profile.jpg'">
                        <div class="staff-info">
                          <h6><?= h($m['name']) ?></h6>
                          <?php if (!empty($m['role'])): ?><span><?= h($m['role']) ?></span><?php endif; ?>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php else: ?>
                <div class="staff-section"><p style="color:#6b7280">Belum ada anggota terdaftar untuk divisi ini.</p></div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="divisions-container">
        <div style="text-align:center; color:#6b7280; width:100%">Belum ada divisi untuk departemen ini.</div>
      </div>
    <?php endif; ?>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>