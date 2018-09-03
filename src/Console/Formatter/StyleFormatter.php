<?php

namespace App\Console\Formatter;

use InvalidArgumentException;

class StyleFormatter implements OutputFormatterInterface
{
    /**
     * @var array
     */
    private static $availableForegroundColors = [
        'black' => ['set' => 30, 'unset' => 39],
        'red' => ['set' => 31, 'unset' => 39],
        'green' => ['set' => 32, 'unset' => 39],
        'yellow' => ['set' => 33, 'unset' => 39],
        'blue' => ['set' => 34, 'unset' => 39],
        'magenta' => ['set' => 35, 'unset' => 39],
        'cyan' => ['set' => 36, 'unset' => 39],
        'white' => ['set' => 37, 'unset' => 39],
        'default' => ['set' => 39, 'unset' => 39],
    ];

    /**
     * @var array
     */
    private static $availableBackgroundColors = [
        'black' => ['set' => 40, 'unset' => 49],
        'red' => ['set' => 41, 'unset' => 49],
        'green' => ['set' => 42, 'unset' => 49],
        'yellow' => ['set' => 43, 'unset' => 49],
        'blue' => ['set' => 44, 'unset' => 49],
        'magenta' => ['set' => 45, 'unset' => 49],
        'cyan' => ['set' => 46, 'unset' => 49],
        'white' => ['set' => 47, 'unset' => 49],
        'default' => ['set' => 49, 'unset' => 49],
    ];

    /**
     * @var array
     */
    private static $availableOptions = [
        'bold' => ['set' => 1, 'unset' => 22],
        'underscore' => ['set' => 4, 'unset' => 24],
        'blink' => ['set' => 5, 'unset' => 25],
        'reverse' => ['set' => 7, 'unset' => 27],
        'conceal' => ['set' => 8, 'unset' => 28],
    ];

    /**
     * @var array
     */
    private $foreground;

    /**
     * @var array
     */
    private $background;

    /**
     * @var array
     */
    private $options = [];

    public function __construct(string $foreground = null, string $background = null, array $options = [])
    {
        if ($foreground !== null) {
            $this->setForeground($foreground);
        }
        if ($background !== null) {
            $this->setBackground($background);
        }
        if (\count($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function apply(string $text): string
    {
        $setCodes = [];
        $unsetCodes = [];

        if ($this->foreground !== null) {
            $setCodes[] = $this->foreground['set'];
            $unsetCodes[] = $this->foreground['unset'];
        }
        if ($this->background !== null) {
            $setCodes[] = $this->background['set'];
            $unsetCodes[] = $this->background['unset'];
        }
        if (\count($this->options)) {
            foreach ($this->options as $option) {
                $setCodes[] = $option['set'];
                $unsetCodes[] = $option['unset'];
            }
        }

        if (0 === \count($setCodes)) {
            return $text;
        }

        return sprintf("\033[%sm%s\033[%sm", implode(';', $setCodes), $text, implode(';', $unsetCodes));
    }

    /**
     * {@inheritdoc}
     */
    public function setForeground(string $color = null): void
    {
        if ($color === null) {
            $this->foreground = null;

            return;
        }

        if (!isset(static::$availableForegroundColors[$color])) {
            throw new InvalidArgumentException(sprintf(
                'Invalid foreground color specified: "%s". Expected one of (%s)',
                $color,
                implode(', ', array_keys(static::$availableForegroundColors))
            ));
        }

        $this->foreground = static::$availableForegroundColors[$color];
    }

    /**
     * {@inheritdoc}
     * @throws InvalidArgumentException
     */
    public function setBackground(string $color = null): void
    {
        if ($color === null) {
            $this->background = null;

            return;
        }

        if (!isset(static::$availableBackgroundColors[$color])) {
            throw new InvalidArgumentException(sprintf(
                'Invalid background color specified: "%s". Expected one of (%s)',
                $color,
                implode(', ', array_keys(static::$availableBackgroundColors))
            ));
        }

        $this->background = static::$availableBackgroundColors[$color];
    }

    /**
     * {@inheritdoc}
     * @throws InvalidArgumentException
     */
    public function setOption(string $option): void
    {
        if (!isset(static::$availableOptions[$option])) {
            throw new InvalidArgumentException(sprintf(
                'Invalid option specified: "%s". Expected one of (%s)',
                $option,
                implode(', ', array_keys(static::$availableOptions))
            ));
        }

        if (!\in_array(static::$availableOptions[$option], $this->options, true)) {
            $this->options[] = static::$availableOptions[$option];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options): void
    {
        $this->options = [];

        foreach ($options as $option) {
            $this->setOption($option);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unsetOption(string $option): void
    {
        if (!isset(static::$availableOptions[$option])) {
            throw new InvalidArgumentException(sprintf(
                'Invalid option specified: "%s". Expected one of (%s)',
                $option,
                implode(', ', array_keys(static::$availableOptions))
            ));
        }

        $pos = array_search(static::$availableOptions[$option], $this->options, true);
        if ($pos !== false) {
            unset($this->options[$pos]);
        }
    }
}