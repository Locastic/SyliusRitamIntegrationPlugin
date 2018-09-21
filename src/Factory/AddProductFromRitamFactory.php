<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Factory;

use Locastic\SyliusRitamIntegrationPlugin\Entity\Product;
use Locastic\SyliusRitamIntegrationPlugin\Entity\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class AddProductFromRitamFactory implements ProductFromRitamFactoryInterface
{

    /** @var FactoryInterface */
    private $createProductFromRitamFactory;

    /**
     * @var string
     */
    private $locale;

    public function __construct(FactoryInterface $createProductFromRitamFactory, string $locale = 'hr_HR')
    {
        $this->createProductFromRitamFactory = $createProductFromRitamFactory;
        $this->locale = $locale;
    }

    public function create($ritamProduct): ProductInterface
    {
        $product = $this->createProductFromRitamFactory->createNew();

        return $this->populateProductFromRitamProduct($product, $ritamProduct);
    }

    public function update(Product $product, $ritamProduct): ProductInterface
    {
        return $this->populateProductFromRitamProduct($product, $ritamProduct);
    }

    private function populateProductFromRitamProduct(Product $product, $ritamProduct)
    {
        $product->setCurrentLocale($this->locale);
        $product->setRitamId($ritamProduct->item_id);
        $product->setUnitOfMeasure($ritamProduct->item_unit);
        $product->setName($ritamProduct->item_name);
        $product->setCode($ritamProduct->item_code);
        $product->setDescription($ritamProduct->item_description);

        return $product;
    }
}