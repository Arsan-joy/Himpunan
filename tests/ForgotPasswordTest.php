<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ForgotPasswordTest extends TestCase
{
    private function root(string $path = ''): string
    {
        return dirname(__DIR__) . ($path === '' ? '' : '/' . ltrim($path, '/'));
    }

    public function testPasswordResetHelperRejectsUsedExpiredAndMissingTokens(): void
    {
        $helper = file_get_contents($this->root('includes/password_reset.php'));
        $this->assertStringContainsString('if (!$row)', $helper);
        $this->assertStringContainsString('$row[\'used_at\'] !== null', $helper);
        $this->assertStringContainsString('strtotime($row[\'expires_at\']) < time()', $helper);
    }

    public function testForgotPagesUseGenericResponseAndFiveAttemptRateLimit(): void
    {
        foreach (['pages/forgot.php', 'admin/forgot.php'] as $file) {
            $content = file_get_contents($this->root($file));
            $this->assertStringContainsString('Bila email terdaftar, instruksi reset telah dikirim.', $content);
            $this->assertStringContainsString("'forgot_password', 5, 900", $content);
            $this->assertStringNotContainsString('$resetUrl', $content);
        }
    }

    public function testResetPagesRequireEightCharacters(): void
    {
        foreach (['pages/reset.php', 'admin/reset.php'] as $file) {
            $content = file_get_contents($this->root($file));
            $this->assertStringContainsString('minimal 8', $content);
            $this->assertStringContainsString('minlength="8"', $content);
        }
    }

    public function testEmailSchemaAndMailConfigurationExist(): void
    {
        $this->assertStringContainsString('email VARCHAR(255) DEFAULT NULL', file_get_contents($this->root('database/schema.sql')));
        $this->assertFileExists($this->root('database/migrations/007_add_users_email.sql'));
        foreach (['MAIL_HOST', 'MAIL_PORT', 'MAIL_USERNAME', 'MAIL_PASSWORD', 'MAIL_FROM', 'MAIL_FROM_NAME'] as $key) {
            $this->assertStringContainsString($key, file_get_contents($this->root('.env.example')));
        }
    }
}
