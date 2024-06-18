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

use WEM\SmartgearBundle\Classes\Config\ConfigEnvInterface;

class EnvFile implements ConfigEnvInterface
{
    public const MODE_DEV = 'dev';

    public const MODE_PROD = 'prod';

    public const MODES_ALLOWED = [
        self::MODE_DEV,
        self::MODE_PROD,
    ];

    public const DEFAULT_MODE = self::MODE_DEV;

    protected string $APP_ENV = self::DEFAULT_MODE;

    protected ?string $APP_SECRET = null;

    protected ?string $COOKIE_WHITELIST = null;

    protected ?string $COOKIE_REMOVE_FROM_DENY_LIST = null;

    protected ?string $QUERY_PARAMS_ALLOW_LIST = null;

    protected ?string $QUERY_PARAMS_REMOVE_FROM_DENY_LIST = null;

    protected ?string $DATABASE_URL = null;

    protected ?string $MAILER_URL = null;

    protected ?string $TRUSTED_PROXIES = null;

    protected ?string $TRUSTED_HOSTS = null;

    public function reset(): self
    {
        $this->setAPPENV(self::DEFAULT_MODE)
            ->setAPPSECRET(null)
            ->setCOOKIEWHITELIST(null)
            ->setCOOKIEREMOVEFROMDENYLIST(null)
            ->setQUERYPARAMSALLOWLIST(null)
            ->setQUERYPARAMSREMOVEFROMDENYLIST(null)
            ->setDATABASEURL(null)
            ->setMAILERURL(null)
            ->setTRUSTEDPROXIES(null)
            ->setTRUSTEDHOSTS(null)
        ;

        return $this;
    }

    public function import(array $content): self
    {
        $this->setAPPENV($content['APP_ENV'] ?? self::DEFAULT_MODE)
            ->setAPPSECRET($content['APP_SECRET'] ?? null)
            ->setCOOKIEWHITELIST($content['COOKIE_WHITELIST'] ?? null)
            ->setCOOKIEREMOVEFROMDENYLIST($content['COOKIE_REMOVE_FROM_DENY_LIST'] ?? null)
            ->setQUERYPARAMSALLOWLIST($content['QUERY_PARAMS_ALLOW_LIST'] ?? null)
            ->setQUERYPARAMSREMOVEFROMDENYLIST($content['QUERY_PARAMS_REMOVE_FROM_DENY_LIST'] ?? null)
            ->setDATABASEURL($content['DATABASE_URL'] ?? null)
            ->setMAILERURL($content['MAILER_URL'] ?? null)
            ->setTRUSTEDPROXIES($content['TRUSTED_PROXIES'] ?? null)
            ->setTRUSTEDHOSTS($content['TRUSTED_HOSTS'] ?? null)
        ;

        return $this;
    }

    public function export(): string
    {
        $str = 'APP_ENV=' . $this->getAPPENV() . "\n";
        if (null !== $this->getAPPSECRET()) {
            $str .= 'APP_SECRET='.$this->getAPPSECRET()."\n";
        }

        if (null !== $this->getCOOKIEWHITELIST()) {
            $str .= 'COOKIE_WHITELIST='.$this->getCOOKIEWHITELIST()."\n";
        }

        if (null !== $this->getCOOKIEREMOVEFROMDENYLIST()) {
            $str .= 'COOKIE_REMOVE_FROM_DENY_LIST='.$this->getCOOKIEREMOVEFROMDENYLIST()."\n";
        }

        if (null !== $this->getQUERYPARAMSALLOWLIST()) {
            $str .= 'QUERY_PARAMS_ALLOW_LIST='.$this->getQUERYPARAMSALLOWLIST()."\n";
        }

        if (null !== $this->getQUERYPARAMSREMOVEFROMDENYLIST()) {
            $str .= 'QUERY_PARAMS_REMOVE_FROM_DENY_LIST='.$this->getQUERYPARAMSREMOVEFROMDENYLIST()."\n";
        }

        if (null !== $this->getDATABASEURL()) {
            $str .= 'DATABASE_URL='.$this->getDATABASEURL()."\n";
        }

        if (null !== $this->getMAILERURL()) {
            $str .= 'MAILER_URL='.$this->getMAILERURL()."\n";
        }

        if (null !== $this->getTRUSTEDPROXIES()) {
            $str .= 'TRUSTED_PROXIES='.$this->getTRUSTEDPROXIES()."\n";
        }

        if (null !== $this->getTRUSTEDHOSTS()) {
            $str .= 'TRUSTED_HOSTS='.$this->getTRUSTEDHOSTS()."\n";
        }

        return $str;
    }

    public function getTRUSTEDHOSTS(): ?string
    {
        return $this->TRUSTED_HOSTS;
    }

    public function setTRUSTEDHOSTS(?string $TRUSTED_HOSTS): self
    {
        $this->TRUSTED_HOSTS = $TRUSTED_HOSTS;

        return $this;
    }

    public function getAPPENV(): ?string
    {
        return $this->APP_ENV;
    }

    public function setAPPENV(?string $APP_ENV): self
    {
        $this->APP_ENV = $APP_ENV;

        return $this;
    }

    public function getAPPSECRET(): ?string
    {
        return $this->APP_SECRET;
    }

    public function setAPPSECRET(?string $APP_SECRET): self
    {
        $this->APP_SECRET = $APP_SECRET;

        return $this;
    }

    public function getCOOKIEWHITELIST(): ?string
    {
        return $this->COOKIE_WHITELIST;
    }

    public function setCOOKIEWHITELIST(?string $COOKIE_WHITELIST): self
    {
        $this->COOKIE_WHITELIST = $COOKIE_WHITELIST;

        return $this;
    }

    public function getCOOKIEREMOVEFROMDENYLIST(): ?string
    {
        return $this->COOKIE_REMOVE_FROM_DENY_LIST;
    }

    public function setCOOKIEREMOVEFROMDENYLIST(?string $COOKIE_REMOVE_FROM_DENY_LIST): self
    {
        $this->COOKIE_REMOVE_FROM_DENY_LIST = $COOKIE_REMOVE_FROM_DENY_LIST;

        return $this;
    }

    public function getQUERYPARAMSALLOWLIST(): ?string
    {
        return $this->QUERY_PARAMS_ALLOW_LIST;
    }

    public function setQUERYPARAMSALLOWLIST(?string $QUERY_PARAMS_ALLOW_LIST): self
    {
        $this->QUERY_PARAMS_ALLOW_LIST = $QUERY_PARAMS_ALLOW_LIST;

        return $this;
    }

    public function getQUERYPARAMSREMOVEFROMDENYLIST(): ?string
    {
        return $this->QUERY_PARAMS_REMOVE_FROM_DENY_LIST;
    }

    public function setQUERYPARAMSREMOVEFROMDENYLIST(?string $QUERY_PARAMS_REMOVE_FROM_DENY_LIST): self
    {
        $this->QUERY_PARAMS_REMOVE_FROM_DENY_LIST = $QUERY_PARAMS_REMOVE_FROM_DENY_LIST;

        return $this;
    }

    public function getDATABASEURL(): ?string
    {
        return $this->DATABASE_URL;
    }

    public function setDATABASEURL(?string $DATABASE_URL): self
    {
        $this->DATABASE_URL = $DATABASE_URL;

        return $this;
    }

    public function getMAILERURL(): ?string
    {
        return $this->MAILER_URL;
    }

    public function setMAILERURL(?string $MAILER_URL): self
    {
        $this->MAILER_URL = $MAILER_URL;

        return $this;
    }

    public function getTRUSTEDPROXIES(): ?string
    {
        return $this->TRUSTED_PROXIES;
    }

    public function setTRUSTEDPROXIES(?string $TRUSTED_PROXIES): self
    {
        $this->TRUSTED_PROXIES = $TRUSTED_PROXIES;

        return $this;
    }
}
