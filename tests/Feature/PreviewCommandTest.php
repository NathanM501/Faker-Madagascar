<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Manguithre\FakerMadagascar\Data\PhonePrefixRepository;
use Manguithre\FakerMadagascar\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class PreviewCommandTest extends TestCase
{
    #[Test]
    public function it_runs_preview_command_for_address(): void
    {
        Artisan::call('fakermg:preview', ['--count' => 2, '--type' => 'address']);

        $output = Artisan::output();

        // The region is randomly picked, so assert on the stable structure.
        foreach (['Region', 'District', 'Commune', 'Fokontany'] as $column) {
            $this->assertStringContainsString($column, $output);
        }
    }

    #[Test]
    public function it_runs_preview_command_for_person(): void
    {
        Artisan::call('fakermg:preview', ['--count' => 2, '--type' => 'person']);

        $output = Artisan::output();

        foreach (['First name', 'Last name', 'Full name'] as $column) {
            $this->assertStringContainsString($column, $output);
        }
    }

    #[Test]
    public function it_runs_preview_command_for_contact(): void
    {
        Artisan::call('fakermg:preview', ['--count' => 1, '--type' => 'contact']);

        $output = Artisan::output();

        $this->assertStringContainsString('Phone', $output);
        $this->assertStringContainsString('CIN', $output);

        // The generated phone must start with a verified prefix.
        $prefixFound = false;

        foreach (PhonePrefixRepository::all() as $prefix) {
            if (str_contains($output, $prefix)) {
                $prefixFound = true;
            }
        }

        $this->assertTrue($prefixFound, 'No verified phone prefix found in command output');
    }

    #[Test]
    public function it_exports_to_json_in_temp_dir(): void
    {
        Artisan::call('fakermg:preview', ['--count' => 2, '--type' => 'person', '--export' => 'json']);

        $output = Artisan::output();

        $this->assertStringContainsString('Exported to', $output);
        $this->assertStringContainsString(sys_get_temp_dir(), $output);

        $file = $this->extractExportPath($output);

        $this->assertFileExists($file);

        $decoded = json_decode((string) file_get_contents($file), true);

        $this->assertCount(2, $decoded);
    }

    #[Test]
    public function it_exports_to_csv_in_temp_dir(): void
    {
        Artisan::call('fakermg:preview', ['--count' => 2, '--type' => 'contact', '--export' => 'csv']);

        $output = Artisan::output();

        $file = $this->extractExportPath($output);

        $this->assertFileExists($file);

        $lines = array_filter(explode("\n", (string) file_get_contents($file)));

        $this->assertCount(3, $lines, 'CSV should contain a header + 2 rows');
    }

    private function extractExportPath(string $output): string
    {
        if (!preg_match('/Exported to (.+)/', trim($output), $matches)) {
            $this->fail("Could not find export path in output: $output");
        }

        return trim($matches[1]);
    }
}
