<?php

namespace Locastic\SyliusRitamIntegrationPlugin\Service;

use Locastic\SyliusRitamIntegrationPlugin\Factory\ProductFromRitamFactoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;

/**
 * Class ProductImportHandler
 * @package Locastic\SyliusRitamIntegrationPlugin\Service
 */
class ProductImportHandler
{
    /**
     * @var ProductFromRitamFactoryInterface
     */
    private $productFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * ProductImportHandler constructor.
     * @param ProductFromRitamFactoryInterface $productFactory
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ProductFromRitamFactoryInterface $productFactory,
        ProductRepositoryInterface $productRepository
    ) {
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * @param $ritamProducts
     * @return int
     */
    public function importProducts($ritamProducts)
    {
        $importedProductCount = 0;
        foreach ($ritamProducts as $ritamProduct) {

            if (!isset($ritamProduct->item_code)) {
                continue;
            }

            $product = $this->productRepository->findOneByCode($ritamProduct->item_code);

            if (is_null($product)) {
                $product = $this->productFactory->create($ritamProduct);
            } else {
                $product = $this->productFactory->update($product, $ritamProduct);
            }

            $this->productRepository->add($product);
            $importedProductCount++;
        }

        return $importedProductCount;
    }
}