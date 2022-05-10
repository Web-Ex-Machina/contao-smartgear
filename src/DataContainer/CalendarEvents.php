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

namespace WEM\SmartgearBundle\DataContainer;

use Contao\DataContainer;

class CalendarEvents extends \tl_calendar_events
{
    /**
     * Add the source options depending on the allowed fields (see #5498).
     *
     * @return array
     */
    public function getSourceOptions(DataContainer $dc)
    {
        $arrOptions = parent::getSourceOptions($dc);
        $valuesToKeep = ['default', 'external'];
        foreach ($arrOptions as $index => $value) {
            if (!\in_array($value, $valuesToKeep, true)) {
                unset($arrOptions[$index]);
            }
        }

        return $arrOptions;
    }

    public function fillCoordinates(DataContainer $dc): void
    {
        // Return if there is no active record (override all)
        if (!$dc->activeRecord
        || empty($dc->activeRecord->address)
        ) {
            return;
        }

        $arrSet['addressLat'] = $dc->activeRecord->addressLat;
        $arrSet['addressLon'] = $dc->activeRecord->addressLon;

        // if coordinates are already calculated, skip
        if (!empty($arrSet['addressLat']) && !empty($arrSet['addressLon'])) {
            return;
        }

        // check if there are other events with same address and filled coordinates
        $otherItemsWithSameAddressAndCoordinatesFilled = \Contao\CalendarEventsModel::findBy(['address = ?', 'addressLat != ""', 'addressLon != ""'], $dc->activeRecord->address);
        // If those events exist, use their coordinates instead of calling the API
        if ($otherItemsWithSameAddressAndCoordinatesFilled) {
            while ($otherItemsWithSameAddressAndCoordinatesFilled->next()) {
                $arrSet['addressLat'] = $otherItemsWithSameAddressAndCoordinatesFilled->addressLat;
                $arrSet['addressLon'] = $otherItemsWithSameAddressAndCoordinatesFilled->addressLon;
                continue;
            }
        } else {
            /** @var \WEM\SmartgearBundle\Api\Nominatim\V4\Api */
            $api = \Contao\System::getContainer()->get('smartgear.api.nominatim.v4.api');
            try {
                $response = $api->search($dc->activeRecord->address);
                $arrSet['addressLat'] = $response->getLat() ?? '';
                $arrSet['addressLon'] = $response->getLon() ?? '';
            } catch (\Exception $e) {
                return;
            }
        }

        $this->Database->prepare('UPDATE tl_calendar_events %s WHERE id=?')->set($arrSet)->execute($dc->id);
    }
}
