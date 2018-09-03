<?php

namespace App\Helper;

use App\Entity\Url;
use App\Exception\ParserException;
use DOMDocument;
use DOMNodeList;

final class DomLoader
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @var DOMDocument
     */
    private $dom;

    /**
     * DomLoader constructor.
     * @param Url $url
     * @throws ParserException
     */
    public function __construct(Url $url)
    {
        $this->url = $url;
        $this->loadDom();
    }

    /**
     * @return string
     */
    private function loadContent(): string
    {
        return @file_get_contents($this->url->getUrl());
    }

    /**
     * @return void
     * @throws ParserException
     */
    private function loadDom(): void
    {
        $content = $this->loadContent();
        if ($content === false || $content === '') {
            throw new ParserException('Can not load content!');
        }
        $this->dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $this->dom->loadHTML($content);
        libxml_clear_errors();
    }

    /**
     * @param string $tagName
     *
     * @return DOMNodeList
     */
    public function getTag(string $tagName): DOMNodeList
    {
        return $this->dom->getElementsByTagName($tagName);
    }
}