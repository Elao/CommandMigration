<?php

namespace Elao\ElaoCommandMigration\Parser;

class YamlParser implements ParserInterface
{
    public function getMigrations(): array
    {
        return [
            '1234567' => ['php bin/console cache:clear'],
            '20180823161327' => ['php -r "echo \"hello\";"'],
        ];
    }

    public function getVersions(): array
    {
        return array_keys($this->getMigrations());
    }
}
