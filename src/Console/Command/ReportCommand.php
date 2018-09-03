<?php

namespace App\Console\Command;

use App\Console\Formatter\OutputFormatterInterface;
use App\Console\Formatter\StyleFormatter;
use App\Console\Input\InputInterface;
use App\Console\Output\OutputInterface;
use App\Entity\Url;
use App\Exception\ParserException;
use App\Helper\OutputHelper;

final class ReportCommand implements CommandInterface
{
    private const READ_BINARY_MODE = 'rb';

    /**
     * @var int
     */
    private $totalCount = 0;

    /**
     * @var OutputFormatterInterface
     */
    private $errorFormatter;

    /**
     * @var OutputFormatterInterface
     */
    private $infoStyleFormatter;

    /**
     * @var OutputHelper
     */
    private $outputHelper;

    /**
     * @var Url
     */
    private $url;

    /**
     * @return void
     */
    public function init(): void
    {
        $this->errorFormatter = new StyleFormatter('white', 'red');
        $this->infoStyleFormatter = new StyleFormatter('green');
        $this->outputHelper = new OutputHelper();
    }

    /**
     * {@inheritdoc}
     * @throws ParserException
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->init();
        $this->url = new Url($input->getArgument('url'));
        $fileName = CSV_STORAGE_DIR . $this->url->getFileName();
        if (!file_exists($fileName)) {
            $output->write($this->errorFormatter->apply(sprintf('Url: %s has not been parsed yet!', $this->url->getUrl())));

            return;
        }

        $this->printStatistic($this->prepareStatistic($fileName), $output);
    }

    /**
     * @param array $imageStatistic
     * @param OutputInterface $output
     *
     * @return void
     */
    private function printStatistic(array $imageStatistic, OutputInterface $output): void
    {
        $width = $this->outputHelper->getMaxWidth(array_keys($imageStatistic), 30);
        $lines = [];
        $lines[] = $this->infoStyleFormatter->apply(sprintf('Images have been parsed: %d', $this->totalCount));
        /** @var CommandInterface $command */
        foreach ($imageStatistic as $domain => $count) {
            $spacingWidth = $width - \strlen($domain);
            $lines[] = $this->infoStyleFormatter->apply(sprintf('  %s %s%d', $domain, str_repeat(' ', $spacingWidth), $count));
        }
        $lines[] = $this->infoStyleFormatter->apply(sprintf('Detailed review: %s', realpath(CSV_STORAGE_DIR . $this->url->getFileName())));
        $output->writeln($lines);
    }

    /**
     * @param string $fileName
     *
     * @return array
     * @throws ParserException
     */
    private function prepareStatistic(string $fileName): array
    {
        $handle = fopen($fileName, self::READ_BINARY_MODE);
        if ($handle === false) {
            throw new ParserException(sprintf('Can not open file %s', $fileName));
        }

        $imageStatistic = [];
        $cols = array_flip(fgetcsv($handle));
        while (($row = fgetcsv($handle)) !== false) {
            ++$this->totalCount;
            if (isset($imageStatistic[$row[$cols['Host']]])) {
                ++$imageStatistic[$row[$cols['Host']]];
            } else {
                $imageStatistic[$row[$cols['Host']]] = 1;
            }
        }

        return $imageStatistic;
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
        return 'report';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return 'Show info about parsed url';
    }

    /**
     * @return int
     */
    public function getTotalCsvRows(): int
    {
        return $this->totalCsvRows;
    }
}