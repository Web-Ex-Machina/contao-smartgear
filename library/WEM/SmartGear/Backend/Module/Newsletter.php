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
use Contao\FrontendTemplate;
use Contao\NewsletterChannelModel;
use Contao\NewsletterModel;
use Contao\ModuleModel;
use Contao\PageModel;

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
	 * Check Module Status
	 * @return [String] [Template of the module check status]
	 */
	public function getStatus(){
		/*try{
			$objTemplate = new FrontendTemplate($strTemplate);
			$objTemplate->title = "SmartGear | Module | Newsletter";
			$objTemplate->module = "newsletter";
			$objTemplate->request = \Environment::get('request');
			$objTemplate->token = \RequestToken::get();
			$arrActions = array();
			$bundles = \System::getContainer()->getParameter('kernel.bundles');

			if(!isset($bundles['ContaoNewsletterBundle'])){
				$objTemplate->msgClass = 'tl_error';
				$objTemplate->msgText = 'Le module Newsletter n\'est pas installé. Veuillez utiliser le <a href="{{env::/}}/contao-manager.phar.php" title="Contao Manager" target="_blank">Contao Manager</a> pour cela.';
			} else if(!Config::get('sgNewsletterInstall') || 0 === NewsletterChannelModel::countById(Config::get('sgNewsletterChannel'))){
				$objTemplate->msgClass = 'tl_info';
				$objTemplate->msgText = 'Le module Newsletter est installé, mais pas configuré.';
				$arrActions[] = ['action'=>'install', 'label'=>'Installer'];
			} else {
				$objTemplate->msgClass = 'tl_confirm';
				$objTemplate->msgText = 'Le module Newsletter est installé et configuré.';
				$arrActions[] = ['action'=>'reset', 'label'=>'Réinitialiser'];
				$arrActions[] = ['action'=>'remove', 'label'=>'Supprimer'];
			}

			$objTemplate->actions = $arrActions;
		}
		catch(Exception $e){
			$objTemplate->isError = true;
			$objTemplate->error = $e->getMessage();
			$objTemplate->trace = $e->getTrace();
		}

		return $objTemplate->parse();*/
	}

	/**
	 * Setup the module
	 */
	public function install(){
		// Create the channel
		$objNewsletterChannel = new NewsletterChannelModel();
		$objNewsletterChannel->tstamp = time();
		$objNewsletterChannel->title = "Newsletter";
		$objNewsletterChannel->template = "mail_newsletter";
		$objNewsletterChannel->sender = Config::get('adminEmail');
		$objNewsletterChannel->senderName = Config::get('websiteTitle');
		$objNewsletterChannel->save();

		// Create the subscribe module
		$objSubscribeModule = new ModuleModel();
		$objSubscribeModule->tstamp = time();
		$objSubscribeModule->pid = Config::get("sgInstallTheme");
		$objSubscribeModule->name = "Newsletter - Inscription";
		$objSubscribeModule->type = "subscribe";
		$objSubscribeModule->nl_channels = serialize([0=>$objNewsletterChannel->id]);
		$objSubscribeModule->nl_hideChannels = 1;
		$objSubscribeModule->nl_subscribe = "Votre inscription à notre newsletter est confirmée !";
		$objSubscribeModule->jumpTo = 0;
		$objSubscribeModule->save();

		// Create the unsubscribe module
		$objUnsubscribeModule = new ModuleModel();
		$objUnsubscribeModule->tstamp = time();
		$objUnsubscribeModule->pid = Config::get("sgInstallTheme");
		$objUnsubscribeModule->name = "Newsletter - Désinscription";
		$objUnsubscribeModule->type = "unsubscribe";
		$objUnsubscribeModule->nl_channels = serialize([0=>$objNewsletterChannel->id]);
		$objUnsubscribeModule->nl_hideChannels = 1;
		$objUnsubscribeModule->nl_subscribe = "Votre désinscription à notre newsletter est prise en compte.";
		$objUnsubscribeModule->jumpTo = 0;
		$objUnsubscribeModule->save();

		// Create the list module
		$objListModule = new ModuleModel();
		$objListModule->tstamp = time();
		$objListModule->pid = Config::get("sgInstallTheme");
		$objListModule->name = "Newsletter - Liste";
		$objListModule->type = "newsletterlist";
		$objListModule->nl_channels = serialize([0=>$objNewsletterChannel->id]);
		$objListModule->save();

		// Create the reader module
		$objReaderModule = new ModuleModel();
		$objReaderModule->tstamp = time();
		$objReaderModule->pid = Config::get("sgInstallTheme");
		$objReaderModule->name = "Newsletter - Lecteur";
		$objReaderModule->type = "newsletterreader";
		$objReaderModule->nl_channels = serialize([0=>$objNewsletterChannel->id]);
		$objReaderModule->save();
		
		// Create the pages
		$intListPage = $this->createPageWithModule("Newsletters", $objListModule->id);
		$intReaderPage = $this->createPageWithModule("Newsletters - Lecteur", $objReaderModule->id, $intListPage);
		$intSubscribePage = $this->createPageWithModule("Newsletters - Inscription", $objSubscribeModule->id, $intListPage);
		$intConfirmSubscribePage = $this->createPageWithText("Newsletters - Confirmation d'inscription", "Votre inscription est confirmée !", $intSubscribePage);
		$intUnsubscribePage = $this->createPageWithModule("Newsletters - Désinscription", $objUnsubscribeModule->id, $intListPage);
		$intConfirmUnsubscribePage = $this->createPageWithText("Newsletters - Confirmation de désinscription", "Votre désinscription est prise en compte.", $intUnsubscribePage);

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
		$objNewletter = new NewsletterModel();
		$objNewletter->tstamp = time();
		$objNewletter->pid = $objNewsletterChannel->id;
		$objNewletter->subject = "Newsletter Exemple 01";
		$objNewletter->alias = \StringUtil::generateAlias("Newsletter Exemple 01");
		$objNewletter->content = file_get_contents("system/modules/wem-contao-smartgear/assets/examples/newsletter_1.html");
		$objNewletter->text = strip_tags($objNewletter->content);
		$objNewletter->save();

		// And save stuff in config
		$this->updateConfig([
			"sgNewsletterInstall"=>1
			,"sgNewsletterChannel"=>$objNewsletterChannel->id
			,"sgNewsletterPage"=>$intListPage
			,"sgNewsletterModules"=>serialize([$objSubscribeModule->id, $objUnsubscribeModule->id, $objListModule->id, $objReaderModule->id])
		]);
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
		if($objNewsletterChannel = NewsletterChannelModel::findByPk(Config::get("sgNewsletterChannel")))
			$objNewsletterChannel->delete();
		if($objPage = PageModel::findByPk(Config::get("sgNewsletterPage")))
			$objPage->delete();
		if($arrModules = deserialize(Config::get("sgNewsletterModules")))
			foreach($arrModules as $intModule)
				if($objModule = ModuleModel::findByPk($intModule))
					$objModule->delete();

		$this->updateConfig([
			"sgNewsletterInstall"=>''
			,"sgNewsletterChannel"=>''
			,"sgNewsletterPage"=>''
			,"sgNewsletterModules"=>''
		]);
	}
}