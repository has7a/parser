<?php

namespace App\Handler;

use App\Entity\Url;
use App\Exception\HandlerException;

final class ImageHandler implements HandlerInterface
{
    /**
     * @var Url
     */
    private $url;

    public function __construct(Url $url)
    {
        $this->url = $url;
    }

    /**
     * @param array $list
     *
     * @return array
     */
    public function handle(array $list): array
    {
        return array_map([$this, 'getHostAndUri'], $list);
    }

    /**
     * @param string $src
     *
     * @return array
     * @throws HandlerException
     */
    private function getHostAndUri(string $src): array
    {
        $parsedUrl = parse_url($src);
        if ($parsedUrl['path'] === false) {
            throw new HandlerException('Invalid input source');
        }

        if (isset($parsedUrl['scheme'], $parsedUrl['host'])) {
            return [$parsedUrl['scheme'] . '://' . $parsedUrl['host'], $parsedUrl['path']];
        }

        return [$this->url->getUrl(), $parsedUrl['path']];
    }
}