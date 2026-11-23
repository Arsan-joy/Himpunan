<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PhaseSixCabinetFilterTest extends TestCase
{
    private function root(string $path = ''): string
    {
        return dirname(__DIR__) . ($path === '' ? '' : '/' . ltrim($path, '/'));
    }

    public function testSchemaAndMigrationAddCabinetIdToStructuralTables(): void
    {
        $schema = file_get_contents($this->root('database/schema.sql'));
        $migration = file_get_contents($this->root('database/migrations/005_kabinet_structural_filter.sql'));

        foreach (['divisions', 'leaders', 'organization_leaders', 'programs'] as $table) {
            $this->assertStringContainsString("CREATE TABLE IF NOT EXISTS {$table}", $schema);
        }
        $this->assertMatchesRegularExpression('/CREATE TABLE IF NOT EXISTS divisions[\s\S]*kabinet_id INT DEFAULT NULL[\s\S]*FOREIGN KEY \(kabinet_id\)/', $schema);
        $this->assertMatchesRegularExpression('/CREATE TABLE IF NOT EXISTS leaders[\s\S]*kabinet_id INT DEFAULT NULL[\s\S]*FOREIGN KEY \(kabinet_id\)/', $schema);
        $this->assertMatchesRegularExpression('/CREATE TABLE IF NOT EXISTS organization_leaders[\s\S]*kabinet_id INT DEFAULT NULL[\s\S]*FOREIGN KEY \(kabinet_id\)/', $schema);
        $this->assertMatchesRegularExpression('/CREATE TABLE IF NOT EXISTS programs[\s\S]*kabinet_id INT DEFAULT NULL[\s\S]*FOREIGN KEY \(kabinet_id\)/', $schema);

        $this->assertStringContainsString("period = '2026-2027'", $migration);
        $this->assertStringContainsString('ADD COLUMN IF NOT EXISTS kabinet_id', $migration);
    }

    public function testMigrationAddsFiveDepartmentsAndNoProgramRows(): void
    {
        $migration = file_get_contents($this->root('database/migrations/005_kabinet_structural_filter.sql'));

        foreach (['sekretaris-umum','bendahara-umum','kewirausahaan','manajemen-mutu-anggota','big-data'] as $slug) {
            $this->assertStringContainsString("'{$slug}'", $migration);
        }
        $this->assertStringNotContainsString('INSERT INTO programs', strtolower($migration));
    }

    public function testPublicPagesFilterAndPassCabinetSelection(): void
    {
        $struktur = file_get_contents($this->root('pages/struktur.php'));
        $samagri = file_get_contents($this->root('pages/samagri.php'));
        $department = file_get_contents($this->root('pages/department.php'));
        $helper = file_get_contents($this->root('includes/department_helpers.php'));
        $orgHelper = file_get_contents($this->root('includes/organization_helpers.php'));

        $this->assertStringContainsString('pages/samagri.php?kabinet=', $struktur);
        $this->assertStringContainsString("filter_input(INPUT_GET, 'kabinet', FILTER_VALIDATE_INT)", $samagri);
        $this->assertStringContainsString('get_organization_leaders(null, $kabinetId)', $samagri);
        $this->assertStringContainsString('&kabinet=', $samagri);
        $this->assertStringContainsString("filter_input(INPUT_GET, 'kabinet', FILTER_VALIDATE_INT)", $department);
        $this->assertStringContainsString('(kabinet_id = ? OR kabinet_id IS NULL)', $helper);
        $this->assertStringContainsString("strtolower(trim((string)(\$a['role'] ?? ''))) === 'kepala divisi'", $helper);
        $this->assertStringContainsString('(kabinet_id = ? OR kabinet_id IS NULL)', $orgHelper);
    }

    public function testAdminModulesExposeCabinetField(): void
    {
        $manage = file_get_contents($this->root('admin/manage.php'));
        $this->assertSame(5, substr_count($manage, "'kabinet_id' => ['label'=>'Kabinet'"));
    }
}
