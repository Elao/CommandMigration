<?php

namespace Elao\CommandMigration\Tests\e2e;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class E2ETest extends TestCase
{
    public function testE2E()
    {
        $pathToFile = sprintf('%s/elao_command_migration.yaml', __DIR__);
        $process = new Process(sprintf('php bin/elao-command-migration %s', $pathToFile));
        $process->run();

        $this->assertFileExists('elao_command_migration_test.txt');
        unlink('elao_command_migration_test.txt');
    }
}
