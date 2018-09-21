<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Factory;

use Locastic\SyliusRitamIntegrationPlugin\Entity\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface ProductFromRitamFactoryInterface extends FactoryInterface
{
    public function create($ritamProduct): ProductInterface;

    public function createWithVariant(): ProductInterface;
}