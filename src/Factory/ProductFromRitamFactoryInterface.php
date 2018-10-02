<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Factory;

use Locastic\SyliusRitamIntegrationPlugin\Entity\ProductInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface ProductFromRitamFactoryInterface extends FactoryInterface
{
    public function createWithChannelPricing($ritamProduct, ChannelPricingInterface $channelPricing): ProductInterface;

    public function createWithVariant(): ProductInterface;
}