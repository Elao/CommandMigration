<?php

namespace Elao\ElaoCommandMigration\Adapter;

use Elao\ElaoCommandMigration\Parser\Exception\InvalidYamlSchemaException;

final class AdapterFactory
{
    public function create(array $adapterConfiguration): AdapterInterface
    {
        if (!isset($adapterConfiguration['type'])) {
            throw new InvalidYamlSchemaException('Missing type of adapter');
        }

        switch (strtolower($adapterConfiguration['type'])) {
            case 'dbal':
                if (!isset($adapterConfiguration['dsn'])) {
                    throw new InvalidYamlSchemaException('Missing DSN parameter of dbal adapter');
                }

                return new DoctrineAdapter(
                    $adapterConfiguration['dsn'],
                    $adapterConfiguration['table_name'] ?? DoctrineAdapter::TABLE_NAME,
                    $adapterConfiguration['column_name'] ?? DoctrineAdapter::COLUMN_NAME
                );
            default:
                throw new InvalidYamlSchemaException('Missing compatible adapter');
        }
    }
}
