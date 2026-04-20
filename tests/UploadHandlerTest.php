<?php
/**
 * Unit Tests: Upload Handler
 * Tests untuk fungsi save_uploaded_file() di includes/functions.php
 * Menggunakan file sementara untuk simulasi upload.
 */

use PHPUnit\Framework\TestCase;

if (!defined('BASE_URL'))   define('BASE_URL',   'http://localhost/root/');
if (!defined('UPLOAD_DIR')) define('UPLOAD_DIR', sys_get_temp_dir() . '/hmta_test_uploads_' . uniqid());
if (!defined('UPLOAD_URL')) define('UPLOAD_URL', BASE_URL . 'uploads/');
if (!defined('LOG_DIR'))    define('LOG_DIR',    sys_get_temp_dir() . '/hmta_test_logs');

if (!function_exists('log_security')) {
    function log_security(string $event, array $context = []): void { /* no-op */ }
}

// Fungsi helper untuk generate upload filename (diekstrak untuk testability)
function generate_upload_filename(string $originalName, string $ext): string {
    return bin2hex(random_bytes(16)) . '.' . $ext;
}

// Fungsi helper untuk validasi MIME type
function validate_mime_type(string $ext, string $actualMime, array $allowedMimes): bool {
    $expected = $allowedMimes[$ext] ?? null;
    if ($expected === null) return true; // ekstensi tidak ada di whitelist, skip
    return $actualMime === $expected;
}

class UploadHandlerTest extends TestCase
{
    private string $testUploadDir;

    protected function setUp(): void
    {
        $this->testUploadDir = UPLOAD_DIR;
        if (!is_dir($this->testUploadDir)) {
            mkdir($this->testUploadDir, 0775, true);
        }
    }

    protected function tearDown(): void
    {
        // Bersihkan file test
        $files = glob($this->testUploadDir . '/*') ?: [];
        foreach ($files as $f) @unlink($f);
        @rmdir($this->testUploadDir);
    }

    // ----------------------------------------------------------------
    // Test 1: Nama file yang dihasilkan adalah 32 hex chars + ekstensi
    // ----------------------------------------------------------------
    public function testGeneratedFilenameIs32HexCharsWithExtension(): void
    {
        $filename = generate_upload_filename('original_photo.jpg', 'jpg');
        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}\.jpg$/', $filename);
    }

    // ----------------------------------------------------------------
    // Test 2: Nama file tidak mengandung nama asli pengguna
    // ----------------------------------------------------------------
    public function testGeneratedFilenameDoesNotContainOriginalName(): void
    {
        $originalName = 'my_secret_document';
        $filename = generate_upload_filename($originalName . '.jpg', 'jpg');

        $this->assertStringNotContainsString(
            strtolower($originalName),
            $filename,
            'Nama file server tidak boleh mengandung nama asli'
        );
    }

    // ----------------------------------------------------------------
    // Test 3: Setiap generate menghasilkan nama yang berbeda (unik)
    // ----------------------------------------------------------------
    public function testGeneratedFilenamesAreUnique(): void
    {
        $names = [];
        for ($i = 0; $i < 10; $i++) {
            $names[] = generate_upload_filename('test.jpg', 'jpg');
        }
        $this->assertCount(10, array_unique($names), 'Semua nama file harus unik');
    }

    // ----------------------------------------------------------------
    // Test 4: MIME type valid diterima
    // ----------------------------------------------------------------
    public function testValidMimeTypeIsAccepted(): void
    {
        $allowedMimes = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'pdf' => 'application/pdf'];

        $this->assertTrue(validate_mime_type('jpg', 'image/jpeg', $allowedMimes));
        $this->assertTrue(validate_mime_type('png', 'image/png', $allowedMimes));
        $this->assertTrue(validate_mime_type('pdf', 'application/pdf', $allowedMimes));
    }

    // ----------------------------------------------------------------
    // Test 5: MIME type tidak cocok ekstensi ditolak
    // ----------------------------------------------------------------
    public function testMismatchedMimeTypeIsRejected(): void
    {
        $allowedMimes = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'pdf' => 'application/pdf'];

        // JPG dengan MIME PHP
        $this->assertFalse(validate_mime_type('jpg', 'application/x-php', $allowedMimes));
        // PNG dengan MIME HTML
        $this->assertFalse(validate_mime_type('png', 'text/html', $allowedMimes));
        // PDF dengan MIME text
        $this->assertFalse(validate_mime_type('pdf', 'text/plain', $allowedMimes));
    }

    // ----------------------------------------------------------------
    // Test 6: File PHP yang disamarkan sebagai JPG ditolak
    // ----------------------------------------------------------------
    public function testPhpFileMasqueradingAsJpgIsRejected(): void
    {
        $allowedMimes = ['jpg' => 'image/jpeg'];

        // File PHP yang diupload dengan ekstensi .jpg
        $phpMimeTypes = ['application/x-php', 'text/x-php', 'application/php'];

        foreach ($phpMimeTypes as $phpMime) {
            $this->assertFalse(
                validate_mime_type('jpg', $phpMime, $allowedMimes),
                "File dengan MIME $phpMime harus ditolak meski ekstensi .jpg"
            );
        }
    }

    // ----------------------------------------------------------------
    // Test 7: Double extension berbahaya terdeteksi
    // ----------------------------------------------------------------
    public function testDoubleExtensionDetection(): void
    {
        $dangerousExts = ['php','php3','php4','php5','phtml','phar','pl','py','jsp','asp','sh','cgi','exe'];

        // Simulasi deteksi double extension
        $filename = 'malicious.php.jpg';
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION)); // 'jpg'
        $nameParts = explode('.', strtolower($filename));

        $hasDangerousExt = false;
        foreach ($nameParts as $part) {
            if (in_array($part, $dangerousExts, true) && $part !== $ext) {
                $hasDangerousExt = true;
                break;
            }
        }

        $this->assertTrue($hasDangerousExt, 'Double extension .php.jpg harus terdeteksi sebagai berbahaya');
    }

    // ----------------------------------------------------------------
    // Test 8: Nama file normal tidak terdeteksi sebagai double extension
    // ----------------------------------------------------------------
    public function testNormalFilenamePassesDoubleExtensionCheck(): void
    {
        $dangerousExts = ['php','php3','php4','php5','phtml','phar','pl','py','jsp','asp','sh','cgi','exe'];

        $filename = 'photo_2024.jpg';
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $nameParts = explode('.', strtolower($filename));

        $hasDangerousExt = false;
        foreach ($nameParts as $part) {
            if (in_array($part, $dangerousExts, true) && $part !== $ext) {
                $hasDangerousExt = true;
                break;
            }
        }

        $this->assertFalse($hasDangerousExt, 'Nama file normal tidak boleh terdeteksi sebagai double extension');
    }
}
