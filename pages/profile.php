<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title     = 'Profile HMTA ITERA';
$additional_css = ['styleprofile.css',]; // pastikan file ada di Resource/css/styleprofile.css
$additional_js  = ['profile.js', 'index.js'];       // pastikan file ada di Resource/js/profile.js

include __DIR__ . '/../includes/header.php';
?>

<main>  
    <section class="intro">
        <div class="container">
            <h1>Himpunan Mahasiswa Teknik Pertambangan<br>Institut Teknologi Sumatera</h1>
            <p>Diawali dengan angkatan pertama Tahun 2019 Teknik Pertambangan ITERA sepakat untuk membentuk himpunan sebagai wadah seluruh mahasiswa Teknik Pertambangan ITERA. Dilanjutkan pada Maret 2020 terbentuklah Panitia Penyusun Himpunan yang difokuskan untuk menyiapkan segala sesuatu untuk pembentukan himpunan. Pada tanggal 10 Desember 2020, tepatnya di Gedung B PKOR Bandar Lampung, dilakukan pengesahan Himpunan Mahasiswa Teknik Pertambangan (HMTA) ITERA oleh seluruh Mahasiswa Teknik Pertambangan ITERA. Ketua himpunan Mahasiswa Teknik Pertambangan (HMTA) yang pertama yaitu Alkhodri Rahman. HMTA ITERA memiliki asas kekeluargaan dan keprofesian.</p>
        </div>
    </section>
 
    <section class="philosophy">
        <div class="container">
            <h2>Filosofi Logo HMTA ITERA</h2>
            <div class="filosofi">
                <!-- Ganti path gambar ke assets/img -->
                <img src="<?php echo IMG_URL; ?>IMG_1381.png" alt="Logo HMTA ITERA">
            <div class="philosophy-content">
                <ol>
                    <li><strong>Roda Gerigi:</strong> Organisasi teknik (engineering).</li>
                    <li><strong>Bukit:</strong> Geografis Sumatera yang kaya.</li>
                    <li><strong>Belencong:</strong> Simbol keberanian dan ketangguhan.</li>
                    <li><strong>Mahkota:</strong> Tanggung jawab dan kebijaksanaan.</li>
                    <li><strong>Warna:</strong> Tujuan, ketekunan, dan kepercayaan diri.</li>
                </ol>
            </div>
        </div>
    </section>

    <!-- Video Profil -->
    <section class="video-profile">
        <div class="container">
            <h2>Video Profil HMTA ITERA</h2>
            <div class="video-wrapper">
                <div class="video-card loading" id="videoCard">
                    <!-- Lazy load: source menggunakan data-src, preload=none -->
                    <video id="profileVideo" class="profile-video lazy-video" poster="<?php echo IMG_URL; ?>IMG_1381.png" preload="none" playsinline webkit-playsinline tabindex="0" aria-label="Video profil HMTA ITERA">
                        <source data-src="/Resource/videos/profile.mp4" type="video/mp4">
                        Video tidak didukung di peramban Anda. Unduh video <a href="/Resource/videos/profile.mp4">di sini</a>.
                    </video>

                    <!-- Loading indicator -->
                    <div class="video-loader" id="videoLoader" aria-hidden="true">
                        <div class="spinner" role="status" aria-hidden="true"></div>
                    </div>

                    <button id="videoPlayBtn" class="video-play-btn" aria-label="Play video">
                        <span class="visually-hidden">Play</span>
                        <svg width="64" height="64" viewBox="0 0 64 64" aria-hidden="true">
                            <circle cx="32" cy="32" r="32" fill="rgba(0,0,0,0.45)"></circle>
                            <polygon points="26,20 26,44 46,32" fill="#fff"></polygon>
                        </svg>
                    </button>
                </div>
                <p class="video-caption">Tonton video profil HMTA ITERA untuk mengetahui visi, misi, dan kegiatan kami. Jika video tidak muncul, pastikan file video berada di /Resource/videos/profile.mp4</p>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>