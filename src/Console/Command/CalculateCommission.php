<?php

declare(strict_types=1);

namespace CommissionTask\Console\Command;

use CommissionTask\Model\Operation;
use CommissionTask\Service\Importers\RowsReaderInterface;
use CommissionTask\Service\MathCalculator;
use Psr\Log\LoggerInterface;
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
    /** @var LoggerInterface */
    private $logger;

    public function __construct(RowsReaderInterface $rowsReader, MathCalculator $mathCalculator, LoggerInterface $logger)
    {
        parent::__construct();
        $this->rowsReader = $rowsReader;
        $this->mathCalculator = $mathCalculator;
        $this->logger = $logger;
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
        try {
            foreach ($this->rowsReader->rows($input->getArgument('file')) as $row) {
                try {
                    $operation = new Operation($row);
                    $output->writeln('<info>' . $this->mathCalculator->computeCommission($operation) . '</info>');
                } catch (\Error $error) {
                    $this->logger->error($error);
                }
            }
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
            return Command::FAILURE;
        }


        return Command::SUCCESS;
    }
}
