<?php

declare(strict_types=1);

namespace CommissionTask\Console\Command;

use CommissionTask\Factory\OperationFactory;
use CommissionTask\Service\Importers\RowsReaderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CalculateCommission extends Command
{
    protected static $defaultName = 'commission:calculate';

    public function __construct(
        private RowsReaderInterface $rowsReader,
        private LoggerInterface $logger,
        private OperationFactory $operationFactory
    ) {
        parent::__construct();
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
                if (is_array($row)) {
                    try {
                        $operation = $this->operationFactory->createOperationByTypes($row);
                        $output->writeln($operation->getCommission());
                    } catch (\Error $error) {
                        $this->logger->error($error);
                        $output->writeln($error->getMessage());
                    }
                }
            }
        } catch (\Throwable $exception) {
            $this->logger->critical($exception);
            $output->writeln($exception->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
