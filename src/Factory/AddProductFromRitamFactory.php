<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Locastic\SyliusRitamIntegrationPlugin\Entity\ProductInterface;

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

    public function createNew()
    {
        return $this->createProductFromRitamFactory->createNew();
    }

    public function create($ritamProduct): ProductInterface
    {
        $product = $this->createProductFromRitamFactory->createNew();

        $product->setCurrentLocale($this->locale);
        $product->setRitamId(intval($ritamProduct->item_id));
        $product->setUnitOfMeasure($ritamProduct->item_unit);
        $product->setName($ritamProduct->item_name);
        $product->setCode($ritamProduct->item_code);
        $product->setDescription($ritamProduct->item_description);

        return $product;
    }
}