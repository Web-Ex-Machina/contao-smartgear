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

namespace WEM\SmartgearBundle\Classes\Backend\Traits;

trait MessagesTrait
{
    /** @var array */
    protected $messages = [];

    /**
     * Reset the errors array.
     *
     * @param mixed|null $strScope
     */
    public function getMessages($strScope = null)
    {
        return $this->messages;
    }

    /**
     * Reset the errors array.
     */
    protected function resetMessages(): void
    {
        $this->messages = [];
    }

    /**
     * Return true if there is errors in this block.
     */
    protected function hasErrors(): bool
    {
        if (!empty($this->messages)) {
            foreach ($this->messages as $m) {
                if ('tl_error' === $m['class']) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Return true if there is updates in this block.
     */
    protected function hasUpdates(): bool
    {
        if (!empty($this->messages)) {
            foreach ($this->messages as $m) {
                if ('tl_new' === $m['class']) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Add an error.
     */
    protected function addError(string $msg): void
    {
        $this->messages[] = [
            'class' => 'tl_error',
            'text' => $msg,
        ];
    }

    /**
     * Add an error.
     */
    protected function addInfo(string $msg): void
    {
        $this->messages[] = [
            'class' => 'tl_info',
            'text' => $msg,
        ];
    }

    /**
     * Add an error.
     */
    protected function addConfirm(string $msg): void
    {
        $this->messages[] = [
            'class' => 'tl_confirm',
            'text' => $msg,
        ];
    }

    /**
     * Add an error.
     */
    protected function addNew(string $msg): void
    {
        $this->messages[] = [
            'class' => 'tl_new',
            'text' => $msg,
        ];
    }
}
