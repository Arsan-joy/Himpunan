<?php

use PHPUnit\Framework\TestCase;

if (!function_exists('get_uploaded_files')) {
    function get_uploaded_files(string $field): array {
        if (empty($_FILES[$field])) return [];
        $f = $_FILES[$field];

        if (!is_array($f['name'] ?? null)) {
            return [[
                'name'     => (string)($f['name'] ?? ''),
                'type'     => (string)($f['type'] ?? ''),
                'tmp_name' => (string)($f['tmp_name'] ?? ''),
                'error'    => (int)($f['error'] ?? UPLOAD_ERR_NO_FILE),
                'size'     => (int)($f['size'] ?? 0),
            ]];
        }

        $names = (array)($f['name'] ?? []);
        $types = (array)($f['type'] ?? []);
        $tmps  = (array)($f['tmp_name'] ?? []);
        $errs  = (array)($f['error'] ?? []);
        $sizes = (array)($f['size'] ?? []);
        $count = max(count($names), count($types), count($tmps), count($errs), count($sizes));

        $out = [];
        for ($i = 0; $i < $count; $i++) {
            $out[] = [
                'name'     => (string)($names[$i] ?? ''),
                'type'     => (string)($types[$i] ?? ''),
                'tmp_name' => (string)($tmps[$i] ?? ''),
                'error'    => (int)($errs[$i] ?? UPLOAD_ERR_NO_FILE),
                'size'     => (int)($sizes[$i] ?? 0),
            ];
        }
        return $out;
    }
}

class MultiUploadHelperTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($_FILES['file_upload']);
    }

    public function testGetUploadedFilesNormalizesSingleUploadShape(): void
    {
        $_FILES['file_upload'] = [
            'name' => 'materi-1.pdf',
            'type' => 'application/pdf',
            'tmp_name' => '/tmp/php123',
            'error' => UPLOAD_ERR_OK,
            'size' => 12345,
        ];

        $files = get_uploaded_files('file_upload');

        $this->assertCount(1, $files);
        $this->assertSame('materi-1.pdf', $files[0]['name']);
        $this->assertSame(UPLOAD_ERR_OK, $files[0]['error']);
        $this->assertSame(12345, $files[0]['size']);
    }

    public function testGetUploadedFilesNormalizesMultipleUploadShape(): void
    {
        $_FILES['file_upload'] = [
            'name' => ['a.pdf', 'b.pdf'],
            'type' => ['application/pdf', 'application/pdf'],
            'tmp_name' => ['/tmp/phpa', '/tmp/phpb'],
            'error' => [UPLOAD_ERR_OK, UPLOAD_ERR_NO_FILE],
            'size' => [100, 0],
        ];

        $files = get_uploaded_files('file_upload');

        $this->assertCount(2, $files);
        $this->assertSame('a.pdf', $files[0]['name']);
        $this->assertSame(UPLOAD_ERR_OK, $files[0]['error']);
        $this->assertSame('b.pdf', $files[1]['name']);
        $this->assertSame(UPLOAD_ERR_NO_FILE, $files[1]['error']);
    }
}
