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

namespace WEM\SmartgearBundle\Classes\Config;

interface ConfigModuleInterface extends ConfigJsonInterface
{
    /**
     * Get the installation status.
     *
     * @return bool true if installation is complete, false otherwise
     */
    public function getSgInstallComplete(): bool;

    /**
     * Set the installation status.
     *
     * @param bool $sgInstallComplete true if installation is complete, false otherwise
     */
    public function setSgInstallComplete(bool $sgInstallComplete): self;

    public function getContaoModulesIds(): array;

    public function getContaoPagesIds(): array;

    public function getContaoContentsIds(): array;

    public function getContaoArticlesIds(): array;

    public function getContaoFoldersIds(): array;

    public function getContaoUsersIds(): array;

    public function getContaoUserGroupsIds(): array;

    public function getContaoMembersIds(): array;

    public function getContaoMemberGroupsIds(): array;
}
