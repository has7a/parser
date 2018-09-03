<?php

namespace App\Console\Resolver;

use App\Console\Command\CommandInterface;
use App\Exception\ParserException;

final class CommandResolver implements CommandResolverInterface
{
    public const HELP_COMMAND = 'help';

    /**
     * @var array
     */
    private $commandMap;

    public function __construct()
    {
        $this->registerCommand();
    }

    /**
     * @return void
     */
    public function registerCommand(): void
    {
        $this->commandMap = require BIN_DIR . '/command_config.php';
    }

    /**
     * @param string $name
     *
     * @return CommandInterface
     */
    public function getExistCommand($name): CommandInterface
    {
        if ($name === null) {
            $name = self::HELP_COMMAND;
        }

        $command = $this->getCommand($name);
        if ($command === null) {
            return $this->getCommand(self::HELP_COMMAND);
        }

        return $command;
    }

    /**
     * @param string $name
     *
     * @return null|CommandInterface
     */
    private function getCommand(string $name): ?CommandInterface
    {
        $commandMap = $this->getCommandMap();
        if (isset($commandMap[$name])) {
            $class = $commandMap[$name];

            return new $class();
        }

        return null;
    }

    /**
     * @param array $nameList
     *
     * @return array
     */
    public function getCommandList(array $nameList): array
    {
        $commandList = [];
        foreach ($nameList as $name => $class) {
            $commandList[] = $this->getExistCommand($name);
        }

        return $commandList;
    }

    /**
     * @return array
     */
    public function getCommandMap(): array
    {
        return $this->commandMap;
    }
}