<?php

namespace App\Console\Input;

use App\Console\Command\CommandInterface;
use App\Exception\ParserException;
use App\Exception\ValidatorException;

class Input implements InputInterface
{
    /**
     * @var CommandInterface
     */
    private $associatedCommand;

    /**
     * @var array
     */
    private $commands;

    /**
     * @var array
     */
    private $parsed;

    public function __construct(array $argv = null)
    {
        if ($argv === null) {
            $argv = $_SERVER['argv'];
        }

        array_shift($argv);

        $this->setCommands($argv);
    }

    /**
     * @return array
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * @param array $commands
     */
    public function setCommands(array $commands): void
    {
        $this->commands = $commands;
    }

    /**
     * @param CommandInterface $command
     */
    public function bind(CommandInterface $command): void
    {
        $this->associatedCommand = $command;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstArgument(): ?string
    {
        return array_shift($this->commands);
    }

    /**
     * {@inheritdoc}
     * @throws ParserException
     * @throws ValidatorException
     */
    public function parse(): void
    {
        if ($this->associatedCommand === null) {
            throw new ParserException('U can not validate arguments without associated command!');
        }

        if ($this->validateCount() === false) {
            throw new ValidatorException('Wrong argument count!');
        }

        $this->parsed = array_combine($this->associatedCommand->getRequiredOptions(), $this->commands);
    }

    /**
     * @param string $name
     *
     * @return null|string
     */
    public function getArgument(string $name): ?string
    {
        return $this->parsed[$name] ?? null;
    }

    /**
     * @return bool
     */
    private function validateCount(): bool
    {
        return \count($this->commands) === \count($this->associatedCommand->getRequiredOptions());
    }
}