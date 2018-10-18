<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class ImportController extends AbstractController
{
    public function importRitamProductsAction(KernelInterface $kernel)
    {
        $content = $this->callCommand($kernel, 'locastic:sylius:import-ritam-products');

        return new Response($content);
    }

    public function importRitamProductPricesAction(KernelInterface $kernel)
    {
        $content = $this->callCommand($kernel, 'locastic:sylius:import-ritam-prices');

        return new Response($content);
    }

    public function importRitamProductStockAction(KernelInterface $kernel)
    {
        $content = $this->callCommand($kernel, 'locastic:sylius:import-ritam-stock');

        return new Response($content);
    }

    private function callCommand(KernelInterface $kernel, string $command)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array('command' => $command));

        $output = new BufferedOutput();
        $application->run($input, $output);

        return $output->fetch();
    }
}