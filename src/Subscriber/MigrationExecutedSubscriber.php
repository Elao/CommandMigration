<?php

namespace Elao\ElaoCommandMigration\Subscriber;

use Elao\ElaoCommandMigration\Storage\StorageInterface;
use Elao\ElaoCommandMigration\Event\MigrationExecutedEvent;
use Elao\ElaoCommandMigration\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MigrationExecutedSubscriber implements EventSubscriberInterface
{
    /** @var StorageInterface */
    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::MIGRATION_EXECUTED => 'onMigrationExecuted',
        ];
    }

    public function onMigrationExecuted(MigrationExecutedEvent $event): void
    {
        $this->storage->markVersion($event->getVersion());
    }
}
