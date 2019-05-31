<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2019 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartGear\Backend;

use Exception;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Util
{
    /**
     * Store the path to the config file
     * @var String
     */
    protected static $strConfigPath = "assets/smartgear/config.json";

    /**
     * Get available colors in Smartgear
     *
     * @param String $strFor -
     *
     * @return Array - An Array of classes / color names
     */
    public static function getSmartgearColors($strFor = 'rsce')
    {
        try {
            // Prepare the array of available colors
            // @todo get everything from a JSON, built elsewhere
            $arrColors = array(
                "" => ["label" => "Par défaut", "hexa" => ""]
                ,'blue'=> ["label" => "Bleu (#004C79)", "hexa" => "004C79"]
                ,'darkblue'=> ["label" => "Bleu foncé (#0a1d29)", "hexa" => "0a1d29"]
                ,'green'=> ["label" => "Vert (#5cb85c)", "hexa" => "5cb85c"]
                ,'orange'=> ["label" => "Rouge (#DC6053)", "hexa" => "DC6053"]
                ,'gold'=> ["label" => "Doré (#edbe5f)", "hexa" => "edbe5f"]
                ,'black'=> ["label" => "Noir (#000000)", "hexa" => "000000"]
                ,'blacklight'=> ["label" => "Noir 90% (#111414)", "hexa" => "111414"]
                ,'blacklighter'=> ["label" => "Noir 80% (#222222)", "hexa" => "222222"]
                ,'greystronger'=> ["label" => "Noir 70% (#424041)", "hexa" => "424041"]
                ,'greystrong'=> ["label" => "Noir 60% (#535052)", "hexa" => "535052"]
                ,'grey'=> ["label" => "Gris (#7A7778)", "hexa" => "7A7778"]
                ,'greylight'=> ["label" => "Gris 50% (#DDDDDD)", "hexa" => "DDDDDD"]
                ,'greylighter'=> ["label" => "Gris 25% (#EEEEEE)", "hexa" => "EEEEEE"]
                ,'white'=> ["label" => "Blanc (#ffffff)", "hexa" => "ffffff"]
                ,'none'=> ["label" => "Transparent", "hexa" => ""]
            );

            // Depending on who asks the array, we will need a specific format
            $colors = [];
            switch ($strFor) {
                case 'tinymce':
                    foreach ($arrColors as $k => $c) {
                        if ("" == $k) {
                            continue;
                        }

                        $colors[] = $c["hexa"];
                        $colors[] = $c["label"];
                    }
                    $colors = json_encode($colors);
                    break;

                case 'rsce-ft':
                    foreach ($arrColors as $k => $c) {
                        if ("" == $k) {
                            $colors[$k] = $c["label"];
                        } else {
                            $colors["ft-".$k] = $c["label"];
                        }
                    }
                    break;
                
                case 'rsce':
                default:
                    foreach ($arrColors as $k => $c) {
                        $colors[$k] = $c["label"];
                    }
            }

            return $colors;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Find and Create an Object, depending on type and module
     * @param  [String] $strType   [Type / Folder]
     * @param  [String] $strModule [Class / File]
     * @return [Object]            [Object of the class]
     */
    public static function findAndCreateObject($strType, $strModule = '')
    {
        try {
            // If module is missing, try to explode strType
            if ('' === $strModule && false != strpos($strType, '_')) {
                $arrObject = explode('_', $strType);
                $strType = $arrObject[0];
                $strModule = $arrObject[1];
            }

            // Parse the classname
            $strClass = sprintf("WEM\SmartGear\Backend\%s\%s", ucfirst($strType), ucfirst($strModule));

            // Throw error if class doesn't exists
            if (!class_exists($strClass)) {
                throw new Exception(sprintf("Unknown class %s", $strClass));
            }

            // Create the object
            $objModule = new $strClass;

            // And return
            return $objModule;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get Smartgear Config
     * @param  [String] $strKey [Config key wanted]
     * @return [Mixed]          [Config value]
     */
    public static function loadSmartgearConfig()
    {
        try {
            $objFiles = \Files::getInstance();
            if (!file_exists(static::$strConfigPath)) {
                $objFiles->mkdir(str_replace("/config.json", "", static::$strConfigPath));
                $objFiles->fopen(static::$strConfigPath, "wb");
            }
            $objFile = $objFiles->fopen(static::$strConfigPath, "a");
            $arrConfig = [];

            // Get the config file
            if ($strConfig = file_get_contents(static::$strConfigPath)) {
                $arrConfig = (array)json_decode($strConfig);
            }

            // And return the entire config, updated
            return $arrConfig;
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Update Contao Config
     * @param  [Array] $arrVars [Key/Value Array]
     */
    public static function updateConfig($arrVars)
    {
        try {
            $objFiles = \Files::getInstance();
            if (!file_exists(static::$strConfigPath)) {
                $objFiles->mkdir(str_replace("/config.json", "", static::$strConfigPath));
                $objFiles->fopen(static::$strConfigPath, "wb");
            }
            $strConfig = file_get_contents(static::$strConfigPath);
            $arrConfig = [];

            // Decode the config
            if ($strConfig) {
                $arrConfig = (array)json_decode($strConfig);
            }
            
            // Update the config
            foreach ($arrVars as $strKey => $varValue) {
                // Make sure arrays are converted in varValues (for blob compatibility)
                if (is_array($varValue)) {
                    $varValue = serialize($varValue);
                }

                // And update the global array
                $arrConfig[$strKey] = $varValue;
            }

            // Open and update the config file
            $objFile = $objFiles->fopen(static::$strConfigPath, "w");
            $objFiles->fputs($objFile, json_encode($arrConfig, JSON_PRETTY_PRINT));
            
            // And return the entire config, updated
            return $arrConfig;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Shortcut for page creation
     */
    public static function createPage($strTitle, $intPid = 0, $arrData = [])
    {
        $arrConfig = static::loadSmartgearConfig();
        if (0 === $intPid) {
            $intPid = $arrConfig["sgInstallRootPage"];
        }

        // Create the page
        $objPage = new \PageModel();
        $objPage->tstamp = time();
        $objPage->pid = $intPid;
        $objPage->sorting = (\PageModel::countBy("pid", $intPid) + 1) * 128;
        $objPage->title = $strTitle;
        $objPage->alias = \StringUtil::generateAlias($objPage->title);
        $objPage->type = "regular";
        $objPage->pageTitle = $strTitle;
        $objPage->robots = "index,follow";
        $objPage->sitemap = "map_default";
        $objPage->published = 1;

        // Now we get the default values, get the arrData table
        if (!empty($arrData)) {
            foreach ($arrData as $k => $v) {
                $objPage->$k = $v;
            }
        }
        
        $objPage->save();

        // Return the model
        return $objPage;
    }

    /**
     * Shortcut for article creation
     */
    public static function createArticle($objPage, $arrData = [])
    {
        // Create the article
        $objArticle = new \ArticleModel();
        $objArticle->tstamp = time();
        $objArticle->pid = $objPage->id;
        $objArticle->sorting = (\ArticleModel::countBy("pid", $objPage->id) + 1) * 128;
        $objArticle->title = $objPage->title;
        $objArticle->alias = $objPage->alias;
        $objArticle->author = 1;
        $objArticle->inColumn = "main";
        $objArticle->published = 1;

        // Now we get the default values, get the arrData table
        if (!empty($arrData)) {
            foreach ($arrData as $k => $v) {
                $objArticle->$k = $v;
            }
        }

        $objArticle->save();

        // Return the model
        return $objArticle;
    }

    /**
     * Shortcut for content creation
     */
    public static function createContent($objArticle, $arrData = [])
    {
        // Create the content
        $objContent = new \ContentModel();
        $objContent->tstamp = time();
        $objContent->pid = $objArticle->id;
        $objContent->ptable = "tl_article";
        $objContent->sorting = (\ContentModel::countPublishedByPidAndTable($objArticle->id, "tl_article") + 1) * 128;
        $objContent->type = "text";

        // Now we get the default values, get the arrData table
        if (!empty($arrData)) {
            foreach ($arrData as $k => $v) {
                $objContent->$k = $v;
            }
        }

        $objContent->save();

        // Return the model
        return $objContent;
    }

    /**
     * Shortcut for page w/ modules creations
     */
    public static function createPageWithModules($strTitle, $arrModules, $intPid = 0)
    {
        $arrConfig = static::loadSmartgearConfig();
        if (0 === $intPid) {
            $intPid = $arrConfig["sgInstallRootPage"];
        }
        
        // Create the page
        $objPage = static::createPage($strTitle, $intPid);

        // Create the article
        $objArticle = static::createArticle($objPage);

        // Create the contents
        foreach ($arrModules as $intModule) {
            $objContent = static::createContent($objArticle, ["type"=>"module", "module"=>$intModule]);
        }

        // Return the page ID
        return $objPage->id;
    }

    /**
     * Shortcut for page w/ texts creations
     */
    public static function createPageWithText($strTitle, $strText, $intPid = 0, $arrHl = null)
    {
        $arrConfig = static::loadSmartgearConfig();
        if (0 === $intPid) {
            $intPid = $arrConfig["sgInstallRootPage"];
        }
        
        // Create the page
        $objPage = static::createPage($strTitle, $intPid);

        // Create the article
        $objArticle = static::createArticle($objPage);

        // Create the content
        $objContent = static::createContent($objArticle, ["text"=>$strText, "headline"=>$arrHl]);

        // Return the page ID
        return $objPage->id;
    }

    /**
     * Contao Friendly Base64 Converter to FileSystem
     * @param  [String]  $base64          [Base64 String to decode]
     * @param  [String]  $folder          [Folder name]
     * @param  [String]  $file            [File name]
     * @param  [Boolean] $blnReturnFile   [Return the File Object if set to true]
     * @return [Object]                   [File Object]
     */
    public static function base64ToImage($base64, $folder, $file, $blnReturnFile = true)
    {
        try {
            // split the string on commas
            // $data[ 0 ] == "data:image/png;base64"
            // $data[ 1 ] == <actual base64 string>
            $data = explode(',', $base64);
            $ext = substr($data[0], strpos($data[0], "/")+1, (strpos($data[0], ";") - strpos($data[0], "/") - 1));
            $img = base64_decode($data[1]);

            if (strpos(\Config::get('validImageTypes'), $ext) === false) {
                throw new \Exception("Invalid image type : ".$ext);
            }

            // Determine a filename if absent
            $path = $folder.'/'.$file.'.'.$ext;

            // Create & Close the file to generate the Model, and then, reopen the file
            // Because ->close() do not return the File object but true \o/
            $objFile = new \File($path);
            $objFile->write($img);

            if (!$objFile->close()) {
                throw new \Exception(sprintf("The file %s hasn't been saved correctly", $path));
            }

            if ($blnReturnFile) {
                $objFile = new \File($path);
                return $objFile;
            }
                
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
