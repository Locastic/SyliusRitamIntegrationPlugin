<?php

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

    public function __construct(RitamApiHandler $ritamConnectionHandler, ProductImportHandler $productImportHandler)
    {
        $this->ritamConnectionHandler = $ritamConnectionHandler;
        $this->productImportHandler = $productImportHandler;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('locastic:import-products')
            ->setDescription('Imports available data into sylius product entity.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        try {
            $timeStart = time();

            $output->writeln(
                [
                    "-------------------------------------------",
                    "\nPlease wait while data is being imported...\n",
                    "-------------------------------------------",
                ]
            );

            $ritamProducts = $this->ritamConnectionHandler->getRitamProducts();


            if (is_string($ritamProducts)) {
                $output->writeln(
                    [
                        '<error>An error occured: '.$ritamProducts.'</error>',
                    ]
                );

                return 1;
            }


            $importedProductsCount = $this->productImportHandler->importProducts($ritamProducts);

            $timeEnd = time();
            $time = round(($timeEnd - $timeStart) / 60, 2);

            $output->writeln(
                [
                    "-------------------------------------------",
                    "\nImported ".$importedProductsCount." products!\n",
                    "Execution time: ".$time." minutes\n",
                    "-------------------------------------------",
                ]
            );

            return 0;

        } catch (\Exception $e) {

            $output->writeln(
                [
                    "\nAn error occured: \n",
                    '<error>'.$e->getMessage().'</error>',
                ]
            );

            return 1;
        }
    }
}