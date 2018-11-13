<?php

namespace Elao\CommandMigration\Subscriber;

use Elao\CommandMigration\Event\MigrationExecutedEvent;
use Elao\CommandMigration\Events;
use Elao\CommandMigration\Storage\StorageInterface;
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
