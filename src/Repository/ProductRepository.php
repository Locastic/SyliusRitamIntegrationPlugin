<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Repository;

use Locastic\SyliusRitamIntegrationPlugin\Entity\Product;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;

class ProductRepository extends BaseProductRepository implements ProductRepositoryInterface
{
    public function persist(Product $product): void
    {
        $this->_em->persist($product);
    }

    public function savePersisted(): void
    {
        $this->_em->flush();
        $this->_em->clear();
    }

    public function disableSqlLogger(): void
    {
        $this->_em->getConnection()->getConfiguration()->setSQLLogger(null);
    }

    public function findOneByRitamId(int $ritamId)
    {
        return $this->createQueryBuilder('o')
            ->where('o.ritamId = :ritamId')
            ->setParameter('ritamId', $ritamId)
            ->getQuery()
            ->getSingleResult();
    }
}