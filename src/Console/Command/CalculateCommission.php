<?php

declare(strict_types=1);

namespace CommissionTask\Console\Command;

use CommissionTask\Model\Operation;
use CommissionTask\Service\Importers\RowsReaderInterface;
use CommissionTask\Service\MathCalculator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CalculateCommission extends Command
{
    protected static $defaultName = 'commission:calculate';

    /** @var RowsReaderInterface */
    private $rowsReader;
    /** @var MathCalculator */
    private $mathCalculator;

    public function __construct(RowsReaderInterface $rowsReader, MathCalculator $mathCalculator)
    {
        parent::__construct();
        $this->rowsReader = $rowsReader;
        $this->mathCalculator = $mathCalculator;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Please provide file')
            ->setDescription('Calculate commission from csv input file.')
            ->setHelp('This command allows you to calculate commission from csv input file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->rowsReader->rows($input->getArgument('file')) as $row) {
            if (is_array($row)) {
                $operation = new Operation($row);
                $output->writeln('<fg=black;bg=cyan>' . $this->mathCalculator->computeCommission($operation) . '</>');
            } else {
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
