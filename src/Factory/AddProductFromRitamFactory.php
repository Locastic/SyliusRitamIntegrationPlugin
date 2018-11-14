<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Factory;

use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Locastic\SyliusRitamIntegrationPlugin\Entity\ProductInterface;

class AddProductFromRitamFactory implements ProductFromRitamFactoryInterface
{
    /** @var FactoryInterface */
    private $createProductFromRitamFactory;

    /**
     * @var string
     */
    private $locale;

    public function __construct(FactoryInterface $createProductFromRitamFactory, string $locale = 'en_US')
    {
        $this->createProductFromRitamFactory = $createProductFromRitamFactory;
        $this->locale = $locale;
    }

    public function createNew()
    {
        return $this->createProductFromRitamFactory->createNew();
    }


    public function createWithVariant(): \Sylius\Component\Product\Model\ProductInterface
    {
        return $this->createProductFromRitamFactory->createWithVariant();
    }

    public function createWithChannelPricing($ritamProduct, ChannelPricingInterface $channelPricing): ProductInterface
    {
        /**
         * @var ProductInterface $product
         */
        $product = $this->createProductFromRitamFactory->createWithVariant();

        $product->setCurrentLocale($this->locale);
        $product->setRitamId((int)($ritamProduct->item_id));
        $product->setUnitOfMeasure($ritamProduct->item_unit);
        $product->setName($ritamProduct->item_name);
        $product->setCode($ritamProduct->item_code);
        $product->setDescription($ritamProduct->item_description);
        $product->setShortDescription($ritamProduct->item_description);

        /**
         * @var ProductVariantInterface $productVariant
         */
        $productVariant = $product->getVariants()->first();
        $productVariant->setCurrentLocale($this->locale);
        $productVariant->setCode($ritamProduct->item_code);
        $productVariant->setName($ritamProduct->item_name);

        $productVariant->addChannelPricing($channelPricing);

        return $product;
    }
}