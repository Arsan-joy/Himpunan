<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PhaseFourTest extends TestCase
{
    private function root(string $path = ''): string
    {
        return dirname(__DIR__) . ($path === '' ? '' : '/' . ltrim($path, '/'));
    }

    public function testHomepageHasOneH1DatabaseCounterAndSeoMetadata(): void
    {
        $index = file_get_contents($this->root('index.php'));
        $this->assertSame(1, substr_count($index, '<h1'));
        $this->assertStringContainsString('$settings', $index);
        $this->assertStringNotContainsString('data-target="200"', $index);

        $header = file_get_contents($this->root('includes/header.php'));
        $this->assertStringContainsString('rel="canonical"', $header);
        $this->assertStringContainsString('property="og:title"', $header);
    }

    public function testFooterEmailAndMinorFixes(): void
    {
        $footer = file_get_contents($this->root('includes/footer.php'));
        $this->assertStringContainsString('hmta.balakosa.itera@gmail.com', $footer);
        $this->assertStringNotContainsString('hmta.balakosa.itera.com', $footer);

        $config = file_get_contents($this->root('database/config.php'));
        $this->assertStringContainsString('hmta.balakosa.itera@gmail.com', $config);
        $this->assertStringNotContainsString('Pertambagan', $config);

        $miner = file_get_contents($this->root('pages/annual/minerfesto.php'));
        $this->assertStringContainsString("/includes/footer.php", $miner);
    }

    public function testSeoFilesDepartmentTitleAndAdminForeignKeys(): void
    {
        $this->assertFileExists($this->root('robots.txt'));
        $this->assertFileExists($this->root('sitemap.xml'));

        $department = file_get_contents($this->root('pages/department.php'));
        $this->assertStringContainsString("preg_replace('/^Departemen", $department);

        $manage = file_get_contents($this->root('admin/manage.php'));
        $this->assertStringContainsString("fetch_options_sql(\$meta['options_sql'])", $manage);
        $this->assertStringContainsString("'pengaturan'", $manage);
    }
}
