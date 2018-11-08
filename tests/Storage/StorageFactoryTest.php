<?php

namespace Elao\ElaoCommandMigration\Tests\Storage;

use Elao\ElaoCommandMigration\Storage\StorageFactory;
use Elao\ElaoCommandMigration\Storage\DoctrineStorage;
use Elao\ElaoCommandMigration\Parser\Exception\InvalidYamlSchemaException;
use PHPUnit\Framework\TestCase;

class StorageFactoryTest extends TestCase
{
    public function testCreateWithoutType()
    {
        $this->expectException(InvalidYamlSchemaException::class);
        $configuration = [];

        $factory = new StorageFactory();
        $factory->create($configuration);
    }

    public function testCreateWithUnknown()
    {
        $this->expectException(InvalidYamlSchemaException::class);
        $configuration = [
            'type' => 'unknown'
        ];

        $factory = new StorageFactory();
        $factory->create($configuration);
    }

    public function testCreateWithDbalWithoutDsn()
    {
        $this->expectException(InvalidYamlSchemaException::class);
        $configuration = [
            'type' => 'dbal'
        ];

        $factory = new StorageFactory();
        $factory->create($configuration);
    }

    public function testCreateWithDbalWithDsn()
    {
        $configuration = [
            'type' => 'dbal',
            'dsn' => 'mysql://root@127.0.0.1/my_database'
        ];

        $factory = new StorageFactory();
        $this->assertInstanceOf(DoctrineStorage::class, $factory->create($configuration));
    }
}
