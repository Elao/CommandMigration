<?php

namespace Elao\CommandMigration\Process;

use Elao\CommandMigration\Adapter\ProcessAdapter;
use Elao\CommandMigration\Migration\GetNotExecutedMigrations;
use Elao\CommandMigration\Parser\YamlParser;
use Elao\CommandMigration\Storage\StorageFactory;
use Elao\CommandMigration\Subscriber\MigrationExecutedSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Run
{
    public function __invoke(array $storageConfiguration, array $migrations, array $versions): \Generator
    {
        $storageFactory = new StorageFactory();
        $storage = $storageFactory->create($storageConfiguration);

        $notExecutedMigrations = (new GetNotExecutedMigrations($storage))
        (
            $migrations,
            $versions
        );

        $dispatcher = new EventDispatcher();
        $subscriber = new MigrationExecutedSubscriber($storage);
        $dispatcher->addSubscriber($subscriber);

        return (new ExecuteVersion($dispatcher, new ProcessAdapter()))($notExecutedMigrations);
    }
}
