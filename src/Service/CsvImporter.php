<?php

declare(strict_types=1);

namespace CommissionTask\Service;

use Symfony\Component\Console\Exception\InvalidArgumentException;

class CsvImporter
{
    public function rows(string $fileName): \Generator
    {
        $file = (file_exists($fileName) ? fopen($fileName, 'rb') : null);
        if (!$file) {
            throw new InvalidArgumentException('Can`t find file ' . $fileName);
        }

        try {
            while (!feof($file)) {
                yield fgetcsv($file, 0, ',');
            }
        } finally {
            fclose($file);
        }
    }
}
