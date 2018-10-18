<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Core\Model\TaxonInterface;

interface TaxonFromRitamFactoryInterface extends FactoryInterface
{
    public function createParentTaxonFromRitam($ritamProduct): ?TaxonInterface;

    public function createChildTaxonFromRitam($ritamProduct): ?TaxonInterface;

}