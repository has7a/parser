<?php

namespace App\Console\Resolver;

interface CommandResolverInterface
{
    /**
     * @param array $commandNames
     *
     * @return array
     */
    public function getCommandList(array $commandNames): array;

    /**
     * Register command
     *
     * @return void
     */
    public function registerCommand(): void;

    /**
     * Array of registered commands
     *
     * @return array
     */
    public function getCommandMap(): array;
}