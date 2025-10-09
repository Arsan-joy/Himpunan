<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title     = 'Annual Activities';
$additional_css = ['styleannual.css']; // pastikan ada di Resource/css/styleannual.css
$additional_js  = ['annual.js'];       // pastikan ada di Resource/js/annual.js

include __DIR__ . '/../includes/header.php';
?>
<main>
  <!-- Hero Section -->
  <section class="hero">
    <div class="container">
      <h1>Program Tahunan HMTA ITERA</h1>
      <p>Minerfesto dan Lustrum - Momentum Kebangkitan dan Perayaan Pertambangan ITERA</p>
      <a href="#programs" class="btn-primary">Lihat Program</a>
    </div>
  </section>

  <!-- Programs Section -->
  <section id="programs" class="programs">
    <div class="container">
      <h2 class="section-title">Program Unggulan Tahunan</h2>
      <div class="program-cards">
        <!-- Minerfesto Card -->
        <div class="program-card" id="minerfesto">
          <div class="program-image">
            <img src="<?php echo BASE_URL; ?>Resource/img/minerfesto.jpg" alt="Minerfesto HMTA ITERA"
                 onerror="this.src='<?php echo BASE_URL; ?>Resource/default.jpg'">
          </div>
          <div class="program-content">
            <h3>Minerfesto</h3>
            <p class="program-tagline">Manifestasi Ide dan Inovasi Pertambangan</p>
            <p>Minerfesto adalah acara tahunan yang menampilkan berbagai kompetisi pertambangan, seminar, workshop, dan pameran teknologi pertambangan terbaru. Acara ini bertujuan untuk memperluas wawasan dan keterampilan mahasiswa dalam industri pertambangan.</p>
            <div class="program-highlights">
              <div class="highlight">
                <i class="fas fa-trophy"></i>
                <span>Kompetisi Nasional</span>
              </div>
              <div class="highlight">
                <i class="fas fa-users"></i>
                <span>Networking</span>
              </div>
              <div class="highlight">
                <i class="fas fa-graduation-cap"></i>
                <span>Edukasi</span>
              </div>
            </div>
            <a href="<?php echo BASE_URL; ?>pages/annual/minerfesto.php" class="btn-secondary">Selengkapnya</a>
          </div>
        </div>

        <!-- Lustrum Card -->
        <div class="program-card" id="lustrum">
          <div class="program-image">
            <img src="<?php echo BASE_URL; ?>Resource/lustrum.jpg" alt="Lustrum HMTA ITERA"
                 onerror="this.src='<?php echo BASE_URL; ?>Resource/default.jpg'">
          </div>
          <div class="program-content">
            <h3>Lustrum</h3>
            <p class="program-tagline">Perayaan 5 Tahun Perjalanan Program Studi Teknik Pertambangan ITERA</p>
            <p>Lustrum merupakan perayaan yang diadakan setiap 5 tahun untuk memperingati berdirinya Program Studi Teknik Pertambangan ITERA. Acara ini menampilkan rangkaian kegiatan yang mencerminkan pencapaian, pertumbuhan, dan visi HMTA ITERA ke depan.</p>
            <div class="program-highlights">
              <div class="highlight">
                <i class="fas fa-calendar-alt"></i>
                <span>Setiap 5 Tahun</span>
              </div>
              <div class="highlight">
                <i class="fas fa-rocket"></i>
                <span>Inovasi</span>
              </div>
              <div class="highlight">
                <i class="fas fa-handshake"></i>
                <span>Kolaborasi</span>
              </div>
            </div>
            <a href="<?php echo BASE_URL; ?>pages/lustrum.php" class="btn-secondary">Selengkapnya</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Timeline Section -->
  <section class="timeline">
    <div class="container">
      <h2 class="section-title">Perjalanan Program Tahunan HMTA</h2>
      <div class="timeline-container">
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-content">
            <h3>2022</h3>
            <h4>Minerfesto 1</h4>
            <p>Perayaan 2 tahun HMTA ITERA dengan tema "Keberlanjutan Pertambangan untuk Masa Depan"</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-content">
            <h3>2023</h3>
            <h4>Minerfesto II</h4>
            <p>Kolaborasi dengan industri pertambangan untuk pengembangan SDM unggul</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-content">
            <h3>2024</h3>
            <h4>Minerfesto III & Lustrum I</h4>
            <p>Perayaan 4 tahun berdiri nya HMTA ITERA dan 5 tahun Program Studi Teknik Pertambagan</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="testimonials">
    <div class="container">
      <h2 class="section-title">Testimonial Anggota Himpunan</h2>
      <div class="testimonial-slider" id="testimonialSlider">
        <div class="testimonial-slide">
          <div class="testimonial-content">
            <p>"Minerfesto memberikan saya kesempatan untuk mengembangkan keterampilan teknis dan membangun jaringan profesional yang luas di industri pertambangan."</p>
            <div class="testimonial-author">
              <img src="<?php echo BASE_URL; ?>Resource/testimonial1.jpg" alt="Testimonial Author"
                   onerror="this.src='<?php echo BASE_URL; ?>Resource/default-profile.jpg'">
              <div>
                <h4>Andi Pratama</h4>
                <p>Mahasiswa Teknik Pertambangan 2022</p>
              </div>
            </div>
          </div>
        </div>
        <div class="testimonial-slide">
          <div class="testimonial-content">
            <p>"Acara Lustrum HMTA ITERA memperlihatkan bagaimana organisasi ini terus berkembang dan memberikan dampak positif bagi mahasiswa dan industri pertambangan Indonesia."</p>
            <div class="testimonial-author">
              <img src="<?php echo BASE_URL; ?>Resource/testimonial2.jpg" alt="Testimonial Author"
                   onerror="this.src='<?php echo BASE_URL; ?>Resource/default-profile.jpg'">
              <div>
                <h4>Dina Safitri</h4>
                <p>Alumni Teknik Pertambangan 2019</p>
              </div>
            </div>
          </div>
        </div>
        <div class="testimonial-slide">
          <div class="testimonial-content">
            <p>"Kompetisi di Minerfesto memberikan tantangan nyata yang mempersiapkan saya untuk menghadapi dunia kerja di sektor pertambangan."</p>
            <div class="testimonial-author">
              <img src="<?php echo BASE_URL; ?>Resource/testimonial3.jpg" alt="Testimonial Author"
                   onerror="this.src='<?php echo BASE_URL; ?>Resource/default-profile.jpg'">
              <div>
                <h4>Budi Santoso</h4>
                <p>Pemenang Kompetisi Minerfesto 2023</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="slider-controls">
        <button class="prev-btn" id="prevBtn"><i class="fas fa-chevron-left"></i></button>
        <div class="slider-dots" id="sliderDots">
          <span class="dot active"></span>
          <span class="dot"></span>
          <span class="dot"></span>
        </div>
        <button class="next-btn" id="nextBtn"><i class="fas fa-chevron-right"></i></button>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="cta">
    <div class="container">
      <h2>Jadilah Bagian dari Sejarah HMTA ITERA</h2>
      <p>Ikuti program tahunan kami dan kembangkan potensi diri Anda dalam dunia pertambangan!</p>
      <div class="cta-buttons">
        <a href="#" class="btn-primary">Daftar Minerfesto</a>
        <a href="#" class="btn-secondary">Kontak Kami</a>
      </div>
    </div>
  </section>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>