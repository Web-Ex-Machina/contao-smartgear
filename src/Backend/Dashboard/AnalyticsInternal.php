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
use Contao\BackendTemplate;
use Contao\Config;
use Contao\Pagination;
use DateInterval;
use DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
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
        $GLOBALS['TL_JAVASCRIPT'][] = 'https://cdn.jsdelivr.net/npm/chart.js';

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

        // visits
        $this->Template->visits = $this->getVisitsAnalytics();

        // referers
        $this->Template->referers = $this->getReferersAnalytics();

        // most viewed pages
        $this->Template->pagesUrl = $this->getPagesUrlAnalytics();
    }

    protected function getVisitsAnalytics(): string
    {
        $objTemplate = new BackendTemplate('be_wem_sg_dashboard_analytics_internal_visits');

        // visits this week
        $today = new DateTime('now');
        $todayLastWeek = new DateTime('now');
        $todayLastWeek->sub(new DateInterval('P7D'));
        $dateFormat = Config::get('dateFormat');
        $dateIntervalOneDay = new DateInterval('P1D');

        for ($i = 0; $i < 7; ++$i) {
            $arrDays[] = $today->format('d/m');
            $arrVisits[] = [
                'this_week' => [
                    'visits' => $this->getPageVisitsForDay($today),
                    'date' => $today->format($dateFormat),
                ],
                'previous_week' => [
                    'visits' => $this->getPageVisitsForDay($todayLastWeek),
                    'date' => $todayLastWeek->format($dateFormat),
                ],
                'x' => $arrDays[$i],
            ];
            $today->sub($dateIntervalOneDay);
            $todayLastWeek->sub($dateIntervalOneDay);
        }

        $objTemplate->arrDays = json_encode(array_reverse($arrDays));
        $objTemplate->arrVisits = json_encode(array_reverse($arrVisits));
        $objTemplate->visitsTitle = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSINTERNAL.visitsTitle', [], 'contao_default');
        $objTemplate->thisWeekSerieTitle = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSINTERNAL.thisWeekSerieTitle', [], 'contao_default');
        $objTemplate->previousWeekSerieTitle = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSINTERNAL.previousWeekSerieTitle', [], 'contao_default');

        return $objTemplate->parse();
    }

    protected function getPageVisitsForDay(DateTime $dt): int
    {
        return PageVisit::countItems([
            'where' => [sprintf('createdAt BETWEEN %d AND %d',
                    $dt->setTime(0, 0, 0, 0)->getTimestamp(),
                    $dt->setTime(23, 59, 59, 999)->getTimestamp()
                )],
            'exclude_be_login' => true,
        ]);
    }

    protected function getReferersAnalytics(): string
    {
        $dt = new DateTime();
        $arrConfig = [
            'where' => [sprintf('createdAt BETWEEN %d AND %d',
                    $dt->setTime(0, 0, 0, 0)->getTimestamp(),
                    $dt->sub(new DateInterval('P7D'))->setTime(23, 59, 59, 999)->getTimestamp()
                )],
            'exclude_be_login' => true,
        ];
        $arrOptions = ['group' => 'referer', 'order' => 'amount DESC'];
        $limit = 1;

        $objTemplate = new BackendTemplate('be_wem_sg_dashboard_analytics_internal_referers');
        $objTemplate->referers = PageVisit::getReferersAnalytics($arrConfig, $limit, 0, $arrOptions)->fetchAllAssoc();
        $objTemplate->referersTitle = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSINTERNAL.referersTitle', [], 'contao_default');

        $objTemplate->referersUrlHeader = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSINTERNAL.referersUrlHeader', [], 'contao_default');
        $objTemplate->referersAmountHeader = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSINTERNAL.referersAmountHeader', [], 'contao_default');

        // $objTemplate->pagination = (new Pagination(PageVisit::countReferersAnalytics($arrConfig, $arrOptions), $limit))->generate();

        return $objTemplate->parse();
    }

    protected function getPagesUrlAnalytics(): string
    {
        $dt = new DateTime();
        $arrConfig = [
            'where' => [sprintf('createdAt BETWEEN %d AND %d',
                    $dt->setTime(0, 0, 0, 0)->getTimestamp(),
                    $dt->sub(new DateInterval('P7D'))->setTime(23, 59, 59, 999)->getTimestamp()
                )],
            'exclude_be_login' => true,
        ];
        $arrOptions = ['group' => 'page_url', 'order' => 'amount DESC'];
        $limit = 5;

        $objTemplate = new BackendTemplate('be_wem_sg_dashboard_analytics_internal_pagesurl');
        $objTemplate->pagesUrl = PageVisit::getPagesUrlAnalytics($arrConfig, $limit, 0, $arrOptions)->fetchAllAssoc();
        $objTemplate->pagesUrlTitle = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSINTERNAL.pagesUrlTitle', [], 'contao_default');
        $objTemplate->pagesUrlUrlHeader = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSINTERNAL.pagesUrlUrlHeader', [], 'contao_default');
        $objTemplate->pagesUrlAmountHeader = $this->translator->trans('WEMSG.DASHBOARD.ANALYTICSINTERNAL.pagesUrlAmountHeader', [], 'contao_default');
        // $objTemplate->pagination = (new Pagination(PageVisit::countPagesUrlAnalytics($arrConfig, $arrOptions), $limit))->generate();

        return $objTemplate->parse();
    }
}
