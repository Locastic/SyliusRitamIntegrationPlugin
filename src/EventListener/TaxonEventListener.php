<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Locastic\SyliusRitamIntegrationPlugin\Entity\Product;
use Sylius\Component\Core\Model\Taxon;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

class TaxonEventListener
{
    /**
     * @var TaxonRepositoryInterface
     */
    private $taxonRepository;

    /**
     * @var FactoryInterface
     */
    private $productTaxonFactory;

    /**
     * @var string
     */
    private $taxonCode;

    public function __construct(
        TaxonRepositoryInterface $taxonRepository,
        FactoryInterface $productTaxonFactory,
        string $taxonCode = "category"
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->productTaxonFactory = $productTaxonFactory;
        $this->taxonCode = $taxonCode;
    }


    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Product || $entity->getProductTaxons()->count() != 0) {
            return;
        }

        $taxon = $this->taxonRepository->findOneByCode($this->taxonCode);

        if (!$taxon instanceof Taxon) {
            return;
        }

        $productTaxon = $this->productTaxonFactory->createNew();
        $productTaxon->setTaxon($taxon);
        $productTaxon->setProduct($entity);

        $entity->addProductTaxon($productTaxon);
        $entity->setMainTaxon($taxon);
    }
}