<?php

namespace Elao\ElaoCommandMigration\Configuration\Adapter;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Connections\MasterSlaveConnection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;

final class DoctrineAdapter implements AdapterInterface
{
    /** @var Connection */
    private $connection;

    /** @var string */
    private $migrationsTableName;

    /** @var string */
    private $migrationColumnName;

    public function __construct(
        Connection $connection,
        string $migrationsTableName = 'command_migrations',
        string $migrationsColumnName = 'version'
    ) {
        $this->connection = $connection;
        $this->migrationsTableName = $migrationsTableName;
        $this->migrationsColumnName = $migrationsColumnName;
    }

    public function initialize(): void
    {
        $this->connect();
        if ($this->connection->getSchemaManager()->tablesExist([$this->migrationsTableName])) {
            return;
        }

        $columns = [
            $this->migrationsColumnName => new Column(
                $this->migrationsColumnName,
                Type::getType('string'),
                ['length' => 255]
            ),
        ];
        $table = new Table($this->migrationsTableName, $columns);
        $table->setPrimaryKey([$this->migrationsColumnName]);
        $this->connection->getSchemaManager()->createTable($table);
    }

    public function markVersion(string $version): void
    {
        $this->initialize();

        $this->connection->insert(
            $this->migrationsTableName,
            [
                $this->migrationColumnName => $version,
            ]
        );
    }

    /**
     * Explicitely opens the database connection. This is done to play nice
     * with DBAL's MasterSlaveConnection. Which, in some cases, connects to a
     * follower when fetching the executed migrations. If a follower is lagging
     * significantly behind that means the migrations system may see unexecuted
     * migrations that were actually executed earlier.
     *
     * @return bool The same value returned from the `connect` method
     */
    private function connect(): bool
    {
        if ($this->connection instanceof MasterSlaveConnection) {
            return $this->connection->connect('master');
        }

        return $this->connection->connect();
    }
}
