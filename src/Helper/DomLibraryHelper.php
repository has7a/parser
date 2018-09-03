<?php

namespace App\Helper;

use DOMElement;
use DOMNodeList;

final class DomLibraryHelper
{
    /**
     * @param DOMNodeList $list
     * @param string $attributeName
     *
     * @return array
     */
    public static function nodeAsArray(DOMNodeList $list, string $attributeName): array
    {
        $arr = [];
        /** @var DOMElement $node */
        foreach ($list as $node) {
            $arr[] = $node->getAttribute($attributeName);
        }

        return $arr;
    }
}