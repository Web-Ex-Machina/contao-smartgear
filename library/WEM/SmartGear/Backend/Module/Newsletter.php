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

use WEM\SmartGear\Backend\Block;
use WEM\SmartGear\Backend\BlockInterface;
use WEM\SmartGear\Backend\Util;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Newsletter extends Block implements BlockInterface
{
	/**
	 * Module dependancies
	 * @var Array
	 */
	protected $require = ["core_core"];

	/**
	 * Constructor
	 */
	public function __construct(){
		$this->type = "module";
		$this->module = "newsletter";
		$this->icon = "cogs";
		$this->title = "SmartGear | Module | Newsletter";

		parent::__construct();
	}

	/**
	 * Check Module Status
	 * @return [String] [Template of the module check status]
	 */
	public function getStatus(){
		if(!isset($this->bundles['ContaoNewsletterBundle'])){
			$this->messages[] = ['class' => 'tl_error', 'text' => 'Le module Newsletter n\'est pas installé. Veuillez utiliser le <a href="{{env::/}}/contao-manager.phar.php" title="Contao Manager" target="_blank">Contao Manager</a> pour cela.'];
			$this->status = 0;
		}
		else if(!$this->sgConfig['sgNewsletterInstall'] || 0 === \NewsletterChannelModel::countById($this->sgConfig['sgNewsletterChannel'])){
			$this->messages[] = ['class' => 'tl_info', 'text' => 'Le module Newsletter est installé, mais pas configuré.'];
			$this->actions[] = ['action'=>'install', 'label'=>'Installer'];
			$this->status = 0;
		}
		else{
			$this->messages[] = ['class' => 'tl_confirm', 'text' => 'Le module Newsletter est installé et configuré.'];
			$this->actions[] = ['action'=>'reset', 'label'=>'Réinitialiser'];
			$this->actions[] = ['action'=>'remove', 'label'=>'Supprimer'];
			$this->status = 1;
		}
	}

	/**
	 * Setup the module
	 */
	public function install(){
		// Create the channel
		$objNewsletterChannel = new \NewsletterChannelModel();
		$objNewsletterChannel->tstamp = time();
		$objNewsletterChannel->title = "Newsletter";
		$objNewsletterChannel->template = "mail_newsletter";
		$objNewsletterChannel->sender = $this->sgConfig['adminEmail'];
		$objNewsletterChannel->senderName = $this->sgConfig['websiteTitle'];
		$objNewsletterChannel->save();

		// Create the subscribe module
		$objSubscribeModule = new \ModuleModel();
		$objSubscribeModule->tstamp = time();
		$objSubscribeModule->pid = $this->sgConfig["sgInstallTheme"];
		$objSubscribeModule->name = "Newsletter - Inscription";
		$objSubscribeModule->type = "subscribe";
		$objSubscribeModule->nl_channels = serialize([0=>$objNewsletterChannel->id]);
		$objSubscribeModule->nl_hideChannels = 1;
		$objSubscribeModule->nl_subscribe = "Votre inscription à notre newsletter est confirmée !";
		$objSubscribeModule->jumpTo = 0;
		$objSubscribeModule->save();

		// Create the unsubscribe module
		$objUnsubscribeModule = new \ModuleModel();
		$objUnsubscribeModule->tstamp = time();
		$objUnsubscribeModule->pid = $this->sgConfig["sgInstallTheme"];
		$objUnsubscribeModule->name = "Newsletter - Désinscription";
		$objUnsubscribeModule->type = "unsubscribe";
		$objUnsubscribeModule->nl_channels = serialize([0=>$objNewsletterChannel->id]);
		$objUnsubscribeModule->nl_hideChannels = 1;
		$objUnsubscribeModule->nl_subscribe = "Votre désinscription à notre newsletter est prise en compte.";
		$objUnsubscribeModule->jumpTo = 0;
		$objUnsubscribeModule->save();

		// Create the list module
		$objListModule = new \ModuleModel();
		$objListModule->tstamp = time();
		$objListModule->pid = $this->sgConfig["sgInstallTheme"];
		$objListModule->name = "Newsletter - Liste";
		$objListModule->type = "newsletterlist";
		$objListModule->nl_channels = serialize([0=>$objNewsletterChannel->id]);
		$objListModule->save();

		// Create the reader module
		$objReaderModule = new \ModuleModel();
		$objReaderModule->tstamp = time();
		$objReaderModule->pid = $this->sgConfig["sgInstallTheme"];
		$objReaderModule->name = "Newsletter - Lecteur";
		$objReaderModule->type = "newsletterreader";
		$objReaderModule->nl_channels = serialize([0=>$objNewsletterChannel->id]);
		$objReaderModule->save();
		
		// Create the pages
		$intListPage = Util::createPageWithModules("Newsletters", [$objListModule->id]);
		$intReaderPage = Util::createPageWithModules("Newsletters - Lecteur", [$objReaderModule->id], $intListPage);
		$intSubscribePage = Util::createPageWithModules("Newsletters - Inscription", [$objSubscribeModule->id], $intListPage);
		$intConfirmSubscribePage = Util::createPageWithText("Newsletters - Confirmation d'inscription", "Votre inscription est confirmée !", $intSubscribePage);
		$intUnsubscribePage = Util::createPageWithModules("Newsletters - Désinscription", [$objUnsubscribeModule->id], $intListPage);
		$intConfirmUnsubscribePage = Util::createPageWithText("Newsletters - Confirmation de désinscription", "Votre désinscription est prise en compte.", $intUnsubscribePage);

		// Update the newsletter channel
		$objNewsletterChannel->jumpTo = $intReaderPage;
		$objNewsletterChannel->save();

		// Update the subscribe module
		$objSubscribeModule->jumpTo = $intConfirmSubscribePage;
		$objSubscribeModule->save();

		// Update the unsubscribe module
		$objUnsubscribeModule->jumpTo = $intConfirmUnsubscribePage;
		$objUnsubscribeModule->save();

		// Create a newsletter template
		$objNewletter = new \NewsletterModel();
		$objNewletter->tstamp = time();
		$objNewletter->pid = $objNewsletterChannel->id;
		$objNewletter->subject = "Newsletter Exemple 01";
		$objNewletter->alias = \StringUtil::generateAlias("Newsletter Exemple 01");
		$objNewletter->content = file_get_contents("system/modules/wem-contao-smartgear/assets/examples/newsletter_1.html");
		$objNewletter->text = strip_tags($objNewletter->content);
		$objNewletter->save();

		// And save stuff in config
		Util::updateConfig([
			"sgNewsletterInstall"=>1
			,"sgNewsletterChannel"=>$objNewsletterChannel->id
			,"sgNewsletterPage"=>$intListPage
			,"sgNewsletterModules"=>serialize([$objSubscribeModule->id, $objUnsubscribeModule->id, $objListModule->id, $objReaderModule->id])
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
					,"args"	 => ["block-".$this->type."-".$this->module]
				]
			]
		];
	}

	/**
	 * Remove the module
	 */
	public function remove(){
		if($objNewsletterChannel = \NewsletterChannelModel::findByPk($this->sgConfig["sgNewsletterChannel"]))
			$objNewsletterChannel->delete();
		if($objPage = \PageModel::findByPk($this->sgConfig["sgNewsletterPage"]))
			$objPage->delete();
		if($arrModules = deserialize($this->sgConfig["sgNewsletterModules"]))
			foreach($arrModules as $intModule)
				if($objModule = \ModuleModel::findByPk($intModule))
					$objModule->delete();

		Util::updateConfig([
			"sgNewsletterInstall"=>''
			,"sgNewsletterChannel"=>''
			,"sgNewsletterPage"=>''
			,"sgNewsletterModules"=>''
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
					,"args"	 => ["block-".$this->type."-".$this->module]
				]
			]
		];
	}
}