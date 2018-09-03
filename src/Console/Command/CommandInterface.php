<?php

namespace App\Console\Command;

use App\Console\Input\InputInterface;
use App\Console\Output\OutputInterface;

interface CommandInterface
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output): void;

    /**
     * @return array
     */
    public function getRequiredOptions(): array;

    /**
     * @return string
     */
    public function getName(): string ;

    /**
     * @return string
     */
    public function getDescription(): string;
}