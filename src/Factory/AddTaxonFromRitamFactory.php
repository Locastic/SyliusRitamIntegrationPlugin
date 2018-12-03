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

        return $this->createTaxon($taxon, ucfirst(mb_strtolower($ritamProduct->item_group, 'UTF-8')));
    }

    public function createChildTaxonFromRitam($ritamProduct, $taxonCode): ?TaxonInterface
    {
        if (!isset($ritamProduct->item_subgroup) || $ritamProduct->item_subgroup == "") {
            return null;
        }

        $taxon = $this->createTaxonFromRitamFactory->createNew();

        return $this->createTaxon($taxon, ucfirst(mb_strtolower($ritamProduct->item_subgroup, 'UTF-8')),$taxonCode);

    }

    private function createTaxon(TaxonInterface $taxon, string $taxonName, $taxonCode = null): TaxonInterface
    {
        $taxon->setCurrentLocale($this->locale);
        $taxon->setName($taxonName);
        if ($taxonCode === null) {
            $taxon->setCode(str_replace(' ', '-', $taxonName));
        }else{
            $taxon->setCode($taxonCode);
        }
        $taxon->setDescription($taxonName);

        return $taxon;
    }

    /**
     * @param \Sylius\Component\Taxonomy\Model\TaxonInterface $parent
     *
     * @return \Sylius\Component\Taxonomy\Model\TaxonInterface
     */
    public function createForParent(\Sylius\Component\Taxonomy\Model\TaxonInterface $parent
    ): \Sylius\Component\Taxonomy\Model\TaxonInterface {
        $taxon = $this->createNew();
        $taxon->setParent($parent);

        return $taxon;
    }
}