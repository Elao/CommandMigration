<?php

namespace Elao\ElaoCommandMigration\Tests\Process;

use Elao\ElaoCommandMigration\Adapter\ProcessAdapter;
use Elao\ElaoCommandMigration\Event\MigrationExecutedEvent;
use Elao\ElaoCommandMigration\Events;
use Elao\ElaoCommandMigration\Process\ExecuteVersion;
use Elao\ElaoCommandMigration\Process\ResultView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Process;

class ExecuteVersionTest extends TestCase
{
    public function testExecuteVersion(): void
    {
        $migrations = [
            'identifier1' => [
                'php bin/elaoCommandMigration inception.yml',
            ],
            '1234567890' => [
                'php -r "echo \"toto\";"',
                'php -r "echo \"test\";"',
            ],
        ];

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $processAdapter = $this->prophesize(ProcessAdapter::class);

        $firstProcess = $this->prophesize(Process::class);
        $processAdapter->getProcess('php bin/elaoCommandMigration inception.yml')->shouldBeCalled()->willReturn($firstProcess);
        $firstProcess->run()->shouldBeCalled();
        $firstProcess->isSuccessful()->shouldBeCalled()->willReturn(false);
        $firstProcess->getErrorOutput()->shouldBeCalled()->willReturn('Endless spinning');

        $secondProcess = $this->prophesize(Process::class);
        $processAdapter->getProcess('php -r "echo \"toto\";"')->shouldBeCalled()->willReturn($secondProcess);
        $secondProcess->run()->shouldBeCalled();
        $secondProcess->isSuccessful()->shouldBeCalled()->willReturn(true);
        $secondProcess->getErrorOutput()->shouldBeCalled()->willReturn('');

        $thirdProcess = $this->prophesize(Process::class);
        $processAdapter->getProcess('php -r "echo \"test\";"')->shouldBeCalled()->willReturn($thirdProcess);
        $thirdProcess->run()->shouldBeCalled();
        $thirdProcess->isSuccessful()->shouldBeCalled()->willReturn(true);
        $thirdProcess->getErrorOutput()->shouldBeCalled()->willReturn('');

        $eventDispatcher->dispatch(Events::MIGRATION_EXECUTED, new MigrationExecutedEvent('identifier1'))->shouldBeCalled();
        $eventDispatcher->dispatch(Events::MIGRATION_EXECUTED, new MigrationExecutedEvent('1234567890'))->shouldBeCalled();

        $executeVersion = new ExecuteVersion($eventDispatcher->reveal(), $processAdapter->reveal());
        $generators = $executeVersion($migrations);

        $expected = [
            0 => new ResultView(false, 'php bin/elaoCommandMigration inception.yml', 'Endless spinning'),
            1 => new ResultView(true, 'php -r "echo \"toto\";"', ''),
            2 => new ResultView(true, 'php -r "echo \"test\";"', ''),
        ];

        foreach ($generators as $key => $generator) {
            $this->assertInstanceOf(ResultView::class, $generator);
            $this->assertTrue(isset($expected[$key]));
            $this->assertEquals($expected[$key], $generator);
        }
    }
}
