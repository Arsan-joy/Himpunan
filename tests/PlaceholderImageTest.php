<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PlaceholderImageTest extends TestCase
{
    private function root(string $path = ''): string
    {
        return dirname(__DIR__) . ($path === '' ? '' : '/' . ltrim($path, '/'));
    }

    public function testRequiredPlaceholderFilesExist(): void
    {
        $this->assertFileExists($this->root('Resource/default-event.jpg'));
        $this->assertFileExists($this->root('Resource/default-profile.jpg'));
    }

    public function testMissingLocalImageReturnsEventPlaceholder(): void
    {
        $functions = file_get_contents($this->root('includes/functions.php'));
        $this->assertStringContainsString('function event_image_url(?string $url): string', $functions);
        $this->assertStringContainsString("BASE_URL . 'Resource/default-event.jpg'", $functions);
        $this->assertStringContainsString('$candidate = realpath', $functions);
    }

    public function testExistingLocalImageAndExternalUrlRemainUnchanged(): void
    {
        $functions = file_get_contents($this->root('includes/functions.php'));
        $this->assertStringContainsString("if (preg_match('/^https?:\\/\\//i', \$url)", $functions);
        $this->assertStringContainsString('str_starts_with($url, BASE_URL)', $functions);
    }

    public function testNoExternalPlaceholderOrMissingFallbackNamesRemain(): void
    {
        $files = [
            $this->root('pages/past.php'),
            $this->root('pages/gallery.php'),
            $this->root('pages/department.php'),
            $this->root('pages/annual/minerfesto.php'),
        ];

        foreach ($files as $file) {
            $content = file_get_contents($file);
            $externalPlaceholder = 'via.' . 'placeholder.com';
            $missingEvent = 'Resource/default.' . 'jpg';
            $missingProfile = 'Resource/default.' . 'png';
            $this->assertStringNotContainsString($externalPlaceholder, $content);
            $this->assertStringNotContainsString($missingEvent, $content);
            $this->assertStringNotContainsString($missingProfile, $content);
        }
    }
}
