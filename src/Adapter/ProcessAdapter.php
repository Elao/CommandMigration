<?php

/*
 * This file is part of the ElaoCommandMigration project.
 *
 * Copyright (C) ElaoCommandMigration
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\ElaoCommandMigration\Adapter;

use Symfony\Component\Process\Process;

class ProcessAdapter
{
    public function getProcess(string $commandLine): Process
    {
        return new Process($commandLine);
    }
}
