<?php

namespace App\Storage;

interface StorageInterface
{
    /**
     * Returns link to saved data
     *
     * @param array $data
     *
     * @return string
     */
    public function save(array $data): string;
}