<?php

namespace Elao\CommandMigration\Adapter;

use Symfony\Component\Process\Process;

class ProcessAdapter
{
    public function getProcess(string $commandLine): Process
    {
        return new Process($commandLine);
    }
}
