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

use Contao\Config;
use Contao\Controller;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\File;
use Contao\Files;
use Contao\PageModel;
use Contao\System;
use Contao\UserGroupModel;
use DateInterval;
use Exception;
use Psr\Log\LogLevel;
use WEM\SmartgearBundle\Classes\Utils\Configuration\ConfigurationUtil;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Model\Configuration\Configuration;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Util
{
    /**
     * Store the path to the config file.
     */
    protected static string $strConfigPath = 'assets/smartgear/config.json';

    /**
     * Permissions cache system.
     */
    protected static array $arrContaoPermissions;

    /**
     * Extract colors used in Framway.
     *
     * @return array Framway colors
     */
    public static function getDefaultColors(): array
    {
        return [
            '' => ['label' => 'Par défaut', 'hexa' => ''], 'blue' => ['label' => 'Bleu (#004C79)', 'hexa' => '004C79'], 'darkblue' => ['label' => 'Bleu foncé (#0a1d29)', 'hexa' => '0a1d29'], 'green' => ['label' => 'Vert (#5cb85c)', 'hexa' => '5cb85c'], 'orange' => ['label' => 'Rouge (#DC6053)', 'hexa' => 'DC6053'], 'gold' => ['label' => 'Doré (#edbe5f)', 'hexa' => 'edbe5f'], 'black' => ['label' => 'Noir (#000000)', 'hexa' => '000000'], 'blacklight' => ['label' => 'Noir 90% (#111414)', 'hexa' => '111414'], 'blacklighter' => ['label' => 'Noir 80% (#222222)', 'hexa' => '222222'], 'greystronger' => ['label' => 'Noir 70% (#424041)', 'hexa' => '424041'], 'greystrong' => ['label' => 'Noir 60% (#535052)', 'hexa' => '535052'], 'grey' => ['label' => 'Gris (#7A7778)', 'hexa' => '7A7778'], 'greylight' => ['label' => 'Gris 50% (#DDDDDD)', 'hexa' => 'DDDDDD'], 'greylighter' => ['label' => 'Gris 25% (#EEEEEE)', 'hexa' => 'EEEEEE'], 'white' => ['label' => 'Blanc (#ffffff)', 'hexa' => 'ffffff'], 'none' => ['label' => 'Transparent', 'hexa' => ''],
        ];
    }

    /**
     * Extract colors used in Framway.
     *
     * @param ?string $strFWTheme Get the colors of a specific theme
     *
     * @return array Framway colors
     * @todo Find a way to add friendly names to the colors retrieved
     * @todo Maybe store these colors into a file to avoid load/format a shitload of stuff ?
     */
    public static function getFramwayColors(string $table, int $id, ?string $strFWTheme = ''): array
    {
        $framwayUtil = System::getContainer()->get('smartgear.classes.util_framway');
        $objConfiguration = ConfigurationUtil::findConfigurationForItem($table, $id);
        $fwPath = $objConfiguration instanceof Configuration ? $objConfiguration->framway_path : Configuration::DEFAULT_FRAMWAY_PATH;
        $colors = $strFWTheme === null || $strFWTheme === '' || $strFWTheme === '0' ? $framwayUtil->getCombinedColors($fwPath) : $framwayUtil->getThemeColors($fwPath, $strFWTheme);
        $return = [];
        foreach ($colors as $label => $hexa) {
            $return[$label] = ['label' => trim((string) $label), 'hexa' => trim(str_replace('#', '', $hexa))];
        }

        return $return;
    }

    /**
     * Get available colors in Smartgear.
     *
     * @param string $strFor Format wanted
     * @param ?string $strFWTheme Framway theme wanted
     *
     * @return array|false|string An Array of classes / color names
     */
    public static function getSmartgearColors(string $table, int $id, string $strFor = 'rsce', ?string $strFWTheme = ''): array|false|string
    {
        try {
            // Extract colors from installed Framway
            $arrColors = self::getFramwayColors($table, $id, $strFWTheme);
        } catch (\Exception $exception) {
            System::getContainer()
                ->get('monolog.logger.contao')
                ->log(
                    LogLevel::ERROR,
                    'Error when trying to get Framway Colors : '.$exception->getMessage(),
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
    }

    /**
     * Find and Create an Object, depending on type and module.
     *
     * @param string $strType   Type / Folder
     * @param string $strModule Class / File
     *
     * @throws Exception
     */
    public static function findAndCreateObject(string $strType,string $strModule = ''): string
    {
        if ('' === $strModule && str_contains($strType, '_')) {
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
    }

    /**
     * Get Smartgear Config.
     */
    public static function loadSmartgearConfig(): CoreConfig
    {
        $configManager = System::getContainer()->get('smartgear.config.manager.core');
        return $configManager->load();
    }

    /**
     * Update Smartgear Config.
     *
     * @param array $arrVars [Key/Value Array]
     *
     * @deprecated
     */
    public static function updateConfig(array $arrVars): array
    {
        trigger_deprecation(package: "SmartGear", version: '1.0', message:"please dont Use");
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
    }

    /**
     * Reset Smartgear Config.
     *
     * @deprecated
     */
    public static function resetConfig(): void
    {
        $objFiles = Files::getInstance();
        // Open and update the config file
        $objFile = $objFiles->fopen(static::$strConfigPath, 'w');
        $objFiles->fputs($objFile, '{}');
        $objFiles->fclose($objFile);
    }

    /**
     * Contao Friendly Base64 Converter to FileSystem.
     *
     * @param string  $base64        [Base64 String to decode]
     * @param string  $folder        [Folder name]
     * @param string  $file          [File name]
     * @param boolean $blnReturnFile [Return the File Object if set to true]
     * @throws Exception
     */
    public static function base64ToImage(string $base64, string $folder, string $file,bool $blnReturnFile = true): File|true
    {
        $data = explode(',', $base64);
        $ext = substr($data[0], strpos($data[0], '/') + 1, (strpos($data[0], ';') - strpos($data[0], '/') - 1));
        $img = base64_decode($data[1], true);
        if (!str_contains((string) Config::get('validImageTypes'), $ext)) {
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
    }

    /**
     * Return a list of files in a certain dir.
     */
    public static function getFileList(string $strDir): array
    {
        $result = [];
        $root = scandir($strDir);
        foreach ($root as $value) {
            if ('.' === $value || '..' === $value) {
                continue;
            }

            if (is_file(sprintf('%s/%s', $strDir, $value))) {
                $result[] = sprintf('%s/%s', $strDir, $value);
                continue;
            }

            foreach (static::getFileList(sprintf('%s/%s', $strDir, $value)) as $value) {
                $result[] = $value;
            }
        }

        return $result;
    }

    public static function getFileListByLanguages(string $strDir): array
    {
        $files = [];
        $root = scandir($strDir);
        foreach ($root as $value) {
            if ('.' === $value || '..' === $value) {
                continue;
            }

            if (is_dir(sprintf('%s/%s', $strDir, $value))) {
                $files[$value] = [];

                $filesInDir = scandir(sprintf('%s/%s', $strDir, $value));
                foreach ($filesInDir as $subValue) {
                    if (is_file(sprintf('%s/%s/%s', $strDir, $value, $subValue))) {
                        $files[$value][] = sprintf('%s/%s/%s', $strDir, $value, $subValue);
                        // continue;
                    }
                }
            }

            // foreach (static::getFileList("$strDir/$value") as $value) {
            //     $result[] = $value;
            // }
        }

        return $files;
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
        $projectDir = System::getContainer()->getParameter('kernel.project_dir');
        $packages = json_decode(file_get_contents($projectDir.'/vendor/composer/installed.json'));

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
     */
    public function getCurrentVersion(): string
    {
        $conf = self::loadSmartgearConfig();

        return $conf->getSgVersion();
    }

    /**
     * Add permissions to user group.
     *
     * @param string|array $varPermission [Permission name / Array of permission names to add]
     * @param int|null $intGroup [User group ID, if not specified, we'll take Smartgear default user group]
     *
     * @return array [Permissions Array]
     * @throws Exception
     */
    public static function addPermissions(string|array $varPermission, int $intGroup = null): array
    {
        $arrPermissions = [];
        if (null === $intGroup) {
            $conf = self::loadSmartgearConfig();

            // if ($conf['sgInstallUserGroup']) {
            $objUserGroup = UserGroupModel::findOneById($conf->getSgUserGroupRedactors());
        // }
        } else {
            $objUserGroup = UserGroupModel::findByPk($intGroup);
        }

        if ($objUserGroup) {
            $arrPermissions = StringUtil::deserialize($objUserGroup->alexf) ?? [];
        }

        // Add the permissions
        if (\is_array($varPermission)) {
            foreach ($varPermission as $strPermission) {
                if (self::canAddPermission($strPermission) && !\in_array($strPermission, $arrPermissions, true)) {
                    $arrPermissions[] = $strPermission;
                }
            }
        } elseif (self::canAddPermission($varPermission) && !\in_array($varPermission, $arrPermissions, true)) {
            $arrPermissions[] = $varPermission;
        }

        return $arrPermissions;
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
        $arrPermissions = [];
        if (null === $intGroup) {
            $conf = self::loadSmartgearConfig();

            if ($conf['sgInstallUserGroup']) {
                $objUserGroup = UserGroupModel::findByPk($conf->getSgUserGroupRedactors());
            }
        } else {
            $objUserGroup = UserGroupModel::findByPk($intGroup);
        }

        if ($objUserGroup) {
            $arrPermissions = StringUtil::deserialize($objUserGroup->alexf);
        }

        // Add the permissions
        if (\is_array($varPermission)) {
            foreach ($varPermission as $strPermission) {
                if (\in_array($strPermission, $arrPermissions, true)) {
                    unset($arrPermissions[array_search($strPermission, $arrPermissions, true)]);
                }
            }
        } elseif (\in_array($varPermission, $arrPermissions, true)) {
            unset($arrPermissions[array_search($varPermission, $arrPermissions, true)]);
        }

        return $arrPermissions;
    }

    public static function formatActions(array $arrUnformattedActions): array
    {
        $arrActions = [];
        foreach ($arrUnformattedActions as &$action) {
            switch ($action['v']) {
                case 2:
                    $arrAttributes = [];
                    if ($action['attrs']) {
                        if (!$action['attrs']['class']) {
                            $action['attrs']['class'] = 'tl_submit';
                        } elseif (!str_contains((string) $action['attrs']['class'], 'tl_submit')) {
                            $action['attrs']['class'] .= ' tl_submit';
                        }

                        foreach ($action['attrs'] as $k => $v) {
                            $arrAttributes[] = sprintf('%s="%s"', $k, $v);
                        }
                    }

                    $arrActions[] = sprintf(
                        '<%s %s>%s</%s>',
                        ($action['tag']) ?: 'button',
                        ([] !== $arrAttributes) ? implode(' ', $arrAttributes) : '',
                        ($action['text']) ?: 'text missing',
                        ($action['tag']) ?: 'button'
                    );
                    break;
                default:
                    $arrActions[] = sprintf(
                        '<button type="submit" name="action" value="%s" class="tl_submit" %s>%s</button>',
                        $action['action'],
                        ($action['attributes']) ?: "" ,
                        $action['label']
                    );
            }
        }

        return $arrActions;
    }

    public static function messagesToToastrCallbacksParameters(array $messages): array
    {
        $callbacks = [];
        foreach ($messages as $message) {
            switch ($message['class']) {
                case 'tl_error':
                    $class = 'error';
                break;
                case 'tl_info':
                case 'tl_new':
                    $class = 'info';
                break;
                case 'tl_confirm':
                    $class = 'success';
                break;
            }

            $callbacks[] = [$class, $message['text']];
        }

        return $callbacks;
    }

    public static function humanReadableFilesize(int $size, ?int $precision = 2): string
    {
        for ($i = 0; ($size / 1024) > 0.9; $i++, $size /= 1024) {}

        return round($size, $precision).['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'][$i];
    }

    public static function log($message, ?string $filename = 'debug.log'): void
    {
        $message = \is_string($message) ? $message : print_r($message, true);
        file_put_contents(System::getContainer()->getParameter('kernel.project_dir').'/vendor/webexmachina/contao-smartgear/'.$filename, $message.\PHP_EOL, \FILE_APPEND);
    }

    /**
     * Converts a number of milliseconds into a human-readable duration.
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

    /**
     * @throws Exception
     */
    public static function getLocalizedTemplateContent(string $tplPath, string $language, ?string $fallbackTplPath = null): string
    {
        $projectDir = System::getContainer()->getParameter('kernel.project_dir');
        $tplPath = str_replace(['{root}', '{lang}', '{public_or_web}'], [$projectDir, $language, self::getPublicOrWebDirectory()], $tplPath);
        $fallbackTplPath = str_replace(['{root}', '{lang}', '{public_or_web}'], [$projectDir, $language, self::getPublicOrWebDirectory()], $fallbackTplPath);

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
        return $_COOKIE['wem_sg_visitor_uniq_id_hash'] ?? null;
    }

    public static function transformHostnameForAirtableUse(string $hostname): string
    {
        return str_replace(['https://', 'www.'], '', $hostname);
    }

    public static function getRootPagesDomains(?bool $publishedOnly = false): array
    {
        $rootPages = PageModel::findBy('type', 'root');
        $arrDomains = [];
        if ($rootPages) {
            while ($rootPages->next()) {
                if (!$publishedOnly || ($publishedOnly && $rootPages->current()->published)) {
                    $arrDomains[] = self::transformHostnameForAirtableUse($rootPages->current()->dns);
                }
            }
        }

        return $arrDomains;
    }

    public static function getAirtableClientsRef(array $hostingInformations): array
    {
        $clientsRef = [];
        foreach ($hostingInformations as $hostnameHostingInformations) {
            if (!empty($hostnameHostingInformations['client_reference'])
                && '' !== $hostnameHostingInformations['client_reference'][0]
                ) {
                $clientsRef[] = $hostnameHostingInformations['client_reference'][0];
            }
        }

        return $clientsRef;
    }

    public static function formatDateInterval(DateInterval $interval, ?bool $includeSeconds = false): string
    {
        $result = '';
        if ($interval->y) {
            $result .= $interval->format('%y '.strtolower((string) $GLOBALS['TL_LANG']['MSC']['year'.($interval->y > 1 ? 's' : '')]).' ');
        }

        if ($interval->m) {
            $result .= $interval->format('%m '.strtolower((string) $GLOBALS['TL_LANG']['MSC']['month'.($interval->m > 1 ? 's' : '')]).' ');
        }

        if ($interval->d) {
            $result .= $interval->format('%d '.strtolower((string) $GLOBALS['TL_LANG']['MSC']['day'.($interval->d > 1 ? 's' : '')]).' ');
        }

        if ($interval->h) {
            $result .= $interval->format('%h '.strtolower((string) $GLOBALS['TL_LANG']['MSC']['hour'.($interval->h > 1 ? 's' : '')]).' ');
        }

        if ($interval->i) {
            $result .= $interval->format('%i '.strtolower((string) $GLOBALS['TL_LANG']['MSC']['minute'.($interval->i > 1 ? 's' : '')]).' ');
        }

        if ($includeSeconds && $interval->s) {
            $result .= $interval->format('%s '.strtolower((string) $GLOBALS['TL_LANG']['MSC']['second'.($interval->s > 1 ? 's' : '')]).' ');
        }

        return trim($result);
    }

    public static function formatPhpMemoryLimitToBytes($value): int
    {
        if ('-1' === (string) $value) {
            return -1;
        }

        if (\is_int($value) || preg_match('/^(\d*)$/', (string) $value)) {
            return (int) $value;
        }

        // now parse to find G (value in gigabytes), k/m/g (value in kilobytes, megabytes, gigabytes because shorthand is case-insensitive)
        $value = trim((string) $value);
        $last = strtolower($value[\strlen($value) - 1]);
        $value = substr($value, 0, -1);
        switch ($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $value *= 1024;
                // no break
            case 'm':
                $value *= 1024;
                // no break
            case 'k':
                $value *= 1024;
        }

        return $value;
    }

    /**
     * Check if a permission can be added into.
     *
     * @param string $strPermission [Permission to add]
     * @throws Exception
     */
    private static function canAddPermission(string $strPermission): bool
    {
        return \in_array($strPermission, self::getContaoPermissions(), true);
    }

    /**
     * Retrieve Contao permissions.
     *
     * @param bool $blnRefresh [description]
     *
     * @throws Exception
     */
    private static function getContaoPermissions(bool $blnRefresh = false): array
    {
        if (!static::$arrContaoPermissions || $blnRefresh) {
            Controller::loadDataContainer('tl_user_group');
            $stdClass = $GLOBALS['TL_DCA']['tl_user_group']['fields']['alexf']['options_callback'][0];
            $stdMethod = $GLOBALS['TL_DCA']['tl_user_group']['fields']['alexf']['options_callback'][1];
            $objClass = new $stdClass();
            $arrContaoPermissions = $objClass->$stdMethod();

            // Format available permissions into one flat array
            if (!\is_array($arrContaoPermissions) || $arrContaoPermissions === []) {
                throw new \Exception("Les permissions Contao n'ont pas été correctement récupérées");
            }

            $arrPermissions = [];
            foreach ($arrContaoPermissions as $arrContaoPermissions) {
                if (!\is_array($arrContaoPermissions) || $arrContaoPermissions === []) {
                    continue;
                }

                foreach (array_keys($arrContaoPermissions) as $strPermission) {
                    $arrPermissions[] = $strPermission;
                }
            }

            static::$arrContaoPermissions = $arrPermissions;
        }

        return static::$arrContaoPermissions;
    }
}
