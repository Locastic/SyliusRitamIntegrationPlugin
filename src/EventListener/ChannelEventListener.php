<?php

declare(strict_types=1);

namespace Locastic\SyliusRitamIntegrationPlugin\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Locastic\SyliusRitamIntegrationPlugin\Entity\Product;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\Channel;

class ChannelEventListener
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Product || $entity->getChannels()->count() != 0) {
            return;
        }

        $channel = $this->channelRepository->findOneByCode('US_WEB');

        if (!$channel instanceof Channel) {
            return;
        }

        $entity->addChannel($channel);
    }
}