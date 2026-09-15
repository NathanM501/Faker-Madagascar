<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Console;

use Illuminate\Console\Command;
use Manguithre\FakerMadagascar\FakerMadagascar;
use Manguithre\FakerMadagascar\ValueObjects\MalagasyAddress;

class PreviewCommand extends Command
{
    protected $signature = 'fakermg:preview
                            {--count=1 : Number of items to generate}
                            {--type=address : Type of data to generate (address, person, contact)}
                            {--export= : Export format (json, csv)}';

    protected $description = 'Generate preview data from Faker Madagascar';

    public function __construct(private FakerMadagascar $fakerMadagascar)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $count = max(1, (int) $this->option('count'));
        $type = (string) $this->option('type');
        $export = (string) $this->option('export');

        $rows = [];

        for ($i = 0; $i < $count; ++$i) {
            if ($type === 'person') {
                $first = $this->fakerMadagascar->firstName();
                $last = $this->fakerMadagascar->lastName();

                $rows[] = [
                    'First name' => $first,
                    'Last name' => $last,
                    'Full name' => $first . ' ' . $last,
                ];

                continue;
            }

            $rows[] = match ($type) {
                'contact' => [
                    'Phone' => $this->fakerMadagascar->phoneNumber(),
                    'CIN' => $this->fakerMadagascar->cin(),
                ],
                default => $this->formatAddress($this->fakerMadagascar->address()),
            };
        }

        if ($export === 'json') {
            $this->writeJson($rows, $type);

            return self::SUCCESS;
        }

        if ($export === 'csv') {
            $this->writeCsv($rows, $type);

            return self::SUCCESS;
        }

        $this->table($rows === [] ? [] : array_keys($rows[0]), $rows);

        return self::SUCCESS;
    }

    private function formatAddress(MalagasyAddress $address): array
    {
        return [
            'Region' => $address->region,
            'District' => $address->district,
            'Commune' => $address->commune,
            'Fokontany' => $address->fokontany,
        ];
    }

    private function writeJson(array $rows, string $type): void
    {
        $filename = sprintf('%s/fakermg-%s-%d.json', sys_get_temp_dir(), $type, time());

        file_put_contents($filename, json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("Exported to $filename");
    }

    private function writeCsv(array $rows, string $type): void
    {
        $filename = sprintf('%s/fakermg-%s-%d.csv', sys_get_temp_dir(), $type, time());
        $file = fopen($filename, 'w');

        if ($file === false) {
            $this->error("Unable to write to $filename");

            return;
        }

        if ($rows !== []) {
            fputcsv($file, array_keys($rows[0]));

            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
        }

        fclose($file);

        $this->info("Exported to $filename");
    }
}
