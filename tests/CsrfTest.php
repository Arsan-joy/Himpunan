<?php
/**
 * Unit Tests: CSRF Protection
 * Tests untuk fungsi di includes/csrf.php
 */

use PHPUnit\Framework\TestCase;

// Bootstrap minimal untuk testing tanpa full app
if (!defined('BASE_URL')) define('BASE_URL', 'http://localhost/root/');
if (!defined('LOG_DIR'))  define('LOG_DIR', __DIR__ . '/../logs');

// Mock log_security agar tidak menulis file saat testing
if (!function_exists('log_security')) {
    function log_security(string $event, array $context = []): void { /* no-op in tests */ }
}

require_once __DIR__ . '/../includes/csrf.php';

class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        // Mulai session baru untuk setiap test
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
        session_start();
    }

    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
    }

    // ----------------------------------------------------------------
    // Test 1: Token dihasilkan dan disimpan di $_SESSION
    // ----------------------------------------------------------------
    public function testTokenIsGeneratedAndStoredInSession(): void
    {
        $this->assertArrayNotHasKey('csrf_token', $_SESSION);

        $token = csrf_token();

        $this->assertArrayHasKey('csrf_token', $_SESSION);
        $this->assertEquals($token, $_SESSION['csrf_token']);
    }

    // ----------------------------------------------------------------
    // Test 2: Token adalah 32 karakter hexadecimal
    // ----------------------------------------------------------------
    public function testTokenIs32HexChars(): void
    {
        $token = csrf_token();
        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $token);
    }

    // ----------------------------------------------------------------
    // Test 3: Token yang sama dikembalikan pada pemanggilan berikutnya
    // ----------------------------------------------------------------
    public function testTokenIsSameOnSubsequentCalls(): void
    {
        $token1 = csrf_token();
        $token2 = csrf_token();
        $this->assertEquals($token1, $token2);
    }

    // ----------------------------------------------------------------
    // Test 4: csrf_field() menghasilkan HTML hidden input
    // ----------------------------------------------------------------
    public function testCsrfFieldReturnsHiddenInput(): void
    {
        $token = csrf_token();
        $field = csrf_field();

        $this->assertStringContainsString('type="hidden"', $field);
        $this->assertStringContainsString('name="csrf_token"', $field);
        $this->assertStringContainsString('value="' . $token . '"', $field);
    }

    // ----------------------------------------------------------------
    // Test 5: Verifikasi berhasil dengan token yang benar
    // ----------------------------------------------------------------
    public function testVerifySucceedsWithCorrectToken(): void
    {
        $token = csrf_token();
        $_POST['csrf_token'] = $token;

        $this->assertTrue(csrf_verify());
    }

    // ----------------------------------------------------------------
    // Test 6: Verifikasi gagal dengan token kosong
    // ----------------------------------------------------------------
    public function testVerifyFailsWithEmptyToken(): void
    {
        csrf_token(); // generate token di session
        $_POST['csrf_token'] = '';

        $this->assertFalse(csrf_verify());
    }

    // ----------------------------------------------------------------
    // Test 7: Verifikasi gagal dengan token salah
    // ----------------------------------------------------------------
    public function testVerifyFailsWithWrongToken(): void
    {
        csrf_token();
        $_POST['csrf_token'] = 'wrongtoken12345678901234567890ab';

        $this->assertFalse(csrf_verify());
    }

    // ----------------------------------------------------------------
    // Test 8: Verifikasi gagal jika token tidak ada di POST
    // ----------------------------------------------------------------
    public function testVerifyFailsWithMissingToken(): void
    {
        csrf_token();
        unset($_POST['csrf_token']);

        $this->assertFalse(csrf_verify());
    }

    // ----------------------------------------------------------------
    // Test 9: csrf_regenerate() menghasilkan token baru yang berbeda
    // ----------------------------------------------------------------
    public function testRegenerateProducesDifferentToken(): void
    {
        $oldToken = csrf_token();
        csrf_regenerate();
        $newToken = csrf_token();

        $this->assertNotEquals($oldToken, $newToken);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $newToken);
    }
}
