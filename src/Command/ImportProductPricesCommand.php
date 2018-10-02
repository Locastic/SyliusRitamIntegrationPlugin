<?php
namespace Locastic\SyliusRitamIntegrationPlugin\Command;

use Locastic\SyliusRitamIntegrationPlugin\Service\ProductPricesImportHandler;
use Locastic\SyliusRitamIntegrationPlugin\Service\RitamApiHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportProductPricesCommand extends Command
{
    /**
     * @var RitamApiHandler
     */
    private $ritamConnectionHandler;

    /**
     * @var ProductPricesImportHandler
     */
    private $productPricesImportHandler;

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(RitamApiHandler $ritamConnectionHandler, ProductPricesImportHandler $productPricesImportHandler)
    {
        $this->ritamConnectionHandler = $ritamConnectionHandler;
        $this->productPricesImportHandler = $productPricesImportHandler;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('locastic:sylius:import-ritam-prices')
            ->setDescription('Imports available prices data into sylius product entity.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $this->output = $output;

        try {
            $timeStart = time();

            $this->writeInfo(["Please wait while data is being imported..."]);

            $ritamProductPrices = $this->ritamConnectionHandler->getRitamProductPrices();

            $this->writeInfo(["Fetched all product prices from Ritam."]);

            if (is_string($ritamProductPrices)) {
                $this->writeError($ritamProductPrices);

                return 1;
            }

            $this->writeInfo(["Saving product prices to database..."]);

            $importedProductsCount = $this->productPricesImportHandler->importProductPrices($ritamProductPrices);

            $timeEnd = time();
            $time = round(($timeEnd - $timeStart) / 60, 2);

            $this->writeInfo(
                [
                    "Imported ".$importedProductsCount." product prices!",
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