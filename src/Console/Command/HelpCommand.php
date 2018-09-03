<?php

namespace App\Console\Command;

use App\Console\Formatter\OutputFormatterInterface;
use App\Console\Formatter\StyleFormatter;
use App\Console\Input\InputInterface;
use App\Console\Output\OutputInterface;
use App\Console\Resolver\CommandResolver;
use App\Console\Resolver\CommandResolverInterface;
use App\Helper\OutputHelper;

final class HelpCommand implements CommandInterface
{
    /**
     * @var OutputHelper
     */
    private $outputHelper;

    /**
     * @var OutputFormatterInterface
     */
    private $systemStyleFormatter;

    /**
     * @var OutputFormatterInterface
     */
    private $infoStyleFormatter;

    /**
     * @var CommandResolverInterface
     */
    private $commandResolver;

    /**
     * @return void
     */
    public function init(): void
    {
        $this->outputHelper = new OutputHelper();
        $this->systemStyleFormatter = new StyleFormatter('yellow');
        $this->infoStyleFormatter = new StyleFormatter('green');
        $this->commandResolver = new CommandResolver();
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->init();
        $commandNameList = $this->commandResolver->getCommandMap();
        $commandList = $this->commandResolver->getCommandList($commandNameList);
        $this->printUsageString($output);
        $width = $this->outputHelper->getMaxWidth(array_keys($commandNameList));
        $lines = [];
        /** @var CommandInterface $command */
        foreach ($commandList as $command) {
            $spacingWidth = $width - \strlen($command->getName());
            $lines[] = $this->infoStyleFormatter->apply(sprintf('  %s %s', $command->getName(), str_repeat(' ', $spacingWidth))) . $command->getDescription();
            if ($command->getRequiredOptions() !== []) {
                $lines[] = $this->systemStyleFormatter->apply('    Arguments: [' . implode(',', $command->getRequiredOptions()) . ']' . PHP_EOL);
            }
        }

        $output->writeln($lines);
    }

    /**
     * @param OutputInterface $output
     */
    private function printUsageString(OutputInterface $output): void
    {
        $infoStyleFormatter = new StyleFormatter('yellow');
        $lines[] = "Usage:\n  " . $infoStyleFormatter->apply('Command [arguments]') . PHP_EOL;
        $lines[] = 'Available Commands:' . PHP_EOL;
        $output->writeln($lines);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'help';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return 'Command show list of all commands';
    }
}