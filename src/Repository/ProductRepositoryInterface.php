<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Repository;

use Locastic\SyliusRitamIntegrationPlugin\Entity\Product;
use Sylius\Component\Core\Repository\ProductRepositoryInterface as BaseProductRepositoryInterface;

interface ProductRepositoryInterface extends BaseProductRepositoryInterface
{
    public function persist(Product $product): void;

    public function savePersisted(): void;

    public function disableSqlLogger(): void;

    public function findOneByRitamId(int $ritamId);
}