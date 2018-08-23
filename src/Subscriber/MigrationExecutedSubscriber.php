<?php

namespace Elao\ElaoCommandMigration\Subscriber;

use Elao\ElaoCommandMigration\Adapter\AdapterInterface;
use Elao\ElaoCommandMigration\Event\MigrationExecutedEvent;
use Elao\ElaoCommandMigration\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MigrationExecutedSubscriber implements EventSubscriberInterface
{
    /** @var AdapterInterface */
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::MIGRATION_EXECUTED => 'onMigrationExecuted',
        ];
    }

    public function onMigrationExecuted(MigrationExecutedEvent $event): void
    {
        $this->adapter->markVersion($event->getVersion());
    }
}
