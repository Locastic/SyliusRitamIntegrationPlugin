<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Entity;

use Sylius\Component\Core\Model\Product as BaseProduct;

class Product extends BaseProduct implements ProductInterface
{
    /**
     * @var string
     */
    protected $unitOfMeasure;

    /**
     * @var int
     */
    protected $ritamId;

    /**
     * @return string
     */
    public function getUnitOfMeasure(): ?string
    {
        return $this->unitOfMeasure;
    }

    public function setUnitOfMeasure(string $unitOfMeasure): void
    {
        $this->unitOfMeasure = $unitOfMeasure;
    }

    public function getRitamId(): ?int
    {
        return $this->ritamId;
    }

    public function setRitamId(int $ritamId): void
    {
        $this->ritamId = $ritamId;
    }
}