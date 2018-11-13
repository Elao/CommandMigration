<?php

namespace Elao\CommandMigration\Process;

use Elao\CommandMigration\Adapter\ProcessAdapter;
use Elao\CommandMigration\Event\MigrationExecutedEvent;
use Elao\CommandMigration\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ExecuteVersion
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var ProcessAdapter */
    private $processAdapter;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ProcessAdapter $processAdapter
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->processAdapter = $processAdapter;
    }

    public function __invoke(array $migrationsToExecute): \Generator
    {
        foreach ($migrationsToExecute as $version => $commands) {
            foreach ($commands as $command) {
                $process = $this->processAdapter->getProcess($command);
                $process->run();

                yield new ResultView($process->isSuccessful(), $command, $process->getErrorOutput());
            }

            $this->eventDispatcher->dispatch(
                Events::MIGRATION_EXECUTED,
                new MigrationExecutedEvent($version)
            );
        }
    }
}
