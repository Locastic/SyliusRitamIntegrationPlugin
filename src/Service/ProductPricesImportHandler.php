<?php

namespace Locastic\SyliusRitamIntegrationPlugin\Service;

use Locastic\SyliusRitamIntegrationPlugin\Entity\ProductInterface;
use Locastic\SyliusRitamIntegrationPlugin\Factory\ChannelPricingFromRitamFactoryInterface;
use Locastic\SyliusRitamIntegrationPlugin\Repository\ProductRepositoryInterface;

class ProductPricesImportHandler
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ChannelPricingFromRitamFactoryInterface
     */
    private $channelPricingFactory;

    /**
     * @var string
     */
    private $channelCode;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ChannelPricingFromRitamFactoryInterface $channelPricingRepository,
        string $channelCode = 'US_WEB'
    ) {
        $this->productRepository = $productRepository;
        $this->channelPricingFactory = $channelPricingRepository;
        $this->channelCode = $channelCode;
    }


    public function importProductPrices($ritamProductPrices)
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

            if ($productVariant->getChannelPricings()->count() != 0) {
                $channelPricing = $productVariant->getChannelPricings()->first();
            } else {
                $channelPricing = $this->channelPricingFactory->createFromRitam($ritamProductPrice);
            }

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

}