<?php
namespace Locastic\SyliusRitamIntegrationPlugin\Factory;


use Locastic\SyliusRitamIntegrationPlugin\Entity\Product;
use Locastic\SyliusRitamIntegrationPlugin\Entity\ProductInterface;

interface ProductFromRitamFactoryInterface
{
    /**
     * @return ProductInterface
     */
    public function create($ritamProduct): ProductInterface;

    /**
     * @param $ritamProduct
     * @return ProductInterface
     */
    public function update(Product $product, $ritamProduct): ProductInterface;

}