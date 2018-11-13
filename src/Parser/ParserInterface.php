<?php

namespace Elao\CommandMigration\Parser;

interface ParserInterface
{
    public function getMigrations(): array;

    public function getVersions(): array;
}
