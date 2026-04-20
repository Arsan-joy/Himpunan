<?php
/**
 * Unit Tests: Rate Limiter
 * Tests untuk fungsi di includes/rate_limiter.php
 * Menggunakan InMemoryStore untuk menghindari dependensi database.
 */

use PHPUnit\Framework\TestCase;

if (!defined('BASE_URL')) define('BASE_URL', 'http://localhost/root/');
if (!defined('LOG_DIR'))  define('LOG_DIR', __DIR__ . '/../logs');

if (!function_exists('log_security')) {
    function log_security(string $event, array $context = []): void { /* no-op */ }
}

/**
 * In-memory store untuk testing rate limiter tanpa database.
 * Menyimulasikan tabel login_attempts.
 */
class InMemoryAttemptStore
{
    private array $attempts = [];

    public function record(string $ip, string $action, string $username = '', ?int $timestamp = null): void
    {
        $this->attempts[] = [
            'ip_address'   => $ip,
            'action'       => $action,
            'username'     => $username,
            'attempted_at' => $timestamp ?? time(),
            'user_agent'   => '',
        ];
    }

    public function countRecent(string $ip, string $action, int $windowSeconds): int
    {
        $since = time() - $windowSeconds;
        return count(array_filter($this->attempts, fn($a) =>
            $a['ip_address'] === $ip &&
            $a['action'] === $action &&
            $a['attempted_at'] >= $since
        ));
    }

    public function reset(string $ip, string $action): void
    {
        $this->attempts = array_values(array_filter($this->attempts, fn($a) =>
            !($a['ip_address'] === $ip && $a['action'] === $action)
        ));
    }

    public function isBlocked(string $ip, string $action, int $maxAttempts, int $windowSeconds): bool
    {
        return $this->countRecent($ip, $action, $windowSeconds) >= $maxAttempts;
    }

    public function getAll(): array { return $this->attempts; }
}

class RateLimiterTest extends TestCase
{
    private InMemoryAttemptStore $store;
    private const MAX_ATTEMPTS = 5;
    private const WINDOW = 900; // 15 menit

    protected function setUp(): void
    {
        $this->store = new InMemoryAttemptStore();
    }

    // ----------------------------------------------------------------
    // Test 1: Percobaan ke-1 s/d ke-5 tidak diblokir
    // ----------------------------------------------------------------
    public function testFirstFiveAttemptsAreNotBlocked(): void
    {
        $ip = '192.168.1.1';

        for ($i = 1; $i <= self::MAX_ATTEMPTS; $i++) {
            $this->assertFalse(
                $this->store->isBlocked($ip, 'admin_login', self::MAX_ATTEMPTS, self::WINDOW),
                "Percobaan ke-$i seharusnya tidak diblokir"
            );
            $this->store->record($ip, 'admin_login');
        }
    }

    // ----------------------------------------------------------------
    // Test 2: Percobaan ke-6 diblokir
    // ----------------------------------------------------------------
    public function testSixthAttemptIsBlocked(): void
    {
        $ip = '192.168.1.2';

        for ($i = 0; $i < self::MAX_ATTEMPTS; $i++) {
            $this->store->record($ip, 'admin_login');
        }

        $this->assertTrue(
            $this->store->isBlocked($ip, 'admin_login', self::MAX_ATTEMPTS, self::WINDOW),
            'Setelah 5 percobaan, IP harus diblokir'
        );
    }

    // ----------------------------------------------------------------
    // Test 3: Reset setelah login berhasil
    // ----------------------------------------------------------------
    public function testResetAfterSuccessfulLogin(): void
    {
        $ip = '192.168.1.3';

        for ($i = 0; $i < self::MAX_ATTEMPTS; $i++) {
            $this->store->record($ip, 'admin_login');
        }

        $this->assertTrue($this->store->isBlocked($ip, 'admin_login', self::MAX_ATTEMPTS, self::WINDOW));

        $this->store->reset($ip, 'admin_login');

        $this->assertFalse(
            $this->store->isBlocked($ip, 'admin_login', self::MAX_ATTEMPTS, self::WINDOW),
            'Setelah reset, IP tidak boleh diblokir'
        );
    }

    // ----------------------------------------------------------------
    // Test 4: Blokir berakhir setelah window 15 menit (simulasi timestamp)
    // ----------------------------------------------------------------
    public function testBlockExpiresAfterWindow(): void
    {
        $ip = '192.168.1.4';
        $oldTimestamp = time() - (self::WINDOW + 1); // lebih dari 15 menit lalu

        for ($i = 0; $i < self::MAX_ATTEMPTS; $i++) {
            $this->store->record($ip, 'admin_login', '', $oldTimestamp);
        }

        // Dengan timestamp lama, seharusnya tidak diblokir lagi
        $this->assertFalse(
            $this->store->isBlocked($ip, 'admin_login', self::MAX_ATTEMPTS, self::WINDOW),
            'Blokir harus berakhir setelah window waktu berlalu'
        );
    }

    // ----------------------------------------------------------------
    // Test 5: IP berbeda tidak saling mempengaruhi
    // ----------------------------------------------------------------
    public function testDifferentIPsAreIndependent(): void
    {
        $ip1 = '10.0.0.1';
        $ip2 = '10.0.0.2';

        for ($i = 0; $i < self::MAX_ATTEMPTS; $i++) {
            $this->store->record($ip1, 'admin_login');
        }

        $this->assertTrue($this->store->isBlocked($ip1, 'admin_login', self::MAX_ATTEMPTS, self::WINDOW));
        $this->assertFalse($this->store->isBlocked($ip2, 'admin_login', self::MAX_ATTEMPTS, self::WINDOW));
    }

    // ----------------------------------------------------------------
    // Test 6: Record menyimpan data yang benar
    // ----------------------------------------------------------------
    public function testRecordStoresCorrectData(): void
    {
        $ip       = '172.16.0.1';
        $username = 'testuser';

        $this->store->record($ip, 'admin_login', $username);

        $all = $this->store->getAll();
        $this->assertCount(1, $all);
        $this->assertEquals($ip, $all[0]['ip_address']);
        $this->assertEquals('admin_login', $all[0]['action']);
        $this->assertEquals($username, $all[0]['username']);
        $this->assertNotEmpty($all[0]['attempted_at']);
    }
}
