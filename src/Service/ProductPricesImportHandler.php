<?php

namespace Locastic\SyliusRitamIntegrationPlugin\Service;

use Locastic\SyliusRitamIntegrationPlugin\Entity\ProductInterface;
use Locastic\SyliusRitamIntegrationPlugin\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class ProductPricesImportHandler
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var FactoryInterface
     */
    private $channelPricingFactory;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        FactoryInterface $channelPricingRepository
    ) {
        $this->productRepository = $productRepository;
        $this->channelPricingFactory = $channelPricingRepository;
    }


    public function importProductStock($ritamProductPrices)
    {
        $importedProductStockCount = 0;
        $batchSize = 100;

        $this->productRepository->disableSqlLogger();
        foreach ($ritamProductPrices as $ritamProductPrice) {

            if (!isset($ritamProductPrice->item_id)) {
                continue;
            }

            $product = $this->productRepository->findOneByRitamId(intval($ritamProductPrice->item_id));

            if (!$product instanceof ProductInterface) {
                continue;
            }

            $productVariant = $product->getVariants()->first();

            /**
             * @var ChannelPricingInterface $channelPricing
             */
            $channelPricing = $this->channelPricingFactory->createNew();
            $channelPricing->setOriginalPrice($this->formatPrice($ritamProductPrice->item_mpc));
            $channelPricing->setPrice($this->formatPrice($ritamProductPrice->item_vpc));
            $channelPricing->setChannelCode('US_WEB');

            $productVariant->addChannelPricing($channelPricing);

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

    private function formatPrice($price)
    {
        return intval(floatval($price) * 100);
    }
}