<?php

namespace Elao\ElaoCommandMigration\Adapter;

interface AdapterInterface
{
    public function initialize(): void;

    public function markVersion(string $version): void;

    public function getMigratedVersions(): array;
}
