<?php

namespace Locastic\SyliusRitamIntegrationPlugin\Service;


use Locastic\SyliusRitamIntegrationPlugin\Entity\ProductInterface;
use Locastic\SyliusRitamIntegrationPlugin\Factory\TaxonFromRitamFactoryInterface;
use Locastic\SyliusRitamIntegrationPlugin\Repository\TaxonRepositoryInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class ProductTaxonImportHandler
{
    /**
     * @var TaxonRepositoryInterface
     */
    private $taxonRepository;

    /**
     * @var TaxonFromRitamFactoryInterface
     */
    private $taxonFactory;

    /**
     * @var FactoryInterface
     */
    private $productTaxonFactory;

    /**
     * @var string
     */
    private $defaultTaxonCode;

    /**
     * @var string
     */
    private $defaultLocale;

    public function __construct(
        TaxonRepositoryInterface $taxonRepository,
        TaxonFromRitamFactoryInterface $taxonFactory,
        FactoryInterface $productTaxonFactory,
        string $defaultTaxonCode = 'category',
        string $defaultLocale = 'en_US'
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->taxonFactory = $taxonFactory;
        $this->productTaxonFactory = $productTaxonFactory;
        $this->defaultTaxonCode = $defaultTaxonCode;
        $this->defaultLocale = $defaultLocale;
    }


    public function importTaxons($ritamProduct, ProductInterface $product): ProductInterface
    {
        $group = str_replace(' ', '-',ucfirst(mb_strtolower($ritamProduct->item_group, 'UTF-8')));
        $parentTaxon = $this->taxonRepository->findOneByCode($group);

        if (is_null($parentTaxon)) {
            $parentTaxon = $this->taxonFactory->createParentTaxonFromRitam($ritamProduct);
            $defaultTaxon = $this->taxonRepository->findOneByCode($this->defaultTaxonCode);

            if (is_null($parentTaxon)) {
                $product = $this->importProductTaxons($defaultTaxon, $product);
            }else{
                $parentTaxon->setParent($defaultTaxon);
                $this->taxonRepository->add($parentTaxon);
            }

        }

        $product = $this->importProductTaxons($parentTaxon, $product);
        $product->setMainTaxon($parentTaxon);

        $subgroup = str_replace(' ', '-',ucfirst(mb_strtolower($ritamProduct->item_subgroup, 'UTF-8')));
        $childTaxon = $this->taxonRepository->findOneByCode($subgroup);

        if (is_null($childTaxon)) {
            $childTaxon = $this->taxonFactory->createChildTaxonFromRitam($ritamProduct);

            if (is_null($childTaxon)) {
                return $product;
            }

            $childTaxon->setParent($parentTaxon);
            $this->taxonRepository->add($childTaxon);
        }

        $product = $this->importProductTaxons($childTaxon, $product);


        return $product;
    }

    private function importProductTaxons(TaxonInterface $taxon, ProductInterface $product)
    {
        $productTaxon = $this->productTaxonFactory->createNew();
        $productTaxon->setTaxon($taxon);
        $productTaxon->setProduct($product);
        $product->addProductTaxon($productTaxon);

        return $product;
    }
}