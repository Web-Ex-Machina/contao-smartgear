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

namespace WEM\SmartgearBundle\Backend\Component\Events\ConfigurationStep;

use Contao\Input;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Component\Events\Events as EventsConfig;

class Dataset extends ConfigurationStep
{
    /** @var TranslatorInterface */
    protected $translator;
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var CommandUtil */
    protected $commandUtil;
    /** @var string */
    protected $sourceDirectory;

    private $locations = [
        'Super Bazar' => [
            'location' => 'Super Bazar',
            'address' => 'Super Bazar, NH52, Murarji Peth, Solapur, Solapur North, Solapur, Maharashtra, 413001, Inde',
            'lat' => '17.6743412',
            'lon' => '75.89812057062778',
        ],
        'Pulperia Heniz' => [
            'location' => 'Pulperia Heniz',
            'address' => 'Minas de plata, V-384, San Jose del Potrero, Honduras',
            'lat' => '14.8421963',
            'lon' => '-87.3334089',
        ],
        'Chateau Puyferrat' => [
            'location' => 'Chateau Puyferrat',
            'address' => 'chateau puyferrat, 24110 Saint-Astier',
            'lat' => '45.1488475',
            'lon' => '0.506535272182014',
        ],
        'Sanctuaire Shinto' => [
            'location' => 'Sanctuaire Shinto',
            'address' => 'Kutsukiarakawa, Takashima, Shiga 520-1411, Japon',
            'lat' => '35.3680012',
            'lon' => '135.9409092',
        ],
    ];

    // protected $strTemplate = 'be_wem_sg_install_block_configuration_step_events_general';

    public function __construct(
        string $module,
        string $type,
        TranslatorInterface $translator,
        ConfigurationManager $configurationManager,
        CommandUtil $commandUtil,
        string $sourceDirectory
    ) {
        parent::__construct($module, $type);
        $this->configurationManager = $configurationManager;
        $this->commandUtil = $commandUtil;
        $this->translator = $translator;
        $this->sourceDirectory = str_replace('[public_or_web]', Util::getPublicOrWebDirectory(true), $sourceDirectory);

        $this->title = $this->translator->trans('WEMSG.EVENTS.INSTALL_DATASET.title', [], 'contao_default');
        /** @var EventsConfig */
        $config = $this->configurationManager->load()->getSgEvents();

        $datasetOptions = [];

        $datasetOptions[] = ['value' => 'none', 'label' => $this->translator->trans('WEMSG.EVENTS.INSTALL_DATASET.datasetOptionNone', [], 'contao_default')];
        $datasetOptions[] = ['value' => 'A', 'label' => $this->translator->trans('WEMSG.EVENTS.INSTALL_DATASET.datasetOptionA', [], 'contao_default')];
        $datasetOptions[] = ['value' => 'B', 'label' => $this->translator->trans('WEMSG.EVENTS.INSTALL_DATASET.datasetOptionB', [], 'contao_default')];

        $this->addSelectField('dataset', $this->translator->trans('WEMSG.EVENTS.INSTALL_DATASET.dataset', [], 'contao_default'), $datasetOptions, 'none', true, false, '', 'select', $this->translator->trans('WEMSG.EVENTS.INSTALL_DATASET.datasetHelp', [], 'contao_default'));
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (null === Input::post('dataset', null)) {
            throw new Exception($this->translator->trans('WEMSG.EVENTS.INSTALL_DATASET.datasetMissing', [], 'contao_default'));
        }

        return true;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
        $this->cleanDatasets();
        $this->installDataset(Input::post('dataset', null));
        $this->commandUtil->executeCmdPHP('cache:clear');
    }

    protected function installDataset(string $dataset): void
    {
        switch ($dataset) {
            case 'none':
                // nothing
            break;
            case 'A':
                $this->installDatasetA();
            break;
            case 'B':
                $this->installDatasetB();
            break;
        }
    }

    protected function installDatasetA(): void
    {
        $eventsConfig = $this->configurationManager->load()->getSgEvents();
        $filesDirectory = $eventsConfig->getSgEventsFolder();
        $calendarId = $eventsConfig->getSgCalendar();
        $fileNamesToCopy = ['fileA.png', 'fileB.png', 'fileC.png'];

        $this->copyFiles($fileNamesToCopy);

        $this->createOrUpdateCalendarEvent($calendarId, 'Event Test A', 'event-test-a', 1, strtotime('-1 year'), strtotime('-1 year'), $this->getLoremIpsum(140), 'Pulperia Heniz', $filesDirectory.\DIRECTORY_SEPARATOR.'fileA.png', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Event Test B', 'event-test-b', 1, strtotime('-1 week'), strtotime('-1 week'), $this->getLoremIpsum(240), 'Super Bazar', $filesDirectory.\DIRECTORY_SEPARATOR.'fileB.png', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité C', 'évènement-c', 1, strtotime('-1 day'), strtotime('-1 day'), $this->getLoremIpsum(80), 'Chateau Puyferrat', $filesDirectory.\DIRECTORY_SEPARATOR.'fileC.png', true);
    }

    protected function installDatasetB(): void
    {
        $eventsConfig = $this->configurationManager->load()->getSgEvents();
        $filesDirectory = $eventsConfig->getSgEventsFolder();
        $calendarId = $eventsConfig->getSgCalendar();
        $fileNamesToCopy = ['fileA.png', 'fileB.png', 'fileC.png', 'fileD.png', 'fileE.png', 'fileF.png', 'fileG.png', 'fileH.png', 'fileI.png', 'fileJ.png', 'fileK.png', 'fileL.png', 'fileM.png', 'fileN.png'];

        $this->copyFiles($fileNamesToCopy);

        $this->createOrUpdateCalendarEvent($calendarId, 'Event Test A', 'event-test-a', 1, strtotime('-1 year'), strtotime('-1 year'), $this->getLoremIpsum(140), 'Pulperia Heniz', $filesDirectory.\DIRECTORY_SEPARATOR.'fileA.png', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Event Test B', 'event-test-b', 1, strtotime('-340 days'), strtotime('-340 days'), $this->getLoremIpsum(240), 'Super Bazar', $filesDirectory.\DIRECTORY_SEPARATOR.'fileB.png', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité C', 'évènement-c', 1, strtotime('-300 days'), strtotime('-300 days'), $this->getLoremIpsum(80), 'Chateau Puyferrat', $filesDirectory.\DIRECTORY_SEPARATOR.'fileC.png', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité D', 'évènement-d', 1, strtotime('-300 days'), strtotime('-300 days +2 hours'), $this->getLoremIpsum(160), 'Sanctuaire Shinto', '', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité E', 'évènement-e', 1, strtotime('-280 days'), strtotime('-280 days'), $this->getLoremIpsum(380), 'Pulperia Heniz', $filesDirectory.\DIRECTORY_SEPARATOR.'fileD.png', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité F', 'évènement-f', 1, strtotime('-270 days'), strtotime('-270 days'), $this->getLoremIpsum(120), 'Chateau Puyferrat', '', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité G', 'évènement-g', 1, strtotime('-240 days'), strtotime('-240 days'), $this->getLoremIpsum(80), 'Chateau Puyferrat', '', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité H', 'évènement-h', 1, strtotime('-180 days'), strtotime('-180 days'), $this->getLoremIpsum(160), 'Chateau Puyferrat', $filesDirectory.\DIRECTORY_SEPARATOR.'fileE.png', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité I', 'évènement-i', 1, strtotime('-160 days'), strtotime('-160 days'), $this->getLoremIpsum(340), 'Sanctuaire Shinto', $filesDirectory.\DIRECTORY_SEPARATOR.'fileF.png', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité J', 'évènement-j', 1, strtotime('-140 days'), strtotime('-140 days'), $this->getLoremIpsum(80), 'Pulperia Heniz', $filesDirectory.\DIRECTORY_SEPARATOR.'fileG.png', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité K', 'évènement-k', 1, strtotime('-135 days'), strtotime('-135 days'), $this->getLoremIpsum(160), 'Super Bazar', $filesDirectory.\DIRECTORY_SEPARATOR.'fileH.png', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité L', 'évènement-l', 1, strtotime('-120 days'), strtotime('-120 days'), $this->getLoremIpsum(360), 'Sanctuaire Shinto', $filesDirectory.\DIRECTORY_SEPARATOR.'fileI.png', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité M', 'évènement-m', 1, strtotime('-90 days'), strtotime('-90 days'), $this->getLoremIpsum(80), 'Super Bazar', $filesDirectory.\DIRECTORY_SEPARATOR.'fileJ.png', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité N', 'évènement-n', 1, strtotime('-85 days'), strtotime('-85 days'), $this->getLoremIpsum(160), 'Pulperia Heniz', $filesDirectory.\DIRECTORY_SEPARATOR.'fileK.png', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité O', 'évènement-o', 1, strtotime('-70 days'), strtotime('-70 days'), $this->getLoremIpsum(600), 'Pulperia Heniz', $filesDirectory.\DIRECTORY_SEPARATOR.'fileL.png', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité P', 'évènement-p', 1, strtotime('-60 days'), strtotime('-60 days'), $this->getLoremIpsum(320), 'Chateau Puyferrat', $filesDirectory.\DIRECTORY_SEPARATOR.'fileM.png', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité Q', 'évènement-q', 1, strtotime('-30 days'), strtotime('-30 days'), $this->getLoremIpsum(80), 'Chateau Puyferrat', '', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité R', 'évènement-r', 1, strtotime('-15 days'), strtotime('-15 days'), $this->getLoremIpsum(120), 'Sanctuaire Shinto', $filesDirectory.\DIRECTORY_SEPARATOR.'fileN.png', true);
    }

    protected function cleanDatasets(): void
    {
        $eventsConfig = $this->configurationManager->load()->getSgEvents();
        $directory = $eventsConfig->getSgEventsFolder();
        $calendarId = $eventsConfig->getSgCalendar();
        $fileNamesToDelete = ['fileA.png', 'fileB.png', 'fileC.png', 'fileD.png', 'fileE.png', 'fileF.png', 'fileG.png', 'fileH.png', 'fileI.png', 'fileJ.png', 'fileK.png', 'fileL.png', 'fileM.png', 'fileN.png'];
        foreach ($fileNamesToDelete as $filenameToDelete) {
            $objFile = new \Contao\File($directory.\DIRECTORY_SEPARATOR.$filenameToDelete);
            if ($objFile->exists()) {
                $objFile->delete();
            }
        }

        $eventsAliasesToDelete = ['event-test-a', 'event-test-b', 'évènement-c', 'évènement-d', 'évènement-e', 'évènement-f', 'évènement-g', 'évènement-h', 'évènement-i', 'évènement-j', 'évènement-k', 'évènement-l', 'évènement-m', 'évènement-n', 'évènement-o', 'évènement-p', 'évènement-q', 'évènement-r'];
        foreach ($eventsAliasesToDelete as $eventsAliasToDelete) {
            $objNews = \Contao\CalendarEventsModel::findOneByAlias($eventsAliasToDelete);
            if ($objNews) {
                $objNews->delete();
            }
        }
    }

    protected function copyFiles(array $filenames): void
    {
        $eventsConfig = $this->configurationManager->load()->getSgEvents();
        $destinationDirectory = $eventsConfig->getSgEventsFolder();
        foreach ($filenames as $filenameToCopy) {
            $objFile = new \Contao\File($this->sourceDirectory.\DIRECTORY_SEPARATOR.$filenameToCopy);
            if (!$objFile->copyTo($destinationDirectory.\DIRECTORY_SEPARATOR.$filenameToCopy)) {
                throw new Exception($this->translator->trans('WEMSG.DIRECTORIESSYNCHRONIZER.error', [$this->sourceDirectory.\DIRECTORY_SEPARATOR.$filenameToCopy, $destinationDirectory.\DIRECTORY_SEPARATOR.$filenameToCopy], 'contao_default'));
            }
        }
    }

    protected function createOrUpdateCalendarEvent(int $pid, string $title, string $alias, int $author, $startDate, $startTime, string $teaser, string $location, string $fileSRC, bool $published): void
    {
        $singleSRC = $fileSRC;
        if (!empty($fileSRC)) {
            $objFile = \Contao\FilesModel::findByPath($fileSRC);
            if ($objFile) {
                $singleSRC = $objFile->uuid;
            } else {
                $singleSRC = null;
            }
        }
        $objCalendarEvent = \Contao\CalendarEventsModel::findOneByAlias($alias) ?? new \Contao\CalendarEventsModel();
        $objCalendarEvent->pid = $pid;
        $objCalendarEvent->title = $title;
        $objCalendarEvent->headline = $title;
        $objCalendarEvent->alias = $alias;
        $objCalendarEvent->author = $author;
        $objCalendarEvent->startDate = $startDate;
        $objCalendarEvent->startTime = $startTime;
        $objCalendarEvent->endTime = (new \DateTime())->setTimestamp((int) $startTime)->setTime(23, 59, 59)->getTimestamp();
        $objCalendarEvent->teaser = $teaser;

        if (\array_key_exists($location, $this->locations)) {
            $objCalendarEvent->location = $this->locations[$location]['location'];
            $objCalendarEvent->address = $this->locations[$location]['address'];
            $objCalendarEvent->addressLat = $this->locations[$location]['lat'];
            $objCalendarEvent->addressLon = $this->locations[$location]['lon'];
        }

        $objCalendarEvent->addImage = !empty($singleSRC);
        $objCalendarEvent->singleSRC = $singleSRC;
        $objCalendarEvent->published = $published;
        $objCalendarEvent->tstamp = time();
        $objCalendarEvent->save();
    }

    protected function getLoremIpsum(int $length)
    {
        return substr('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam at semper sapien. Vivamus ac consequat ligula. Suspendisse dapibus nisi laoreet, porta nisl eget, ornare neque. Aliquam eu ex molestie, rhoncus tortor sed, pellentesque nisi. Donec auctor venenatis sapien, fermentum consequat lorem placerat sit amet. Maecenas ac placerat tellus. Nulla nunc mi, tempus non mollis vitae, venenatis sed purus. Sed eu velit imperdiet, cursus libero et, porttitor risus. Suspendisse potenti. Vestibulum eget nisl lectus. Vestibulum eu interdum tellus, nec rhoncus augue. Ut orci justo, feugiat ut nunc tristique, faucibus consequat quam. Fusce dignissim sagittis lectus, non placerat odio porttitor vitae. Curabitur suscipit erat et dolor hendrerit commodo. Interdum et malesuada fames ac ante ipsum primis in faucibus. Nunc a elit condimentum, semper felis ut, mattis justo.', 0, $length);
    }
}
