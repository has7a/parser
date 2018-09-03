<?php

namespace App\Entity;

use InvalidArgumentException;

final class Url
{
    private const DEFAULT_SCHEMA = 'http://';

    /**
     * @var string
     */
    private $url;

    /**
     * @var array
     */
    private $parsedUrl;

    public function __construct(string $url)
    {
        $schemaUrl = $this->setSchema($url);
        if (filter_var($schemaUrl, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('Invalid Url!');
        }

        $this->url = $schemaUrl;
        $this->parsedUrl = parse_url($schemaUrl);
    }

    /**
     * @param string $url
     *
     * @return string
     */
    private function setSchema(string $url): string
    {
        if (parse_url($url, PHP_URL_SCHEME) === null) {
            return self::DEFAULT_SCHEMA . $url;
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return sha1($this->getUrl());
    }

    /**
     * @return array
     */
    public function getParsedUrl(): array
    {
        return $this->parsedUrl;
    }
}