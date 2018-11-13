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
    /** @var string */
    private $configurationFilePath;

    public function __construct(string $configurationFilePath)
    {
        $this->configurationFilePath = $configurationFilePath;
    }

    public function __invoke(): \Generator
    {
        $yaml = new YamlParser($this->configurationFilePath);
        $storageFactory = new StorageFactory();
        $storage = $storageFactory->create($yaml->getStorageConfiguration());

        $notExecutedMigrations = (new GetNotExecutedMigrations($storage))
        (
            $yaml->getMigrations(),
            $yaml->getVersions()
        );

        $dispatcher = new EventDispatcher();
        $subscriber = new MigrationExecutedSubscriber($storage);
        $dispatcher->addSubscriber($subscriber);

        return (new ExecuteVersion($dispatcher, new ProcessAdapter()))($notExecutedMigrations);
    }
}
