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

use Contao\UserGroupModel;

class UserGroupModelUtil
{
    /**
     * Add Smartgear permissions.
     *
     * @param UserGroupModel $objUserGroup         The UserGroup entity to work on
     * @param array          $smartgearPermissions The permissions
     *
     * @return UserGroupModel The updated UserGroup entity (not saved)
     */
    public static function addSmartgearPermissions(UserGroupModel $objUserGroup, array $smartgearPermissions): UserGroupModel
    {
        $permissions = null !== $objUserGroup->smartgear_permissions ? unserialize($objUserGroup->smartgear_permissions) : [];
        foreach ($smartgearPermissions as $smartgearPermission) {
            $smartgearPermissionIndex = array_search($smartgearPermission, $permissions, true);
            if (false === $smartgearPermissionIndex) {
                $permissions[] = $smartgearPermission;
            }
        }

        $objUserGroup->smartgear_permissions = serialize($permissions);

        return $objUserGroup;
    }

    /**
     * Remove Smartgear permissions.
     *
     * @param UserGroupModel $objUserGroup         The UserGroup entity to work on
     * @param array          $smartgearPermissions The permissions
     *
     * @return UserGroupModel The updated UserGroup entity (not saved)
     */
    public static function removeSmartgearPermissions(UserGroupModel $objUserGroup, array $smartgearPermissions): UserGroupModel
    {
        $permissions = null !== $objUserGroup->smartgear_permissions ? unserialize($objUserGroup->smartgear_permissions) : [];
        foreach ($smartgearPermissions as $smartgearPermission) {
            $smartgearPermissionIndex = array_search($smartgearPermission, $permissions, true);
            if (false !== $smartgearPermissionIndex) {
                unset($permissions[$smartgearPermissionIndex]);
            }
        }

        $objUserGroup->smartgear_permissions = serialize($permissions);

        return $objUserGroup;
    }

    /**
     * Add allowed modules.
     *
     * @param UserGroupModel $objUserGroup      The UserGroup entity to work on
     * @param array          $newAllowedModules The modules names
     *
     * @return UserGroupModel The updated UserGroup entity (not saved)
     */
    public static function addAllowedModules(UserGroupModel $objUserGroup, array $newAllowedModules): UserGroupModel
    {
        $allowedModules = unserialize($objUserGroup->modules);

        foreach ($newAllowedModules as $newAllowedModule) {
            $blogModuleIndex = array_search($newAllowedModule, $allowedModules, true);
            if (false === $blogModuleIndex) {
                $allowedModules[] = $newAllowedModule;
            }
        }
        $objUserGroup->modules = serialize($allowedModules);

        return $objUserGroup;
    }

    /**
     * Remove allowed modules.
     *
     * @param UserGroupModel $objUserGroup     The UserGroup entity to work on
     * @param array          $unallowedModules The modules names
     *
     * @return UserGroupModel The updated UserGroup entity (not saved)
     */
    public static function removeAllowedModules(UserGroupModel $objUserGroup, array $unallowedModules): UserGroupModel
    {
        $allowedModules = unserialize($objUserGroup->modules);

        foreach ($unallowedModules as $unallowedModule) {
            $blogModuleIndex = array_search($unallowedModule, $allowedModules, true);
            if (false !== $blogModuleIndex) {
                unset($allowedModules[$unallowedModule]);
            }
        }
        $objUserGroup->modules = serialize($allowedModules);

        return $objUserGroup;
    }

    /**
     * Add allowed news archive (do not assign news permissions).
     *
     * @param UserGroupModel $objUserGroup   The UserGroup entity to work on
     * @param array          $newsArchiveIds The News Archives IDs
     *
     * @return UserGroupModel The updated UserGroup entity (not saved)
     */
    public static function addAllowedNewsArchive(UserGroupModel $objUserGroup, array $newsArchiveIds): UserGroupModel
    {
        // update allowed news archives
        $allowedNewsArchives = null !== $objUserGroup->news ? unserialize($objUserGroup->news) : [];
        foreach ($newsArchiveIds as $newsArchiveId) {
            $newsArchiveIndex = array_search((string) $newsArchiveId, $allowedNewsArchives, true);
            if (false === $newsArchiveIndex) {
                $allowedNewsArchives[] = (string) $newsArchiveId;
            }
        }
        $objUserGroup->news = serialize($allowedNewsArchives);

        return $objUserGroup;
    }

    /**
     * Remove allowed news archive (do not remove news permissions).
     *
     * @param UserGroupModel $objUserGroup   The UserGroup entity to work on
     * @param array          $newsArchiveIds The News Archives IDs
     *
     * @return UserGroupModel The updated UserGroup entity (not saved)
     */
    public static function removeAllowedNewsArchive(UserGroupModel $objUserGroup, array $newsArchiveIds): UserGroupModel
    {
        // update allowed news archives
        $allowedNewsArchives = null !== $objUserGroup->news ? unserialize($objUserGroup->news) : [];
        foreach ($newsArchiveIds as $newsArchiveId) {
            $newsArchiveIndex = array_search((string) $newsArchiveId, $allowedNewsArchives, true);
            if (false === $newsArchiveIndex) {
                unset($allowedNewsArchives[$newsArchiveIndex]);
            }
        }
        $objUserGroup->news = serialize($allowedNewsArchives);

        return $objUserGroup;
    }

    /**
     * Add allowed calendars (do not assign news permissions).
     *
     * @param UserGroupModel $objUserGroup The UserGroup entity to work on
     * @param array          $calendarIds  The Calendars IDs
     *
     * @return UserGroupModel The updated UserGroup entity (not saved)
     */
    public static function addAllowedCalendar(UserGroupModel $objUserGroup, array $calendarIds): UserGroupModel
    {
        // update allowed calendar
        $allowedCalendars = null !== $objUserGroup->calendars ? unserialize($objUserGroup->calendars) : [];
        foreach ($calendarIds as $calendarId) {
            $calendarIndex = array_search((string) $calendarId, $allowedCalendars, true);
            if (false === $calendarIndex) {
                $allowedCalendars[] = (string) $calendarId;
            }
        }
        $objUserGroup->calendars = serialize($allowedCalendars);

        return $objUserGroup;
    }

    /**
     * Remove allowed calendars (do not remove calendars permissions).
     *
     * @param UserGroupModel $objUserGroup The UserGroup entity to work on
     * @param array          $calendarIds  The Calendars IDs
     *
     * @return UserGroupModel The updated UserGroup entity (not saved)
     */
    public static function removeAllowedCalendar(UserGroupModel $objUserGroup, array $calendarIds): UserGroupModel
    {
        // update allowed calendar
        $allowedCalendars = null !== $objUserGroup->calendars ? unserialize($objUserGroup->calendars) : [];
        foreach ($calendarIds as $calendarId) {
            $calendarIndex = array_search((string) $calendarId, $allowedCalendars, true);
            if (false === $calendarIndex) {
                unset($allowedCalendars[$calendarIndex]);
            }
        }
        $objUserGroup->calendars = serialize($allowedCalendars);

        return $objUserGroup;
    }

    /**
     * Add allowed faqs (do not assign news permissions).
     *
     * @param UserGroupModel $objUserGroup The UserGroup entity to work on
     * @param array          $faqIds       The Calendars IDs
     *
     * @return UserGroupModel The updated UserGroup entity (not saved)
     */
    public static function addAllowedFaq(UserGroupModel $objUserGroup, array $faqIds): UserGroupModel
    {
        // update allowed calendar
        $allowedFaqs = null !== $objUserGroup->faqs ? unserialize($objUserGroup->faqs) : [];
        foreach ($faqIds as $faqId) {
            $faqIndex = array_search((string) $faqId, $allowedFaqs, true);
            if (false === $faqIndex) {
                $allowedFaqs[] = (string) $faqId;
            }
        }
        $objUserGroup->faqs = serialize($allowedFaqs);

        return $objUserGroup;
    }

    /**
     * Remove allowed faqs (do not remove faqs permissions).
     *
     * @param UserGroupModel $objUserGroup The UserGroup entity to work on
     * @param array          $faqIds       The Calendars IDs
     *
     * @return UserGroupModel The updated UserGroup entity (not saved)
     */
    public static function removeAllowedFaq(UserGroupModel $objUserGroup, array $faqIds): UserGroupModel
    {
        // update allowed calendar
        $allowedFaqs = null !== $objUserGroup->faqs ? unserialize($objUserGroup->faqs) : [];
        foreach ($faqIds as $faqId) {
            $faqIndex = array_search((string) $faqId, $allowedFaqs, true);
            if (false === $faqIndex) {
                unset($allowedFaqs[$faqIndex]);
            }
        }
        $objUserGroup->faqs = serialize($allowedFaqs);

        return $objUserGroup;
    }

    /**
     * Add allowed filemounts (do not assign filemount permissions).
     *
     * @param UserGroupModel $objUserGroup The UserGroup entity to work on
     * @param array          $folderUUIDs  The folders'UUIDs
     *
     * @return UserGroupModel The updated UserGroup entity (not saved)
     */
    public static function addAllowedFilemounts(UserGroupModel $objUserGroup, array $folderUUIDs): UserGroupModel
    {
        $allowedFolders = null !== $objUserGroup->filemounts ? unserialize($objUserGroup->filemounts) : [];
        foreach ($folderUUIDs as $folderUUID) {
            $folderIndex = array_search($folderUUID, $allowedFolders, true);
            if (false === $folderIndex) {
                $allowedFolders[] = $folderUUID;
            }
        }
        $objUserGroup->filemounts = serialize($allowedFolders);

        return $objUserGroup;
    }

    /**
     * Remove allowed filemounts (do not remove filemount permissions).
     *
     * @param UserGroupModel $objUserGroup The UserGroup entity to work on
     * @param array          $folderUUIDs  The folders'UUIDs
     *
     * @return UserGroupModel The updated UserGroup entity (not saved)
     */
    public static function removeAllowedFilemounts(UserGroupModel $objUserGroup, array $folderUUIDs): UserGroupModel
    {
        $allowedFolders = null !== $objUserGroup->filemounts ? unserialize($objUserGroup->filemounts) : [];
        foreach ($folderUUIDs as $folderUUID) {
            $folderIndex = array_search($folderUUID, $allowedFolders, true);
            if (false !== $folderIndex) {
                unset($allowedFolders[$folderIndex]);
            }
        }
        $objUserGroup->filemounts = serialize($allowedFolders);

        return $objUserGroup;
    }

    /**
     * Add allowed fields.
     *
     * @param UserGroupModel $objUserGroup The UserGroup entity to work on
     * @param array          $fields       The fields name to remove (eg tl_news::title)
     *
     * @return UserGroupModel The updated UserGroup entity (not saved)
     */
    public static function addAllowedFields(UserGroupModel $objUserGroup, array $fields): UserGroupModel
    {
        $objUserGroup->alexf = serialize(array_unique(array_merge(unserialize($objUserGroup->alexf), $fields)));

        return $objUserGroup;
    }

    /**
     * Remove allowed fields.
     *
     * @param UserGroupModel $objUserGroup The UserGroup entity to work on
     * @param array          $fields       The fields name to remove (eg tl_news::title)
     *
     * @return UserGroupModel The updated UserGroup entity (not saved)
     */
    public static function removeAllowedFields(UserGroupModel $objUserGroup, array $fields): UserGroupModel
    {
        $objUserGroup->alexf = serialize(array_unique(array_diff(unserialize($objUserGroup->alexf), $fields)));

        return $objUserGroup;
    }

    /**
     * Add allowed fields by table name.
     *
     * @param UserGroupModel $objUserGroup The UserGroup entity to work on
     * @param array          $tables       Name of tables to retrieve fields from their DCA
     *
     * @return UserGroupModel The updated UserGroup entity (not saved)
     */
    public static function addAllowedFieldsByTables(UserGroupModel $objUserGroup, array $tables): UserGroupModel
    {
        $allowedFields = [];
        foreach ($tables as $table) {
            if (!\array_key_exists($table, $GLOBALS['TL_DCA'] ?? [])) {
                $loader = new \Contao\DcaLoader($table);
                $loader->load();
            }
            $allowedFieldsWithoutPrefix = array_keys($GLOBALS['TL_DCA'][$table]['fields'] ?? []);
            foreach ($allowedFieldsWithoutPrefix as $allowedFieldWithoutPrefix) {
                $allowedFields[] = $table.'::'.$allowedFieldWithoutPrefix;
            }
        }

        return static::addAllowedFields($objUserGroup, $allowedFields);
    }

    /**
     * Remove allowed fields by prefix.
     *
     * @param UserGroupModel $objUserGroup The UserGroup entity to work on
     * @param array          $prefixes     Prefixes of fields to remove (eg: tl_news::)
     *
     * @return UserGroupModel The updated UserGroup entity (not saved)
     */
    public static function removeAllowedFieldsByPrefixes(UserGroupModel $objUserGroup, array $prefixes): UserGroupModel
    {
        $alexf = unserialize($objUserGroup->alexf);

        foreach ($prefixes as $prefix) {
            $fieldNameKeyToDelete = $prefix;
            $fieldNameKeyToDeleteLength = \strlen($fieldNameKeyToDelete);
            foreach ($alexf as $index => $fieldName) {
                if ($fieldNameKeyToDelete === substr($fieldName, 0, $fieldNameKeyToDeleteLength)) {
                    unset($alexf[$index]);
                }
            }
        }

        $objUserGroup->alexf = serialize($alexf);

        return $objUserGroup;
    }
}
