<?php

namespace Elao\ElaoCommandMigration\Parser;

use Elao\ElaoCommandMigration\Parser\Exception\InvalidYamlSchemaException;
use Symfony\Component\Yaml\Yaml;
use function is_array;

class YamlParser implements ParserInterface
{
    /** @var mixed */
    private $configuration;

    public function __construct(string $filePath)
    {
        $this->configuration = Yaml::parseFile($filePath);
    }

    public function getStorageConfiguration(): array
    {
        if (!isset($this->configuration['elao_command_migration']['storage'])) {
            throw new InvalidYamlSchemaException('Missing adapter node');
        }

        return $this->configuration['elao_command_migration']['storage'];
    }

    public function getMigrations(): array
    {
        if (!isset($this->configuration['elao_command_migration']['migrations'])
            || !is_array($this->configuration['elao_command_migration']['migrations'])
        ) {
            throw new InvalidYamlSchemaException('Missing migrations node');
        }

        return $this->configuration['elao_command_migration']['migrations'];
    }

    public function getVersions(): array
    {
        return array_keys($this->getMigrations());
    }
}
