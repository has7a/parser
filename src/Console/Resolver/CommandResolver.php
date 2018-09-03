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
     * @throws ParserException
     */
    public function getExistCommand($name): CommandInterface
    {
        if ($name === null) {
            $name = self::HELP_COMMAND;
        }

        try {
            return $this->getCommand($name);
        } catch (ParserException $e) {
            return $this->getCommand(self::HELP_COMMAND);
        }
    }

    /**
     * @param string $name
     *
     * @return mixed
     * @throws ParserException
     */
    private function getCommand(string $name)
    {
        $commandMap = $this->getCommandMap();
        if (isset($commandMap[$name])) {
            $class = $commandMap[$name];

            return new $class();
        }

        throw new ParserException(sprintf('Command \'%s\' does not exits!', $name));//message
    }

    /**
     * @param array $nameList
     *
     * @return array
     * @throws ParserException
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