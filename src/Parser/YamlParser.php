<?php

namespace Elao\ElaoCommandMigration\Parser;

use Elao\ElaoCommandMigration\Parser\Exception\InvalidYamlSchemaException;
use Symfony\Component\Yaml\Yaml;

class YamlParser implements ParserInterface
{
    /** @var Yaml */
    private $yaml;

    /** @var string */
    private $filePath;

    public function __construct(Yaml $yaml, string $filePath)
    {
        $this->yaml = $yaml;
        $this->filePath = $filePath;
    }

    public function getMigrations(): array
    {
        $commandMigrations = $this->yaml->parseFile($this->filePath);

        if (!isset($commandMigrations['elao_command_migration']['migrations'])
            || !is_array($commandMigrations['elao_command_migration']['migrations'])
        ) {
            throw new InvalidYamlSchemaException('Missing migrations node');
        }

        return $commandMigrations['elao_command_migration']['migrations'];
    }

    public function getVersions(): array
    {
        return array_keys($this->getMigrations());
    }
}
