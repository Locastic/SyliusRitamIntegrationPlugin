<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Util;

use DateTime;
use Sylius\Component\Core\Model\OrderInterface;

class RitamOrderFormatter
{
    public static function formatRitamOrder(OrderInterface $order)
    {
        $today = new DateTime();
        $data = [];

        $header = [
            "datum" => $today->format('d.m.Y'),
            "broj" => $order->getNumber(),
            "customer_name" => $order->getCustomer()->getFullName(),
            "customer_email" => $order->getCustomer()->getEmail(),
            "customer_shiping_address" => $order->getShippingAddress()->getStreet(),
            "customer_zip_code" => $order->getShippingAddress()->getPostcode(),
            "customer_place_name" => $order->getShippingAddress()->getCity(),
            "customer_state_code" => $order->getShippingAddress()->getCountryCode(),
        ];

        $data["zaglavlje"] = $header;
        $items = [];

        foreach ($order->getItems() as $item) {
            $item = [
                "art_id" => $item->getProduct()->getRitamId(),
                "kolicina" => $item->getQuantity(),
                "vpc" => $item->getDiscountedUnitPrice(),
                "mpc" => $item->getUnitPrice(),
            ];
            $items[] = $item;
        }

        $data["stavke"] = $items;

        return json_encode($data);
    }
}