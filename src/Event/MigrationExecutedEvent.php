<?php

namespace Elao\CommandMigration\Event;

use Symfony\Component\EventDispatcher\Event;

class MigrationExecutedEvent extends Event
{
    private $version;

    public function __construct(string $version)
    {
        $this->version = $version;
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}
