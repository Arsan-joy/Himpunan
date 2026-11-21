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
        $this->assertStringContainsString("!is_admin()", $download);
    }
}
