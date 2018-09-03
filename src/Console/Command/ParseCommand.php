<?php

namespace App\Console\Command;

use App\Console\Formatter\StyleFormatter;
use App\Entity\Url;
use App\Exception\ParserException;
use App\Handler\HandlerInterface;
use App\Handler\ImageHandler;
use App\Helper\DomLibraryHelper;
use App\Helper\DomLoader;
use App\Helper\UrlHelper;
use App\Console\Input\InputInterface;
use App\Console\Output\OutputInterface;
use App\Storage\CsvStorage;
use App\Storage\StorageInterface;
use DOMElement;

final class ParseCommand implements CommandInterface
{
    private const DEFAULT_UNIQUE_PAGE_LIMIT = 50;

    /**
     * @var array
     */
    private $images = [];

    /**
     * @var array
     */
    private $seenUrls = [];

    /**
     * @var int
     */
    private $maxCountPages;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var StyleFormatter
     */
    private $successFormatter;

    /**
     * @var HandlerInterface
     */
    private $handler;

    /**
     * @var Url
     */
    private $url;

    /**
     * @param Url $url
     *
     * @return void
     */
    public function init(Url $url): void
    {
        $this->url = $url;
        $this->storage = new CsvStorage($url);
        $this->setMaxCountPages(self::DEFAULT_UNIQUE_PAGE_LIMIT);
        $this->handler = new ImageHandler($this->url);
        $this->successFormatter = new StyleFormatter('green');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $url = new Url($input->getArgument('url'));
        $this->init($url);
        $this->parseImages($url->getUrl());
        $handled = $this->handler->handle(array_unique($this->images));
        $this->printResult($this->storage->save($handled), $output);
    }

    /**
     * @param string $path
     * @param OutputInterface $output
     *
     * @return void
     */
    private function printResult(string $path, OutputInterface $output): void
    {
        $line = sprintf(
            "Url: %s, has been successfully parsed!\nCheck result:%s",
            $this->url->getUrl(),
            $path
        );
        $output->write($this->successFormatter->apply($line));
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions(): array
    {
        return ['url'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'parse';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return 'Parse input url';
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    private function linkExists(string $url): bool
    {
        foreach ($this->seenUrls as $seenUrl) {
            if ($seenUrl === $url) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $url
     *
     * @return void
     */
    private function parseImages(string $url): void
    {
        try {
            $dom = new DomLoader(new Url($url));
        } catch (ParserException $e) {
            return;
        }

        $links = $dom->getTag('a');
        foreach ($links as $link) {
            if (\count($this->seenUrls) >= $this->getMaxCountPages()) {
                return;
            }

            /** @var DOMElement $link */
            $href = $link->getAttribute('href');
            if ($href === '') {
                continue;
            }

            //значит считаем как фулл урл
            $href = $this->hrefToUrl($href);
            if ($this->validateHref($href) === false) {
                continue;
            }

            if (!$this->linkExists($href) && \strlen($href) > 1) {
                $images = DomLibraryHelper::nodeAsArray($dom->getTag('img'), 'src');
                $this->addImages($images);
                $this->setSeenUrl($href);
                $this->parseImages($href);
            }
        }
    }

    /**
     * @param array $images
     */
    private function addImages(array $images): void
    {
        $this->images = array_merge($this->images, $images);
    }

    /**
     * @param string $href
     *
     * @return string
     */
    private function hrefToUrl(string $href): string
    {
        $parsedUrl = $this->url->getParsedUrl();
        $schemePlusHost = UrlHelper::createUrlFromPath($parsedUrl['scheme'], $parsedUrl['host']);
        if (\in_array($href[0], ['/', '?'], true)) {
            return $schemePlusHost . $href;
        }

        return $href;
    }

    /**
     * @param string $href
     *
     * @return bool
     */
    private function validateHref(string $href): bool
    {
        $parsedUrl = parse_url($href);

        return parse_url($href) !== false && isset($parsedUrl['host']);
    }

    /**
     * @param string $seenUrls
     *
     * @return void
     */
    private function setSeenUrl(string $seenUrls): void
    {
        $this->seenUrls[] = $seenUrls;
    }

    /**
     * @return int
     */
    private function getMaxCountPages(): int
    {
        return $this->maxCountPages;
    }

    /**
     * @param int $maxCountPages
     */
    private function setMaxCountPages(int $maxCountPages): void
    {
        $this->maxCountPages = $maxCountPages;
    }
}