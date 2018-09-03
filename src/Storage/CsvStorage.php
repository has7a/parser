<?php

namespace App\Storage;

use App\Entity\Url;
use App\Exception\ParserException;

final class CsvStorage implements StorageInterface
{
    private const FILE_WRITE_MODE = 'wb';
    private const HEADER = ['Host', 'URL'];

    /**
     * @var string
     */
    private $fileName;

    public function __construct(Url $url)
    {
        $this->fileName = $url->getFileName();
    }

    /**
     * {@inheritdoc}
     * @throws ParserException
     */
    public function save(array $data): string
    {
        $fileName = $this->generateFileName();
        $handle = fopen($fileName, self::FILE_WRITE_MODE);
        if ($handle === false) {
            throw new ParserException(sprintf('Can not open file %s', $fileName));
        }

        fputcsv($handle, $this->getHeader());
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return realpath($fileName);
    }

    /**
     * @return string
     */
    private function generateFileName(): string
    {
        return CSV_STORAGE_DIR . $this->fileName;
    }

    /**
     * @return array
     */
    public function getHeader(): array
    {
        return self::HEADER;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }
}