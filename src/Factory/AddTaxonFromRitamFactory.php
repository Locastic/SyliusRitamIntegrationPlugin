<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Factory;


use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class AddTaxonFromRitamFactory implements TaxonFromRitamFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $createTaxonFromRitamFactory;

    /**
     * @var string
     */
    private $locale;

    public function __construct(
        FactoryInterface $createTaxonFromRitamFactory,
        string $locale = 'en_US'
    ) {
        $this->createTaxonFromRitamFactory = $createTaxonFromRitamFactory;
        $this->locale = $locale;
    }

    public function createNew()
    {
        return $this->createTaxonFromRitamFactory->createNew();
    }

    public function createParentTaxonFromRitam($ritamProduct): ?TaxonInterface
    {
        if (!isset($ritamProduct->item_group) || $ritamProduct->item_group == "") {
            return null;
        }

        $taxon = $this->createTaxonFromRitamFactory->createNew();

        return $this->createTaxon($taxon,  ucfirst(strtolower($ritamProduct->item_group)));
    }

    public function createChildTaxonFromRitam($ritamProduct): ?TaxonInterface
    {
        if (!isset($ritamProduct->item_subgroup) || $ritamProduct->item_subgroup == "") {
            return null;
        }

        $taxon = $this->createTaxonFromRitamFactory->createNew();

        return $this->createTaxon($taxon,  ucfirst(strtolower($ritamProduct->item_subgroup)));

    }

    private function createTaxon(TaxonInterface $taxon, string $taxonName): TaxonInterface
    {
        $taxon->setCurrentLocale($this->locale);
        $taxon->setName($taxonName);
        $taxon->setCode(str_replace(' ', '-', $taxonName));
        $taxon->setDescription($taxonName);

        return $taxon;
    }
}