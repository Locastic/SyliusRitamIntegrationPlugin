<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Command;

use Locastic\SyliusRitamIntegrationPlugin\Service\ProductImportHandler;
use Locastic\SyliusRitamIntegrationPlugin\Service\RitamApiHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportProductsCommand extends Command
{
    /**
     * @var RitamApiHandler
     */
    private $ritamConnectionHandler;

    /**
     * @var ProductImportHandler
     */
    private $productImportHandler;

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(RitamApiHandler $ritamConnectionHandler, ProductImportHandler $productImportHandler)
    {
        $this->ritamConnectionHandler = $ritamConnectionHandler;
        $this->productImportHandler = $productImportHandler;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('locastic:sylius:import-ritam-products')
            ->setDescription('Imports available data into sylius product entity.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $this->output = $output;

        try {
            $timeStart = time();

            $this->writeInfo(["Please wait while data is being imported..."]);

            $ritamProducts = $this->ritamConnectionHandler->getRitamProducts();

            if (is_string($ritamProducts)) {
                $this->writeError($ritamProducts);

                return 1;
            }

            $this->writeInfo(["Fetched all products from Ritam."]);

            $this->writeInfo(["Saving products to database..."]);

            $importedProductsCount = $this->productImportHandler->importProducts($ritamProducts);

            $timeEnd = time();
            $time = round(($timeEnd - $timeStart) / 60, 2);

            $this->writeInfo(
                [
                    "Imported ".$importedProductsCount." products!",
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