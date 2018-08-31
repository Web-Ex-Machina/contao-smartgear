<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2018 Web ex Machina
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */

namespace WEM\SmartGear\Backend\Module;

use \Exception;
use Contao\Config;
use Contao\FormModel;
use Contao\FormFieldModel;
use Contao\FrontendTemplate;
use Contao\PageModel;
use Contao\ArticleModel;
use Contao\ContentModel;
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
	 * Constructor
	 */
	public function __construct(){
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
	public function getStatus(){
		if(!Config::get('sgFormsInstall') || 0 === FormModel::countById(Config::get('sgForm'))){
			$this->messages[] = ['class' => 'tl_info', 'text' => 'Les formulaires sont installés, mais pas configurés.'];
			$this->actions[] = ['action'=>'install', 'label'=>'Installer'];
		}
		else{
			$this->messages[] = ['class' => 'tl_confirm', 'text' => 'Les formulaires sont installés et configurés.'];
			$this->actions[] = ['action'=>'reset', 'label'=>'Réinitialiser'];
			$this->actions[] = ['action'=>'remove', 'label'=>'Supprimer'];
		}
	}

	/**
	 * Setup the module
	 */
	public function install(){
		// Create the form page
		$objPage = new PageModel();
		$objPage->tstamp = time();
		$objPage->pid = Config::get("sgInstallRootPage");
		$objPage->sorting = (PageModel::countBy("pid", Config::get("sgInstallRootPage")) + 1) * 128;
		$objPage->title = "Contact";
		$objPage->alias = \StringUtil::generateAlias($objPage->title);
		$objPage->type = "regular";
		$objPage->pageTitle = "Contact";
		$objPage->robots = "index,follow";
		$objPage->sitemap = "map_default";
		$objPage->published = 1;
		$objPage->save();

		// Create the article
		$objArticle = new ArticleModel();
		$objArticle->tstamp = time();
		$objArticle->pid = $objPage->id;
		$objArticle->sorting = 128;
		$objArticle->title = $objPage->title;
		$objArticle->alias = $objPage->alias;
		$objArticle->author = Config::get("sgInstallUser");
		$objArticle->inColumn = "main";
		$objArticle->published = 1;
		$objArticle->save();

		// Create the form page where the users will be redirected
		$objJumpPage = new PageModel();
		$objJumpPage->tstamp = time();
		$objJumpPage->pid = $objPage->id;
		$objJumpPage->sorting = 128;
		$objJumpPage->title = "Contact envoyé";
		$objJumpPage->alias = \StringUtil::generateAlias($objJumpPage->title);
		$objJumpPage->type = "regular";
		$objJumpPage->robots = "noindex,nofollow";
		$objJumpPage->sitemap = "map_never";
		$objJumpPage->hide = 1;
		$objJumpPage->published = 1;
		$objJumpPage->save();

		// Create the article
		$objJumpArticle = new ArticleModel();
		$objJumpArticle->tstamp = time();
		$objJumpArticle->pid = $objJumpPage->id;
		$objJumpArticle->sorting = 128;
		$objJumpArticle->title = $objJumpPage->title;
		$objJumpArticle->alias = $objJumpPage->alias;
		$objJumpArticle->author = Config::get("sgInstallUser");
		$objJumpArticle->inColumn = "main";
		$objJumpArticle->published = 1;
		$objJumpArticle->save();

		// Create the content
		$objContent = new ContentModel();
		$objContent->tstamp = time();
		$objContent->pid = $objJumpArticle->id;
		$objContent->ptable = "tl_article";
		$objContent->sorting = 128;
		$objContent->type = "text";
		$objContent->headline = serialize(["unit"=>"h1", "value"=>"Message envoyé !"]);
		$objContent->text = '<p>Votre message a bien été envoyé ! <a href="{{env::/}}" title="Retour à l\'accueil">Retour à l\'accueil</a></p>';
		$objContent->save();

		// Create the notification
		$objNotification = new NCNotification();
		$objNotification->tstamp = time();
		$objNotification->title = "Formulaire de contact";
		$objNotification->type = "core_form";
		$objNotification->save();

		$objGateway = NCGateway::findByPk(Config::get('sgInstallNcGateway'));
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

		$strHtml = sprintf("
				<p>Bonjour ##form_name##
				<br />Vous avez rempli notre formulaire de contact avec les informations suivantes :
				<br />- ##formlabel_name## : ##form_name##
				<br />- ##formlabel_phone## : ##form_phone##
				<br />- ##formlabel_email## : ##form_email##
				<br />- ##formlabel_message## : ##form_message##
				<br />Nous vous répondrons dans les plus brefs délais.
				<br />Cordialement, 
				<br />%s
			"
			,Config::get("websiteTitle")
		);

		$objLanguage = new NCLanguage();
		$objLanguage->tstamp = time();
		$objLanguage->pid = $objMessage->id;
		$objLanguage->gateway_type = $objGateway->type;
		$objLanguage->language = "fr";
		$objLanguage->fallback = 1;
		$objLanguage->email_sender_name = Config::get("websiteTitle");
		$objLanguage->email_sender_address = "##admin_email##";
		$objLanguage->recipients = "##form_email##";
		$objLanguage->email_recipient_cc = "";
		$objLanguage->email_recipient_bcc = "";
		$objLanguage->email_replyTo = "";
		$objLanguage->email_subject = sprintf("Votre message sur %s", Config::get("websiteTitle"));
		$objLanguage->email_mode = "textAndHtml";
		$objLanguage->email_text = strip_tags($strHtml);
		$objLanguage->email_html = $strHtml;
		$objLanguage->email_external_images = "";
		$objLanguage->save();

		// Create the form
		$objForm = new FormModel();
		$objForm->tstamp = time();
		$objForm->title = "Formulaire de contact";
		$objForm->alias = \StringUtil::generateAlias($objForm->title);
		$objForm->jumpTo = $obJumpPage->id;
		$objForm->nc_notification = $objNotification->id;
		$objForm->save();

		// Create the form fields
		$objField = new FormFieldModel();
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

		$objField = new FormFieldModel();
		$objField->tstamp = time();
		$objField->pid = $objForm->id;
		$objField->sorting = 256;
		$objField->type = "text";
		$objField->name = "phone";
		$objField->label = "Votre téléphone :";
		$objField->rgxp = "phone";
		$objField->placeholder = "Saisissez votre numéro de téléphone";
		$objField->save();

		$objField = new FormFieldModel();
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

		$objField = new FormFieldModel();
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

		$objField = new FormFieldModel();
		$objField->tstamp = time();
		$objField->pid = $objForm->id;
		$objField->sorting = 640;
		$objField->type = "captcha";
		$objField->label = "Question de sécurité :";
		$objField->placeholder = "Veuillez répondre à la question de sécurité";
		$objField->save();

		$objField = new FormFieldModel();
		$objField->tstamp = time();
		$objField->pid = $objForm->id;
		$objField->sorting = 768;
		$objField->type = "submit";
		$objField->slabel = "Envoyer";
		$objField->save();

		// Create the content
		$objContent = new ContentModel();
		$objContent->tstamp = time();
		$objContent->pid = $objArticle->id;
		$objContent->ptable = "tl_article";
		$objContent->sorting = 128;
		$objContent->type = "form";
		$objContent->headline = serialize(["unit"=>"h1", "value"=>"Message envoyé !"]);
		$objContent->form = $objForm->id;
		$objContent->save();

		// And save stuff in config
		$this->updateConfig([
			"sgFormsInstall"=>1
			,"sgForm"=>$objForm->id
			,"sgFormPage"=>$objPage->id
			,"sgFormJumpPage"=>$obJumpPage->id
		]);

		// TODO 
		// Poursuivre le module quand le système de réponse aux forms sera en place
	}

	/**
	 * Reset the module
	 */
	public function reset(){
		$this->remove();
		$this->install();
	}

	/**
	 * Remove the module
	 */
	public function remove(){
		// Get the form
		$objForm = FormModel::findByPk(Config::get('sgForm'));

		// Delete all the form fields
		\Database::getInstance()->prepare("DELETE FROM tl_form_field WHERE pid = ?")->execute($objForm->id);

		// Get and delete the notification
		if($objNotification = NCNotification::findByPk($objForm->nc_notification))
			$objNotification->delete();

		// Get and delete the form page
		if($objPage = PageModel::findByPk(Config::get('sgFormPage')))
			$objPage->delete();

		// Get and delete the form jump page
		if($objPage = PageModel::findByPk(Config::get('sgFormJumpPage')))
			$objPage->delete();

		// Delete the form
		$objForm->delete();

		// And save stuff in config
		$this->updateConfig([
			"sgFormsInstall"=>""
			,"sgForm"=>""
			,"sgFormPage"=>""
			,"sgFormJumpPage"=>""
		]);
	}
}