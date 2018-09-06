<?php

namespace Elao\ElaoCommandMigration\Process;

use Elao\ElaoCommandMigration\Adapter\AdapterFactory;
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
        $adapterFactory = new AdapterFactory();
        $adapter = $adapterFactory->create($yaml->getAdapterConfiguration());

        $notExecutedMigrations = (new GetNotExecutedMigrations($adapter))
        (
            $yaml->getMigrations(),
            $yaml->getVersions()
        );

        $dispatcher = new EventDispatcher();
        $subscriber = new MigrationExecutedSubscriber($adapter);
        $dispatcher->addSubscriber($subscriber);

        return (new ExecuteVersion($dispatcher))($notExecutedMigrations);
    }
}
