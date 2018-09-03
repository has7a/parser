<?php

namespace App\Helper;

final class OutputHelper
{
    private const SPACES_AFTER_COMMAND_NAME = 10;

    /**
     * @param array $params
     * @param int $spacesCount
     *
     * @return int
     */
    public function getMaxWidth(array $params, $spacesCount = self::SPACES_AFTER_COMMAND_NAME): int
    {
        $widths = [];
        foreach ($params as $param) {
            $widths[] = \strlen($param);
        }

        return $widths !== [] ? max($widths) + $spacesCount : 0;
    }
}