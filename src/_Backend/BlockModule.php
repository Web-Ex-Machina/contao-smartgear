<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2021 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Backend;

use Exception;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class BlockModule extends Block
{
    /**
     * Construct the block object.
     */
    public function __construct()
    {
        parent::construct();
    }

    /**
     * Reset the module.
     */
    public function reset()
    {
        $this->remove();
        $this->install();

        // And return an explicit status with some instructions
        return [
            'toastr' => [
                'status' => 'success', 'msg' => 'Réinitialisation effectuée avec succès.',
            ], 'callbacks' => [
                0 => [
                    'method' => 'refreshBlock', 'args' => ['block-'.$this->type.'-'.$this->module],
                ],
            ],
        ];
    }

    /**
     * Get generic callbacks for requests.
     *
     * @param [String] $key  [Key of the callbacks array]
     * @param [Array]  $args [Optional array of arguments]
     *
     * @return [Array] [Callback array]
     */
    public function callback($key, $args = null)
    {
        try {
            switch ($key) {
                case 'refreshBlock':
                    return ['method' => 'refreshBlock', 'args' => ['block-'.$this->type.'-'.$this->module]];
                break;

                default:
                    return parent::callback($key, $args);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}
