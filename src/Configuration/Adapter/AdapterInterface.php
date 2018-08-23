<?php

namespace Elao\ElaoCommandMigration\Configuration\Adapter;

interface AdapterInterface
{
    public function initialize(): void;
    public function markVersion(string $version): void;
}
