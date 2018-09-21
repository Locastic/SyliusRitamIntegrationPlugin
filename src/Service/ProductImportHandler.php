<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Service;

use Locastic\SyliusRitamIntegrationPlugin\Factory\ProductFromRitamFactoryInterface;
use Locastic\SyliusRitamIntegrationPlugin\Repository\ProductRepositoryInterface;

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

    public function __construct(
        ProductFromRitamFactoryInterface $productFactory,
        ProductRepositoryInterface $productRepository
    ) {
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
    }

    public function importProducts($ritamProducts)
    {
        $importedProductCount = 0;
        $batchSize = 100;

        $this->productRepository->disableSqlLogger();
        foreach ($ritamProducts as $ritamProduct) {

            if (!isset($ritamProduct->item_code)) {
                continue;
            }

            $product = $this->productRepository->findOneByCode($ritamProduct->item_code);

            if (!is_null($product)) {
                continue;
            }

            $product = $this->productFactory->create($ritamProduct);
            $this->productRepository->persist($product);

            // bulk insert - flush after every $batchSize persists
            if (($importedProductCount % $batchSize) === 0) {
                $this->productRepository->savePersisted();
            }

            $importedProductCount++;
        }

        // save remaining
        $this->productRepository->savePersisted();

        return $importedProductCount;
    }
}