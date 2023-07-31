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

namespace WEM\SmartgearBundle\Classes\Migration;

use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Version\Version;

abstract class MigrationAbstract implements MigrationInterface
{
    /** @var TranslatorInterface */
    protected $translator;
    protected $name;
    protected $description;
    protected $version;
    protected $translation_key = '';

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTranslatedName(): string
    {
        return $this->translator->trans($this->buildTranslationKey('name'), [], 'contao_default');
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTranslatedDescription(): string
    {
        return $this->translator->trans($this->buildTranslationKey('description'), [], 'contao_default');
    }

    public function getVersion(): Version
    {
        return (new Version())->fromString($this->version);
    }

    public function getTranslationKey(): string
    {
        return $this->translation_key;
    }

    abstract public function shouldRun(): Result;

    abstract public function do(): Result;

    /**
     * This methods build the translation key using the current class' ones, taking inheritance in account
     * No need to duplicate this method to make it work.
     *
     * @param string $property The property to translate
     *
     * @return string The built translation key
     */
    protected function buildTranslationKey(string $property): string
    {
        return $this->getTranslationKey().'.'.$property;
    }

    /**
     * This methods build the translation key using the current class' ones, without taking inheritance in account
     * To make it work, duplicate this method in the desired class.
     *
     * @param string $property The property to translate
     *
     * @return string The built translation key
     */
    protected function buildTranslationKeyLocal(string $property): string
    {
        return $this->translation_key.'.'.$property;
    }
}
