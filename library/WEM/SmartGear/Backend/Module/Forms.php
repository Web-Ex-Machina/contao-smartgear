<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2019 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartGear\Backend\Module;

use \Exception;

use NotificationCenter\Model\Notification as NCNotification;
use NotificationCenter\Model\Message as NCMessage;
use NotificationCenter\Model\Language as NCLanguage;
use NotificationCenter\Model\Gateway as NCGateway;

use WEM\SmartGear\Backend\Block;
use WEM\SmartGear\Backend\BlockInterface;
use WEM\SmartGear\Backend\Util;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Forms extends Block implements BlockInterface
{
    /**
     * Module dependancies
     * @var Array
     */
    protected $require = ["core_core"];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->type = "module";
        $this->module = "forms";
        $this->icon = "cogs";
        $this->title = "SmartGear | Module | Formulaires";

        parent::__construct();
    }

    /**
     * Check Module Status
     * @return [String] [Template of the module check status]
     */
    public function getStatus()
    {
        if (!$this->sgConfig['sgFormsInstall'] || 0 === \FormModel::countById($this->sgConfig['sgForm'])) {
            $this->messages[] = ['class' => 'tl_info', 'text' => 'Les formulaires sont installés, mais pas configurés.'];
            $this->actions[] = ['action'=>'install', 'label'=>'Installer'];
            $this->status = 0;
        } else {
            $this->messages[] = ['class' => 'tl_confirm', 'text' => 'Les formulaires sont installés et configurés.'];
            $this->actions[] = ['action'=>'reset', 'label'=>'Réinitialiser'];
            $this->actions[] = ['action'=>'remove', 'label'=>'Supprimer'];
            $this->status = 1;
        }
    }

    /**
     * Setup the module
     */
    public function install()
    {
        // Create the form page
        $objPage = Util::createPage("Contact");

        // Create the article
        $objArticle = Util::createArticle($objPage);

        // Create the form page where the users will be redirected
        $objJumpPage = Util::createPage("Contact envoyé", $objPage->id, ["hide"=>1]);
        $objJumpArticle = Util::createArticle($objJumpPage);
        $strText = '<p>Votre message a bien été envoyé ! <a href="{{env::/}}" title="Retour à l\'accueil">Retour à l\'accueil</a></p>';
        $arrHl = ["unit"=>"h1", "value"=>"Message envoyé !"];
        $objJumpContent = Util::createContent($objJumpArticle, ["text"=>$strText, "headline"=>$arrHl]);

        // Create the notification
        $objNotification = new NCNotification();
        $objNotification->tstamp = time();
        $objNotification->title = "Formulaire de contact";
        $objNotification->type = "core_form";
        $objNotification->save();

        $objGateway = NCGateway::findByPk($this->sgConfig['sgInstallNcGateway']);
        $objMessage = new NCMessage();
        $objMessage->tstamp = time();
        $objMessage->pid = $objNotification->id;
        $objMessage->title = "Accusé réception";
        $objMessage->gateway = $objGateway->id;
        $objMessage->gateway_type = $objGateway->type;
        $objMessage->email_priority = 1;
        $objMessage->email_template = 'mail_default';
        $objMessage->published = 1;
        $objMessage->save();

        $strHtml = sprintf(
            "
				<p>Bonjour ##form_name##
				<br />Vous avez rempli notre formulaire de contact avec les informations suivantes :
				<br />- ##formlabel_name## : ##form_name##
				<br />- ##formlabel_phone## : ##form_phone##
				<br />- ##formlabel_email## : ##form_email##
				<br />- ##formlabel_message## : ##form_message##
				<br />Nous vous répondrons dans les plus brefs délais.
				<br />Cordialement, 
				<br />%s
			",
            $this->sgConfig["websiteTitle"]
        );

        $objLanguage = new NCLanguage();
        $objLanguage->tstamp = time();
        $objLanguage->pid = $objMessage->id;
        $objLanguage->gateway_type = $objGateway->type;
        $objLanguage->language = "fr";
        $objLanguage->fallback = 1;
        $objLanguage->email_sender_name = $this->sgConfig["websiteTitle"];
        $objLanguage->email_sender_address = "##admin_email##";
        $objLanguage->recipients = "##form_email##";
        $objLanguage->email_recipient_cc = "";
        $objLanguage->email_recipient_bcc = "";
        $objLanguage->email_replyTo = "";
        $objLanguage->email_subject = sprintf("Votre message sur %s", $this->sgConfig["websiteTitle"]);
        $objLanguage->email_mode = "textAndHtml";
        $objLanguage->email_text = strip_tags($strHtml);
        $objLanguage->email_html = $strHtml;
        $objLanguage->email_external_images = "";
        $objLanguage->save();

        // Create the form
        $objForm = new \FormModel();
        $objForm->tstamp = time();
        $objForm->title = "Formulaire de contact";
        $objForm->alias = \StringUtil::generateAlias($objForm->title);
        $objForm->jumpTo = $obJumpPage->id;
        $objForm->nc_notification = $objNotification->id;
        $objForm->save();

        // Create the form fields
        $objField = new \FormFieldModel();
        $objField->tstamp = time();
        $objField->pid = $objForm->id;
        $objField->sorting = 128;
        $objField->type = "text";
        $objField->name = "name";
        $objField->label = "Votre nom :";
        $objField->mandatory = 1;
        $objField->rgxp = "extnd";
        $objField->placeholder = "Saisissez votre nom et prénom";
        $objField->save();

        $objField = new \FormFieldModel();
        $objField->tstamp = time();
        $objField->pid = $objForm->id;
        $objField->sorting = 256;
        $objField->type = "text";
        $objField->name = "phone";
        $objField->label = "Votre téléphone :";
        $objField->rgxp = "phone";
        $objField->placeholder = "Saisissez votre numéro de téléphone";
        $objField->save();

        $objField = new \FormFieldModel();
        $objField->tstamp = time();
        $objField->pid = $objForm->id;
        $objField->sorting = 384;
        $objField->type = "text";
        $objField->name = "email";
        $objField->label = "Votre email :";
        $objField->mandatory = 1;
        $objField->rgxp = "email";
        $objField->placeholder = "Saisissez votre adresse email";
        $objField->save();

        $objField = new \FormFieldModel();
        $objField->tstamp = time();
        $objField->pid = $objForm->id;
        $objField->sorting = 512;
        $objField->type = "textarea";
        $objField->name = "message";
        $objField->label = "Votre message :";
        $objField->mandatory = 1;
        $objField->rgxp = "extnd";
        $objField->placeholder = "Saisissez votre message";
        $objField->save();

        $objField = new \FormFieldModel();
        $objField->tstamp = time();
        $objField->pid = $objForm->id;
        $objField->sorting = 640;
        $objField->type = "captcha";
        $objField->label = "Question de sécurité :";
        $objField->placeholder = "Veuillez répondre à la question de sécurité";
        $objField->save();

        $objField = new \FormFieldModel();
        $objField->tstamp = time();
        $objField->pid = $objForm->id;
        $objField->sorting = 768;
        $objField->type = "submit";
        $objField->slabel = "Envoyer";
        $objField->save();

        $objField = new \FormFieldModel();
        $objField->tstamp = time();
        $objField->pid = $objForm->id;
        $objField->sorting = 896;
        $objField->type = "explanation";
        $objField->text = sprintf(
            "<p><u>Informations légales</u></p>
            <p>Les informations recueillies sur ce formulaire sont enregistrées dans un fichier informatisé par %s afin d’optimiser le traitement des demandes clients. Elles sont conservées pendant une durée d’un an et effacées par la suite. Conformément au Règlement (UE) 2016/679 du Parlement européen et du Conseil du 27 avril 2016 [en application depuis mai 2018], vous pouvez exercer votre droit d'accès aux données vous concernant et les faire rectifier en contactant %s au travers de ce même formulaire.</p>
            ",
            \Config::get('websiteTitle'),
            \Config::get('websiteTitle')
        );
        $objField->save();

        // Create the content
        $objContent = new \ContentModel();
        $objContent->tstamp = time();
        $objContent->pid = $objArticle->id;
        $objContent->ptable = "tl_article";
        $objContent->sorting = 128;
        $objContent->type = "form";
        $objContent->headline = serialize(["unit"=>"h1", "value"=>"Contact"]);
        $objContent->form = $objForm->id;
        $objContent->save();

        // And save stuff in config
        Util::updateConfig([
            "sgFormsInstall"=>1
            ,"sgForm"=>$objForm->id
            ,"sgFormPage"=>$objPage->id
            ,"sgFormJumpPage"=>$obJumpPage->id
        ]);

        // And return an explicit status with some instructions
        return [
            "toastr" => [
                "status"=>"success"
                ,"msg"=>"La configuration du module a été effectuée avec succès."
            ]
            ,"callbacks" => [
                0 => [
                    "method" => "refreshBlock"
                    ,"args"  => ["block-".$this->type."-".$this->module]
                ]
            ]
        ];
    }

    /**
     * Remove the module
     */
    public function remove()
    {
        // Get the form
        $objForm = \FormModel::findByPk($this->sgConfig['sgForm']);

        // Delete all the form fields
        \Database::getInstance()->prepare("DELETE FROM tl_form_field WHERE pid = ?")->execute($objForm->id);

        // Get and delete the notification
        if ($objNotification = NCNotification::findByPk($objForm->nc_notification)) {
            $objNotification->delete();
        }

        // Get and delete the form page
        if ($objPage = \PageModel::findByPk($this->sgConfig['sgFormPage'])) {
            $objPage->delete();
        }

        // Get and delete the form jump page
        if ($objPage = \PageModel::findByPk($this->sgConfig['sgFormJumpPage'])) {
            $objPage->delete();
        }

        // Delete the form
        $objForm->delete();

        // And save stuff in config
        Util::updateConfig([
            "sgFormsInstall"=>""
            ,"sgForm"=>""
            ,"sgFormPage"=>""
            ,"sgFormJumpPage"=>""
        ]);

        // And return an explicit status with some instructions
        return [
            "toastr" => [
                "status"=>"success"
                ,"msg"=>"La suppression du module a été effectuée avec succès."
            ]
            ,"callbacks" => [
                0 => [
                    "method" => "refreshBlock"
                    ,"args"  => ["block-".$this->type."-".$this->module]
                ]
            ]
        ];
    }
}
