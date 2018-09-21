<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Command;

use Locastic\SyliusRitamIntegrationPlugin\Service\ProductStockImportHandler;
use Locastic\SyliusRitamIntegrationPlugin\Service\RitamApiHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportProductStockCommand extends Command
{
    /**
     * @var RitamApiHandler
     */
    private $ritamConnectionHandler;

    /**
     * @var ProductStockImportHandler
     */
    private $productStockImportHandler;

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(RitamApiHandler $ritamConnectionHandler, ProductStockImportHandler $productImportHandler)
    {
        $this->ritamConnectionHandler = $ritamConnectionHandler;
        $this->productStockImportHandler = $productImportHandler;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('locastic:sylius:import-ritam-stock')
            ->setDescription('Imports available stock data into sylius product entity.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $this->output = $output;
        try {
            $timeStart = time();

            $this->writeInfo(["Please wait while data is being imported..."]);

            $ritamProductStock = $this->ritamConnectionHandler->getRitamProductStock();

            $this->writeInfo(["Fetched all stock information from Ritam."]);

            if (is_string($ritamProductStock)) {
                $this->writeError($ritamProductStock);

                return 1;
            }

            $this->writeInfo(["Saving info to database..."]);

            $importedProductsCount = $this->productStockImportHandler->importProductStock($ritamProductStock);

            $timeEnd = time();
            $time = round(($timeEnd - $timeStart) / 60, 2);

            $this->writeInfo(
                [
                    "Imported ".$importedProductsCount." product stock info!",
                    "Execution time: ".$time." minutes",
                ]
            );

            return 0;

        } catch (\Exception $e) {

            $this->writeError($e->getMessage());

            return 1;
        }
    }

    private function writeError(string $errorMessage)
    {
        $this->output->writeln(
            [
                "\nAn error occured: \n",
                '<error>'.$errorMessage.'</error>',
            ]
        );
    }

    private function writeInfo(array $messages)
    {
        foreach ($messages as $message) {
            $this->output->writeln(["\n".$message."\n",]);
        }
    }
}