<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\Repository;

use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository as BaseTaxonRepository;

class TaxonRepository extends BaseTaxonRepository implements TaxonRepositoryInterface
{
    public function findOneByName(string $name, string $locale)
    {
        return$this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation')
            ->andWhere('translation.name = :name')
            ->andWhere('translation.locale = :locale')
            ->setParameter('name', $name)
            ->setParameter('locale', $locale)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}