<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Factory;

use Locastic\SyliusRitamIntegrationPlugin\Entity\ProductInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Product\Factory\ProductFactoryInterface;

interface ProductFromRitamFactoryInterface extends ProductFactoryInterface
{
    public function createWithChannelPricing($ritamProduct, ChannelPricingInterface $channelPricing): ProductInterface;
}