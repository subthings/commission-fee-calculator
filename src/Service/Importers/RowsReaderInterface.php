<?php

declare(strict_types=1);

namespace CommissionTask\Service\Importers;

interface RowsReaderInterface
{
    public function rows(string $fileName): \Generator;
}