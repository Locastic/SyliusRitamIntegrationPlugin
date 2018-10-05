<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Service;

use Locastic\SyliusRitamIntegrationPlugin\Util\RitamOrderFormatter;
use Sylius\Component\Core\Model\OrderInterface;

class OrderSender
{
    /**
     * @var RitamApiHandler
     */
    private $ritamApiHandler;

    public function __construct(RitamApiHandler $ritamApiHandler)
    {
        $this->ritamApiHandler = $ritamApiHandler;
    }

    public function sendSyliusOrderToRitamApi(OrderInterface $order)
    {
        $ritamOrder = RitamOrderFormatter::formatRitamOrder($order);
        $this->ritamApiHandler->postOrderToRitam($ritamOrder);
    }
}