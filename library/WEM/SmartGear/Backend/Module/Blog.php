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

use WEM\SmartGear\Backend\Block;
use WEM\SmartGear\Backend\BlockInterface;
use WEM\SmartGear\Backend\Util;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Blog extends Block implements BlockInterface
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
        $this->module = "blog";
        $this->icon = "cogs";
        $this->title = "SmartGear | Module | Blog";

        parent::__construct();
    }

    /**
     * Check Module Status
     * @return [String] [Template of the module check status]
     */
    public function getStatus()
    {
        if (!isset($this->bundles['ContaoNewsBundle'])) {
            $this->messages[] = ['class' => 'tl_error', 'text' => 'Le blog n\'est pas installé. Veuillez utiliser le <a href="{{env::/}}/contao-manager.phar.php" title="Contao Manager" target="_blank">Contao Manager</a> pour cela.'];

            $this->status = 0;
        } elseif (!$this->sgConfig['sgBlogInstall'] || 0 === \NewsArchiveModel::countById($this->sgConfig['sgBlogNewsArchive'])) {
            $this->messages[] = ['class' => 'tl_info', 'text' => 'Le blog est installé, mais pas configuré.'];
            $this->actions[] = ['action'=>'install', 'label'=>'Installer'];

            $this->status = 0;
        } else {
            $href = sprintf(
                'contao?do=smartgear&act=modal&type=%s&module=%s&function=%s&popup=1&rt=%s',
                $this->type,
                $this->module,
                "configure",
                \RequestToken::get()
            );
            $this->messages[] = ['class' => 'tl_confirm', 'text' => 'Le blog est installé et configuré.'];
            $this->actions[] = ['v'=>2, 'tag'=>'a', 'text'=>'Configurer', 'attrs'=>['href'=>$href, 'title'=>'Configurer le blog', 'class'=>'openSmartgearModal', 'data-title'=>'Configurer le blog']];
            $this->actions[] = ['action'=>'reset', 'label'=>'Réinitialiser'];
            $this->actions[] = ['action'=>'remove', 'label'=>'Supprimer'];

            $this->status = 1;
        }
    }

    /**
     * Display & Update Blog config
     */
    public function configure()
    {
        if (\Input::post('TL_WEM_AJAX')) {
            try {
                if (!\Input::post('config') || empty(\Input::post('config'))) {
                    throw new Exception("No data sent");
                }

                $blnUpdate = false;
                foreach (\Input::post('config') as $k => $v) {
                    if ($v != $this->sgConfig[$k]) {
                        $this->sgConfig[$k] = $v;
                        $blnUpdate = true;
                    }
                }

                if ($blnUpdate) {
                    Util::updateConfig($this->sgConfig);

                    $arrResponse = [
                        "toastr" => [
                            "status"=>"success"
                            ,"msg"=>"Configuration sauvegardée"
                        ]
                    ];
                } else {
                    $arrResponse = [
                        "toastr" => [
                            "status"=>"info"
                            ,"msg"=>"Pas de changements détectés"
                        ]
                    ];
                }
            } catch (Exception $e) {
                $arrResponse = ["status"=>"error", "msg"=>$e->getMessage(), "trace"=>$e->getTrace()];
            }

            // Add Request Token to JSON answer and return
            $arrResponse["rt"] = \RequestToken::get();
            echo json_encode($arrResponse);
            die;
        }

        $objTemplate = new \FrontendTemplate('be_wem_sg_install_modal_blog_configure');
        $objTemplate->config = $this->sgConfig;

        $objNewsArchives = \NewsArchiveModel::findAll();
        $arrNewsArchives = [];
        if ($objNewsArchives) {
            while ($objNewsArchives->next()) {
                $arrNewsArchives[$objNewsArchives->id] = [
                    "name"      => $objNewsArchives->title
                    ,"selected" => ($this->sgConfig['sgBlogNewsArchive'] == $objNewsArchives->id) ? true : false
                ];
            }
        }
        $objTemplate->newsarchives = $arrNewsArchives;

        $objListModules = \ModuleModel::findByType('newslist');
        $arrModules = [];
        if ($objListModules) {
            while ($objListModules->next()) {
                $arrModules[$objListModules->id] = [
                    "name"      => $objListModules->name
                    ,"selected" => ($this->sgConfig['sgBlogModuleList'] == $objListModules->id) ? true : false
                ];
            }
        }
        $objTemplate->listmodules = $arrModules;

        $objReaderModules = \ModuleModel::findByType('newsreader');
        $arrModules = [];
        if ($objReaderModules) {
            while ($objReaderModules->next()) {
                $arrModules[$objReaderModules->id] = [
                    "name"      => $objReaderModules->name
                    ,"selected" => ($this->sgConfig['sgBlogModuleReader'] == $objReaderModules->id) ? true : false
                ];
            }
        }
        $objTemplate->readermodules = $arrModules;

        $objListPages = \PageModel::findByPid($this->sgConfig['sgInstallRootPage']);
        $arrListPages = [];
        if ($objListPages) {
            while ($objListPages->next()) {
                $arrListPages[$objListPages->id] = [
                    "name"      => $objListPages->title
                    ,"selected" => ($this->sgConfig['sgBlogPageList'] == $objListPages->id) ? true : false
                ];
            }
        }
        $objTemplate->listpages = $arrListPages;

        $objReaderPages = \PageModel::findByPid($this->sgConfig['sgInstallRootPage']);
        $arrReaderPages = [];
        if ($objReaderPages) {
            while ($objReaderPages->next()) {
                $arrReaderPages[$objReaderPages->id] = [
                    "name"      => $objReaderPages->title
                    ,"selected" => ($this->sgConfig['sgBlogPageReader'] == $objReaderPages->id) ? true : false
                ];
            }
        }
        $objTemplate->readerpages = $arrReaderPages;
        
        return $objTemplate;
    }

    /**
     * Setup the module
     */
    public function install()
    {
        // Create the archive
        $objArchive = new \NewsArchiveModel();
        $objArchive->tstamp = time();
        $objArchive->title = "Blog";
        $objArchive->save();

        // Create the list module
        $objListModule = new \ModuleModel();
        $objListModule->tstamp = time();
        $objListModule->pid = $this->sgConfig["sgInstallTheme"];
        $objListModule->name = "Blog - List";
        $objListModule->type = "newslist";
        $objListModule->news_archives = serialize([0=>$objArchive->id]);
        $objListModule->numberOfItems = 0;
        $objListModule->news_featured = "all_items";
        $objListModule->perPage = 15;
        $objListModule->news_template = 'news_latest';
        $objListModule->skipFirst = 0;
        $objListModule->news_metaFields = serialize([0=>'date']);
        $objListModule->news_order = 'order_date_desc';
        $objListModule->save();

        // Create the reader module
        $objReaderModule = new \ModuleModel();
        $objReaderModule->tstamp = time();
        $objReaderModule->pid = $this->sgConfig["sgInstallTheme"];
        $objReaderModule->name = "Blog - Reader";
        $objReaderModule->type = "newsreader";
        $objReaderModule->news_archives = serialize([0=>$objArchive->id]);
        $objReaderModule->news_metaFields = serialize([0=>'date']);
        $objReaderModule->news_template = 'news_full';
        $objReaderModule->save();

        // Create the list page
        $intPage = Util::createPageWithModules("Blog", [$objListModule->id, $objReaderModule->id]);

        // Update the archive jumpTo
        $objArchive->jumpTo = $intPage;
        $objArchive->save();
        
        // And save stuff in config
        Util::updateConfig([
            "sgBlogInstall"=>1
            ,"sgBlogNewsArchive"=>$objArchive->id
            ,"sgBlogModuleList"=>$objListModule->id
            ,"sgBlogModuleReader"=>$objReaderModule->id
            ,"sgBlogPageList"=>$intPage
            ,"sgBlogPageReader"=>$intPage
        ]);

        // And return an explicit status with some instructions
        return [
            "toastr" => [
                "status"=>"success"
                ,"msg"=>"La configuration du blog a été effectuée avec succès."
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
        if ($objArchive = \NewsArchiveModel::findByPk($this->sgConfig["sgBlogNewsArchive"])) {
            $objArchive->delete();
        }
        if ($objModule = \ModuleModel::findByPk($this->sgConfig["sgBlogModuleList"])) {
            $objModule->delete();
        }
        if ($objModule = \ModuleModel::findByPk($this->sgConfig["sgBlogModuleReader"])) {
            $objModule->delete();
        }
        if ($objPage = \PageModel::findByPk($this->sgConfig["sgBlogPageList"])) {
            $objPage->delete();
        }
        if ($objPage = \PageModel::findByPk($this->sgConfig["sgBlogPageReader"])) {
            $objPage->delete();
        }

        Util::updateConfig([
            "sgBlogInstall"=>''
            ,"sgBlogNewsArchive"=>''
            ,"sgBlogModuleList"=>''
            ,"sgBlogModuleReader"=>''
            ,"sgBlogPageList"=>''
            ,"sgBlogPageReader"=>''
        ]);

        // And return an explicit status with some instructions
        return [
            "toastr" => [
                "status"=>"success"
                ,"msg"=>"La suppression du blog a été effectuée avec succès."
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
