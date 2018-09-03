<?php

namespace App\Storage;

interface StorageInterface
{
    /**
     * @param array $data
     *
     * @return string
     */
    public function save(array $data): string;
}