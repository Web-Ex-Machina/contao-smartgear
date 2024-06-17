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

namespace WEM\SmartgearBundle\Classes\Utils;

use Contao\ImageSizeModel;

class ImageSizeUtil
{
    /**
     * Shortcut for image size creation.
     */
    public static function createImageSize(int $pid, ?array $arrData = []): ImageSizeModel
    {
        // Create the article
        $objImageSize = isset($arrData['id']) ? ImageSizeModel::findById($arrData['id']) ?? new ImageSizeModel() : new ImageSizeModel();
        $objImageSize->pid = $pid;
        $objImageSize->tstamp = time();

        // Now we get the default values, get the arrData table
        if ($arrData !== null && $arrData !== []) {
            foreach ($arrData as $k => $v) {
                $objImageSize->$k = $v;
            }
        }

        $objImageSize->save();

        // Return the model
        return $objImageSize;
    }

    public static function createImageSize_16_9(int $pid, ?array $arrData = []): ImageSizeModel
    {
        return self::createImageSize($pid, array_merge([
            'name' => '16:9',
            'width' => '1920',
            'height' => '1080',
            'densities' => '0.5x, 1x, 2x',
            'resizeMode' => 'crop',
            'lazyLoading' => 1,
        ], $arrData));
    }

    public static function createImageSize_2_1(int $pid, ?array $arrData = []): ImageSizeModel
    {
        return self::createImageSize($pid, array_merge([
            'name' => '2:1',
            'width' => '1920',
            'height' => '960',
            'densities' => '2x',
            'lazyLoading' => 1,
        ], $arrData));
    }

    public static function createImageSize_1_2(int $pid, ?array $arrData = []): ImageSizeModel
    {
        return self::createImageSize($pid, array_merge([
            'name' => '1:2',
            'width' => '960',
            'height' => '1920',
            'densities' => '0.5x',
            'lazyLoading' => 1,
        ], $arrData));
    }

    public static function createImageSize_1_1(int $pid, ?array $arrData = []): ImageSizeModel
    {
        return self::createImageSize($pid, array_merge([
            'name' => '1:1',
            'width' => '1920',
            'height' => '1920',
            'densities' => '1x',
            'lazyLoading' => 1,
        ], $arrData));
    }

    public static function createImageSize_4_3(int $pid, ?array $arrData = []): ImageSizeModel
    {
        return self::createImageSize($pid, array_merge([
            'name' => '4:3',
            'width' => '1920',
            'height' => '1440',
            'densities' => '0.5x, 1x, 2x',
            'resizeMode' => 'crop',
        ], $arrData));
    }
}
