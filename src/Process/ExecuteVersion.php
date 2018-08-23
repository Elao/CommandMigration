<?php

namespace Elao\ElaoCommandMigration\Process;

use Elao\ElaoCommandMigration\Event\MigrationExecutedEvent;
use Elao\ElaoCommandMigration\Events;
use Elao\ElaoCommandMigration\Migration\GetNotExecutedMigrations;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Process;

class ExecuteVersion
{
    /** @var GetNotExecutedMigrations */
    private $getNotExecutedMigrations;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        GetNotExecutedMigrations $getNotExecutedMigrations,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->getNotExecutedMigrations = $getNotExecutedMigrations;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke()
    {
        $migrationsToExecute = ($this->getNotExecutedMigrations)();

        foreach ($migrationsToExecute as $version => $commands) {
            foreach ($commands as $command) {
                $proccess = new Process($command);
                $proccess->run();
            }

            $this->eventDispatcher->dispatch(
                Events::MIGRATION_EXECUTED,
                new MigrationExecutedEvent($version)
            );
        }
    }
}
