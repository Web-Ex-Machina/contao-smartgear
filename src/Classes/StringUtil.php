<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2022 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Classes;

use WEM\UtilsBundle\Classes\StringUtil as StringUtilBase;

class StringUtil extends StringUtilBase
{
    public static function getFormStorageDataValueAsString($mixed): string
    {
        $value = self::deserialize($mixed);
        if (\is_array($value)) {
            $formattedValue = [];
            foreach ($value as $valueChunk) {
                $formattedValue[] = sprintf('%s (%s)', $valueChunk['label'], $valueChunk['value']);
            }
            $formattedValue = implode(',', $formattedValue);
        } else {
            $formattedValue = (string) $value;
        }

        return $formattedValue;
    }
}