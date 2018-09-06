<?php

namespace Elao\ElaoCommandMigration\Tests\Adapter;

use Elao\ElaoCommandMigration\Adapter\AdapterFactory;
use Elao\ElaoCommandMigration\Adapter\DoctrineAdapter;
use Elao\ElaoCommandMigration\Parser\Exception\InvalidYamlSchemaException;
use PHPUnit\Framework\TestCase;

class AdapterFactoryTest extends TestCase
{
    public function testCreateWithoutType()
    {
        $this->expectException(InvalidYamlSchemaException::class);
        $configuration = [];

        $factory = new AdapterFactory();
        $factory->create($configuration);
    }

    public function testCreateWithUnknown()
    {
        $this->expectException(InvalidYamlSchemaException::class);
        $configuration = [
            'type' => 'unknown'
        ];

        $factory = new AdapterFactory();
        $factory->create($configuration);
    }

    public function testCreateWithDbalWithoutDsn()
    {
        $this->expectException(InvalidYamlSchemaException::class);
        $configuration = [
            'type' => 'dbal'
        ];

        $factory = new AdapterFactory();
        $factory->create($configuration);
    }

    public function testCreateWithDbalWithDsn()
    {
        $configuration = [
            'type' => 'dbal',
            'dsn' => 'mysql://root@127.0.0.1/my_database'
        ];

        $factory = new AdapterFactory();
        $this->assertInstanceOf(DoctrineAdapter::class, $factory->create($configuration));
    }
}
