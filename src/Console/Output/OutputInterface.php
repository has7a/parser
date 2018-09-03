<?php

namespace App\Console\Output;

interface OutputInterface
{
    /**
     * @param string $message
     *
     * @return void
     */
    public function write(string $message): void;

    /**
     * @param array $message
     *
     * @return void
     */
    public function writeln(array $message): void;
}