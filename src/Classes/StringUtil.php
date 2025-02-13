<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2023 Web ex Machina
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
                if (is_array($valueChunk) && array_key_exists('label', $valueChunk)) {
                    $formattedValue[] = sprintf('%s (%s)', $valueChunk['label'], $valueChunk['value']);
                } else {
                    $formattedValue[] = $valueChunk;
                }
            }
            $formattedValue = implode(', ', $formattedValue);
        } else {
            $formattedValue = (string) $value;
        }

        return $formattedValue;
    }

    /**
     * Clean the tinyMCE data, see rules below
     * Rule #1 : Replace [nbsp] tags by ' '
     * Rule #2 : Find special characters and add an [nbsp] just before.
     *
     * @param string $varValue [Value to clean]
     */
    public static function cleanSpaces(string $varValue): string
    {
        // Rule #1
        $varValue = str_replace(['[nbsp]', '&nbsp;'], [' ', ' '], $varValue);

        // Rule #2
        $varValue = preg_replace("/\s(\?|\!|\:|\;|\»)/", '&nbsp;\\1', $varValue);

        return preg_replace("/(\«)\s/", '\\1&nbsp;', $varValue);
    }

    /**
     * Generate a random key.
     *
     * @param int $length Optional password length
     *
     * @return string
     *
     * @todo Add pattern rules
     */
    public static function generateKey(?int $length = 16): string
    {
        $characters = 'abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789_-';
        $randstring = '';
        for ($i = 0; $i < $length; ++$i) {
            $randstring .= $characters[random_int(0, \strlen($characters) - 1)];
        }

        return $randstring;
    }
}
