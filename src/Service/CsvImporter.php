<?php

declare(strict_types=1);

namespace CommissionTask\Service;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class CsvImporter
{
    /** @var string */
    private $fileName;
    private $file;

    public function rows(string $fileName): \Generator
    {
        $this->fileName = $fileName;
        $this->setFile();
        if (!$this->file) {
            throw new InvalidArgumentException('Can`t find file '.$this->fileName);
        }

        try {
            while (!feof($this->file)) {
                $row = fgetcsv($this->file, 0, ',');

                yield $row;
            }
        } finally {
            fclose($this->file);
        }

        return;
    }

    /**
     * @throws FileNotFoundException
     */
    public function setFile(): void
    {
        $this->file = (file_exists($this->fileName) ? fopen($this->fileName, 'r') : null);
    }
}
