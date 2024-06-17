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

use Contao\System;

trait MessagesTrait
{

    protected array $messages = [];

    /**
     * Reset the errors array.
     *
     * @param mixed|null $strScope
     */
    // public function getMessages(?string $scope = 'smartgear'): array
    public function getMessages(mixed $strScope = null): array // needs to be the same as \Contao\System::getMessages, otherwise a warning is thrown "because"
    {
        $scope = $strScope ?? 'smartgear';
        $sfb = System::getContainer()->get('session')->getFlashBag();

        // return array_merge(($sfb->get($this->getFlashBagKey('tl_new', $scope)) ?? []), ($sfb->get($this->getFlashBagKey('tl_info', $scope)) ?? []), ($sfb->get($this->getFlashBagKey('tl_error', $scope)) ?? []), ($sfb->get($this->getFlashBagKey('tl_confirm', $scope)) ?? []));
        return $sfb->get($this->getFlashBagKeyWithoutType($scope));
    }

    /**
     * Return the flash bag key.
     *
     * @param string $strType The message type
     *
     * @return string The flash bag key
     */
    public function getFlashBagKey(string $strType, ?string $scope = 'smartgear'): string
    {
        return $this->getFlashBagKeyWithoutType($scope).strtolower(str_replace('tl_', '', $strType));
    }

    /**
     * Return the flash bag key without type.
     *
     * @return string The flash bag key
     */
    public function getFlashBagKeyWithoutType(?string $scope = 'smartgear'): string
    {
        return 'wemsg.message.'.strtolower((string) $scope).'.'; // do not remove this end dot
    }

    /**
     * Reset the messages array.
     */
    protected function resetMessages(?string $scope = 'smartgear'): void
    {
        $this->messages[$scope] = [];

        $session = System::getContainer()->get('session');

        if (!$session->isStarted()) {
            return;
        }

        $flashBag = $session->getFlashBag();

        // Find all wemsg.message.$scope. keys
        $keys = preg_grep('(^wemsg\.message\.'.$scope.'\.)', $flashBag->keys());

        foreach ($keys as $key) {
            $flashBag->get($key); // clears the message
        }
    }

    /**
     * Return true if there is errors in this block.
     */
    protected function hasErrors(?string $scope = 'smartgear'): bool
    {
        if (!empty($this->messages) && \array_key_exists($scope, $this->messages)) {
            foreach ($this->messages[$scope] as $m) {
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
    protected function hasUpdates(?string $scope = 'smartgear'): bool
    {
        if (!empty($this->messages) && \array_key_exists($scope, $this->messages)) {
            foreach ($this->messages[$scope] as $m) {
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
    protected function addError(string $msg, ?string $scope = 'smartgear'): void
    {
        $this->addMessage($msg, 'tl_error', $scope);
    }

    /**
     * Add an info.
     */
    protected function addInfo(string $msg, ?string $scope = 'smartgear'): void
    {
        $this->addMessage($msg, 'tl_info', $scope);
    }

    /**
     * Add an confirm.
     */
    protected function addConfirm(string $msg, ?string $scope = 'smartgear'): void
    {
        $this->addMessage($msg, 'tl_confirm', $scope);
    }

    /**
     * Add an new.
     */
    protected function addNew(string $msg, ?string $scope = 'smartgear'): void
    {
        $this->addMessage($msg, 'tl_new', $scope);
    }

    /**
     * Add a message.
     */
    // protected function addMessage(string $scope, string $msg): void
    protected function addMessage($strMessage, $strType, ?string $scope = 'smartgear'): void // needs to be the same as \Contao\System::addMessage, otherwise a warning is thrown "because"
    {
        $message = [
            'class' => $strType,
            'text' => $strMessage,
        ];
        $this->messages[$scope][] = $message;
        System::getContainer()->get('session')->getFlashBag()->add($this->getFlashBagKey($strType, $scope), $message);
        System::getContainer()->get('session')->getFlashBag()->add($this->getFlashBagKeyWithoutType($scope), $message);
    }

    /**
     * Add a message.
     */
    protected function addMessages(array $messages, ?string $scope = 'smartgear'): void
    {
        foreach ($messages as $message) {
            $this->addMessage($message['text'], $message['class'], $scope);
        }
    }
}
