<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Factory\TaxonFactoryInterface;

interface TaxonFromRitamFactoryInterface extends TaxonFactoryInterface
{
    public function createParentTaxonFromRitam($ritamProduct): ?TaxonInterface;

    public function createChildTaxonFromRitam($ritamProduct, $taxonCode): ?TaxonInterface;

}