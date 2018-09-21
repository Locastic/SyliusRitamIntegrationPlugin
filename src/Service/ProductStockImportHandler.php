<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Service;

use Locastic\SyliusRitamIntegrationPlugin\Entity\ProductInterface;
use Locastic\SyliusRitamIntegrationPlugin\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Model\ProductVariant;

class ProductStockImportHandler
{

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function importProductStock($ritamProductStock)
    {
        $importedProductStockCount = 0;
        $batchSize = 100;

        $this->productRepository->disableSqlLogger();
        foreach ($ritamProductStock as $ritamProductStockInfo) {

            if (!isset($ritamProductStockInfo->item_id)) {
                continue;
            }

            $product = $this->productRepository->findOneByRitamId(intval($ritamProductStockInfo->item_id));

            if (!$product instanceof ProductInterface) {
                continue;
            }

            $productVariant = $product->getVariants()->first();
            /**
             * @var ProductVariant $productVariant
             */
            $productVariant->setOnHand(intval($ritamProductStockInfo->item_qty));

            $this->productRepository->persist($product);

            // bulk insert - flush after every $batchSize persists
            if (($importedProductStockCount % $batchSize) === 0) {
                $this->productRepository->savePersisted();
            }

            $importedProductStockCount++;
        }

        // save remaining
        $this->productRepository->savePersisted();

        return $importedProductStockCount;
    }
}