<?php

namespace Locastic\SyliusRitamIntegrationPlugin\Util;

class PriceFormatter
{

    public static function formatPrice($price)
    {
        return intval(floatval($price) * 100);
    }
}