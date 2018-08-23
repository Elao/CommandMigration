<?php

namespace Elao\ElaoCommandMigration\Configuration\Factory\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

final class DBALConnectionFactory
{
    /** @var array */
    private $config;

    /** @var Connection */
    private $connection;

    private const SUPPORTED_DSN = [
        'db2' => 'db2',
        'ibm-db2' => 'ibm-db2',
        'mssql' => 'mssql',
        'sqlsrv+pdo' => 'pdo_sqlsrv',
        'mysql' => 'mysql',
        'mysql2' => 'mysql2',
        'mysql+pdo' => 'pdo_mysql',
        'pgsql' => 'pgsql',
        'postgres' => 'postgres',
        'pgsql+pdo' => 'pdo_pgsql',
        'sqlite' => 'sqlite',
        'sqlite3' => 'sqlite3',
        'sqlite+pdo' => 'pdo_sqlite',
    ];

    public function __construct(string $config)
    {
        $this->config = array_replace_recursive(
            [
                'connection' => [],
                'polling_interval' => 1000,
                'lazy' => true,
            ],
            $this->parseDsn($config)
        );
    }

    public function close(): void
    {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    public function getConnection(): Connection
    {
        if (!$this->connection) {
            $this->connection = DriverManager::getConnection($this->config['connection']);
            $this->connection->connect();
        }

        return $this->connection;
    }

    private function parseDsn(string $dsn): array
    {
        if (false === strpos($dsn, ':')) {
            throw new \LogicException(sprintf('The DSN is invalid. It does not have scheme separator ":".'));
        }

        if (false === parse_url($dsn)) {
            throw new \LogicException(sprintf('Failed to parse DSN "%s"', $dsn));
        }

        list($scheme) = explode(':', $dsn, 2);
        $scheme = strtolower($scheme);

        if (false == preg_match('/^[a-z\d+-.]*$/', $scheme)) {
            throw new \LogicException('The DSN is invalid. Scheme contains illegal symbols.');
        }

        if (false == isset(self::SUPPORTED_DSN[$scheme])) {
            throw new \LogicException(
                sprintf(
                    'The given DSN schema "%s" is not supported. There are supported schemes: "%s".',
                    $scheme,
                    implode('", "', array_keys(self::SUPPORTED_DSN))
                )
            );
        }

        $doctrineScheme = self::SUPPORTED_DSN[$scheme];

        return [
            'lazy' => true,
            'connection' => [
                'url' => $scheme . ':' === $dsn ? $doctrineScheme . '://root@localhost' : str_replace($scheme, $doctrineScheme, $dsn),
            ],
        ];
    }
}
