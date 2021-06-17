<?php

declare(strict_types=1);

namespace CommissionTask\Console\Command;

use CommissionTask\Factory\OperationFactory;
use CommissionTask\Service\CurrencyService;
use CommissionTask\Service\Importers\RowsReaderInterface;
use CommissionTask\Service\MoneyCalculator;
use CommissionTask\Service\UserBalanceStore;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CalculateCommission extends Command
{
    protected static $defaultName = 'commission:calculate';

    private RowsReaderInterface $rowsReader;
    private LoggerInterface $logger;
    private UserBalanceStore $userBalanceStore;
    private CurrencyService $currencyService;
    private MoneyCalculator $moneyCalculator;

    public function __construct(
        RowsReaderInterface $rowsReader,
        LoggerInterface $logger,
        UserBalanceStore $userBalanceStore,
        CurrencyService $currencyService,
        MoneyCalculator $moneyCalculator
    ) {
        parent::__construct();
        $this->rowsReader = $rowsReader;
        $this->logger = $logger;
        $this->userBalanceStore = $userBalanceStore;
        $this->currencyService = $currencyService;
        $this->moneyCalculator = $moneyCalculator;
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
                    $operation = OperationFactory::createOperationByTypes(
                        $row,
                        $this->userBalanceStore,
                        $this->currencyService,
                        $this->moneyCalculator
                    );
                    $output->writeln('<info>'.$operation->getCommission().'</info>');
                } catch (GuzzleException $exception) {
                    $this->logger->critical($exception->getMessage());
                    $output->writeln('<fg=#c0392b>'.$exception->getMessage().'</>');

                    return Command::FAILURE;
                } catch (\JsonException $exception) {
                    $this->logger->critical($exception->getMessage());
                    $output->writeln('<fg=#c0392b>'.$exception->getMessage().'</>');
                } catch (\Error $error) {
                    $this->logger->error($error);
                }
            }
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $output->writeln('<error>'.$exception->getMessage().'</error>');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
