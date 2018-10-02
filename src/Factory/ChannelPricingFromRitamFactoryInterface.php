<?php
namespace Locastic\SyliusRitamIntegrationPlugin\Factory;

use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface ChannelPricingFromRitamFactoryInterface extends FactoryInterface
{
    public function createFromRitam($ritamProduct): ChannelPricingInterface;
}