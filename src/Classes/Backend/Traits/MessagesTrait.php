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
     * Add an info.
     */
    protected function addInfo(string $msg): void
    {
        $this->messages[] = [
            'class' => 'tl_info',
            'text' => $msg,
        ];
    }

    /**
     * Add an confirm.
     */
    protected function addConfirm(string $msg): void
    {
        $this->messages[] = [
            'class' => 'tl_confirm',
            'text' => $msg,
        ];
    }

    /**
     * Add an new.
     */
    protected function addNew(string $msg): void
    {
        $this->messages[] = [
            'class' => 'tl_new',
            'text' => $msg,
        ];
    }

    /**
     * Add a message.
     */
    // protected function addMessage(string $scope, string $msg): void
    protected function addMessage($strMessage, $strType): void // needs to be the same as \Contao\System::addMessage, otherwise a warning is thrown "because"
    {
        switch ($strType) {
            case 'tl_new':
                $this->addNew($strMessage);
            break;
            case 'tl_confirm':
                $this->addConfirm($strMessage);
            break;
            case 'tl_info':
                $this->addInfo($strMessage);
            break;
            case 'tl_error':
                $this->addError($strMessage);
            break;
        }
    }

    /**
     * Add a message.
     */
    protected function addMessages(array $messages): void
    {
        foreach ($messages as $scope => $message) {
            $this->addMessage($scope, $message);
        }
    }
}
