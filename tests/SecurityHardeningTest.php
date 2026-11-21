<?php

use PHPUnit\Framework\TestCase;

class SecurityHardeningTest extends TestCase
{
    public function testAdminDeleteUiUsesPostAndCsrf(): void
    {
        $source = file_get_contents(__DIR__ . '/../admin/manage.php');
        $this->assertStringContainsString("if (isset(\$_GET['del']))", $source);
        $this->assertStringContainsString('<form method="post"', $source);
        $this->assertStringContainsString('<input type="hidden" name="action" value="delete">', $source);
        $this->assertStringContainsString('csrf_field()', $source);
    }

    public function testSuperadminProtectedPagesUseSuperadminGuard(): void
    {
        $import = file_get_contents(__DIR__ . '/../admin/tools/import_members.php');
        $maintenance = file_get_contents(__DIR__ . '/../admin/maintenance.php');
        $this->assertStringContainsString('require_super_admin();', $import);
        $this->assertStringContainsString('require_super_admin();', $maintenance);
        $this->assertStringContainsString('csrf_field()', $import);
    }

    public function testSvgUploadsAreRejectedAndMaterialsDirectoryIsDeniedDirectly(): void
    {
        $functions = file_get_contents(__DIR__ . '/../includes/functions.php');
        $htaccess = file_get_contents(__DIR__ . '/../uploads/materials/.htaccess');
        $this->assertStringContainsString('$ext === \'svg\'', $functions);
        $this->assertStringContainsString('Require all denied', $htaccess);
    }

    public function testPrivateMaterialsUseProtectedDownloadScript(): void
    {
        $materi = file_get_contents(__DIR__ . '/../pages/materi.php');
        $download = file_get_contents(__DIR__ . '/../pages/materi_download.php');
        $this->assertStringContainsString('materi_download.php', $materi);
        $this->assertStringContainsString('is_public', $materi);
        $this->assertStringContainsString("require_login();", $download);
        $this->assertStringContainsString('is_public', $download);
        $this->assertStringNotContainsString('!is_admin()', $download);
    }

    public function testLoggedInUsersSeePrivateMaterials(): void
    {
        $materi = file_get_contents(__DIR__ . '/../pages/materi.php');
        $this->assertStringContainsString('require_login();', $materi);
        $this->assertStringNotContainsString('is_public = 1', $materi);
    }

    public function testSecurityHeadersAllowSameOriginFraming(): void
    {
        $headers = file_get_contents(__DIR__ . '/../includes/security_headers.php');
        $this->assertStringContainsString("header('X-Frame-Options: SAMEORIGIN');", $headers);
        $this->assertStringContainsString("\"frame-ancestors 'self'\"", $headers);
        $this->assertStringNotContainsString('X-Frame-Options: DENY', $headers);
        $this->assertStringNotContainsString("\"frame-ancestors 'none'\"", $headers);
    }

    public function testMaterialDownloadDoesNotSendCspSandbox(): void
    {
        $download = file_get_contents(__DIR__ . '/../pages/materi_download.php');
        $this->assertStringContainsString("header('X-Content-Type-Options: nosniff');", $download);
        $this->assertStringNotContainsString('Content-Security-Policy', $download);
        $this->assertStringNotContainsString('sandbox', $download);
    }
}
