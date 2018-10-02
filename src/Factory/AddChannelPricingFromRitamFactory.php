<?php
namespace Locastic\SyliusRitamIntegrationPlugin\Factory;

use Locastic\SyliusRitamIntegrationPlugin\Util\PriceFormatter;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class AddChannelPricingFromRitamFactory implements ChannelPricingFromRitamFactoryInterface
{
    /** @var FactoryInterface */
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

        $price = $originalPrice = 0;
        /**
         * @var ChannelPricingInterface $channelPricing
         */
        if (isset($ritamProduct->item_mpc) && isset($ritamProduct->item_vpc)) {
            $price = $ritamProduct->item_vpc;
            $originalPrice = $ritamProduct->item_mpc;
        }

        $channelPricing->setOriginalPrice(PriceFormatter::formatPrice($originalPrice));
        $channelPricing->setPrice(PriceFormatter::formatPrice($price));
        $channelPricing->setChannelCode($this->channelCode);

        return $channelPricing;
    }
}