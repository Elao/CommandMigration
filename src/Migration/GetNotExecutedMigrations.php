<?php

namespace Elao\CommandMigration\Migration;

use Elao\CommandMigration\Storage\StorageInterface;

class GetNotExecutedMigrations
{
    /** @var StorageInterface */
    private $adapter;

    public function __construct(StorageInterface $adapter)
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
