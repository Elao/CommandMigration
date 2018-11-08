<?php

namespace Elao\ElaoCommandMigration\Storage;

use Elao\ElaoCommandMigration\Parser\Exception\InvalidYamlSchemaException;

final class StorageFactory
{
    public function create(array $adapterConfiguration): StorageInterface
    {
        if (!isset($adapterConfiguration['type'])) {
            throw new InvalidYamlSchemaException('Missing type of adapter');
        }

        switch (strtolower($adapterConfiguration['type'])) {
            case 'dbal':
                if (!isset($adapterConfiguration['dsn'])) {
                    throw new InvalidYamlSchemaException('Missing DSN parameter of dbal adapter');
                }

                return new DoctrineStorage(
                    $adapterConfiguration['dsn'],
                    $adapterConfiguration['table_name'] ?? DoctrineStorage::TABLE_NAME,
                    $adapterConfiguration['column_name'] ?? DoctrineStorage::COLUMN_NAME
                );
            default:
                throw new InvalidYamlSchemaException('Missing compatible adapter');
        }
    }
}
