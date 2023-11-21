<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2023 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Classes\Utils;

use Contao\ContentModel;
use WEM\SmartgearBundle\Model\Configuration\Configuration;

class ContentUtil
{
    /**
     * Shortcut for content creation.
     */
    public static function createContent($objArticle, $arrData = []): ContentModel
    {
        // Dynamic ptable support
        if (!$arrData['ptable']) {
            $arrData['ptable'] = 'tl_article';
        }

        $objContentHighestSorting = ContentModel::findOneBy(['pid = ?', 'ptable = ?'], [$objArticle->id, $arrData['ptable']], ['order' => 'sorting DESC']);

        // Create the content
        $objContent = isset($arrData['id']) ? ContentModel::findById($arrData['id']) ?? new ContentModel() : new ContentModel();
        $objContent->tstamp = time();
        $objContent->pid = $objArticle->id;
        $objContent->ptable = $arrData['ptable'];
        $objContent->sorting = $objContentHighestSorting->sorting + 128;
        $objContent->type = 'text';

        // Now we get the default values, get the arrData table
        if (!empty($arrData)) {
            foreach ($arrData as $k => $v) {
                $objContent->$k = $v;
            }
        }

        $objContent->save();

        // Return the model
        return $objContent;
    }

    public static function buildContentLegalNotice(string $customTpl, Configuration $objConfiguration): string
    {
        $strText = file_get_contents($customTpl);
        $strHtml = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PageLegalNoticeTextDefault'];
        if ($strText) {
            /**
             * 1: URL du site entière
             * 2: URL du site sans https://
             * 3: Nom de l'entreprise
             * 4: Statut de l'entreprise
             * 5: Siret de l'entreprise
             * 6: Adresse du siège de l'entreprise
             * 7: Adresse mail de l'entreprise
             * 8: Nom & Adresse de l'hébergeur.
             */
            $strHtml = sprintf(
                    $strText,
                    $objConfiguration->domain ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                    str_replace('https://', '', $objConfiguration->domain) ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                    $objConfiguration->legal_owner_company_name ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                    $objConfiguration->legal_owner_company_status ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                    $objConfiguration->legal_owner_company_identifier ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                    $objConfiguration->legal_owner_company_street.' '.$objConfiguration->legal_owner_company_postal_code.' '.$objConfiguration->legal_owner_company_city.' '.$objConfiguration->legal_owner_company_region.' '.$objConfiguration->legal_owner_company_country ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                    $objConfiguration->owner_email ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                    $objConfiguration->host_name ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled']
                );
        }

        return $strHtml;
    }

    public static function buildContentPrivacyPolitics(string $customTpl, string $legalNoticePageUrl, Configuration $objConfiguration): string
    {
        $strText = file_get_contents($customTpl);
        $strHtml = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['WEBSITE']['PagePrivacyPoliticsTextDefault'];
        if ($strText) {
            /**
             * 1: Nom de la boite
             * 2: Adresse
             * 3: SIRET
             * 4: URL de la page confidentialité
             * 5: Date
             * 6: Contact email.
             */
            $strHtml = sprintf(
                $strText,
                $objConfiguration->legal_owner_company_name ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                $objConfiguration->legal_owner_company_street.' '.$objConfiguration->legal_owner_company_postal_code.' '.$objConfiguration->legal_owner_company_city.' '.$objConfiguration->legal_owner_company_region.' '.$objConfiguration->legal_owner_company_country ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                $objConfiguration->legal_owner_company_identifier ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                $legalNoticePageUrl ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled'],
                date('d/m/Y'),
                $objConfiguration->owner_email ?: $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['DEFAULT']['NotFilled']
            );
        }

        return $strHtml;
    }
}
