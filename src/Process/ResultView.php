<?php

namespace Elao\ElaoCommandMigration\Process;

class ResultView
{
    /** @var bool */
    private $successful;

    /** @var string */
    private $command;

    /** @var string */
    private $output;

    public function __construct(
        bool $successful,
        string $command,
        string $output
    ) {
        $this->successful = $successful;
        $this->command = $command;
        $this->output = $output;
    }

    public function isSuccessful(): bool
    {
        return $this->successful;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getOutput(): string
    {
        return $this->output;
    }
}
