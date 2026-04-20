<?php
/**
 * Unit Tests: File-Based Cache
 * Tests untuk fungsi di includes/cache.php
 */

use PHPUnit\Framework\TestCase;

// Gunakan folder cache sementara untuk testing
$testCacheDir = sys_get_temp_dir() . '/hmta_test_cache_' . uniqid();
if (!defined('CACHE_DIR')) define('CACHE_DIR', $testCacheDir);

require_once __DIR__ . '/../includes/cache.php';

class CacheTest extends TestCase
{
    protected function setUp(): void
    {
        // Bersihkan cache sebelum setiap test
        cache_flush();
    }

    protected function tearDown(): void
    {
        // Bersihkan cache setelah setiap test
        cache_flush();
        // Hapus folder test jika kosong
        @rmdir(CACHE_DIR);
    }

    // ----------------------------------------------------------------
    // Test 1: Cache miss mengembalikan null
    // ----------------------------------------------------------------
    public function testCacheMissReturnsNull(): void
    {
        $result = cache_get('nonexistent_key_xyz');
        $this->assertNull($result);
    }

    // ----------------------------------------------------------------
    // Test 2: Cache hit mengembalikan data yang disimpan
    // ----------------------------------------------------------------
    public function testCacheHitReturnsStoredData(): void
    {
        $data = ['id' => 1, 'name' => 'Test Department'];
        cache_set('test_key', $data, 3600);

        $result = cache_get('test_key');
        $this->assertEquals($data, $result);
    }

    // ----------------------------------------------------------------
    // Test 3: Cache menyimpan berbagai tipe data
    // ----------------------------------------------------------------
    public function testCacheStoresVariousDataTypes(): void
    {
        cache_set('string_key', 'hello world', 3600);
        cache_set('int_key', 42, 3600);
        cache_set('array_key', [1, 2, 3], 3600);
        cache_set('bool_key', true, 3600);

        $this->assertEquals('hello world', cache_get('string_key'));
        $this->assertEquals(42, cache_get('int_key'));
        $this->assertEquals([1, 2, 3], cache_get('array_key'));
        $this->assertTrue(cache_get('bool_key'));
    }

    // ----------------------------------------------------------------
    // Test 4: Cache expired mengembalikan null
    // ----------------------------------------------------------------
    public function testExpiredCacheReturnsNull(): void
    {
        // TTL = 1 detik, tapi kita manipulasi file langsung
        cache_set('expired_key', 'some data', 3600);

        // Manipulasi file cache untuk mensimulasikan expired
        $cacheFile = CACHE_DIR . '/expired_key.json';
        if (file_exists($cacheFile)) {
            $payload = json_decode(file_get_contents($cacheFile), true);
            $payload['expires_at'] = time() - 1; // sudah expired
            file_put_contents($cacheFile, json_encode($payload));
        }

        $result = cache_get('expired_key');
        $this->assertNull($result);
    }

    // ----------------------------------------------------------------
    // Test 5: cache_delete menghapus entri spesifik
    // ----------------------------------------------------------------
    public function testCacheDeleteRemovesSpecificEntry(): void
    {
        cache_set('key_to_delete', 'data', 3600);
        cache_set('key_to_keep', 'other data', 3600);

        cache_delete('key_to_delete');

        $this->assertNull(cache_get('key_to_delete'));
        $this->assertEquals('other data', cache_get('key_to_keep'));
    }

    // ----------------------------------------------------------------
    // Test 6: cache_delete_pattern menghapus semua key dengan prefix
    // ----------------------------------------------------------------
    public function testCacheDeletePatternRemovesMatchingKeys(): void
    {
        cache_set('departments_all', ['dept1', 'dept2'], 3600);
        cache_set('departments_active', ['dept1'], 3600);
        cache_set('members_all', ['member1'], 3600);

        $count = cache_delete_pattern('departments_');

        $this->assertGreaterThanOrEqual(2, $count);
        $this->assertNull(cache_get('departments_all'));
        $this->assertNull(cache_get('departments_active'));
        $this->assertNotNull(cache_get('members_all')); // tidak terhapus
    }

    // ----------------------------------------------------------------
    // Test 7: cache_flush menghapus semua cache
    // ----------------------------------------------------------------
    public function testCacheFlushRemovesAllEntries(): void
    {
        cache_set('key1', 'data1', 3600);
        cache_set('key2', 'data2', 3600);
        cache_set('key3', 'data3', 3600);

        cache_flush();

        $this->assertNull(cache_get('key1'));
        $this->assertNull(cache_get('key2'));
        $this->assertNull(cache_get('key3'));
    }

    // ----------------------------------------------------------------
    // Test 8: cache_set mengembalikan true saat berhasil
    // ----------------------------------------------------------------
    public function testCacheSetReturnsTrueOnSuccess(): void
    {
        $result = cache_set('success_key', 'data', 3600);
        $this->assertTrue($result);
    }

    // ----------------------------------------------------------------
    // Test 9: Cache corrupt mengembalikan null (graceful degradation)
    // ----------------------------------------------------------------
    public function testCorruptCacheReturnsNull(): void
    {
        cache_set('corrupt_key', 'data', 3600);

        // Korupsi file cache
        $cacheFile = CACHE_DIR . '/corrupt_key.json';
        if (file_exists($cacheFile)) {
            file_put_contents($cacheFile, 'INVALID JSON {{{');
        }

        $result = cache_get('corrupt_key');
        $this->assertNull($result);
    }
}
