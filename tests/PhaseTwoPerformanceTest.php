<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PhaseTwoPerformanceTest extends TestCase
{
    private function root(string $path = ''): string
    {
        return dirname(__DIR__) . ($path === '' ? '' : '/' . ltrim($path, '/'));
    }

    public function testOptimizedHomepageAssetsExist(): void
    {
        foreach (['banner1.webp', 'banner2.webp', 'banner3.webp', 'logo-header-120.png', 'favicon-32.png'] as $file) {
            $this->assertFileExists($this->root('Resource/img/' . $file));
        }

        $header = file_get_contents($this->root('includes/header.php'));
        $this->assertStringContainsString('banner1.webp', $header);
        $this->assertStringContainsString('logo-header-120.png', $header);
        $this->assertStringContainsString('favicon-32.png', $header);
    }

    public function testCanonicalRedirectCacheAndPaginationAreConfigured(): void
    {
        $htaccess = file_get_contents($this->root('.htaccess'));
        $this->assertStringContainsString('www\\.hmtaitera\\.com', $htaccess);
        $this->assertStringContainsString('public, max-age=60, must-revalidate', $htaccess);

        $materi = file_get_contents($this->root('pages/materi.php'));
        $this->assertStringContainsString('const MATERI_PER_PAGE = 10;', $materi);

        $gallery = file_get_contents($this->root('pages/gallery.php'));
        $this->assertStringContainsString('const GALLERY_PER_PAGE = 10;', $gallery);
    }

    public function testDepartmentMembersAreBatchLoadedAndCacheIsRequired(): void
    {
        $department = file_get_contents($this->root('pages/department.php'));
        $this->assertStringContainsString('get_division_members_batch', $department);
        $this->assertStringNotContainsString('get_division_members((int)', $department);

        $helpers = file_get_contents($this->root('includes/department_helpers.php'));
        $this->assertStringContainsString('get_division_members_batch', $helpers);
        $this->assertStringContainsString('WHERE division_id IN ($placeholders)', $helpers);

        $functions = file_get_contents($this->root('includes/functions.php'));
        $this->assertStringContainsString("require_once __DIR__ . '/cache.php';", $functions);
    }

    public function testPhaseThreeCabinetAndDepartmentDataAreDatabaseDriven(): void
    {
        $index = file_get_contents($this->root('index.php'));
        $this->assertStringContainsString('$active_kabinet', $index);
        $this->assertStringNotContainsString('Kabinet Samagri 2024-2025', $index);

        $functions = file_get_contents($this->root('includes/functions.php'));
        $this->assertStringContainsString('function get_active_kabinet(', $functions);

        $structure = file_get_contents($this->root('pages/struktur.php'));
        $samagri = file_get_contents($this->root('pages/samagri.php'));
        $this->assertStringContainsString('get_active_kabinet(', $structure);
        $this->assertStringContainsString('get_active_kabinet(', $samagri);

        $helpers = file_get_contents($this->root('includes/department_helpers.php'));
        $this->assertStringContainsString('logo_url', $helpers);
        $department = file_get_contents($this->root('pages/department.php'));
        $this->assertStringContainsString('$dept[\'logo_url\']', $department);

        foreach (['medkom.php', 'psda.php', 'keilmuan.php', 'external.php', 'kaderisasi.php', 'internal.php'] as $file) {
            $legacy = file_get_contents($this->root('pages/departemensamagri/' . $file));
            $this->assertStringContainsString('/pages/department.php', str_replace('\\\\', '/', $legacy));
            $this->assertStringNotContainsString('Logo Departemen Medkomvis', $legacy);
        }
    }
}
