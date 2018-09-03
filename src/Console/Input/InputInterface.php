<?php

namespace App\Console\Input;

use App\Console\Command\CommandInterface;

interface InputInterface
{
    /**
     * Delete and return first argument from input arguments
     *
     * @return mixed
     */
    public function getFirstArgument();

    /**
     * @return array
     */
    public function getCommands(): array;

    /**
     * @param string $name
     */
    public function getArgument(string $name);

    /**
     * @param CommandInterface $command
     */
    public function bind(CommandInterface $command);
}