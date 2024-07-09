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

namespace WEM\SmartgearBundle\Backend\Component\FormContact\ConfigurationStep;

use Contao\ArticleModel;
use Contao\BackendUser;
use Contao\ContentModel;
use Contao\CoreBundle\String\HtmlDecoder;
use Contao\FormModel;
use Contao\Input;
use Contao\PageModel;
use Contao\UserGroupModel;
use Exception;
use WEM\SmartgearBundle\Model\NotificationCenter\Language as NotificationLanguageModel;
use WEM\SmartgearBundle\Model\NotificationCenter\Message as NotificationMessageModel;
use WEM\SmartgearBundle\Model\NotificationCenter\Notification as NotificationModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Classes\Utils\ArticleUtil;
use WEM\SmartgearBundle\Classes\Utils\ContentUtil;
use WEM\SmartgearBundle\Classes\Utils\FormFieldUtil;
use WEM\SmartgearBundle\Classes\Utils\FormUtil;
use WEM\SmartgearBundle\Classes\Utils\Notification\NcNotificationMessageLanguageUtil;
use WEM\SmartgearBundle\Classes\Utils\Notification\NcNotificationMessageUtil;
use WEM\SmartgearBundle\Classes\Utils\Notification\NcNotificationUtil;
use WEM\SmartgearBundle\Classes\Utils\PageUtil;
// use WEM\SmartgearBundle\DataContainer\NotificationGateway;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Component\FormContact\FormContact as FormContactConfig;
use WEM\SmartgearBundle\Model\Module;
use WEM\SmartgearBundle\Classes\StringUtil;

class General extends ConfigurationStep
{

    protected string $language;

    public function __construct(
        string                         $module,
        string                         $type,
        protected TranslatorInterface  $translator,
        protected ConfigurationManager $configurationManager,
        protected CommandUtil          $commandUtil,
        protected HtmlDecoder          $htmlDecoder
    ) {
        parent::__construct($module, $type);
        $this->language = BackendUser::getInstance()->language;

        $this->title = $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.title', [], 'contao_default');
        /** @var FormContactConfig $config */
        $config = $this->configurationManager->load()->getSgFormContact();

        $this->addTextField('formContactTitle', $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.formContactTitle', [], 'contao_default'), $config->getSgFormContactTitle(), true);

        $this->addTextField('pageTitle', $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.pageTitle', [], 'contao_default'), $config->getSgPageTitle(), true);
    }

    /**
     * @throws Exception
     */
    public function isStepValid(): bool
    {
        // check if the step is correct
        if (null === Input::post('formContactTitle', null)) {
            throw new Exception($this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.formContactTitleMissing', [], 'contao_default'));
        }

        if (null === Input::post('pageTitle', null)) {
            throw new Exception($this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.pageTitleMissing', [], 'contao_default'));
        }

        return true;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
        $this->updateModuleConfiguration();

        $pages = $this->createPages();

        $articles = $this->createArticles($pages);
        $notification = $this->createNotificationGatewayNotification(Input::post('formContactTitle'));
        $notificationGatewayMessages = $this->createNotificationGatewayMessages($notification);
        $notificationGatewayMessagesLanguages = $this->createNotificationGatewayMessagesLanguages($notificationGatewayMessages, Input::post('formContactTitle'));
        $form = $this->createForm(Input::post('formContactTitle'), $pages['formSent'], $notification);
        $formInputs = $this->createFormInputs($form);

        $contents = $this->createContents($pages, $articles, $form);
        $this->updateModuleConfigurationAfterGenerations($pages, $articles, $contents, $notification, $notificationGatewayMessages, $notificationGatewayMessagesLanguages, $form, $formInputs);
        $this->updateUserGroups();
        $this->commandUtil->executeCmdPHP('cache:clear');
    }

    public function updateUserGroups(): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formContactConfig = $config->getSgFormContact();

        // retrieve the webmaster's group and update the permissions
        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupRedactors()), $formContactConfig);
        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupAdministrators()), $formContactConfig);
    }

    protected function updateModuleConfiguration(): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formContactConfig = $config->getSgFormContact();

        $formContactConfig
            ->setSgFormContactTitle(Input::post('formContactTitle'))
            ->setSgPageTitle(Input::post('pageTitle'))
            ->setSgArchived(false)
            ->setSgArchivedMode(FormContactConfig::ARCHIVE_MODE_EMPTY)
            ->setSgArchivedAt(0)
        ;
        $config->setSgFormContact($formContactConfig);

        $this->configurationManager->save($config);
    }

    protected function createPageForm(): PageModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formContactConfig = $config->getSgFormContact();

        $rootPage = PageModel::findById($config->getSgPageRoot());

        $page = PageModel::findById($formContactConfig->getSgPageForm());

        $page = PageUtil::createPageFormContact($formContactConfig->getSgPageTitle(), (int) $rootPage->id, array_merge([
            // $page = PageUtil::createPage($formContactConfig->getSgPageTitle(), 0, array_merge([
            //     'pid' => $rootPage->id,
            //     'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $rootPage->id),
            //     'type' => 'regular',
            //     'robots' => 'index,follow',
            //     'description' => $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.pageFormDescription', [$formContactConfig->getSgPageTitle(), $config->getSgWebsiteTitle()], 'contao_default'),
            //     'published' => 1,
        ], null !== $page ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setFormContactConfigKey('setSgPageForm', (int) $page->id);

        return $page;
    }

    protected function createPageFormSent(PageModel $pageFormContact): PageModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formContactConfig = $config->getSgFormContact();

        $rootPage = PageModel::findById($config->getSgPageRoot());

        $page = PageModel::findById($formContactConfig->getSgPageFormSent());

        $page = PageUtil::createPageFormContactSent($this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.pageFormSentTitle', [$formContactConfig->getSgPageTitle()], 'contao_default'), (int) $rootPage->id, array_merge([
            // $page = PageUtil::createPage($this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.pageFormSentTitle', [$formContactConfig->getSgPageTitle()], 'contao_default'), 0, array_merge([
            //     'pid' => $pageFormContact->id,
            //     'sorting' => PageUtil::getNextAvailablePageSortingByParentPage((int) $pageFormContact->id),
            //     'type' => 'regular',
            //     'robots' => 'noindex,nofollow',
            //     'description' => $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.pageFormSentDescription', [$formContactConfig->getSgPageTitle(), $config->getSgWebsiteTitle()], 'contao_default'),
            //     'published' => 1,
            //     'hide' => 1,
        ], null !== $page ? ['id' => $page->id, 'sorting' => $page->sorting] : []));

        $this->setFormContactConfigKey('setSgPageFormSent', (int) $page->id);

        return $page;
    }

    protected function createPages(): array
    {
        $pageForm = $this->createPageForm();

        return ['form' => $pageForm, 'formSent' => $this->createPageFormSent($pageForm)];
    }

    protected function createArticlePageForm(PageModel $page): ArticleModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formContactConfig = $config->getSgFormContact();

        $article = ArticleModel::findById($formContactConfig->getSgArticleForm());

        $article = ArticleUtil::createArticle($page, null !== $article ? ['id' => $article->id] : []);

        $this->setFormContactConfigKey('setSgArticleForm', (int) $article->id);

        return $article;
    }

    protected function createArticlePageFormSent(PageModel $page): ArticleModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formContactConfig = $config->getSgFormContact();

        $article = ArticleModel::findById($formContactConfig->getSgArticleFormSent());

        $article = ArticleUtil::createArticle($page, null !== $article ? ['id' => $article->id] : []);

        $this->setFormContactConfigKey('setSgArticleFormSent', (int) $article->id);

        return $article;
    }

    protected function createArticles(array $pages): array
    {
        return [
            'form' => $this->createArticlePageForm($pages['form']),
            'formSent' => $this->createArticlePageFormSent($pages['formSent']),
        ];
    }

    protected function createContentsPageForm(PageModel $page, ArticleModel $article, FormModel $form): array
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formContactConfig = $config->getSgFormContact();

        $headline = ContentModel::findOneById((int) $formContactConfig->getSgContentHeadlineArticleForm());
        $headline = ContentUtil::createContent($article, array_merge([
            'headline' => serialize(['unit' => 'h1', 'value' => $page->title]),
            'cssID' => ',sep-bottom',
        ], null !== $headline ? ['id' => $headline->id] : []));

        $this->setFormContactConfigKey('setSgContentHeadlineArticleForm', (int) $headline->id);

        $contentForm = ContentModel::findOneById((int) $formContactConfig->getSgContentFormArticleForm());
        $contentForm = ContentUtil::createContent($article, array_merge([
            'type' => 'form',
            'form' => $form->id,
        ], null !== $contentForm ? ['id' => $contentForm->id] : []));

        $this->setFormContactConfigKey('setSgContentFormArticleForm', (int) $contentForm->id);

        return ['headline' => $headline, 'form' => $contentForm];
    }

    protected function createContentsPageFormSent(PageModel $page, ArticleModel $article): array
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formContactConfig = $config->getSgFormContact();

        $headline = ContentModel::findOneById((int) $formContactConfig->getSgContentHeadlineArticleFormSent());
        $headline = ContentUtil::createContent($article, array_merge([
            'headline' => serialize(['unit' => 'h1', 'value' => $page->title]),
            'cssID' => ',sep-bottom',
        ], null !== $headline ? ['id' => $headline->id] : []));

        $this->setFormContactConfigKey('setSgContentHeadlineArticleFormSent', (int) $headline->id);

        $text = ContentModel::findOneById((int) $formContactConfig->getSgContentTextArticleFormSent());
        $text = ContentUtil::createContent($article, array_merge([
            'text' => $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.contentTextPageFormSent', [], 'contao_default'),
        ], null !== $text ? ['id' => $text->id] : []));

        $this->setFormContactConfigKey('setSgContentTextArticleFormSent', (int) $text->id);

        return ['headline' => $headline, 'text' => $text];
    }

    protected function createContents(array $pages, array $articles, FormModel $form): array
    {
        return [
            'form' => $this->createContentsPageForm($pages['form'], $articles['form'], $form),
            'formSent' => $this->createContentsPageFormSent($pages['formSent'], $articles['formSent']),
        ];
    }

    protected function createNotificationGatewayNotification(string $formTitle): NotificationModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formContactConfig = $config->getSgFormContact();

        $nc = NcNotificationUtil::createFormContactSentNotification($formTitle, $formContactConfig->getSgNotification() ? ['id' => $formContactConfig->getSgNotification()] : []);
        // $nc = NotificationModel::findOneById($formContactConfig->getSgNotification()) ?? new NotificationModel();
        // $nc->tstamp = time();
        // $nc->title = $formTitle;
        // $nc->type = 'core_form';
        // $nc->save();

        $this->setFormContactConfigKey('setSgNotification', (int) $nc->id);

        return $nc;
    }

    protected function createNotificationGatewayMessagesUser(NotificationModel $objNotification): NotificationMessageModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formContactConfig = $config->getSgFormContact();

        $nm = NcNotificationMessageUtil::createContactFormSentNotificationMessageUser((int) $objNotification->id, 'email', (int) $config->getSgNotificationGatewayEmail(), $formContactConfig->getSgNotificationMessageUser() ? ['id' => $formContactConfig->getSgNotificationMessageUser()] : []
        );
        // $nm = NotificationMessageModel::findOneById($formContactConfig->getSgNotificationMessageUser()) ?? new NotificationMessageModel();
        // $nm->pid = $objNotification->id;
        // $nm->gateway = $config->getSgNotificationGatewayEmail();
        // $nm->gateway_type = 'email';
        // $nm->tstamp = time();
        // $nm->title = $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.titleNotificationGatewayMessageUser', [], 'contao_default');
        // $nm->published = 1;
        // $nm->save();

        $this->setFormContactConfigKey('setSgNotificationMessageUser', (int) $nm->id);

        return $nm;
    }

    protected function createNotificationGatewayMessagesAdmin(NotificationModel $objNotification): NotificationMessageModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formContactConfig = $config->getSgFormContact();

        $nm = NcNotificationMessageUtil::createContactFormSentNotificationMessageAdmin((int) $objNotification->id, 'email', (int) $config->getSgNotificationGatewayEmail(), $formContactConfig->getSgNotificationMessageAdmin() ? ['id' => $formContactConfig->getSgNotificationMessageAdmin()] : []
        );
        // $nm = NotificationMessageModel::findOneById($formContactConfig->getSgNotificationMessageAdmin()) ?? new NotificationMessageModel();
        // $nm->pid = $gateway->id;
        // $nm->gateway = $config->getSgNotificationGatewayEmail();
        // $nm->gateway_type = 'email';
        // $nm->tstamp = time();
        // $nm->title = $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.titleNotificationGatewayMessageAdmin', [], 'contao_default');
        // $nm->published = 1;
        // $nm->save();

        $this->setFormContactConfigKey('setSgNotificationMessageAdmin', (int) $nm->id);

        return $nm;
    }

    protected function createNotificationGatewayMessages(NotificationModel $gateway): array
    {
        return [
            'user' => $this->createNotificationGatewayMessagesUser($gateway),
            'admin' => $this->createNotificationGatewayMessagesAdmin($gateway),
        ];
    }

    protected function createNotificationGatewayMessagesLanguagesUser(NotificationMessageModel $notificationMessage, string $formTitle): NotificationLanguageModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formContactConfig = $config->getSgFormContact();

        $nl = NcNotificationMessageLanguageUtil::createContactFormSentNotificationMessageUserLanguage(
            (int) $notificationMessage->id,
            $formTitle,
            $config->getSgWebsiteTitle(),
            'fr',
            true,
            $formContactConfig->getSgNotificationMessageUserLanguage() ? ['id' => $formContactConfig->getSgNotificationMessageUserLanguage()] : []
        );

        // $strText = file_get_contents(sprintf('%s/bundles/wemsmartgear/examples/formContact/%s/user_form.html', Util::getPublicOrWebDirectory(), $this->language));

        // $nl = NotificationLanguageModel::findOneById($formContactConfig->getSgNotificationMessageUserLanguage()) ?? new NotificationLanguageModel();
        // $nl->pid = $notificationMessage->id;
        // $nl->tstamp = time();
        // $nl->language = 'fr';
        // $nl->fallback = 1;
        // $nl->recipients = '##form_email##';
        // $nl->gateway_type = 'email';
        // $nl->email_sender_name = $config->getSgWebsiteTitle();
        // $nl->email_sender_address = '##admin_email##';
        // $nl->email_subject = $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.subjectNotificationGatewayMessageLanguageUser', [$config->getSgWebsiteTitle(), $formTitle], 'contao_default');
        // $nl->email_mode = 'textAndHtml';
        // $nl->email_text = $this->htmlDecoder->htmlToPlainText($strText, false);
        // $nl->email_html = $strText;
        // $nl->save();

        $this->setFormContactConfigKey('setSgNotificationMessageUserLanguage', (int) $nl->id);

        return $nl;
    }

    protected function createNotificationGatewayMessagesLanguagesAdmin(NotificationMessageModel $notificationMessage, string $formTitle): NotificationLanguageModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formContactConfig = $config->getSgFormContact();

        $nl = NcNotificationMessageLanguageUtil::createContactFormSentNotificationMessageAdminLanguage(
            (int) $notificationMessage->id,
            $formTitle,
            $config->getSgWebsiteTitle(),
            $config->getSgOwnerEmail(),
            'fr',
            true,
            $formContactConfig->getSgNotificationMessageAdminLanguage() ? ['id' => $formContactConfig->getSgNotificationMessageAdminLanguage()] : []
        );

        // $strText = file_get_contents(sprintf('%s/bundles/wemsmartgear/examples/formContact/%s/admin_form.html', Util::getPublicOrWebDirectory(), $this->language));

        // $nl = NotificationLanguageModel::findOneById($formContactConfig->getSgNotificationMessageAdminLanguage()) ?? new NotificationLanguageModel();
        // $nl->pid = $notificationMessage->id;
        // $nl->tstamp = time();
        // $nl->language = 'fr';
        // $nl->fallback = 1;
        // $nl->recipients = '##admin_email##';
        // $nl->gateway_type = 'email';
        // $nl->email_sender_name = $config->getSgWebsiteTitle();
        // $nl->email_sender_address = $config->getSgOwnerEmail();
        // $nl->email_subject = $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.subjectNotificationGatewayMessageLanguageUser', [$config->getSgWebsiteTitle(), $formTitle], 'contao_default');
        // $nl->email_mode = 'textAndHtml';
        // $nl->email_text = $this->htmlDecoder->htmlToPlainText($strText, false);
        // $nl->email_html = $strText;
        // $nl->email_replyTo = '##form_email##';
        // $nl->save();

        $this->setFormContactConfigKey('setSgNotificationMessageAdminLanguage', (int) $nl->id);

        return $nl;
    }

    protected function createNotificationGatewayMessagesLanguages(array $gatewayMessages, string $formTitle): array
    {
        return [
            'user' => $this->createNotificationGatewayMessagesLanguagesUser($gatewayMessages['user'], $formTitle),
            'admin' => $this->createNotificationGatewayMessagesLanguagesAdmin($gatewayMessages['admin'], $formTitle),
        ];
    }

    protected function createForm(string $formContactTitle, PageModel $page, NotificationModel $notification): FormModel
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formContactConfig = $config->getSgFormContact();

        $form = FormUtil::createFormFormContact($formContactTitle, (int) $page->id, (int) $notification->id, array_merge(
            $config->getSgFormDataManager()->getSgInstallComplete() ? ['storeViaFormDataManager' => true] : [],
            $formContactConfig->getSgFormContact() ? ['id' => $formContactConfig->getSgFormContact()] : []
        ));

        // $form = FormModel::findOneById($formContactConfig->getSgFormContact()) ?? new FormModel();
        // $form->title = $formContactTitle;
        // $form->alias = StringUtil::generateAlias($formContactTitle);
        // $form->jumpTo = $page->id;
        // $form->nc_notification = $notification->id;
        // $form->tstamp = time();
        // if ($config->getSgFormDataManager()->getSgInstallComplete()) {
        //     $form->storeViaFormDataManager = true;
        // }
        // $form->save();

        $this->setFormContactConfigKey('setSgFormContact', (int) $form->id);

        return $form;
    }

    protected function createFormInputs(FormModel $form): array
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formContactConfig = $config->getSgFormContact();

        $inputName = FormFieldUtil::createFormField((int) $form->id, array_merge([
            'sorting' => 128,
            'type' => 'text',
            'name' => 'name',
            'label' => $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.labelFormInputName', [], 'contao_default'),
            'placeholder' => $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.placeholderFormInputName', [], 'contao_default'),
            'mandatory' => 1,
        ],
        $formContactConfig->getSgFieldName() ? ['id' => $formContactConfig->getSgFieldName()] : [],
        $config->getSgFormDataManager()->getSgInstallComplete() ? ['contains_personal_data' => true] : []
        ));

        $this->setFormContactConfigKey('setSgFieldName', (int) $inputName->id);

        $inputEmail = FormFieldUtil::createFormField((int) $form->id, array_merge([
            'sorting' => 256,
            'type' => 'text',
            'name' => 'email',
            'label' => $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.labelFormInputEmail', [], 'contao_default'),
            'placeholder' => $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.placeholderFormInputEmail', [], 'contao_default'),
            'mandatory' => 1,
            'rgxp' => 'email',
            'tstamp' => time(),
        ],
        $formContactConfig->getSgFieldEmail() ? ['id' => $formContactConfig->getSgFieldEmail()] : []
        ));

        $this->setFormContactConfigKey('setSgFieldEmail', (int) $inputEmail->id);

        $inputMessage = FormFieldUtil::createFormField((int) $form->id, array_merge([
            'sorting' => 384,
            'type' => 'textarea',
            'name' => 'message',
            'label' => $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.labelFormInputMessage', [], 'contao_default'),
            'placeholder' => $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.placeholderFormInputMessage', [], 'contao_default'),
            'mandatory' => 1,
        ],
        $formContactConfig->getSgFieldMessage() ? ['id' => $formContactConfig->getSgFieldMessage()] : [],
        $config->getSgFormDataManager()->getSgInstallComplete() ? ['contains_personal_data' => true] : []
        ));

        $this->setFormContactConfigKey('setSgFieldMessage', (int) $inputMessage->id);

        $inputConsentDataTreatment = FormFieldUtil::createFormField((int) $form->id, array_merge([
            'sorting' => 512,
            'type' => 'checkbox',
            'name' => 'consent_data_treatment',
            'options' => serialize([['value' => 1, 'label' => $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.optionLabelFormInputConsentDataTreatment', [], 'contao_default')]]),
            'mandatory' => true,
        ],
        $formContactConfig->getSgFieldConsentDataTreatment() ? ['id' => $formContactConfig->getSgFieldConsentDataTreatment()] : []
        ));

        $this->setFormContactConfigKey('setSgFieldConsentDataTreatment', (int) $inputConsentDataTreatment->id);

        $inputConsentDataSave = FormFieldUtil::createFormField((int) $form->id, array_merge([
            'sorting' => 896,
            'type' => 'checkbox',
            'name' => 'consent_data_save',
            'options' => serialize([['value' => 1, 'label' => $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.optionLabelFormInputConsentDataSave', [], 'contao_default')]]),
            'mandatory' => 1,
            'invisible' => !$config->getSgFormDataManager()->getSgInstallComplete(),
        ],
        $formContactConfig->getSgFieldConsentDataSave() ? ['id' => $formContactConfig->getSgFieldConsentDataSave()] : []
        ));

        $this->setFormContactConfigKey('setSgFieldConsentDataSave', (int) $inputConsentDataSave->id);

        $inputCaptcha = FormFieldUtil::createFormField((int) $form->id, array_merge([
            'sorting' => 1152,
            'type' => 'captcha',
            'name' => 'captcha',
            'label' => $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.labelFormInputCaptcha', [], 'contao_default'),
            'mandatory' => 1,
        ],
        $formContactConfig->getSgFieldCaptcha() ? ['id' => $formContactConfig->getSgFieldCaptcha()] : []
        ));

        $this->setFormContactConfigKey('setSgFieldCaptcha', (int) $inputCaptcha->id);

        $inputSubmit = FormFieldUtil::createFormField((int) $form->id, array_merge([
            'sorting' => 1280,
            'type' => 'submit',
            'name' => 'submit',
            'slabel' => $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.labelFormInputSubmit', [], 'contao_default'),
            'mandatory' => 1,
        ],
        $formContactConfig->getSgFieldSubmit() ? ['id' => $formContactConfig->getSgFieldSubmit()] : []
        ));

        $this->setFormContactConfigKey('setSgFieldSubmit', (int) $inputSubmit->id);

        return ['name' => $inputName, 'email' => $inputEmail, 'message' => $inputMessage, 'consentDataTreatment' => $inputConsentDataTreatment, 'consentDataSave' => $inputConsentDataSave, 'captcha' => $inputCaptcha, 'submit' => $inputSubmit];
    }


    protected function updateModuleConfigurationAfterGenerations(array $pages, array $articles, array $contents, NotificationModel $notification, array $notificationGatewayMessages, array $notificationGatewayMessagesLanguages, FormModel $form, array $formInputs): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formContactConfig = $config->getSgFormContact();

        $formContactConfig
            ->setSgPageForm((int) $pages['form']->id)
            ->setSgPageFormSent((int) $pages['formSent']->id)
            ->setSgArticleForm((int) $articles['form']->id)
            ->setSgArticleFormSent((int) $articles['formSent']->id)
            ->setSgContentHeadlineArticleForm((int) $contents['form']['headline']->id)
            ->setSgContentFormArticleForm((int) $contents['form']['form']->id)
            ->setSgContentHeadlineArticleFormSent((int) $contents['formSent']['headline']->id)
            ->setSgContentTextArticleFormSent((int) $contents['formSent']['text']->id)
            ->setSgFormContact((int) $form->id)
            ->setSgFieldName((int) $formInputs['name']->id)
            ->setSgFieldEmail((int) $formInputs['email']->id)
            ->setSgFieldMessage((int) $formInputs['message']->id)
            ->setSgFieldConsentDataTreatment((int) $formInputs['consentDataTreatment']->id)
            ->setSgFieldConsentDataSave((int) $formInputs['consentDataSave']->id)
            ->setSgFieldCaptcha((int) $formInputs['captcha']->id)
            ->setSgFieldSubmit((int) $formInputs['submit']->id)
            ->setSgNotification((int) $notification->id)
            ->setSgNotificationMessageUser((int) $notificationGatewayMessages['user']->id)
            ->setSgNotificationMessageAdmin((int) $notificationGatewayMessages['admin']->id)
            ->setSgNotificationMessageUserLanguage((int) $notificationGatewayMessagesLanguages['user']->id)
            ->setSgNotificationMessageAdminLanguage((int) $notificationGatewayMessagesLanguages['admin']->id)
        ;

        $config->setSgFormContact($formContactConfig);

        $this->configurationManager->save($config);
    }

    protected function updateUserGroup(UserGroupModel $objUserGroup, FormContactConfig $formContactConfig): void
    {
        $userGroupManipulator = UserGroupModelUtil::create($objUserGroup);
        $userGroupManipulator
            ->addAllowedModules(['form'])
            ->addAllowedForms([$formContactConfig->getSgFormContact()])
            ->addAllowedFormFields(['text', 'textarea', 'captcha', 'submit'])
            ->addAllowedFieldsByTables(['tl_form', 'tl_form_field'])
            ->addAllowedPagemounts($formContactConfig->getContaoPagesIds())
            // ->addAllowedModules(Module::getTypesByIds($formContactConfig->getContaoModulesIds()))
        ;
        $objUserGroup = $userGroupManipulator->getUserGroup();
        $objUserGroup->formp = serialize(['create', 'delete']);
        $objUserGroup->save();
    }

    private function setFormContactConfigKey(string $key, int $value): void
    {
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();
        $formContactConfig = $config->getSgFormContact();

        $formContactConfig->{$key}($value);

        $config->setSgFormContact($formContactConfig);

        $this->configurationManager->save($config);
    }
}
