<?php

namespace App\Console\Output;

class ConsoleOutput implements OutputInterface
{
    /**
     * {@inheritdoc}
     */
    public function write(string $message): void
    {
        echo $message;
    }

    /**
     * {@inheritdoc}
     */
    public function writeln(array $messages): void
    {
        foreach ($messages as $message) {
            $this->write($message . PHP_EOL);
        }
    }
}