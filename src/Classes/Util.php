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

use Contao\ArticleModel;
use Contao\Config;
use Contao\ContentModel;
use Contao\Controller;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\File;
use Contao\Files;
use Contao\PageModel;
use Contao\System;
use Contao\UserGroupModel;
use Exception;
use InvalidArgumentException;
use Psr\Log\LogLevel;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\UtilsBundle\Classes\StringUtil;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Util
{
    /**
     * Store the path to the config file.
     *
     * @var string
     */
    protected static $strConfigPath = 'assets/smartgear/config.json';

    /**
     * Permissions cache system.
     *
     * @var array
     */
    protected static $arrContaoPermissions;

    /**
     * Extract colors used in Framway.
     *
     * @return [Array] [Framway colors]
     */
    public static function getDefaultColors()
    {
        return [
            '' => ['label' => 'Par défaut', 'hexa' => ''], 'blue' => ['label' => 'Bleu (#004C79)', 'hexa' => '004C79'], 'darkblue' => ['label' => 'Bleu foncé (#0a1d29)', 'hexa' => '0a1d29'], 'green' => ['label' => 'Vert (#5cb85c)', 'hexa' => '5cb85c'], 'orange' => ['label' => 'Rouge (#DC6053)', 'hexa' => 'DC6053'], 'gold' => ['label' => 'Doré (#edbe5f)', 'hexa' => 'edbe5f'], 'black' => ['label' => 'Noir (#000000)', 'hexa' => '000000'], 'blacklight' => ['label' => 'Noir 90% (#111414)', 'hexa' => '111414'], 'blacklighter' => ['label' => 'Noir 80% (#222222)', 'hexa' => '222222'], 'greystronger' => ['label' => 'Noir 70% (#424041)', 'hexa' => '424041'], 'greystrong' => ['label' => 'Noir 60% (#535052)', 'hexa' => '535052'], 'grey' => ['label' => 'Gris (#7A7778)', 'hexa' => '7A7778'], 'greylight' => ['label' => 'Gris 50% (#DDDDDD)', 'hexa' => 'DDDDDD'], 'greylighter' => ['label' => 'Gris 25% (#EEEEEE)', 'hexa' => 'EEEEEE'], 'white' => ['label' => 'Blanc (#ffffff)', 'hexa' => 'ffffff'], 'none' => ['label' => 'Transparent', 'hexa' => ''],
        ];
    }

    /**
     * Extract colors used in Framway.
     *
     * @param [String] $strFWTheme [Get the colors of a specific theme]
     *
     * @return [Array] [Framway colors]
     *
     * @todo Find a way to add friendly names to the colors retrieved
     * @todo Maybe store these colors into a file to avoid load/format a shitload of stuff ?
     */
    public static function getFramwayColors(?string $strFWTheme = ''): array
    {
        try {
            /** @var UtilFramway */
            $framwayUtil = System::getContainer()->get('smartgear.classes.util_framway');

            $colors = empty($strFWTheme) ? $framwayUtil->getCombinedColors() : $framwayUtil->getThemeColors($strFWTheme);
            $return = [];

            foreach ($colors as $label => $hexa) {
                $return[$label] = ['label' => trim($label), 'hexa' => trim(str_replace('#', '', $hexa))];
            }

            return $return;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get available colors in Smartgear.
     *
     * @param [String] $strFor     [Format wanted]
     * @param [String] $strFWTheme [Framway theme wanted]
     *
     * @return [Array] An Array of classes / color names
     */
    public static function getSmartgearColors($strFor = 'rsce', $strFWTheme = '')
    {
        try {
            try {
                // Extract colors from installed Framway
                $arrColors = self::getFramwayColors($strFWTheme);
            } catch (\Exception $e) {
                System::getContainer()
                    ->get('monolog.logger.contao')
                    ->log(
                        LogLevel::ERROR,
                        'Error when trying to get Framway Colors : '.$e->getMessage(),
                        ['contao' => new ContaoContext(__METHOD__, 'SMARTGEAR')]
                    )
                ;
                $arrColors = self::getDefaultColors();
            }

            // Depending on who asks the array, we will need a specific format
            $colors = [];
            switch ($strFor) {
                case 'tinymce':
                    foreach ($arrColors as $k => $c) {
                        if ('' === $k) {
                            continue;
                        }

                        $colors[] = $c['hexa'];
                        $colors[] = \array_key_exists($c['label'], $GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS'] ?? [])
                        ? $GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS'][$c['label']]
                        : $c['label']
                        ;
                    }
                    $colors = json_encode($colors);
                    break;

                case 'rsce-ft':
                    foreach ($arrColors as $k => $c) {
                        if ('' === $k) {
                            $colors[$k] = \array_key_exists($c['label'], $GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS'] ?? [])
                        ? $GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS'][$c['label']]
                        : $c['label']
                        ;
                        } else {
                            $colors['ft-'.$k] = \array_key_exists($c['label'], $GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS'] ?? [])
                        ? $GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS'][$c['label']]
                        : $c['label']
                        ;
                        }
                    }
                    $colors = [
                        $GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS']['meaningfulLabel'] => [
                            'ft-primary' => &$GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS']['primary'],
                            'ft-secondary' => &$GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS']['secondary'],
                            'ft-success' => &$GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS']['success'],
                            'ft-error' => &$GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS']['error'],
                            'ft-warning' => &$GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS']['warning'],
                        ],
                        $GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS']['rawLabel'] => $colors,
                    ];
                    break;

                case 'rsce':
                default:
                    foreach ($arrColors as $k => $c) {
                        $colors[$k] = \array_key_exists($c['label'], $GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS'] ?? [])
                        ? $GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS'][$c['label']]
                        : $c['label']
                        ;
                    }
                    $colors = [
                        $GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS']['meaningfulLabel'] => [
                            'primary' => &$GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS']['primary'],
                            'secondary' => &$GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS']['secondary'],
                            'success' => &$GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS']['success'],
                            'error' => &$GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS']['error'],
                            'warning' => &$GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS']['warning'],
                        ],
                        $GLOBALS['TL_LANG']['WEMSG']['FRAMWAY']['COLORS']['rawLabel'] => $colors,
                    ];
            }

            return $colors;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Find and Create an Object, depending on type and module.
     *
     * @param [String] $strType   [Type / Folder]
     * @param [String] $strModule [Class / File]
     *
     * @return [Object] [Object of the class]
     */
    public static function findAndCreateObject($strType, $strModule = '')
    {
        try {
            // If module is missing, try to explode strType
            if ('' === $strModule && false !== strpos($strType, '_')) {
                $arrObject = explode('_', $strType);
                $strType = $arrObject[0];
                $strModule = $arrObject[1];
            }

            // Parse the classname
            $strClass = sprintf("WEM\SmartgearBundle\Backend\%s\%s", ucfirst($strType), ucfirst($strModule));

            // Throw error if class doesn't exists
            if (!class_exists($strClass)) {
                throw new Exception(sprintf('Unknown class %s', $strClass));
            }

            // Create the object
            return new $strClass();

            // And return
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get Smartgear Config.
     *
     * @return [Mixed] [Config value]
     */
    public static function loadSmartgearConfig(): CoreConfig
    {
        try {
            // $objFiles = \Files::getInstance();
            // if (!file_exists(static::$strConfigPath)) {
            //     $objFiles->mkdir(str_replace('/config.json', '', static::$strConfigPath));
            //     $objFiles->fopen(static::$strConfigPath, 'wb');
            // }
            // $objFile = $objFiles->fopen(static::$strConfigPath, 'a');
            // $arrConfig = [];

            // // Get the config file
            // if ($strConfig = file_get_contents(static::$strConfigPath)) {
            //     $arrConfig = (array) json_decode($strConfig);
            // }

            // // And return the entire config, updated
            // return $arrConfig;
            $configManager = System::getContainer()->get('smartgear.config.manager.core');

            return $configManager->load();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Update Smartgear Config.
     *
     * @param [Array] $arrVars [Key/Value Array]
     *
     * @deprecated
     */
    public static function updateConfig($arrVars)
    {
        try {
            $objFiles = Files::getInstance();
            if (!file_exists(static::$strConfigPath)) {
                $objFiles->mkdir(str_replace('/config.json', '', static::$strConfigPath));
                $objFiles->fopen(static::$strConfigPath, 'wb');
            }
            $strConfig = file_get_contents(static::$strConfigPath);
            $arrConfig = [];

            // Decode the config
            if ($strConfig) {
                $arrConfig = (array) json_decode($strConfig);
            }

            // Update the config
            foreach ($arrVars as $strKey => $varValue) {
                // Make sure arrays are converted in varValues (for blob compatibility)
                if (\is_array($varValue)) {
                    $varValue = serialize($varValue);
                }

                // And update the global array
                $arrConfig[$strKey] = $varValue;
            }

            // Open and update the config file
            $objFile = $objFiles->fopen(static::$strConfigPath, 'w');
            $objFiles->fputs($objFile, json_encode($arrConfig, \JSON_PRETTY_PRINT));

            // And return the entire config, updated
            return $arrConfig;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Reset Smartgear Config.
     *
     * @deprecated
     */
    public static function resetConfig(): void
    {
        try {
            $objFiles = Files::getInstance();

            // Open and update the config file
            $objFile = $objFiles->fopen(static::$strConfigPath, 'w');
            $objFiles->fputs($objFile, '{}');
            $objFiles->fclose($objFile);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Shortcut for page creation.
     */
    public static function createPage($strTitle, $intPid = 0, $arrData = []): PageModel
    {
        // Create the page
        if (\array_key_exists('id', $arrData)) {
            $objPage = PageModel::findOneById($arrData['id']);
            if (!$objPage) {
                throw new InvalidArgumentException('La page ayant pour id "'.$arrData['id'].'" n\'existe pas');
            }
        } else {
            $objPage = new PageModel();
        }
        $objPage->tstamp = time();
        $objPage->pid = $intPid;
        if (\array_key_exists('sorting', $arrData)) {
            $objPage->sorting = $arrData['sorting'];
        } elseif (0 !== $intPid) {
            $objPage->sorting = self::getNextAvailablePageSortingByParentPage((int) $intPid);
        } elseif (\array_key_exists('layout', $arrData)) {
            $objPage->sorting = self::getNextAvailablePageSortingByLayout((int) $arrData['layout']);
        }

        $objPage->title = $strTitle;
        // $objPage->alias = StringUtil::generateAlias($objPage->title);
        $objPage->type = 'regular';
        $objPage->pageTitle = $strTitle;
        $objPage->robots = 'index,follow';
        $objPage->sitemap = 'map_default';
        $objPage->published = 1;

        // Now we get the default values, get the arrData table
        if (!empty($arrData)) {
            foreach ($arrData as $k => $v) {
                $objPage->$k = $v;
            }
        }

        $objPage->save();

        \Contao\Controller::loadDataContainer(PageModel::getTable());
        $dc = new \Contao\DC_Table(PageModel::getTable());
        $dc->id = $objPage->id;
        $dc->activeRecord = $objPage;
        $alias = System::getContainer()
            ->get('contao.listener.data_container.page_url')
            ->generateAlias($arrData['alias'] ?? '', $dc)
        ;

        $objPage = PageModel::findById($objPage->id);
        $objPage->alias = $alias;
        $objPage->save();

        // Return the model
        return $objPage;
    }

    /**
     * Shortcut for article creation.
     */
    public static function createArticle($objPage, $arrData = []): ArticleModel
    {
        // Create the article
        $objArticle = isset($arrData['id']) ? ArticleModel::findById($arrData['id']) ?? new ArticleModel() : new ArticleModel();
        $objArticle->tstamp = time();
        $objArticle->pid = $objPage->id;
        $objArticle->sorting = (ArticleModel::countBy('pid', $objPage->id) + 1) * 128;
        $objArticle->title = $objPage->title;
        $objArticle->alias = $objPage->alias;
        $objArticle->author = 1;
        $objArticle->inColumn = 'main';
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
     * Shortcut for content creation.
     */
    public static function createContent($objArticle, $arrData = []): ContentModel
    {
        // Dynamic ptable support
        if (!$arrData['ptable']) {
            $arrData['ptable'] = 'tl_article';
        }

        $objContentHighestSorting = ContentModel::findOneBy(['pid = ?', 'ptable = ?'], [$objArticle->id, $arrData['ptable']], ['order' => 'sorting DESC']);

        // Create the content
        $objContent = isset($arrData['id']) ? ContentModel::findById($arrData['id']) ?? new ContentModel() : new ContentModel();
        $objContent->tstamp = time();
        $objContent->pid = $objArticle->id;
        $objContent->ptable = $arrData['ptable'];
        $objContent->sorting = $objContentHighestSorting->sorting + 128;
        $objContent->type = 'text';

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
     * Shortcut for page w/ modules creations.
     */
    public static function createPageWithModules($strTitle, $arrModules, $intPid = 0, $arrPageData = [])
    {
        $arrConfig = static::loadSmartgearConfig();
        if (0 === $intPid) {
            $intPid = $arrConfig['sgInstallRootPage'];
        }

        // Create the page
        $objPage = static::createPage($strTitle, $intPid, $arrPageData);

        // Create the article
        $objArticle = static::createArticle($objPage);

        // Create the contents
        foreach ($arrModules as $intModule) {
            $objContent = static::createContent($objArticle, ['type' => 'module', 'module' => $intModule]);
        }

        // Return the page ID
        return $objPage->id;
    }

    /**
     * Shortcut for page w/ texts creations.
     *
     * @param mixed|null $arrHl
     */
    public static function createPageWithText($strTitle, $strText, $intPid = 0, $arrHl = null)
    {
        $arrConfig = static::loadSmartgearConfig();
        if (0 === $intPid) {
            $intPid = $arrConfig['sgInstallRootPage'];
        }

        // Create the page
        $objPage = static::createPage($strTitle, $intPid);

        // Create the article
        $objArticle = static::createArticle($objPage);

        // Create the content
        $objContent = static::createContent($objArticle, ['text' => $strText, 'headline' => $arrHl]);

        // Return the page ID
        return $objPage->id;
    }

    /**
     * Contao Friendly Base64 Converter to FileSystem.
     *
     * @param [String]  $base64        [Base64 String to decode]
     * @param [String]  $folder        [Folder name]
     * @param [String]  $file          [File name]
     * @param [Boolean] $blnReturnFile [Return the File Object if set to true]
     *
     * @return [Object] [File Object]
     */
    public static function base64ToImage($base64, $folder, $file, $blnReturnFile = true)
    {
        try {
            // split the string on commas
            // $data[ 0 ] == "data:image/png;base64"
            // $data[ 1 ] == <actual base64 string>
            $data = explode(',', $base64);
            $ext = substr($data[0], strpos($data[0], '/') + 1, (strpos($data[0], ';') - strpos($data[0], '/') - 1));
            $img = base64_decode($data[1], true);

            if (false === strpos(Config::get('validImageTypes'), $ext)) {
                throw new \Exception('Invalid image type : '.$ext);
            }

            // Determine a filename if absent
            $path = $folder.'/'.$file.'.'.$ext;

            // Create & Close the file to generate the Model, and then, reopen the file
            // Because ->close() do not return the File object but true \o/
            $objFile = new File($path);
            $objFile->write($img);

            if (!$objFile->close()) {
                throw new \Exception(sprintf("The file %s hasn't been saved correctly", $path));
            }

            if ($blnReturnFile) {
                return new File($path);
            }

            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Return a list of files in a certain dir.
     *
     * @param string $strDir
     *
     * @return array
     */
    public static function getFileList($strDir)
    {
        $root = scandir($strDir);
        foreach ($root as $value) {
            if ('.' === $value || '..' === $value) {
                continue;
            }
            if (is_file("$strDir/$value")) {
                $result[] = "$strDir/$value";
                continue;
            }
            foreach (static::getFileList("$strDir/$value") as $value) {
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * Get a package's version.
     *
     * @param string $package The package name
     *
     * @return string|null The package version if found, null otherwise
     */
    public static function getCustomPackageVersion(string $package): ?string
    {
        $packages = json_decode(file_get_contents(TL_ROOT.'/vendor/composer/installed.json'));

        foreach ($packages->packages as $p) {
            $p = (array) $p;
            if ($package === $p['name']) {
                return $p['version'];
            }
        }

        return null;
    }

    /**
     * Returns the public directory root in full or relative path (/path/to/contao/public or /path/to/contao/web).
     */
    public static function getPublicOrWebDirectory(?bool $relative = false): string
    {
        $rootDir = System::getContainer()->getParameter('kernel.project_dir');
        $webDir = System::getContainer()->getParameter('contao.web_dir');

        return $relative ? str_replace($rootDir.\DIRECTORY_SEPARATOR, '', $webDir) : $webDir;
    }

    /**
     * Get this package's version.
     *
     * @return string|null The package version if found, null otherwise
     */
    public static function getPackageVersion(): ?string
    {
        return self::getCustomPackageVersion('webexmachina/contao-smartgear');
    }

    /**
     * Return current Smartgear version.
     *
     * @return [Float] Smartgear version
     */
    public function getCurrentVersion()
    {
        $conf = self::loadSmartgearConfig();

        return $conf->getSgVersion();
    }

    /**
     * Add permissions to user group.
     *
     * @param String/Array $varPermission [Permission name / Array of permission names to add]
     * @param int $intGroup [User group ID, if not specified, we'll take Smartgear default user group]
     *
     * @return array [Permissions Array]
     */
    public static function addPermissions($varPermission, $intGroup = null)
    {
        try {
            // Retrieve usergroup existing permissions
            $arrPermissions = [];
            if (null === $intGroup) {
                /** @var CoreConfig */
                $conf = self::loadSmartgearConfig();

                // if ($conf['sgInstallUserGroup']) {
                $objUserGroup = UserGroupModel::findOneById($conf->getSgUserGroupRedactors());
            // }
            } else {
                $objUserGroup = UserGroupModel::findByPk($intGroup);
            }

            if ($objUserGroup) {
                $arrPermissions = deserialize($objUserGroup->alexf) ?? [];
            }

            // Add the permissions
            if (\is_array($varPermission)) {
                foreach ($varPermission as $strPermission) {
                    if (self::canAddPermission($strPermission) && !\in_array($strPermission, $arrPermissions, true)) {
                        $arrPermissions[] = $strPermission;
                    }
                }
            } else {
                if (self::canAddPermission($varPermission) && !\in_array($varPermission, $arrPermissions, true)) {
                    $arrPermissions[] = $varPermission;
                }
            }

            return $arrPermissions;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove permissions to user group.
     *
     * @param String/Array $varPermission [Permission name / Array of permission names to add]
     * @param int $intGroup [User group ID, if not specified, we'll take Smartgear default user group]
     *
     * @return array [Permissions Array]
     */
    public static function removePermissions($varPermission, $intGroup = null)
    {
        try {
            // Retrieve usergroup existing permissions
            $arrPermissions = [];
            if (null === $intGroup) {
                /** @var CoreConfig */
                $conf = self::loadSmartgearConfig();

                if ($conf['sgInstallUserGroup']) {
                    $objUserGroup = UserGroupModel::findByPk($conf->getSgUserGroupRedactors());
                }
            } else {
                $objUserGroup = UserGroupModel::findByPk($intGroup);
            }

            if ($objUserGroup) {
                $arrPermissions = deserialize($objUserGroup->alexf);
            }

            // Add the permissions
            if (\is_array($varPermission)) {
                foreach ($varPermission as $strPermission) {
                    if (\in_array($strPermission, $arrPermissions, true)) {
                        unset($arrPermissions[array_search($strPermission, $arrPermissions, true)]);
                    }
                }
            } else {
                if (\in_array($varPermission, $arrPermissions, true)) {
                    unset($arrPermissions[array_search($varPermission, $arrPermissions, true)]);
                }
            }

            return $arrPermissions;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function formatActions(array $arrUnformattedActions): array
    {
        $arrActions = [];
        if (\is_array($arrUnformattedActions) && !empty($arrUnformattedActions)) {
            foreach ($arrUnformattedActions as &$action) {
                switch ($action['v']) {
                    case 2:
                        $arrAttributes = [];
                        if ($action['attrs']) {
                            if (!$action['attrs']['class']) {
                                $action['attrs']['class'] = 'tl_submit';
                            } elseif (false === strpos($action['attrs']['class'], 'tl_submit')) {
                                $action['attrs']['class'] .= ' tl_submit';
                            }

                            foreach ($action['attrs'] as $k => $v) {
                                $arrAttributes[] = sprintf('%s="%s"', $k, $v);
                            }
                        }
                        $arrActions[] = sprintf(
                            '<%s %s>%s</%s>',
                            ($action['tag']) ?: 'button',
                            (0 < \count($arrAttributes)) ? implode(' ', $arrAttributes) : '',
                            ($action['text']) ?: 'text missing',
                            ($action['tag']) ?: 'button'
                        );
                        break;
                    default:
                        $arrActions[] = sprintf(
                            '<button type="submit" name="action" value="%s" class="tl_submit" %s>%s</button>',
                            $action['action'],
                            ($action['attributes']) ?: $action['attributes'],
                            $action['label']
                        );
                }
            }
        }

        return $arrActions;
    }

    public static function messagesToToastrCallbacksParameters(array $messages)
    {
        $callbacks = [];
        foreach ($messages as $message) {
            switch ($message['class']) {
                case 'tl_error':
                    $class = 'error';
                break;
                case 'tl_info':
                    $class = 'info';
                break;
                case 'tl_confirm':
                    $class = 'success';
                break;
                case 'tl_new':
                    $class = 'info';
                break;
            }
            $callbacks[] = [$class, $message['text']];
        }

        return $callbacks;
    }

    public static function humanReadableFilesize(int $size, ?int $precision = 2)
    {
        for ($i = 0; ($size / 1024) > 0.9; $i++, $size /= 1024) {
        }

        return round($size, $precision).['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'][$i];
    }

    public static function log($message, ?string $filename = 'debug.log'): void
    {
        $message = \is_string($message) ? $message : print_r($message, true);
        file_put_contents(\Contao\System::getContainer()->getParameter('kernel.project_dir').'/vendor/webexmachina/contao-smartgear/'.$filename, $message.\PHP_EOL, \FILE_APPEND);
    }

    /**
     * Converts a number of milliseconds into a human readable duration.
     *
     * @param int $duration Number of milliseconds
     *
     * @return string The duration in 12m34s567ms
     */
    public static function humanReadableDuration(int $duration): string
    {
        $minutes = (int) ($duration / 60000);
        $duration = ($duration % 60000);
        $seconds = (int) ($duration / 1000);
        $duration = ($duration % 1000);
        $ms = $duration;

        return sprintf('%02dm%02ds%03dms', $minutes, $seconds, $ms);
    }

    public static function getLocalizedTemplateContent(string $tplPath, string $language, ?string $fallbackTplPath = null): string
    {
        $tplPath = str_replace(['{root}', '{lang}', '{public_or_web}'], [TL_ROOT, $language, self::getPublicOrWebDirectory()], $tplPath);
        $fallbackTplPath = str_replace(['{root}', '{lang}', '{public_or_web}'], [TL_ROOT, $language, self::getPublicOrWebDirectory()], $fallbackTplPath);

        if (file_exists($tplPath)) {
            return file_get_contents($tplPath);
        }

        if (file_exists($fallbackTplPath)) {
            return file_get_contents($fallbackTplPath);
        }

        throw new Exception(sprintf('Unable to find "%s" nor "%s".', $tplPath, $fallbackTplPath));
    }

    public static function getTimestampsFromDateConfig(?int $year = null, ?int $month = null, ?int $day = null, ?int $startyear = 2000): array
    {
        $timestamps = [];
        if ($day && $month && $year) {
            // 24 hours gap
            $date = \DateTime::createFromFormat('Y-m-d', $year.'-'.$month.'-'.$day);
            $timestamps[] = [
                $date->setTime(0, 0, 0, 0)->getTimestamp(),
                $date->setTime(0, 0, 0, 0)->add(new \DateInterval('P1D'))->getTimestamp(),
            ];
        } elseif ($month && $year) {
            // 1 month gap
            $date = \DateTime::createFromFormat('Y-m-d', $year.'-'.$month.'-01');
            $timestamps[] = [
                $date->setTime(0, 0, 0, 0)->getTimestamp(),
                $date->setTime(0, 0, 0, 0)->add(new \DateInterval('P1M'))->getTimestamp(),
            ];
        } elseif ($day && $year) {
            // every 1st day of each month of the year
            for ($i = 1; $i < 12; ++$i) {
                $date = \DateTime::createFromFormat('Y-m-d', sprintf('%s-%02d-%s', $year, $i, $day));
                $timestamps[] = [
                    $date->setTime(0, 0, 0, 0)->getTimestamp(),
                    $date->setTime(0, 0, 0, 0)->add(new \DateInterval('P1D'))->getTimestamp(),
                ];
            }
        } elseif ($day && $month) {
            // every 1st day of month of each years
            for ($i = $startyear; $i <= date('Y'); ++$i) {
                $date = \DateTime::createFromFormat('Y-m-d', sprintf('%d-%s-%s', $i, $month, $day));
                $timestamps[] = [
                    $date->setTime(0, 0, 0, 0)->getTimestamp(),
                    $date->setTime(0, 0, 0, 0)->add(new \DateInterval('P1M'))->getTimestamp(),
                ];
            }
        } elseif ($year) {
            // 1 year gap
            $date = \DateTime::createFromFormat('Y-m-d', sprintf('%d-01-01', $year));
            $timestamps[] = [
                $date->setTime(0, 0, 0, 0)->getTimestamp(),
                $date->setTime(0, 0, 0, 0)->add(new \DateInterval('P1Y'))->getTimestamp(),
            ];
        } elseif ($month) {
            // every one month of each years
            for ($i = $startyear; $i <= date('Y'); ++$i) {
                $date = \DateTime::createFromFormat('Y-m-d', sprintf('%d-%s-01', $i, $month));
                $timestamps[] = [
                    $date->setTime(0, 0, 0, 0)->getTimestamp(),
                    $date->setTime(0, 0, 0, 0)->add(new \DateInterval('P1M'))->getTimestamp(),
                ];
            }
        } elseif ($day) {
            // every day of each month of each years
            for ($i = $startyear; $i <= date('Y'); ++$i) {
                for ($j = 1; $j < 12; ++$j) {
                    $date = \DateTime::createFromFormat('Y-m-d', sprintf('%d-%02d-%s', $i, $j, $day));
                    $timestamps[] = [
                        $date->setTime(0, 0, 0, 0)->getTimestamp(),
                        $date->setTime(0, 0, 0, 0)->add(new \DateInterval('P1D'))->getTimestamp(),
                    ];
                }
            }
        }

        return $timestamps;
    }

    /**
     * Returns the next available sorting for a page based on its layout.
     *
     * @param int $layoutId The layout ID
     *
     * @return int The next available sorting
     */
    public static function getNextAvailablePageSortingByLayout(int $layoutId): int
    {
        $rootPage = PageModel::findBy(['layout = ?', 'type = ?'], [$layoutId, 'root']);
        if (!$rootPage) {
            return 128;
        }

        $pages = PageModel::findBy('pid', $rootPage, ['order' => 'sorting DESC']);
        if (!$pages) {
            // return (int) $rootPage->sorting + 128;
            return 128;
        }
        $objPage = $pages->first()->current();

        return (int) $objPage->sorting + 128;
    }

    /**
     * Returns the next available page sorting for a page based on its parent page.
     *
     * @param int $parentPageId The page ID
     *
     * @return int The next available sorting
     */
    public static function getNextAvailablePageSortingByParentPage(int $parentPageId): int
    {
        $pidPage = PageModel::findById($parentPageId);
        if (!$pidPage) {
            return 128;
        }
        $pages = PageModel::findBy('pid', $parentPageId, ['order' => 'sorting DESC']);
        if (!$pages) {
            // return (int) $pidPage->sorting + 128;
            return 128;
        }
        $objPage = $pages->first()->current();

        return (int) $objPage->sorting + 128;
    }

    public static function buildCookieVisitorUniqIdHash(): string
    {
        return sha1(System::getContainer()->get('session')->getId().time());
    }

    public static function setCookieVisitorUniqIdHash(string $value): void
    {
        // if (!\array_key_exists('wem_sg_visitor_uniq_id_hash', $_COOKIE)) {
        /*
         * GPDR states 13 month is the maximum duration
         * we can store a cookie for statistics.
         *
         * @see https://www.cnil.fr/fr/cookies-et-autres-traceurs/regles/cookies-solutions-pour-les-outils-de-mesure-daudience
         */
        System::setCookie('wem_sg_visitor_uniq_id_hash', $value, strtotime('+13 month'));
        // }
    }

    public static function getCookieVisitorUniqIdHash(): ?string
    {
        return \array_key_exists('wem_sg_visitor_uniq_id_hash', $_COOKIE) ? $_COOKIE['wem_sg_visitor_uniq_id_hash'] : null;
    }

    /**
     * Check if a permission can be added into.
     *
     * @param string $strPermission [Permission to add]
     *
     * @return bool
     */
    private static function canAddPermission($strPermission)
    {
        if (!\in_array($strPermission, self::getContaoPermissions(), true)) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve Contao permissions.
     *
     * @param bool $blnRefresh [description]
     *
     * @return array
     */
    private static function getContaoPermissions($blnRefresh = false)
    {
        if (!static::$arrContaoPermissions || $blnRefresh) {
            Controller::loadDataContainer('tl_user_group');
            $stdClass = $GLOBALS['TL_DCA']['tl_user_group']['fields']['alexf']['options_callback'][0];
            $stdMethod = $GLOBALS['TL_DCA']['tl_user_group']['fields']['alexf']['options_callback'][1];
            $objClass = new $stdClass();
            $arrContaoPermissions = $objClass->$stdMethod();

            // Format available permissions into one flat array
            if (!\is_array($arrContaoPermissions) || empty($arrContaoPermissions)) {
                throw new \Exception("Les permissions Contao n'ont pas été correctement récupérées");
            }

            $arrPermissions = [];
            foreach ($arrContaoPermissions as $strTable => $arrContaoPermissions) {
                if (!\is_array($arrContaoPermissions) || empty($arrContaoPermissions)) {
                    continue;
                }

                foreach ($arrContaoPermissions as $strPermission => $strLabel) {
                    $arrPermissions[] = $strPermission;
                }
            }
            static::$arrContaoPermissions = $arrPermissions;
        }

        return static::$arrContaoPermissions;
    }
}
