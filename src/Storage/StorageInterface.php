<?php

namespace Elao\ElaoCommandMigration\Storage;

interface StorageInterface
{
    public function initialize(): void;

    public function markVersion(string $version): void;

    public function getMigratedVersions(): array;
}
