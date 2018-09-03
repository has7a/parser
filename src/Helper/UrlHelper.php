<?php

namespace App\Helper;

use App\Exception\ParserException;

final class UrlHelper
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var array
     */
    private $parsedUrl;

    /**
     * UrlHelper constructor.
     * @param string $url
     * @throws ParserException
     */
    public function __construct(string $url)
    {
        $this->url = $url;
        $this->parsedUrl = $this->parseUrl($url);
    }

    /**
     * True if $source belongs to $this->url
     *
     * @param string $source
     *
     * @return bool
     * @throws ParserException
     */
    public function isSrcBelongsToDomain(string $source): bool
    {
        $parsedSource = $this->parseUrl($source);
        if (isset($parsedSource['host'])) {
            return $this->getParsedUrl()['host'] === $parsedSource['host'];
        }

        return true;
    }

    /**
     * @param string $scheme
     * @param string $host
     *
     * @return string
     */
    public static function createUrlFromPath(string $scheme, string $host): string
    {
        return $scheme . '://' . $host;
    }

    /**
     * @param string $url
     *
     * @return array
     * @throws ParserException
     */
    private function parseUrl(string $url): array
    {
        $parsedUrl = parse_url($url);

        if ($parsedUrl === false || $this->validate($parsedUrl)) {
            throw new ParserException('Invalid parsed url!');
        }

        return $parsedUrl;
    }

    /**
     * @param array $parsedUrl
     *
     * @return bool
     */
    private function validate(array $parsedUrl): bool
    {
        return isset($parsedUrl['host'], $parsedUrl['scheme']);
    }

    /**
     * @return array
     */
    private function getParsedUrl(): array
    {
        return $this->parsedUrl;
    }
}