<?php

namespace App\Console\Formatter;

interface OutputFormatterInterface
{
    /**
     * @param string $color
     *
     * @return void
     */
    public function setForeground(string  $color = null): void ;

    /**
     * @param string $color
     *
     * @return void
     */
    public function setBackground(string $color = null): void ;

    /**
     * @param string $option
     *
     * @return void
     */
    public function setOption(string $option):void ;

    /**
     * @param string $option
     *
     * @return void
     */
    public function unsetOption(string $option): void ;

    /**
     * @param array $options
     *
     * @return void
     */
    public function setOptions(array $options): void ;

    /**
     * @param string $text
     *
     * @return string
     */
    public function apply(string $text): string ;
}