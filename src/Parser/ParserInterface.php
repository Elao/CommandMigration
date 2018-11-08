<?php

namespace Elao\ElaoCommandMigration\Parser;

interface ParserInterface
{
    public function getMigrations(): array;

    public function getVersions(): array;
}
