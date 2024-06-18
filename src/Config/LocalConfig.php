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

namespace WEM\SmartgearBundle\Config;

use WEM\SmartgearBundle\Classes\Config\ConfigYamlInterface;

class LocalConfig implements ConfigYamlInterface
{
    protected ?string $dateFormat = null;

    protected ?string $timeFormat = null;

    protected ?string $datimFormat = null;

    protected ?string $timeZone = null;

    protected ?string $characterSet = null;

    protected ?bool $useAutoItem = null;

    protected ?bool $folderUrl = null;

    protected ?int $maxResultsPerPage = null;

    protected ?bool $privacyAnonymizeIp = null;

    protected ?bool $privacyAnonymizeGA = null;

    protected ?int $gdMaxImgWidth = null;

    protected ?int $gdMaxImgHeight = null;

    protected ?int $maxFileSize = null;

    protected ?int $undoPeriod = null;

    protected ?int $versionPeriod = null;

    protected ?int $logPeriod = null;

    protected ?string $allowedTags = null;

    protected ?string $sgOwnerDomain = null;

    protected ?string $sgOwnerHost = null;

    protected ?bool $rejectLargeUploads = null;

    protected ?array $untouchedConfig = null;

    protected ?array $imageSizes = null;

    /** @var bool Default on true because package have troubles @todo : do not manage this settings here once the package is corrected */
    protected bool $fileusageSkipReplaceInsertTags = true;

    protected ?bool $fileusageSkipDatabase = null;

    public function reset(): self
    {
        // we reset everything BUT the untouchedConfig
        $this
            ->setDateFormat(null)
            ->setTimeFormat(null)
            ->setDatimFormat(null)
            ->setTimeZone(null)
            ->setCharacterSet(null)
            ->setUseAutoItem(null)
            ->setFolderUrl(null)
            ->setMaxResultsPerPage(null)
            ->setPrivacyAnonymizeIp(null)
            ->setPrivacyAnonymizeGA(null)
            ->setGdMaxImgWidth(null)
            ->setGdMaxImgHeight(null)
            ->setMaxFileSize(null)
            ->setUndoPeriod(null)
            ->setVersionPeriod(null)
            ->setLogPeriod(null)
            ->setAllowedTags(null)
            ->setSgOwnerDomain(null)
            ->setSgOwnerHost(null)
            ->setRejectLargeUploads(null)
            ->setImageSizes(null)
            ->setFileusageSkipReplaceInsertTags(true)
            ->setFileusageSkipDatabase(null)
        ;

        return $this;
    }

    public function import(array $content): self
    {
        $this
            ->setDateFormat($content['contao']['localconfig']['dateFormat'] ?? null)
            ->setTimeFormat($content['contao']['localconfig']['timeFormat'] ?? null)
            ->setDatimFormat($content['contao']['localconfig']['datimFormat'] ?? null)
            ->setTimeZone($content['contao']['localconfig']['timeZone'] ?? null)
            ->setCharacterSet($content['contao']['localconfig']['characterSet'] ?? null)
            ->setUseAutoItem($content['contao']['localconfig']['useAutoItem'] ?? null)
            ->setFolderUrl($content['contao']['localconfig']['folderUrl'] ?? null)
            ->setMaxResultsPerPage($content['contao']['localconfig']['maxResultsPerPage'] ?? null)
            ->setPrivacyAnonymizeIp($content['contao']['localconfig']['privacyAnonymizeIp'] ?? null)
            ->setPrivacyAnonymizeGA($content['contao']['localconfig']['privacyAnonymizeGA'] ?? null)
            ->setGdMaxImgWidth($content['contao']['localconfig']['gdMaxImgWidth'] ?? null)
            ->setGdMaxImgHeight($content['contao']['localconfig']['gdMaxImgHeight'] ?? null)
            ->setMaxFileSize($content['contao']['localconfig']['maxFileSize'] ?? null)
            ->setUndoPeriod($content['contao']['localconfig']['undoPeriod'] ?? null)
            ->setVersionPeriod($content['contao']['localconfig']['versionPeriod'] ?? null)
            ->setLogPeriod($content['contao']['localconfig']['logPeriod'] ?? null)
            ->setAllowedTags($content['contao']['localconfig']['allowedTags'] ?? null)
            ->setSgOwnerDomain($content['contao']['localconfig']['sgOwnerDomain'] ?? null)
            ->setSgOwnerHost($content['contao']['localconfig']['sgOwnerHost'] ?? null)
            ->setRejectLargeUploads($content['contao']['image']['reject_large_uploads'] ?? null)
            ->setImageSizes($content['contao']['image']['sizes'] ?? null)
            ->setFileusageSkipReplaceInsertTags($content['contao']['localconfig']['fileusageSkipReplaceInsertTags'] ?? true)
            ->setFileusageSkipDatabase($content['contao']['localconfig']['fileusageSkipDatabase'] ?? null)
        ;

        unset($content['contao']['localconfig']['dateFormat'], $content['contao']['localconfig']['timeFormat'], $content['contao']['localconfig']['datimFormat'], $content['contao']['localconfig']['timeZone'], $content['contao']['localconfig']['characterSet'], $content['contao']['localconfig']['useAutoItem'], $content['contao']['localconfig']['folderUrl'], $content['contao']['localconfig']['maxResultsPerPage'], $content['contao']['localconfig']['privacyAnonymizeIp'], $content['contao']['localconfig']['privacyAnonymizeGA'], $content['contao']['localconfig']['gdMaxImgWidth'], $content['contao']['localconfig']['gdMaxImgHeight'], $content['contao']['localconfig']['maxFileSize'], $content['contao']['localconfig']['undoPeriod'], $content['contao']['localconfig']['versionPeriod'], $content['contao']['localconfig']['logPeriod'], $content['contao']['localconfig']['allowedTags'], $content['contao']['localconfig']['sgOwnerDomain'], $content['contao']['localconfig']['sgOwnerHost'], $content['contao']['image']['reject_large_uploads'], $content['contao']['image']['sizes'],$content['contao']['localconfig']['fileusageSkipReplaceInsertTags'],$content['contao']['localconfig']['fileusageSkipDatabase']
    );

        $this->untouchedConfig = $content;

        return $this;
    }

    public function export(): array
    {
        $config = $this->getUntouchedConfig() ?? [];

        if (null !== $this->getDateFormat()) {
            $config['contao']['localconfig']['dateFormat'] = $this->getDateFormat();
        }

        if (null !== $this->getTimeFormat()) {
            $config['contao']['localconfig']['timeFormat'] = $this->getTimeFormat();
        }

        if (null !== $this->getDatimFormat()) {
            $config['contao']['localconfig']['datimFormat'] = $this->getDatimFormat();
        }

        if (null !== $this->getTimeZone()) {
            $config['contao']['localconfig']['timeZone'] = $this->getTimeZone();
        }

        if (null !== $this->getCharacterSet()) {
            $config['contao']['localconfig']['characterSet'] = $this->getCharacterSet();
        }

        if (null !== $this->getUseAutoItem()) {
            $config['contao']['localconfig']['useAutoItem'] = $this->getUseAutoItem();
        }

        if (null !== $this->getFolderUrl()) {
            $config['contao']['localconfig']['folderUrl'] = $this->getFolderUrl();
        }

        if (null !== $this->getMaxResultsPerPage()) {
            $config['contao']['localconfig']['maxResultsPerPage'] = $this->getMaxResultsPerPage();
        }

        if (null !== $this->getPrivacyAnonymizeIp()) {
            $config['contao']['localconfig']['privacyAnonymizeIp'] = $this->getPrivacyAnonymizeIp();
        }

        if (null !== $this->getPrivacyAnonymizeGA()) {
            $config['contao']['localconfig']['privacyAnonymizeGA'] = $this->getPrivacyAnonymizeGA();
        }

        if (null !== $this->getGdMaxImgWidth()) {
            $config['contao']['localconfig']['gdMaxImgWidth'] = $this->getGdMaxImgWidth();
        }

        if (null !== $this->getGdMaxImgHeight()) {
            $config['contao']['localconfig']['gdMaxImgHeight'] = $this->getGdMaxImgHeight();
        }

        if (null !== $this->getMaxFileSize()) {
            $config['contao']['localconfig']['maxFileSize'] = $this->getMaxFileSize();
        }

        if (null !== $this->getUndoPeriod()) {
            $config['contao']['localconfig']['undoPeriod'] = $this->getUndoPeriod();
        }

        if (null !== $this->getVersionPeriod()) {
            $config['contao']['localconfig']['versionPeriod'] = $this->getVersionPeriod();
        }

        if (null !== $this->getLogPeriod()) {
            $config['contao']['localconfig']['logPeriod'] = $this->getLogPeriod();
        }

        if (null !== $this->getAllowedTags()) {
            $config['contao']['localconfig']['allowedTags'] = $this->getAllowedTags();
        }

        if (null !== $this->getSgOwnerDomain()) {
            $config['contao']['localconfig']['sgOwnerDomain'] = $this->getSgOwnerDomain();
        }

        if (null !== $this->getSgOwnerHost()) {
            $config['contao']['localconfig']['sgOwnerHost'] = $this->getSgOwnerHost();
        }

        if (null !== $this->getRejectLargeUploads()) {
            $config['contao']['image']['reject_large_uploads'] = $this->getRejectLargeUploads();
        }

        if (null !== $this->getImageSizes()) {
            $config['contao']['image']['sizes'] = $this->getImageSizes();
        }

        $config['contao']['localconfig']['fileusageSkipReplaceInsertTags'] = $this->getFileusageSkipReplaceInsertTags();

        if (null !== $this->getFileusageSkipDatabase()) {
            $config['contao']['localconfig']['fileusageSkipDatabase'] = $this->getFileusageSkipDatabase();
        }

        if (0 === \count($config['contao']['localconfig'] ?? [])) {
            unset($config['contao']['localconfig']);
        }

        if (0 === \count($config['contao']['image'] ?? [])) {
            unset($config['contao']['image']);
        }

        return $config;
    }

    public function getRejectLargeUploads(): ?bool
    {
        return $this->rejectLargeUploads;
    }

    public function setRejectLargeUploads(?bool $rejectLargeUploads): self
    {
        $this->rejectLargeUploads = $rejectLargeUploads;

        return $this;
    }

    public function getDateFormat(): ?string
    {
        return $this->dateFormat;
    }

    public function setDateFormat(?string $dateFormat): self
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    public function getTimeFormat(): ?string
    {
        return $this->timeFormat;
    }

    public function setTimeFormat(?string $timeFormat): self
    {
        $this->timeFormat = $timeFormat;

        return $this;
    }

    public function getDatimFormat(): ?string
    {
        return $this->datimFormat;
    }

    public function setDatimFormat(?string $datimFormat): self
    {
        $this->datimFormat = $datimFormat;

        return $this;
    }

    public function getTimeZone(): ?string
    {
        return $this->timeZone;
    }

    public function setTimeZone(?string $timeZone): self
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    public function getCharacterSet(): ?string
    {
        return $this->characterSet;
    }

    public function setCharacterSet(?string $characterSet): self
    {
        $this->characterSet = $characterSet;

        return $this;
    }

    public function getUseAutoItem(): ?bool
    {
        return $this->useAutoItem;
    }

    public function setUseAutoItem(?bool $useAutoItem): self
    {
        $this->useAutoItem = $useAutoItem;

        return $this;
    }

    public function getFolderUrl(): ?bool
    {
        return $this->folderUrl;
    }

    public function setFolderUrl(?bool $folderUrl): self
    {
        $this->folderUrl = $folderUrl;

        return $this;
    }

    public function getMaxResultsPerPage(): ?int
    {
        return $this->maxResultsPerPage;
    }

    public function setMaxResultsPerPage(?int $maxResultsPerPage): self
    {
        $this->maxResultsPerPage = $maxResultsPerPage;

        return $this;
    }

    public function getPrivacyAnonymizeIp(): ?bool
    {
        return $this->privacyAnonymizeIp;
    }

    public function setPrivacyAnonymizeIp(?bool $privacyAnonymizeIp): self
    {
        $this->privacyAnonymizeIp = $privacyAnonymizeIp;

        return $this;
    }

    public function getPrivacyAnonymizeGA(): ?bool
    {
        return $this->privacyAnonymizeGA;
    }

    public function setPrivacyAnonymizeGA(?bool $privacyAnonymizeGA): self
    {
        $this->privacyAnonymizeGA = $privacyAnonymizeGA;

        return $this;
    }

    public function getGdMaxImgWidth(): ?int
    {
        return $this->gdMaxImgWidth;
    }

    public function setGdMaxImgWidth(?int $gdMaxImgWidth): self
    {
        $this->gdMaxImgWidth = $gdMaxImgWidth;

        return $this;
    }

    public function getGdMaxImgHeight(): ?int
    {
        return $this->gdMaxImgHeight;
    }

    public function setGdMaxImgHeight(?int $gdMaxImgHeight): self
    {
        $this->gdMaxImgHeight = $gdMaxImgHeight;

        return $this;
    }

    public function getMaxFileSize(): ?int
    {
        return $this->maxFileSize;
    }

    public function setMaxFileSize(?int $maxFileSize): self
    {
        $this->maxFileSize = $maxFileSize;

        return $this;
    }

    public function getUndoPeriod(): ?int
    {
        return $this->undoPeriod;
    }

    public function setUndoPeriod(?int $undoPeriod): self
    {
        $this->undoPeriod = $undoPeriod;

        return $this;
    }

    public function getVersionPeriod(): ?int
    {
        return $this->versionPeriod;
    }

    public function setVersionPeriod(?int $versionPeriod): self
    {
        $this->versionPeriod = $versionPeriod;

        return $this;
    }

    public function getLogPeriod(): ?int
    {
        return $this->logPeriod;
    }

    public function setLogPeriod(?int $logPeriod): self
    {
        $this->logPeriod = $logPeriod;

        return $this;
    }

    public function getAllowedTags(): ?string
    {
        return $this->allowedTags;
    }

    public function setAllowedTags(?string $allowedTags): self
    {
        $this->allowedTags = $allowedTags;

        return $this;
    }

    public function getSgOwnerDomain(): ?string
    {
        return $this->sgOwnerDomain;
    }

    public function setSgOwnerDomain(?string $sgOwnerDomain): self
    {
        $this->sgOwnerDomain = $sgOwnerDomain;

        return $this;
    }

    public function getSgOwnerHost(): ?string
    {
        return $this->sgOwnerHost;
    }

    public function setSgOwnerHost(?string $sgOwnerHost): self
    {
        $this->sgOwnerHost = $sgOwnerHost;

        return $this;
    }

    public function getUntouchedConfig(): ?array
    {
        return $this->untouchedConfig;
    }

    public function setUntouchedConfig(?array $untouchedConfig): self
    {
        $this->untouchedConfig = $untouchedConfig;

        return $this;
    }

    public function getImageSizes(): ?array
    {
        return $this->imageSizes;
    }

    public function setImageSizes(?array $imageSizes): self
    {
        $this->imageSizes = $imageSizes;

        return $this;
    }

    public function getFileusageSkipReplaceInsertTags(): bool
    {
        return $this->fileusageSkipReplaceInsertTags;
    }

    public function setFileusageSkipReplaceInsertTags(bool $fileusageSkipReplaceInsertTags): self
    {
        $this->fileusageSkipReplaceInsertTags = $fileusageSkipReplaceInsertTags;

        return $this;
    }

    public function getFileusageSkipDatabase(): ?bool
    {
        return $this->fileusageSkipDatabase;
    }

    public function setFileusageSkipDatabase(?bool $fileusageSkipDatabase): self
    {
        $this->fileusageSkipDatabase = $fileusageSkipDatabase;

        return $this;
    }
}
