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

namespace WEM\SmartgearBundle\Backend\Dashboard;

use Contao\BackendModule;
use Contao\Config;
use Contao\Date;
use Contao\System;
use DateInterval;
use DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound;
use WEM\SmartgearBundle\Model\PageVisit;

class AnalyticsInternal extends BackendModule
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'be_wem_sg_dashboard_analytics_internal';
    protected $strId = 'wem_sg_dashboard_analytics_internal';
    /** @var TranslatorInterface */
    protected $translator;
    /** @var ConfigurationManager */
    protected $configurationManager;

    /**
     * Initialize the object.
     */
    public function __construct(
        TranslatorInterface $translator,
        configurationManager $configurationManager
    ) {
        parent::__construct();
        $this->translator = $translator;
        $this->configurationManager = $configurationManager;
    }

    public function generate(): string
    {
        return parent::generate();
    }

    public function compile(): void
    {
        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
        } catch (NotFound $e) {
            return;
        }
        $this->Template->title = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSINTERNAL.title', [], 'contao_default');

        // visits this week
        $today = new DateTime('now');
        $arrVisitsThisWeek = [
            ['index' => 7, 'tstamp' => (int) $today->getTimestamp(), 'value' => $this->getPageVisitsForDay($today)],
            ['index' => 6, 'tstamp' => (int) $today->sub(new DateInterval('P1D'))->getTimestamp(), 'value' => $this->getPageVisitsForDay($today)],
            ['index' => 5, 'tstamp' => (int) $today->sub(new DateInterval('P1D'))->getTimestamp(), 'value' => $this->getPageVisitsForDay($today)],
            ['index' => 4, 'tstamp' => (int) $today->sub(new DateInterval('P1D'))->getTimestamp(), 'value' => $this->getPageVisitsForDay($today)],
            ['index' => 3, 'tstamp' => (int) $today->sub(new DateInterval('P1D'))->getTimestamp(), 'value' => $this->getPageVisitsForDay($today)],
            ['index' => 2, 'tstamp' => (int) $today->sub(new DateInterval('P1D'))->getTimestamp(), 'value' => $this->getPageVisitsForDay($today)],
            ['index' => 1, 'tstamp' => (int) $today->sub(new DateInterval('P1D'))->getTimestamp(), 'value' => $this->getPageVisitsForDay($today)],
        ];
        $this->Template->visitsThisWeek = json_encode(array_reverse($arrVisitsThisWeek));
        // visits last week
        $today = new DateTime('now');
        $today->sub(new DateInterval('P7D'));
        $arrVisitsLastWeek = [
            ['index' => 7, 'tstamp' => (int) $today->getTimestamp(), 'value' => $this->getPageVisitsForDay($today)],
            ['index' => 6, 'tstamp' => (int) $today->sub(new DateInterval('P1D'))->getTimestamp(), 'value' => $this->getPageVisitsForDay($today)],
            ['index' => 5, 'tstamp' => (int) $today->sub(new DateInterval('P1D'))->getTimestamp(), 'value' => $this->getPageVisitsForDay($today)],
            ['index' => 4, 'tstamp' => (int) $today->sub(new DateInterval('P1D'))->getTimestamp(), 'value' => $this->getPageVisitsForDay($today)],
            ['index' => 3, 'tstamp' => (int) $today->sub(new DateInterval('P1D'))->getTimestamp(), 'value' => $this->getPageVisitsForDay($today)],
            ['index' => 2, 'tstamp' => (int) $today->sub(new DateInterval('P1D'))->getTimestamp(), 'value' => $this->getPageVisitsForDay($today)],
            ['index' => 1, 'tstamp' => (int) $today->sub(new DateInterval('P1D'))->getTimestamp(), 'value' => $this->getPageVisitsForDay($today)],
        ];
        $this->Template->visitsLastWeek = json_encode(array_reverse($arrVisitsLastWeek));

        // merged visits
        $this->Template->visitsMerged = json_encode(array_reverse([
            [
                'tstamp' => $arrVisitsThisWeek[0]['tstamp'] * 1000,
                'dateThisWeek' => Date::parse(Config::get('dateFormat'), (int) $arrVisitsThisWeek[0]['tstamp']),
                'dateLastWeek' => Date::parse(Config::get('dateFormat'), (int) $arrVisitsLastWeek[0]['tstamp']),
                'valueThisWeek' => $arrVisitsThisWeek[0]['value'],
                'valueLastWeek' => $arrVisitsLastWeek[0]['value'],
            ], [
                'tstamp' => $arrVisitsThisWeek[1]['tstamp'] * 1000,
                'dateThisWeek' => Date::parse(Config::get('dateFormat'), (int) $arrVisitsThisWeek[1]['tstamp']),
                'dateLastWeek' => Date::parse(Config::get('dateFormat'), (int) $arrVisitsLastWeek[1]['tstamp']),
                'valueThisWeek' => $arrVisitsThisWeek[1]['value'],
                'valueLastWeek' => $arrVisitsLastWeek[1]['value'],
            ], [
                'tstamp' => $arrVisitsThisWeek[2]['tstamp'] * 1000,
                'dateThisWeek' => Date::parse(Config::get('dateFormat'), (int) $arrVisitsThisWeek[2]['tstamp']),
                'dateLastWeek' => Date::parse(Config::get('dateFormat'), (int) $arrVisitsLastWeek[2]['tstamp']),
                'valueThisWeek' => $arrVisitsThisWeek[2]['value'],
                'valueLastWeek' => $arrVisitsLastWeek[2]['value'],
            ], [
                'tstamp' => $arrVisitsThisWeek[3]['tstamp'] * 1000,
                'dateThisWeek' => Date::parse(Config::get('dateFormat'), (int) $arrVisitsThisWeek[3]['tstamp']),
                'dateLastWeek' => Date::parse(Config::get('dateFormat'), (int) $arrVisitsLastWeek[3]['tstamp']),
                'valueThisWeek' => $arrVisitsThisWeek[3]['value'],
                'valueLastWeek' => $arrVisitsLastWeek[3]['value'],
            ], [
                'tstamp' => $arrVisitsThisWeek[4]['tstamp'] * 1000,
                'dateThisWeek' => Date::parse(Config::get('dateFormat'), (int) $arrVisitsThisWeek[4]['tstamp']),
                'dateLastWeek' => Date::parse(Config::get('dateFormat'), (int) $arrVisitsLastWeek[4]['tstamp']),
                'valueThisWeek' => $arrVisitsThisWeek[4]['value'],
                'valueLastWeek' => $arrVisitsLastWeek[4]['value'],
            ], [
                'tstamp' => $arrVisitsThisWeek[5]['tstamp'] * 1000,
                'dateThisWeek' => Date::parse(Config::get('dateFormat'), (int) $arrVisitsThisWeek[5]['tstamp']),
                'dateLastWeek' => Date::parse(Config::get('dateFormat'), (int) $arrVisitsLastWeek[5]['tstamp']),
                'valueThisWeek' => $arrVisitsThisWeek[5]['value'],
                'valueLastWeek' => $arrVisitsLastWeek[5]['value'],
            ], [
                'tstamp' => $arrVisitsThisWeek[6]['tstamp'] * 1000,
                'dateThisWeek' => Date::parse(Config::get('dateFormat'), (int) $arrVisitsThisWeek[6]['tstamp']),
                'dateLastWeek' => Date::parse(Config::get('dateFormat'), (int) $arrVisitsLastWeek[6]['tstamp']),
                'valueThisWeek' => $arrVisitsThisWeek[6]['value'],
                'valueLastWeek' => $arrVisitsLastWeek[6]['value'],
            ], ]));

        // referers
        // most viewed pages
        $this->Template->modPageUrl = System::getContainer()->get('router')->generate('contao_backend', ['do' => 'page']);
        $this->Template->linkPageText = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkPageText', [], 'contao_default');
        $this->Template->linkPageTitle = $this->translator->trans('WEMSG.DASHBOARD.SHORTCUTINTERNAL.linkPageTitle', [], 'contao_default');
    }

    protected function getPageVisitsForDay(DateTime $dt): int
    {
        return PageVisit::countItems([
            'where' => [sprintf('createdAt BETWEEN %d AND %d',
                    $dt->setTime(0, 0, 0, 0)->getTimestamp(),
                    $dt->setTime(23, 59, 59, 999)->getTimestamp()
                )], ]) + 3;
    }
}
