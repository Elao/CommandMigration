<?php

namespace Elao\ElaoCommandMigration\Process;

use Elao\ElaoCommandMigration\Event\MigrationExecutedEvent;
use Elao\ElaoCommandMigration\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Process;

class ExecuteVersion
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(array $migrationsToExecute): \Generator
    {
        foreach ($migrationsToExecute as $version => $commands) {
            foreach ($commands as $command) {
                $proccess = new Process($command);
                $proccess->run();

                yield new ResultView($proccess->isSuccessful(), $command, $proccess->getErrorOutput());
            }

            $this->eventDispatcher->dispatch(
                Events::MIGRATION_EXECUTED,
                new MigrationExecutedEvent($version)
            );
        }
    }
}
