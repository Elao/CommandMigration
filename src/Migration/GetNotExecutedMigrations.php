<?php

namespace Elao\ElaoCommandMigration\Migration;

use Elao\ElaoCommandMigration\Adapter\AdapterInterface;

class GetNotExecutedMigrations
{
    /** @var AdapterInterface */
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function __invoke(array $migrations, array $versions): array
    {
        $notExecutedVersions = array_diff($versions, $this->adapter->getMigratedVersions());
        $migrationsToExecute = [];

        foreach ($notExecutedVersions as $version) {
            if (isset($migrations[$version])) {
                $migrationsToExecute[$version] = $migrations[$version];
            }
        }

        return $migrationsToExecute;
    }
}
