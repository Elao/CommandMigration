<?php

namespace Elao\CommandMigration\Bridge\Symfony\Bundle\Command;

use Elao\CommandMigration\Process\Exception\ConnectionException;
use Elao\CommandMigration\Process\ResultView;
use Elao\CommandMigration\Process\Run;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RunCommand extends Command
{
    public const NAME = 'elao:command-migration:run';

    private $storageConfiguration, $migrations;

    public function __construct(
        array $storageConfiguration,
        array $migrations
    ) {
        parent::__construct(self::NAME);

        $this->storageConfiguration = $storageConfiguration;
        $this->migrations = $migrations;
    }

    public function configure(): void
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Run command migrations')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $symfonyStyle->title('Starting command migrations');

        $run = new Run();
        $versions = array_keys($this->migrations);
        $executedCommand = 0;

        try {
            /** @var ResultView $resultView */
            foreach ($run($this->storageConfiguration, $this->migrations, $versions) as $resultView) {
                if ($resultView->isSuccessful()) {
                    $symfonyStyle->success($resultView->getCommand());
                    ++$executedCommand;
                } else {
                    $symfonyStyle->error(
                        sprintf(
                            'Error with command "%s" with output: "%s"',
                            $resultView->getCommand(),
                            $resultView->getOutput()
                        )
                    );
                }
            }

            $endingOutput = '';
            if (0 === $executedCommand) {
                $endingOutput = 'No command executed';
            } elseif (1 === $executedCommand) {
                $endingOutput = '%d command successfully executed';
            } else {
                $endingOutput = '%d command(s) successfully executed';
            }

            $symfonyStyle->text(sprintf($endingOutput, $executedCommand));
        } catch (ConnectionException $exception) {
            $symfonyStyle->error($exception->getMessage());
        }
    }
}
