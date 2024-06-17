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
    private ?UserGroupModel $userGroup = null;

    public function getUserGroup(): UserGroupModel
    {
        return $this->userGroup;
    }

    public function setUserGroup(UserGroupModel $userGroup): self
    {
        $this->userGroup = $userGroup;

        return $this;
    }

    public static function create(UserGroupModel $userGroup): UserGroupModelUtil
    {
        return (new self())->setUserGroup($userGroup);
    }

    /**
     * Add Smartgear permissions.
     *
     * @param array $smartgearPermissions The permissions
     */
    public function addSmartgearPermissions(array $smartgearPermissions): self
    {
        $this->userGroup->smartgear_permissions = $this->addAllowedItems($this->userGroup->smartgear_permissions, $smartgearPermissions);

        return $this;
    }

    /**
     * Remove Smartgear permissions.
     *
     * @param array $smartgearPermissions The permissions
     */
    public function removeSmartgearPermissions(array $smartgearPermissions): self
    {
        $this->userGroup->smartgear_permissions = $this->removeAllowedItems($this->userGroup->smartgear_permissions, $smartgearPermissions);

        return $this;
    }

    /**
     * Add allowed modules.
     *
     * @param array $newAllowedModules The modules names
     */
    public function addAllowedModules(array $newAllowedModules): self
    {
        $this->userGroup->modules = $this->addAllowedItems($this->userGroup->modules, $newAllowedModules);

        return $this;
    }

    /**
     * Remove allowed modules.
     *
     * @param array $unallowedModules The modules names
     */
    public function removeAllowedModules(array $unallowedModules): self
    {
        $this->userGroup->modules = $this->removeAllowedItems($this->userGroup->modules, $unallowedModules);

        return $this;
    }

    /**
     * Add allowed news archive (do not assign news permissions).
     *
     * @param array $newsArchiveIds The News Archives IDs
     */
    public function addAllowedNewsArchive(array $newsArchiveIds): self
    {
        $this->userGroup->news = $this->addAllowedItems($this->userGroup->news, $newsArchiveIds);

        return $this;
    }

    /**
     * Remove allowed news archive (do not remove news permissions).
     *
     * @param array $newsArchiveIds The News Archives IDs
     */
    public function removeAllowedNewsArchive(array $newsArchiveIds): self
    {
        $this->userGroup->news = $this->removeAllowedItems($this->userGroup->news, $newsArchiveIds);

        return $this;
    }

    /**
     * Add allowed calendars (do not assign news permissions).
     *
     * @param array $calendarIds The Calendars IDs
     */
    public function addAllowedCalendar(array $calendarIds): self
    {
        $this->userGroup->calendars = $this->addAllowedItems($this->userGroup->calendars, $calendarIds);

        return $this;
    }

    /**
     * Remove allowed calendars (do not remove calendars permissions).
     *
     * @param array $calendarIds The Calendars IDs
     */
    public function removeAllowedCalendar(array $calendarIds): self
    {
        $this->userGroup->calendars = $this->removeAllowedItems($this->userGroup->calendars, $calendarIds);

        return $this;
    }

    /**
     * Add allowed faqs (do not assign faqs permissions).
     *
     * @param array $faqIds The FAQ IDs
     */
    public function addAllowedFaq(array $faqIds): self
    {
        $this->userGroup->faqs = $this->addAllowedItems($this->userGroup->faqs, $faqIds);

        return $this;
    }

    /**
     * Remove allowed faqs (do not remove faqs permissions).
     *
     * @param array $faqIds The FAQ IDs
     */
    public function removeAllowedFaq(array $faqIds): self
    {
        $this->userGroup->faqs = $this->removeAllowedItems($this->userGroup->faqs, $faqIds);

        return $this;
    }

    /**
     * Add allowed forms.
     *
     * @param array $newAllowedForms The forms names
     */
    public function addAllowedForms(array $newAllowedForms): self
    {
        $this->userGroup->forms = $this->addAllowedItems($this->userGroup->forms, $newAllowedForms);

        return $this;
    }

    /**
     * Remove allowed forms.
     *
     * @param array $unallowedForms The forms names
     */
    public function removeAllowedForms(array $unallowedForms): self
    {
        $this->userGroup->forms = $this->removeAllowedItems($this->userGroup->forms, $unallowedForms);

        return $this;
    }

    /**
     * Add allowed fields (do not assign fields permissions).
     *
     * @param array $fields The fields types
     */
    public function addAllowedFormFields(array $fields): self
    {
        $this->userGroup->fields = $this->addAllowedItems($this->userGroup->fields, $fields);

        return $this;
    }

    /**
     * Remove allowed fields (do not remove fields permissions).
     *
     * @param array $fields The fields types
     */
    public function removeAllowedFormFields(array $fields): self
    {
        $this->userGroup->fields = $this->removeAllowedItems($this->userGroup->fields, $fields);

        return $this;
    }

    /**
     * Add allowed form permissions (do not assign fields).
     *
     * @param array $formp The formp permissions
     */
    public function addAllowedFormPermissions(array $formp): self
    {
        $this->userGroup->formp = $this->addAllowedItems($this->userGroup->formp, $formp);

        return $this;
    }

    /**
     * Remove allowed form permissions (do not remove fields).
     *
     * @param array $formp The formp permissions
     */
    public function removeAllowedFormPermissions(array $formp): self
    {
        $this->userGroup->formp = $this->removeAllowedItems($this->userGroup->formp, $formp);

        return $this;
    }

    /**
     * Add allowed elements.
     *
     * @param array $elements The elements
     */
    public function addAllowedElements(array $elements): self
    {
        $this->userGroup->elements = $this->addAllowedItems($this->userGroup->elements, $elements);

        return $this;
    }

    /**
     * Remove allowed elements.
     *
     * @param array $elements The elements
     */
    public function removeAllowedElements(array $elements): self
    {
        $this->userGroup->elements = $this->removeAllowedItems($this->userGroup->elements, $elements);

        return $this;
    }

    /**
     * Add allowed filemounts (do not assign filemount permissions).
     *
     * @param array $folderUUIDs The folders'UUIDs
     */
    public function addAllowedFilemounts(array $folderUUIDs): self
    {
        $this->userGroup->filemounts = $this->addAllowedItems($this->userGroup->filemounts, $folderUUIDs);

        return $this;
    }

    /**
     * Remove allowed filemounts (do not remove filemount permissions).
     *
     * @param array $folderUUIDs The folders'UUIDs
     */
    public function removeAllowedFilemounts(array $folderUUIDs): self
    {
        $this->userGroup->filemounts = $this->removeAllowedItems($this->userGroup->filemounts, $folderUUIDs);

        return $this;
    }

    /**
     * Add allowed file operation permissions (do not assign filemounts).
     *
     * @param array $fop The file operation permissions
     */
    public function addAllowedFileOperationPermissions(array $fop): self
    {
        $this->userGroup->fop = $this->addAllowedItems($this->userGroup->fop, $fop);

        return $this;
    }

    /**
     * Remove allowed file operation permissions (do not remove filemounts).
     *
     * @param array $fop The file operation permissions
     */
    public function removeAllowedFileOperationPermissions(array $fop): self
    {
        $this->userGroup->fop = $this->removeAllowedItems($this->userGroup->fop, $fop);

        return $this;
    }

    /**
     * Add allowed pagemounts.
     *
     * @param array $pageIds The pages'IDs
     */
    public function addAllowedPagemounts(array $pageIds): self
    {
        $this->userGroup->pagemounts = $this->addAllowedItems($this->userGroup->pagemounts, $pageIds);

        return $this;
    }

    /**
     * Remove allowed pagemounts.
     *
     * @param array $pageIds The pages'IDs
     */
    public function removeAllowedPagemounts(array $pageIds): self
    {
        $this->userGroup->pagemounts = $this->removeAllowedItems($this->userGroup->pagemounts, $pageIds);

        return $this;
    }

    /**
     * Add allowed imageSizes.
     *
     * @param array $imageSizes The pages'IDs
     */
    public function addAllowedImageSizes(array $imageSizes): self
    {
        $this->userGroup->imageSizes = $this->addAllowedItems($this->userGroup->imageSizes, $imageSizes);

        return $this;
    }

    /**
     * Remove allowed imageSizes.
     *
     * @param array $imageSizes The pages'IDs
     */
    public function removeAllowedImageSizes(array $imageSizes): self
    {
        $this->userGroup->imageSizes = $this->removeAllowedItems($this->userGroup->imageSizes, $imageSizes);

        return $this;
    }

    /**
     * Add allowed fields.
     *
     * @param array $fields The fields name to remove (eg tl_news::title)
     */
    public function addAllowedFields(array $fields): self
    {
        $alexf = null !== $this->userGroup->alexf ? unserialize($this->userGroup->alexf) : [];
        $this->userGroup->alexf = serialize(array_unique(array_merge($alexf, $fields)));

        return $this;
    }

    /**
     * Remove allowed fields.
     *
     * @param array $fields The fields name to remove (eg tl_news::title)
     */
    public function removeAllowedFields(array $fields): self
    {
        $alexf = null !== $this->userGroup->alexf ? unserialize($this->userGroup->alexf) : [];
        $this->userGroup->alexf = serialize(array_unique(array_diff($alexf, $fields)));

        return $this;
    }

    /**
     * Add allowed fields by table name.
     *
     * @param array $tables Name of tables to retrieve fields from their DCA
     * @throws \Exception
     */
    public function addAllowedFieldsByTables(array $tables): self
    {
        $allowedFields = [];
        foreach ($tables as $table) {
            if (!\array_key_exists($table, $GLOBALS['TL_DCA'] ?? [])) {
                $loader = new \Contao\DcaLoader($table);
                $loader->load();
            }

            $dcaFields = $GLOBALS['TL_DCA'][$table]['fields'] ?? [];
            foreach ($dcaFields as $key => $config) {
                // see tl_user_group::getExcludedFields
                if (($config['exclude'] ?? null) || ($config['orig_exclude'] ?? null)) {
                    $allowedFields[] = $table.'::'.$key;
                }
            }
        }

        return $this->addAllowedFields($allowedFields);
    }

    /**
     * Remove allowed fields by prefix.
     *
     * @param array $prefixes Prefixes of fields to remove (eg: tl_news::)
     */
    public function removeAllowedFieldsByPrefixes(array $prefixes): self
    {
        $alexf = unserialize($this->userGroup->alexf);

        foreach ($prefixes as $prefix) {
            $fieldNameKeyToDelete = $prefix;
            $fieldNameKeyToDeleteLength = \strlen((string) $fieldNameKeyToDelete);
            foreach ($alexf as $index => $fieldName) {
                if (!\is_string($fieldName)) {
                    unset($alexf[$index]);
                    continue;
                }

                if ($fieldNameKeyToDelete === substr($fieldName, 0, $fieldNameKeyToDeleteLength)) {
                    unset($alexf[$index]);
                }
            }
        }

        $this->userGroup->alexf = serialize($alexf);

        return $this;
    }

    /**
     * Return allowed items in a serialized array.
     *
     * @param ?string $rawValue The current allowed items (as they are stored in DB)
     * @param array   $items    The items
     *
     * @return ?string The allowed items in a serialized array
     */
    protected function addAllowedItems(?string $rawValue, array $items): ?string
    {
        $allowedItems = null !== $rawValue ? unserialize($rawValue) : [];
        foreach ($items as $item) {
            if (!\in_array($item, $allowedItems, true)) {
                $allowedItems[] = $item;
            }

            // $itemIndex = array_search($item, $allowedItems, true);
            // if (false === $itemIndex) {
            //     $allowedItems[] = $item;
            // }
        }

        return serialize($allowedItems);
    }

    /**
     * Return allowed items in a serialized array.
     *
     * @param ?string $rawValue The current allowed items (as they are stored in DB)
     * @param array   $items    The items
     *
     * @return ?string The allowed items in a serialized array
     */
    protected function removeAllowedItems(?string $rawValue, array $items): ?string
    {
        $allowedItems = null !== $rawValue ? unserialize($rawValue) : [];
        foreach ($items as $item) {
            $itemIndex = array_search($item, $allowedItems, true);
            if (false !== $itemIndex) {
                unset($allowedItems[$itemIndex]);
            }
        }

        return serialize($allowedItems);
    }
}
