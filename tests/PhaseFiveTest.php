<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PhaseFiveTest extends TestCase
{
    private function root(string $path = ''): string
    {
        return dirname(__DIR__) . ($path === '' ? '' : '/' . ltrim($path, '/'));
    }

    public function testAdminManageHasPaginationAndSearch(): void
    {
        $manage = file_get_contents($this->root('admin/manage.php'));
        $this->assertStringContainsString('$perPage = 50', $manage);
        $this->assertStringContainsString("name=\"q\"", $manage);
        $this->assertStringContainsString('LIMIT ? OFFSET ?', $manage);
    }

    public function testSchemaIncludesAllRequiredTablesAndColumns(): void
    {
        $schema = file_get_contents($this->root('database/schema.sql'));
        foreach (['users','kabinet','departments','divisions','division_members','members','leaders','organization_leaders','programs','login_attempts','password_resets','materials','photos','events','site_settings'] as $table) {
            $this->assertStringContainsString("CREATE TABLE IF NOT EXISTS {$table}", $schema);
        }
        $this->assertStringContainsString("logo_url VARCHAR(255)", $schema);
        $this->assertStringContainsString("category ENUM", $schema);
        $this->assertStringContainsString("'super_admin','admin','user'", $schema);
    }

    public function testDeadCodeAndDuplicateHelpersAreRemoved(): void
    {
        $this->assertFalse(file_exists($this->root('test_view.php')));
        $this->assertFalse(file_exists($this->root('config/config.php')));
        $this->assertFalse(file_exists($this->root('config/database.php')));
        $this->assertStringContainsString('.phpunit.result.cache', file_get_contents($this->root('.gitignore')));

        $count = 0;
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->root()));
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $count += preg_match_all('/function\s+h\s*\(/', file_get_contents($file->getPathname()));
            }
        }
        $this->assertSame(1, $count);
    }

    public function testMedkomScriptIsOnlyAddedForMedkominfo(): void
    {
        $department = file_get_contents($this->root('pages/department.php'));
        $this->assertStringContainsString("if (\$slug === 'medkominfo')", $department);
        $internal = file_get_contents($this->root('pages/departemensamagri/internal.php'));
        $this->assertStringNotContainsString('medkom-init.js', $internal);
    }

    public function testDepartmentSlugMigrationIsIdempotentAndLimitedToCorrections(): void
    {
        $migration = file_get_contents($this->root('database/migrations/004_fix_department_slugs.sql'));

        $this->assertStringContainsString("UPDATE departments SET slug = 'medkominfo' WHERE slug = 'Internal';", $migration);
        $this->assertStringContainsString("UPDATE departments SET slug = 'internal' WHERE slug = 'intern';", $migration);
        $this->assertStringNotContainsString('DELETE', strtoupper($migration));
        $this->assertStringNotContainsString('INSERT', strtoupper($migration));

        $schema = file_get_contents($this->root('database/schema.sql'));
        $this->assertStringContainsString('UNIQUE KEY slug (slug)', $schema);
    }
}
