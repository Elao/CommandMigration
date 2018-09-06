<?php

namespace Elao\ElaoCommandMigration\Tests\Parser;

use Elao\ElaoCommandMigration\Parser\Exception\InvalidYamlSchemaException;
use Elao\ElaoCommandMigration\Parser\YamlParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;

class YamlParserTest extends TestCase
{
    public function testFileNotExists()
    {
        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('File "not-exists.yaml" does not exist.');

        new YamlParser('not-exists.yaml');
    }

    public function testGetAdapterConfiguration()
    {
        $yamlParser = new YamlParser(__DIR__ . '/../Fixtures/elao_command_migration.yaml');
        $this->assertEquals(
            [
                'type' => 'dbal',
                'dsn' => 'mysql://root@127.0.0.1/my_database',
                'table_name' => 'command_migrations',
            ],
            $yamlParser->getAdapterConfiguration()
        );
    }

    public function testGetMigrations()
    {
        $yamlParser = new YamlParser(__DIR__ . '/../Fixtures/elao_command_migration.yaml');
        $this->assertSame(
            [
                '201809061100020' => [
                    'php -r "echo \"toto\";"',
                    'php -r "echo \"Salut Mathieu\";"',
                ],
                '201809051738230' => [
                    'php -r "echo \"This is a test\";"',
                ],
            ],
            $yamlParser->getMigrations()
        );
    }

    public function testGetMigrationsWithoutMigration()
    {
        $this->expectException(InvalidYamlSchemaException::class);
        $this->expectExceptionMessage('Missing migrations node');

        $yamlParser = new YamlParser(__DIR__ . '/../Fixtures/elao_command_without_migration.yaml');
        $yamlParser->getMigrations();
    }

    public function testGetMigrationsNotAnArray()
    {
        $this->expectException(InvalidYamlSchemaException::class);
        $this->expectExceptionMessage('Missing migrations node');

        $yamlParser = new YamlParser(__DIR__ . '/../Fixtures/elao_command_migration_not_an_array.yaml');
        $yamlParser->getMigrations();
    }

    public function testGetVersions()
    {
        $yamlParser = new YamlParser(__DIR__ . '/../Fixtures/elao_command_migration.yaml');
        $this->assertEquals(
            [
                '201809061100020',
                '201809051738230',
            ],
            $yamlParser->getVersions()
        );
    }
}
