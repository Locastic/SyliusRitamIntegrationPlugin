<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Factory;

use Locastic\SyliusRitamIntegrationPlugin\Entity\Product;
use Locastic\SyliusRitamIntegrationPlugin\Entity\ProductInterface;

interface ProductFromRitamFactoryInterface
{

    public function create($ritamProduct): ProductInterface;

    public function update(Product $product, $ritamProduct): ProductInterface;

}