<?php
/**
 * Unit Tests: ORDER BY Whitelist Validation
 * Tests untuk fungsi validate_order_by() di admin/manage.php
 */

use PHPUnit\Framework\TestCase;

// Ekstrak fungsi validate_order_by untuk testing independen
if (!function_exists('validate_order_by')) {
    function validate_order_by(string $order, array $whitelist, string $default = 'id DESC'): string {
        if (in_array($order, $whitelist, true)) {
            return $order;
        }

        $parts = explode(' ', trim($order), 2);
        $col   = $parts[0] ?? '';
        $dir   = strtoupper($parts[1] ?? 'ASC');

        if (in_array($col, $whitelist, true) && in_array($dir, ['ASC', 'DESC'], true)) {
            return $col . ' ' . $dir;
        }

        if ($order !== $default) {
            error_log('[manage] ORDER BY tidak valid, fallback ke default: ' . $order);
        }
        return $default;
    }
}

class OrderByTest extends TestCase
{
    private array $whitelist = ['id', 'name', 'created_at', 'start_date', 'active'];

    // ----------------------------------------------------------------
    // Test 1: Nilai dalam whitelist dikembalikan apa adanya
    // ----------------------------------------------------------------
    public function testWhitelistedValueIsReturnedAsIs(): void
    {
        foreach ($this->whitelist as $col) {
            $result = validate_order_by($col, $this->whitelist);
            $this->assertEquals($col, $result, "Kolom '$col' harus dikembalikan apa adanya");
        }
    }

    // ----------------------------------------------------------------
    // Test 2: Format "column ASC" yang valid dikembalikan
    // ----------------------------------------------------------------
    public function testValidColumnWithAscDirectionIsReturned(): void
    {
        $result = validate_order_by('name ASC', $this->whitelist);
        $this->assertEquals('name ASC', $result);
    }

    // ----------------------------------------------------------------
    // Test 3: Format "column DESC" yang valid dikembalikan
    // ----------------------------------------------------------------
    public function testValidColumnWithDescDirectionIsReturned(): void
    {
        $result = validate_order_by('start_date DESC', $this->whitelist);
        $this->assertEquals('start_date DESC', $result);
    }

    // ----------------------------------------------------------------
    // Test 4: Nilai di luar whitelist dikembalikan sebagai 'id DESC'
    // ----------------------------------------------------------------
    public function testNonWhitelistedValueFallsBackToDefault(): void
    {
        $maliciousInputs = [
            '1; DROP TABLE users--',
            'SLEEP(5)',
            'id, (SELECT password FROM users LIMIT 1)',
            'username',
            'password_hash',
            'RAND()',
            '',
            '   ',
        ];

        foreach ($maliciousInputs as $input) {
            $result = validate_order_by($input, $this->whitelist);
            $this->assertEquals('id DESC', $result, "Input '$input' harus fallback ke 'id DESC'");
        }
    }

    // ----------------------------------------------------------------
    // Test 5: Direction tidak valid fallback ke default
    // ----------------------------------------------------------------
    public function testInvalidDirectionFallsBackToDefault(): void
    {
        $result = validate_order_by('name LIMIT 1', $this->whitelist);
        $this->assertEquals('id DESC', $result);

        $result = validate_order_by('name; DROP TABLE', $this->whitelist);
        $this->assertEquals('id DESC', $result);
    }

    // ----------------------------------------------------------------
    // Test 6: Custom default dikembalikan jika tidak ada di whitelist
    // ----------------------------------------------------------------
    public function testCustomDefaultIsUsedWhenProvided(): void
    {
        $result = validate_order_by('invalid_column', $this->whitelist, 'created_at DESC');
        $this->assertEquals('created_at DESC', $result);
    }

    // ----------------------------------------------------------------
    // Test 7: Case sensitivity — kolom harus exact match
    // ----------------------------------------------------------------
    public function testColumnMatchIsCaseSensitive(): void
    {
        // 'NAME' bukan 'name' — harus fallback
        $result = validate_order_by('NAME', $this->whitelist);
        $this->assertEquals('id DESC', $result);

        $result = validate_order_by('ID', $this->whitelist);
        $this->assertEquals('id DESC', $result);
    }
}
