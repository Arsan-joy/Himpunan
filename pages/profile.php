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
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>