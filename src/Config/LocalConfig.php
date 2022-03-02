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
    protected $dateFormat;
    protected $timeFormat;
    protected $datimFormat;
    protected $timeZone;
    protected $characterSet;
    protected $useAutoItem;
    protected $folderUrl;
    protected $maxResultsPerPage;
    protected $privacyAnonymizeIp;
    protected $privacyAnonymizeGA;
    protected $gdMaxImgWidth;
    protected $gdMaxImgHeight;
    protected $maxFileSize;
    protected $undoPeriod;
    protected $versionPeriod;
    protected $logPeriod;
    protected $allowedTags;
    protected $sgOwnerDomain;
    protected $sgOwnerHost;
    protected $rejectLargeUploads;
    protected $untouchedConfig;
    protected $imageSizes;

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
        ;

        unset($content['contao']['localconfig']['dateFormat'], $content['contao']['localconfig']['timeFormat'], $content['contao']['localconfig']['datimFormat'], $content['contao']['localconfig']['timeZone'], $content['contao']['localconfig']['characterSet'], $content['contao']['localconfig']['useAutoItem'], $content['contao']['localconfig']['folderUrl'], $content['contao']['localconfig']['maxResultsPerPage'], $content['contao']['localconfig']['privacyAnonymizeIp'], $content['contao']['localconfig']['privacyAnonymizeGA'], $content['contao']['localconfig']['gdMaxImgWidth'], $content['contao']['localconfig']['gdMaxImgHeight'], $content['contao']['localconfig']['maxFileSize'], $content['contao']['localconfig']['undoPeriod'], $content['contao']['localconfig']['versionPeriod'], $content['contao']['localconfig']['logPeriod'], $content['contao']['localconfig']['allowedTags'], $content['contao']['localconfig']['sgOwnerDomain'], $content['contao']['localconfig']['sgOwnerHost'], $content['contao']['image']['reject_large_uploads'], $content['contao']['image']['sizes']);

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

        if (0 === \count($config['contao']['localconfig'])) {
            unset($config['contao']['localconfig']);
        }

        if (0 === \count($config['contao']['image'])) {
            unset($config['contao']['image']);
        }

        return $config;
    }

    public function getRejectLargeUploads()
    {
        return $this->rejectLargeUploads;
    }

    public function setRejectLargeUploads($rejectLargeUploads): self
    {
        $this->rejectLargeUploads = $rejectLargeUploads;

        return $this;
    }

    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    public function setDateFormat($dateFormat): self
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    public function getTimeFormat()
    {
        return $this->timeFormat;
    }

    public function setTimeFormat($timeFormat): self
    {
        $this->timeFormat = $timeFormat;

        return $this;
    }

    public function getDatimFormat()
    {
        return $this->datimFormat;
    }

    public function setDatimFormat($datimFormat): self
    {
        $this->datimFormat = $datimFormat;

        return $this;
    }

    public function getTimeZone()
    {
        return $this->timeZone;
    }

    public function setTimeZone($timeZone): self
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    public function getCharacterSet()
    {
        return $this->characterSet;
    }

    public function setCharacterSet($characterSet): self
    {
        $this->characterSet = $characterSet;

        return $this;
    }

    public function getUseAutoItem()
    {
        return $this->useAutoItem;
    }

    public function setUseAutoItem($useAutoItem): self
    {
        $this->useAutoItem = $useAutoItem;

        return $this;
    }

    public function getFolderUrl()
    {
        return $this->folderUrl;
    }

    public function setFolderUrl($folderUrl): self
    {
        $this->folderUrl = $folderUrl;

        return $this;
    }

    public function getMaxResultsPerPage()
    {
        return $this->maxResultsPerPage;
    }

    public function setMaxResultsPerPage($maxResultsPerPage): self
    {
        $this->maxResultsPerPage = $maxResultsPerPage;

        return $this;
    }

    public function getPrivacyAnonymizeIp()
    {
        return $this->privacyAnonymizeIp;
    }

    public function setPrivacyAnonymizeIp($privacyAnonymizeIp): self
    {
        $this->privacyAnonymizeIp = $privacyAnonymizeIp;

        return $this;
    }

    public function getPrivacyAnonymizeGA()
    {
        return $this->privacyAnonymizeGA;
    }

    public function setPrivacyAnonymizeGA($privacyAnonymizeGA): self
    {
        $this->privacyAnonymizeGA = $privacyAnonymizeGA;

        return $this;
    }

    public function getGdMaxImgWidth()
    {
        return $this->gdMaxImgWidth;
    }

    public function setGdMaxImgWidth($gdMaxImgWidth): self
    {
        $this->gdMaxImgWidth = $gdMaxImgWidth;

        return $this;
    }

    public function getGdMaxImgHeight()
    {
        return $this->gdMaxImgHeight;
    }

    public function setGdMaxImgHeight($gdMaxImgHeight): self
    {
        $this->gdMaxImgHeight = $gdMaxImgHeight;

        return $this;
    }

    public function getMaxFileSize()
    {
        return $this->maxFileSize;
    }

    public function setMaxFileSize($maxFileSize): self
    {
        $this->maxFileSize = $maxFileSize;

        return $this;
    }

    public function getUndoPeriod()
    {
        return $this->undoPeriod;
    }

    public function setUndoPeriod($undoPeriod): self
    {
        $this->undoPeriod = $undoPeriod;

        return $this;
    }

    public function getVersionPeriod()
    {
        return $this->versionPeriod;
    }

    public function setVersionPeriod($versionPeriod): self
    {
        $this->versionPeriod = $versionPeriod;

        return $this;
    }

    public function getLogPeriod()
    {
        return $this->logPeriod;
    }

    public function setLogPeriod($logPeriod): self
    {
        $this->logPeriod = $logPeriod;

        return $this;
    }

    public function getAllowedTags()
    {
        return $this->allowedTags;
    }

    public function setAllowedTags($allowedTags): self
    {
        $this->allowedTags = $allowedTags;

        return $this;
    }

    public function getSgOwnerDomain()
    {
        return $this->sgOwnerDomain;
    }

    public function setSgOwnerDomain($sgOwnerDomain): self
    {
        $this->sgOwnerDomain = $sgOwnerDomain;

        return $this;
    }

    public function getSgOwnerHost()
    {
        return $this->sgOwnerHost;
    }

    public function setSgOwnerHost($sgOwnerHost): self
    {
        $this->sgOwnerHost = $sgOwnerHost;

        return $this;
    }

    public function getUntouchedConfig()
    {
        return $this->untouchedConfig;
    }

    public function setUntouchedConfig($untouchedConfig): self
    {
        $this->untouchedConfig = $untouchedConfig;

        return $this;
    }

    public function getImageSizes()
    {
        return $this->imageSizes;
    }

    public function setImageSizes($imageSizes): self
    {
        $this->imageSizes = $imageSizes;

        return $this;
    }
}
