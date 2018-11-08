<?php

namespace Elao\ElaoCommandMigration\Process;

use Elao\ElaoCommandMigration\Adapter\ProcessAdapter;
use Elao\ElaoCommandMigration\Storage\StorageFactory;
use Elao\ElaoCommandMigration\Migration\GetNotExecutedMigrations;
use Elao\ElaoCommandMigration\Parser\YamlParser;
use Elao\ElaoCommandMigration\Subscriber\MigrationExecutedSubscriber;
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
        $storage = $storageFactory->create($yaml->getAdapterConfiguration());

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
