<?php
declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Sylius\Component\Core\Model\ProductTranslation;
use Sylius\Component\Product\Generator\SlugGenerator;

class SluggableEventSubscriber implements EventSubscriber
{
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

        if (!$entity instanceof ProductTranslation) {
            return;
        }

        $name = $entity->getName().$entity->getTranslatable()->getCode();
        $slug = $this->slugGenerator->generate($name);
        $entity->setSlug($slug);
    }
}