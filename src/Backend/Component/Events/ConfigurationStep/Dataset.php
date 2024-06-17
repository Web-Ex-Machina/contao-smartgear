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

use Contao\CalendarEventsModel;
use Contao\File;
use Contao\FilesModel;
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



    protected array|string $sourceDirectory;

    private array $locations = [ // TODO : WTF ??
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
        string                         $module,
        string                         $type,
        protected TranslatorInterface  $translator,
        protected ConfigurationManager $configurationManager,
        protected CommandUtil          $commandUtil,
        string                         $sourceDirectory
    ) {
        parent::__construct($module, $type);
        $this->sourceDirectory = str_replace('[public_or_web]', Util::getPublicOrWebDirectory(true), $sourceDirectory);

        $this->title = $this->translator->trans('WEMSG.EVENTS.INSTALL_DATASET.title', [], 'contao_default');
        $this->configurationManager->load()->getSgEvents();

        $datasetOptions = [];

        $datasetOptions[] = ['value' => 'none', 'label' => $this->translator->trans('WEMSG.EVENTS.INSTALL_DATASET.datasetOptionNone', [], 'contao_default')];
        $datasetOptions[] = ['value' => 'A', 'label' => $this->translator->trans('WEMSG.EVENTS.INSTALL_DATASET.datasetOptionA', [], 'contao_default')];
        $datasetOptions[] = ['value' => 'B', 'label' => $this->translator->trans('WEMSG.EVENTS.INSTALL_DATASET.datasetOptionB', [], 'contao_default')];

        $this->addSelectField('dataset', $this->translator->trans('WEMSG.EVENTS.INSTALL_DATASET.dataset', [], 'contao_default'), $datasetOptions, 'none', true, false, '', 'select', $this->translator->trans('WEMSG.EVENTS.INSTALL_DATASET.datasetHelp', [], 'contao_default'));
    }

    /**
     * @throws Exception
     */
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

    /**
     * @throws Exception
     */
    protected function installDatasetA(): void
    {
        $coreConfig = $this->configurationManager->load();
        $eventsConfig = $coreConfig->getSgEvents();
        $filesDirectory = $eventsConfig->getSgEventsFolder();
        $calendarId = $eventsConfig->getSgCalendar();
        $fileNamesToCopy = ['fileA.jpg', 'fileB.jpg', 'fileC.jpg'];
        $authorId = $coreConfig->getSgUserWebmaster();

        $this->copyFiles($fileNamesToCopy);

        $this->createOrUpdateCalendarEvent($calendarId, 'Event Test A', 'event-test-a', $authorId, strtotime('-1 year'), strtotime('-1 year'), $this->getLoremIpsum(140), 'Pulperia Heniz', $filesDirectory.\DIRECTORY_SEPARATOR.'fileA.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Event Test B', 'event-test-b', $authorId, strtotime('-1 week'), strtotime('-1 week'), $this->getLoremIpsum(240), 'Super Bazar', $filesDirectory.\DIRECTORY_SEPARATOR.'fileB.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité C', 'évènement-c', $authorId, strtotime('-1 day'), strtotime('-1 day'), $this->getLoremIpsum(80), 'Chateau Puyferrat', $filesDirectory.\DIRECTORY_SEPARATOR.'fileC.jpg', true);

        $this->createOrUpdateCalendarEvent($calendarId, 'Event Test A #2', 'event-test-a-2', $authorId, strtotime('+1 day'), strtotime('+1 day'), $this->getLoremIpsum(140), 'Pulperia Heniz', $filesDirectory.\DIRECTORY_SEPARATOR.'fileA.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Event Test B #2', 'event-test-b-2', $authorId, strtotime('+1 week'), strtotime('+1 week'), $this->getLoremIpsum(240), 'Super Bazar', $filesDirectory.\DIRECTORY_SEPARATOR.'fileB.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité C #2', 'évènement-c-2', $authorId, strtotime('+1 year'), strtotime('+1 year'), $this->getLoremIpsum(80), 'Chateau Puyferrat', $filesDirectory.\DIRECTORY_SEPARATOR.'fileC.jpg', true);
    }

    /**
     * @throws Exception
     */
    protected function installDatasetB(): void
    {
        $coreConfig = $this->configurationManager->load();
        $eventsConfig = $coreConfig->getSgEvents();
        $filesDirectory = $eventsConfig->getSgEventsFolder();
        $calendarId = $eventsConfig->getSgCalendar();
        $fileNamesToCopy = ['fileA.jpg', 'fileB.jpg', 'fileC.jpg', 'fileD.jpg', 'fileE.jpg', 'fileF.jpg', 'fileG.jpg', 'fileH.jpg', 'fileI.jpg', 'fileJ.jpg', 'fileK.jpg', 'fileL.jpg', 'fileM.jpg', 'fileN.jpg'];
        $authorId = $coreConfig->getSgUserWebmaster();

        $this->copyFiles($fileNamesToCopy);

        $this->createOrUpdateCalendarEvent($calendarId, 'Event Test A', 'event-test-a', $authorId, strtotime('-1 year'), strtotime('-1 year'), $this->getLoremIpsum(140), 'Pulperia Heniz', $filesDirectory.\DIRECTORY_SEPARATOR.'fileA.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Event Test B', 'event-test-b', $authorId, strtotime('-340 days'), strtotime('-340 days'), $this->getLoremIpsum(240), 'Super Bazar', $filesDirectory.\DIRECTORY_SEPARATOR.'fileB.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité C', 'évènement-c', $authorId, strtotime('-300 days'), strtotime('-300 days'), $this->getLoremIpsum(80), 'Chateau Puyferrat', $filesDirectory.\DIRECTORY_SEPARATOR.'fileC.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité D', 'évènement-d', $authorId, strtotime('-300 days'), strtotime('-300 days +2 hours'), $this->getLoremIpsum(160), 'Sanctuaire Shinto', '', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité E', 'évènement-e', $authorId, strtotime('-280 days'), strtotime('-280 days'), $this->getLoremIpsum(380), 'Pulperia Heniz', $filesDirectory.\DIRECTORY_SEPARATOR.'fileD.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité F', 'évènement-f', $authorId, strtotime('-270 days'), strtotime('-270 days'), $this->getLoremIpsum(120), 'Chateau Puyferrat', '', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité G', 'évènement-g', $authorId, strtotime('-240 days'), strtotime('-240 days'), $this->getLoremIpsum(80), 'Chateau Puyferrat', '', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité H', 'évènement-h', $authorId, strtotime('-180 days'), strtotime('-180 days'), $this->getLoremIpsum(160), 'Chateau Puyferrat', $filesDirectory.\DIRECTORY_SEPARATOR.'fileE.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité I', 'évènement-i', $authorId, strtotime('-160 days'), strtotime('-160 days'), $this->getLoremIpsum(340), 'Sanctuaire Shinto', $filesDirectory.\DIRECTORY_SEPARATOR.'fileF.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité J', 'évènement-j', $authorId, strtotime('-140 days'), strtotime('-140 days'), $this->getLoremIpsum(80), 'Pulperia Heniz', $filesDirectory.\DIRECTORY_SEPARATOR.'fileG.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité K', 'évènement-k', $authorId, strtotime('-135 days'), strtotime('-135 days'), $this->getLoremIpsum(160), 'Super Bazar', $filesDirectory.\DIRECTORY_SEPARATOR.'fileH.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité L', 'évènement-l', $authorId, strtotime('-120 days'), strtotime('-120 days'), $this->getLoremIpsum(360), 'Sanctuaire Shinto', $filesDirectory.\DIRECTORY_SEPARATOR.'fileI.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité M', 'évènement-m', $authorId, strtotime('-90 days'), strtotime('-90 days'), $this->getLoremIpsum(80), 'Super Bazar', $filesDirectory.\DIRECTORY_SEPARATOR.'fileJ.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité N', 'évènement-n', $authorId, strtotime('-85 days'), strtotime('-85 days'), $this->getLoremIpsum(160), 'Pulperia Heniz', $filesDirectory.\DIRECTORY_SEPARATOR.'fileK.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité O', 'évènement-o', $authorId, strtotime('-70 days'), strtotime('-70 days'), $this->getLoremIpsum(600), 'Pulperia Heniz', $filesDirectory.\DIRECTORY_SEPARATOR.'fileL.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité P', 'évènement-p', $authorId, strtotime('-60 days'), strtotime('-60 days'), $this->getLoremIpsum(320), 'Chateau Puyferrat', $filesDirectory.\DIRECTORY_SEPARATOR.'fileM.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité Q', 'évènement-q', $authorId, strtotime('-30 days'), strtotime('-30 days'), $this->getLoremIpsum(80), 'Chateau Puyferrat', '', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité R', 'évènement-r', $authorId, strtotime('-15 days'), strtotime('-15 days'), $this->getLoremIpsum(120), 'Sanctuaire Shinto', $filesDirectory.\DIRECTORY_SEPARATOR.'fileN.jpg', true);

        $this->createOrUpdateCalendarEvent($calendarId, 'Event Test A #2', 'event-test-a-2', $authorId, strtotime('+15 days'), strtotime('+15 days'), $this->getLoremIpsum(140), 'Pulperia Heniz', $filesDirectory.\DIRECTORY_SEPARATOR.'fileA.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Event Test B #2', 'event-test-b-2', $authorId, strtotime('+30 days'), strtotime('+30 days'), $this->getLoremIpsum(240), 'Super Bazar', $filesDirectory.\DIRECTORY_SEPARATOR.'fileB.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité C #2', 'évènement-c-2', $authorId, strtotime('+60 days'), strtotime('+60 days'), $this->getLoremIpsum(80), 'Chateau Puyferrat', $filesDirectory.\DIRECTORY_SEPARATOR.'fileC.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité D #2', 'évènement-d-2', $authorId, strtotime('+70 days'), strtotime('+70 days +2 hours'), $this->getLoremIpsum(160), 'Sanctuaire Shinto', '', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité E #2', 'évènement-e-2', $authorId, strtotime('+85 days'), strtotime('+85 days'), $this->getLoremIpsum(380), 'Pulperia Heniz', $filesDirectory.\DIRECTORY_SEPARATOR.'fileD.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité F #2', 'évènement-f-2', $authorId, strtotime('+90 days'), strtotime('+90 days'), $this->getLoremIpsum(120), 'Chateau Puyferrat', '', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité G #2', 'évènement-g-2', $authorId, strtotime('+120 days'), strtotime('+120 days'), $this->getLoremIpsum(80), 'Chateau Puyferrat', '', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité H #2', 'évènement-h-2', $authorId, strtotime('+135 days'), strtotime('+135 days'), $this->getLoremIpsum(160), 'Chateau Puyferrat', $filesDirectory.\DIRECTORY_SEPARATOR.'fileE.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité I #2', 'évènement-i-2', $authorId, strtotime('+140 days'), strtotime('+140 days'), $this->getLoremIpsum(340), 'Sanctuaire Shinto', $filesDirectory.\DIRECTORY_SEPARATOR.'fileF.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité J #2', 'évènement-j-2', $authorId, strtotime('+160 days'), strtotime('+160 days'), $this->getLoremIpsum(80), 'Pulperia Heniz', $filesDirectory.\DIRECTORY_SEPARATOR.'fileG.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité K #2', 'évènement-k-2', $authorId, strtotime('+180 days'), strtotime('+180 days'), $this->getLoremIpsum(160), 'Super Bazar', $filesDirectory.\DIRECTORY_SEPARATOR.'fileH.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité L #2', 'évènement-l-2', $authorId, strtotime('+240 days'), strtotime('+240 days'), $this->getLoremIpsum(360), 'Sanctuaire Shinto', $filesDirectory.\DIRECTORY_SEPARATOR.'fileI.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité M #2', 'évènement-m-2', $authorId, strtotime('+270 days'), strtotime('+270 days'), $this->getLoremIpsum(80), 'Super Bazar', $filesDirectory.\DIRECTORY_SEPARATOR.'fileJ.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité N #2', 'évènement-n-2', $authorId, strtotime('+280 days'), strtotime('+280 days'), $this->getLoremIpsum(160), 'Pulperia Heniz', $filesDirectory.\DIRECTORY_SEPARATOR.'fileK.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité O #2', 'évènement-o-2', $authorId, strtotime('+300 days'), strtotime('+300 days'), $this->getLoremIpsum(600), 'Pulperia Heniz', $filesDirectory.\DIRECTORY_SEPARATOR.'fileL.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité P #2', 'évènement-p-2', $authorId, strtotime('+300 days'), strtotime('+300 days'), $this->getLoremIpsum(320), 'Chateau Puyferrat', $filesDirectory.\DIRECTORY_SEPARATOR.'fileM.jpg', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité Q #2', 'évènement-q-2', $authorId, strtotime('+340 days'), strtotime('+340 days'), $this->getLoremIpsum(80), 'Chateau Puyferrat', '', true);
        $this->createOrUpdateCalendarEvent($calendarId, 'Actualité R #2', 'évènement-r-2', $authorId, strtotime('+1 year'), strtotime('+1 year'), $this->getLoremIpsum(120), 'Sanctuaire Shinto', $filesDirectory.\DIRECTORY_SEPARATOR.'fileN.jpg', true);
    }

    /**
     * @throws Exception
     */
    protected function cleanDatasets(): void
    {
        $eventsConfig = $this->configurationManager->load()->getSgEvents();
        $directory = $eventsConfig->getSgEventsFolder();
        $eventsConfig->getSgCalendar();
        $fileNamesToDelete = ['fileA.jpg', 'fileB.jpg', 'fileC.jpg', 'fileD.jpg', 'fileE.jpg', 'fileF.jpg', 'fileG.jpg', 'fileH.jpg', 'fileI.jpg', 'fileJ.jpg', 'fileK.jpg', 'fileL.jpg', 'fileM.jpg', 'fileN.jpg'];
        foreach ($fileNamesToDelete as $filenameToDelete) {
            $objFile = new File($directory.\DIRECTORY_SEPARATOR.$filenameToDelete);
            if ($objFile->exists()) {
                $objFile->delete();
            }
        }

        $eventsAliasesToDelete = ['event-test-a', 'event-test-b', 'évènement-c', 'évènement-d', 'évènement-e', 'évènement-f', 'évènement-g', 'évènement-h', 'évènement-i', 'évènement-j', 'évènement-k', 'évènement-l', 'évènement-m', 'évènement-n', 'évènement-o', 'évènement-p', 'évènement-q', 'évènement-r', 'event-test-a-2', 'event-test-b-2', 'évènement-c-2', 'évènement-d-2', 'évènement-e-2', 'évènement-f-2', 'évènement-g-2', 'évènement-h-2', 'évènement-i-2', 'évènement-j-2', 'évènement-k-2', 'évènement-l-2', 'évènement-m-2', 'évènement-n-2', 'évènement-o-2', 'évènement-p-2', 'évènement-q-2', 'évènement-r-2'];
        foreach ($eventsAliasesToDelete as $eventsAliasToDelete) {
            $objNews = CalendarEventsModel::findOneByAlias($eventsAliasToDelete);
            if ($objNews) {
                $objNews->delete();
            }
        }
    }

    /**
     * @throws Exception
     */
    protected function copyFiles(array $filenames): void
    {
        $eventsConfig = $this->configurationManager->load()->getSgEvents();
        $destinationDirectory = $eventsConfig->getSgEventsFolder();
        foreach ($filenames as $filenameToCopy) {
            $objFile = new File($this->sourceDirectory.\DIRECTORY_SEPARATOR.$filenameToCopy);
            if (!$objFile->copyTo($destinationDirectory.\DIRECTORY_SEPARATOR.$filenameToCopy)) {
                throw new Exception($this->translator->trans('WEMSG.DIRECTORIESSYNCHRONIZER.error', [$this->sourceDirectory.\DIRECTORY_SEPARATOR.$filenameToCopy, $destinationDirectory.\DIRECTORY_SEPARATOR.$filenameToCopy], 'contao_default'));
            }
        }
    }

    protected function createOrUpdateCalendarEvent(int $pid, string $title, string $alias, int $author, $startDate, $startTime, string $teaser, string $location, string $fileSRC, bool $published): void
    {
        $singleSRC = $fileSRC;
        if ($fileSRC !== '' && $fileSRC !== '0') {
            $objFile = FilesModel::findByPath($fileSRC);
            $singleSRC = $objFile ? $objFile->uuid : null;
        }

        $objCalendarEvent = CalendarEventsModel::findOneByAlias($alias) ?? new CalendarEventsModel();
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

    protected function getLoremIpsum(int $length): string
    {
        return substr('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam at semper sapien. Vivamus ac consequat ligula. Suspendisse dapibus nisi laoreet, porta nisl eget, ornare neque. Aliquam eu ex molestie, rhoncus tortor sed, pellentesque nisi. Donec auctor venenatis sapien, fermentum consequat lorem placerat sit amet. Maecenas ac placerat tellus. Nulla nunc mi, tempus non mollis vitae, venenatis sed purus. Sed eu velit imperdiet, cursus libero et, porttitor risus. Suspendisse potenti. Vestibulum eget nisl lectus. Vestibulum eu interdum tellus, nec rhoncus augue. Ut orci justo, feugiat ut nunc tristique, faucibus consequat quam. Fusce dignissim sagittis lectus, non placerat odio porttitor vitae. Curabitur suscipit erat et dolor hendrerit commodo. Interdum et malesuada fames ac ante ipsum primis in faucibus. Nunc a elit condimentum, semper felis ut, mattis justo.', 0, $length);
    }
}
