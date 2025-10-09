<?php
require_once dirname(__DIR__, 2) . '/includes/functions.php';

$page_title     = 'Minerfesto';
$additional_css = ['styleminer.css']; // pastikan ada di Resource/css/styleminer.css
$additional_js  = ['miner.js'];       // pastikan ada di Resource/js/miner.js

include dirname(__DIR__, 2) . '/includes/header.php';
?>
<main>
  <!-- Hero Section -->
  <section class="hero minerfesto-hero">
    <div class="container">
      <h1>Minerfesto</h1>
      <p>Manifestasi Ide dan Inovasi Pertambangan</p>
    </div>
  </section>

  <!-- About Minerfesto Section -->
  <section class="about-program">
    <div class="container">
      <div class="about-content">
        <div class="about-text">
          <h2>Tentang Minerfesto</h2>
          <p>Minerfesto adalah acara tahunan unggulan yang diselenggarakan oleh Himpunan Mahasiswa Teknik Pertambangan Institut Teknologi Sumatera (HMTA ITERA). Acara ini dirancang sebagai wadah untuk mempertemukan mahasiswa, akademisi, dan profesional di bidang pertambangan untuk berbagi pengetahuan, pengalaman, dan inovasi terbaru.</p>
          <p>Sejak pertama kali diadakan pada tahun 2018, Minerfesto telah berkembang menjadi salah satu acara pertambangan terbesar di kalangan mahasiswa di Indonesia. Acara ini menggabungkan kompetisi, seminar, workshop, dan pameran teknologi yang memberikan pengalaman komprehensif bagi para peserta.</p>
          <p>Minerfesto bertujuan untuk:</p>
          <ul>
            <li>Meningkatkan kualitas sumber daya manusia di bidang pertambangan</li>
            <li>Memperkenalkan teknologi dan inovasi terbaru di industri pertambangan</li>
            <li>Membangun jaringan antara mahasiswa, akademisi, dan profesional</li>
            <li>Mendorong pemikiran kritis dan solusi inovatif terhadap tantangan industri pertambangan</li>
          </ul>
        </div>
        <div class="about-image">
          <img src="<?php echo BASE_URL; ?>Resource/minerfesto-about.jpg" alt="Kegiatan Minerfesto"
               onerror="this.src='<?php echo BASE_URL; ?>Resource/default.jpg'">
        </div>
      </div>
    </div>
  </section>

  <!-- Program Activities -->
  <section class="program-activities">
    <div class="container">
      <h2 class="section-title">Rangkaian Kegiatan Minerfesto</h2>
      <div class="activities-grid">
        <div class="activity-card">
          <div class="activity-icon">
            <i class="fas fa-trophy"></i>
          </div>
          <h3>Kompetisi Nasional</h3>
          <p>Berbagai kompetisi di bidang pertambangan yang diikuti oleh mahasiswa dari seluruh Indonesia, termasuk:</p>
          <ul>
            <li>Mining Design Competition</li>
            <li>Mining Paper Competition</li>
            <li>Mining Case Study Competition</li>
            <li>Mine Rescue Competition</li>
          </ul>
        </div>
        <div class="activity-card">
          <div class="activity-icon">
            <i class="fas fa-chalkboard-teacher"></i>
          </div>
          <h3>Seminar Nasional</h3>
          <p>Seminar dengan pembicara dari berbagai latar belakang yang membahas topik-topik terkini dalam industri pertambangan, termasuk:</p>
          <ul>
            <li>Keberlanjutan Pertambangan</li>
            <li>Teknologi Pertambangan Terbaru</li>
            <li>Regulasi dan Kebijakan Pertambangan</li>
            <li>Kewirausahaan di Bidang Pertambangan</li>
          </ul>
        </div>
        <div class="activity-card">
          <div class="activity-icon">
            <i class="fas fa-hands-helping"></i>
          </div>
          <h3>Workshop</h3>
          <p>Workshop praktis yang memberikan keterampilan teknis dan non-teknis yang dibutuhkan dalam industri pertambangan, seperti:</p>
          <ul>
            <li>Software Pertambangan (Surpac, Micromine, dll)</li>
            <li>Teknik Eksplorasi dan Penambangan</li>
            <li>Keselamatan dan Kesehatan Kerja</li>
            <li>Manajemen Proyek Pertambangan</li>
          </ul>
        </div>
        <div class="activity-card">
          <div class="activity-icon">
            <i class="fas fa-broadcast-tower"></i>
          </div>
          <h3>Pameran Teknologi</h3>
          <p>Pameran yang menampilkan teknologi dan inovasi terbaru dalam industri pertambangan, termasuk:</p>
          <ul>
            <li>Peralatan Pertambangan Modern</li>
            <li>Teknologi Pengolahan Mineral</li>
            <li>Solusi Keberlanjutan</li>
            <li>Startup di Bidang Pertambangan</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- Gallery Section -->
  <section class="gallery">
    <div class="container">
      <h2 class="section-title">Galeri Minerfesto</h2>
      <div class="gallery-grid">
        <div class="gallery-item">
          <img src="<?php echo BASE_URL; ?>Resource/minerfesto-gallery1.jpg" alt="Minerfesto Competition"
               onerror="this.src='<?php echo BASE_URL; ?>Resource/default.jpg'">
          <div class="gallery-caption">Kompetisi Mine Design 2023</div>
        </div>
        <div class="gallery-item">
          <img src="<?php echo BASE_URL; ?>Resource/minerfesto-gallery2.jpg" alt="Minerfesto Seminar"
               onerror="this.src='<?php echo BASE_URL; ?>Resource/default.jpg'">
          <div class="gallery-caption">Seminar Nasional Pertambangan 2022</div>
        </div>
        <div class="gallery-item">
          <img src="<?php echo BASE_URL; ?>Resource/minerfesto-gallery3.jpg" alt="Minerfesto Workshop"
               onerror="this.src='<?php echo BASE_URL; ?>Resource/default.jpg'">
          <div class="gallery-caption">Workshop Software Pertambangan 2023</div>
        </div>
        <div class="gallery-item">
          <img src="<?php echo BASE_URL; ?>Resource/minerfesto-gallery4.jpg" alt="Minerfesto Exhibition"
               onerror="this.src='<?php echo BASE_URL; ?>Resource/default.jpg'">
          <div class="gallery-caption">Pameran Teknologi Pertambangan 2024</div>
        </div>
        <div class="gallery-item">
          <img src="<?php echo BASE_URL; ?>Resource/minerfesto-gallery5.jpg" alt="Minerfesto Awarding"
               onerror="this.src='<?php echo BASE_URL; ?>Resource/default.jpg'">
          <div class="gallery-caption">Malam Penghargaan Minerfesto 2023</div>
        </div>
        <div class="gallery-item">
          <img src="<?php echo BASE_URL; ?>Resource/minerfesto-gallery6.jpg" alt="Minerfesto Participants"
               onerror="this.src='<?php echo BASE_URL; ?>Resource/default.jpg'">
          <div class="gallery-caption">Peserta Minerfesto 2024</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Timeline Section -->
  <section class="timeline">
    <div class="container">
      <h2 class="section-title">Perjalanan Minerfesto</h2>
      <div class="timeline-container">
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-content">
            <h3>2022</h3>
            <h4>Minerfesto I</h4>
            <p>Kembali diselenggarakan secara luring dengan tema "Keberlanjutan Pertambangan untuk Masa Depan".</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-content">
            <h3>2023</h3>
            <h4>Minerfesto VI</h4>
            <p>Menjalin kerjasama dengan lebih banyak perusahaan pertambangan untuk pengembangan SDM unggul.</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-content">
            <h3>2024</h3>
            <h4>Minerfesto VII</h4>
            <p>Memperluas jangkauan internasional dengan menghadirkan pembicara dari berbagai negara.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Registration Section -->
  <section class="registration">
    <div class="container">
      <h2 class="section-title">Pendaftaran Minerfesto</h2>
      <div class="registration-content">
        <div class="registration-info">
          <h3>Minerfesto 2025</h3>
          <p>Siap untuk menjadi bagian dari Minerfesto tahun ini? Daftar sekarang dan ikuti berbagai rangkaian acara yang telah kami siapkan!</p>
          <div class="registration-details">
            <div class="detail-item">
              <i class="fas fa-calendar-alt"></i>
              <div>
                <h4>Tanggal Pelaksanaan</h4>
                <p>15-20 September 2025</p>
              </div>
            </div>
            <div class="detail-item">
              <i class="fas fa-map-marker-alt"></i>
              <div>
                <h4>Lokasi</h4>
                <p>Institut Teknologi Sumatera, Lampung</p>
              </div>
            </div>
            <div class="detail-item">
              <i class="fas fa-clipboard-list"></i>
              <div>
                <h4>Pendaftaran Dibuka</h4>
                <p>1 Juni - 31 Agustus 2025</p>
              </div>
            </div>
            <div class="detail-item">
              <i class="fas fa-users"></i>
              <div>
                <h4>Target Peserta</h4>
                <p>Mahasiswa Pertambangan Seluruh Indonesia</p>
              </div>
            </div>
          </div>
          <div class="registration-buttons">
            <a href="#" class="btn-primary">Daftar Sekarang</a>
            <a href="#" class="btn-secondary">Unduh Guidebook</a>
          </div>
        </div>
        <div class="registration-image">
          <img src="<?php echo BASE_URL; ?>Resource/minerfesto-registration.jpg" alt="Pendaftaran Minerfesto"
               onerror="this.src='<?php echo BASE_URL; ?>Resource/default.jpg'">
        </div>
      </div>
    </div>
  </section>

  <!-- Sponsors Section -->
  <section class="sponsors">
    <div class="container">
      <h2 class="section-title">Sponsor dan Mitra Kami</h2>
      <div class="sponsors-grid">
        <div class="sponsor-item"><img src="<?php echo BASE_URL; ?>Resource/sponsor1.png" alt="Sponsor 1" onerror="this.src='<?php echo BASE_URL; ?>Resource/default.png'"></div>
        <div class="sponsor-item"><img src="<?php echo BASE_URL; ?>Resource/sponsor2.png" alt="Sponsor 2" onerror="this.src='<?php echo BASE_URL; ?>Resource/default.png'"></div>
        <div class="sponsor-item"><img src="<?php echo BASE_URL; ?>Resource/sponsor3.png" alt="Sponsor 3" onerror="this.src='<?php echo BASE_URL; ?>Resource/default.png'"></div>
        <div class="sponsor-item"><img src="<?php echo BASE_URL; ?>Resource/sponsor4.png" alt="Sponsor 4" onerror="this.src='<?php echo BASE_URL; ?>Resource/default.png'"></div>
        <div class="sponsor-item"><img src="<?php echo BASE_URL; ?>Resource/sponsor5.png" alt="Sponsor 5" onerror="this.src='<?php echo BASE_URL; ?>Resource/default.png'"></div>
        <div class="sponsor-item"><img src="<?php echo BASE_URL; ?>Resource/sponsor6.png" alt="Sponsor 6" onerror="this.src='<?php echo BASE_URL; ?>Resource/default.png'"></div>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section class="contact">
    <div class="container">
      <h2 class="section-title">Hubungi Kami</h2>
      <div class="contact-content">
        <div class="contact-info">
          <div class="contact-item">
            <i class="fas fa-envelope"></i>
            <div>
              <h4>Email</h4>
              <p><a href="mailto:minerfesto@hmtaitera.com">minerfesto@hmtaitera.com</a></p>
            </div>
          </div>
          <div class="contact-item">
            <i class="fas fa-phone"></i>
            <div>
              <h4>Telepon</h4>
              <p><a href="tel:+6282100000000">0821-XXXX-XXXX</a> (Panitia)</p>
            </div>
          </div>
          <div class="contact-item">
            <i class="fab fa-instagram"></i>
            <div>
              <h4>Instagram</h4>
              <p><a href="https://www.instagram.com/minerfesto_hmtaitera/" target="_blank">@minerfesto_hmtaitera</a></p>
            </div>
          </div>
          <div class="contact-item">
            <i class="fab fa-whatsapp"></i>
            <div>
              <h4>WhatsApp</h4>
              <p><a href="https://wa.me/6282100000000" target="_blank">0821-XXXX-XXXX</a></p>
            </div>
          </div>
        </div>
        <div class="contact-form">
          <form id="contactForm">
            <div class="form-group">
              <label for="name">Nama Lengkap</label>
              <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
              <label for="subject">Subjek</label>
              <input type="text" id="subject" name="subject" required>
            </div>
            <div class="form-group">
              <label for="message">Pesan</label>
              <textarea id="message" name="message" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn-primary">Kirim Pesan</button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ Section -->
  <section class="faq">
    <div class="container">
      <h2 class="section-title">Pertanyaan Umum</h2>
      <div class="faq-container">
        <div class="faq-item">
          <div class="faq-question">
            <h3>Apakah mahasiswa dari jurusan lain dapat berpartisipasi?</h3>
            <span class="faq-toggle"><i class="fas fa-plus"></i></span>
          </div>
          <div class="faq-answer">
            <p>Ya, beberapa kompetisi di Minerfesto terbuka untuk mahasiswa dari jurusan lain, terutama yang memiliki keterkaitan dengan industri pertambangan. Untuk informasi lebih lanjut, silakan periksa guidebook untuk setiap kompetisi.</p>
          </div>
        </div>
        <div class="faq-item">
          <div class="faq-question">
            <h3>Bagaimana cara mendaftar untuk kompetisi di Minerfesto?</h3>
            <span class="faq-toggle"><i class="fas fa-plus"></i></span>
          </div>
          <div class="faq-answer">
            <p>Pendaftaran dapat dilakukan secara online melalui website resmi Minerfesto. Setiap peserta atau tim harus mengisi formulir pendaftaran dan melengkapi persyaratan sesuai dengan panduan kompetisi yang dipilih.</p>
          </div>
        </div>
        <div class="faq-item">
          <div class="faq-question">
            <h3>Berapa biaya untuk mengikuti Minerfesto?</h3>
            <span class="faq-toggle"><i class="fas fa-plus"></i></span>
          </div>
          <div class="faq-answer">
            <p>Biaya pendaftaran bervariasi tergantung pada jenis kompetisi dan kegiatan yang diikuti. Untuk informasi biaya terbaru, silakan lihat pada guidebook yang dapat diunduh di website resmi Minerfesto.</p>
          </div>
        </div>
        <div class="faq-item">
          <div class="faq-question">
            <h3>Apakah ada fasilitas akomodasi untuk peserta dari luar kota?</h3>
            <span class="faq-toggle"><i class="fas fa-plus"></i></span>
          </div>
          <div class="faq-answer">
            <p>Panitia Minerfesto menyediakan informasi tentang akomodasi di sekitar lokasi acara. Untuk beberapa kompetisi, panitia juga menyediakan bantuan akomodasi terbatas bagi peserta dari luar kota.</p>
          </div>
        </div>
        <div class="faq-item">
          <div class="faq-question">
            <h3>Apa saja hadiah yang ditawarkan untuk para pemenang kompetisi?</h3>
            <span class="faq-toggle"><i class="fas fa-plus"></i></span>
          </div>
          <div class="faq-answer">
            <p>Setiap kompetisi menawarkan hadiah berupa uang tunai, sertifikat, dan berbagai hadiah menarik dari sponsor. Total hadiah untuk seluruh kompetisi di Minerfesto tahun ini mencapai puluhan juta rupiah.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
<?php include dirname(__DIR__, 2) . '/../includes/footer.php'; ?>