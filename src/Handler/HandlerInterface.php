<?php

namespace App\Handler;

interface HandlerInterface
{
    /**
     * @param array $list
     *
     * @return array
     */
    public function handle(array $list): array;
}