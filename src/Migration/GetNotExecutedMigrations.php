<?php

namespace Elao\ElaoCommandMigration\Migration;

use Elao\ElaoCommandMigration\Adapter\AdapterInterface;
use Elao\ElaoCommandMigration\Parser\ParserInterface;

class GetNotExecutedMigrations
{
    /** @var AdapterInterface */
    private $adapter;

    /** @var ParserInterface */
    private $parser;

    public function __construct(AdapterInterface $adapter, ParserInterface $parser)
    {
        $this->adapter = $adapter;
        $this->parser = $parser;
    }

    public function __invoke(): array
    {
        $migrations = $this->parser->getMigrations();
        $notExecutedVersions = array_diff($this->parser->getVersions(), $this->adapter->getMigratedVersions());
        $migrationsToExecute = [];

        foreach ($notExecutedVersions as $version) {
            if (isset($migrations[$version])) {
                $migrationsToExecute[$version] = $migrations[$version];
            }
        }

        return $migrationsToExecute;
    }
}
