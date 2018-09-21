<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Entity;

use Sylius\Component\Core\Model\ProductInterface as BaseProductInterface;

interface ProductInterface extends BaseProductInterface
{
    public function getUnitOfMeasure(): ?string;

    public function setUnitOfMeasure(string $unitOfMeasure): void;

    public function getRitamId(): ?int;

    public function setRitamId(int $ritamId): void;
}