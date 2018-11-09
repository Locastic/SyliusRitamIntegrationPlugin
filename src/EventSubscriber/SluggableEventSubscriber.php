<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Sylius\Component\Core\Model\ProductTranslation;
use Sylius\Component\Product\Generator\SlugGenerator;
use Sylius\Component\Taxonomy\Model\TaxonTranslation;

class SluggableEventSubscriber implements EventSubscriber
{
    private static $sluggableEntities = [
        ProductTranslation::class,
        TaxonTranslation::class
    ];
    /**
     * @var SlugGenerator
     */
    private $slugGenerator;

    public function __construct(SlugGenerator $slugGenerator)
    {
        $this->slugGenerator = $slugGenerator;
    }

    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
        );

    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!in_array(get_class($entity), self::$sluggableEntities)) {
            return;
        }

        $name = $entity->getName().$entity->getTranslatable()->getCode().mt_rand(1,10);
        $slug = $this->slugGenerator->generate($name);
        $entity->setSlug($slug);
    }
}