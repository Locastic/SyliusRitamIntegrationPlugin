<?php

namespace Locastic\SyliusRitamIntegrationPlugin\Factory;

use Locastic\SyliusRitamIntegrationPlugin\Util\PriceFormatter;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class AddChannelPricingFromRitamFactory implements ChannelPricingFromRitamFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $createChannelPricingFromRitamFactory;

    /**
     * @var string
     */
    private $channelCode;

    public function __construct(FactoryInterface $createChannelPricingFromRitamFactory, string $channelCode = 'US_WEB')
    {
        $this->createChannelPricingFromRitamFactory = $createChannelPricingFromRitamFactory;
        $this->channelCode = $channelCode;
    }

    public function createNew()
    {
        return $this->createChannelPricingFromRitamFactory->createNew();
    }

    public function createFromRitam($ritamProduct): ChannelPricingInterface
    {
        $channelPricing = $this->createChannelPricingFromRitamFactory->createNew();

        $price = 0;

        /**
         * @var ChannelPricingInterface $channelPricing
         */
        if (isset($ritamProduct->item_mpc)) {
            $price = $ritamProduct->item_mpc;
        }

        $channelPricing->setPrice(PriceFormatter::formatPrice($price));
        $channelPricing->setChannelCode($this->channelCode);

        return $channelPricing;
    }
}