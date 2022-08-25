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

namespace WEM\SmartgearBundle\Backend\Component\FormContact\ConfigurationStep;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\CoreBundle\String\HtmlDecoder;
use Contao\FormFieldModel;
use Contao\FormModel;
use Contao\Input;
use Contao\PageModel;
use Contao\UserGroupModel;
use Exception;
use NotificationCenter\Model\Language as NotificationLanguageModel;
use NotificationCenter\Model\Message as NotificationMessageModel;
use NotificationCenter\Model\Notification as NotificationModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Command\Util as CommandUtil;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\UserGroupModelUtil;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Component\FormContact\FormContact as FormContactConfig;
use WEM\SmartgearBundle\Model\Module;
// use WEM\SmartgearBundle\DataContainer\NotificationGateway;
use WEM\UtilsBundle\Classes\StringUtil;

class General extends ConfigurationStep
{
    /** @var TranslatorInterface */
    protected $translator;
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var CommandUtil */
    protected $commandUtil;
    /** @var HtmlDecoder */
    protected $htmlDecoder;
    /** @var string */
    protected $language;

    public function __construct(
        string $module,
        string $type,
        TranslatorInterface $translator,
        ConfigurationManager $configurationManager,
        CommandUtil $commandUtil,
        HtmlDecoder $htmlDecoder
    ) {
        parent::__construct($module, $type);
        $this->translator = $translator;
        $this->configurationManager = $configurationManager;
        $this->commandUtil = $commandUtil;
        $this->htmlDecoder = $htmlDecoder;
        $this->language = \Contao\BackendUser::getInstance()->language;

        $this->title = $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.title', [], 'contao_default');
        /** @var FormContactConfig */
        $config = $this->configurationManager->load()->getSgFormContact();

        $this->addTextField('formContactTitle', $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.formContactTitle', [], 'contao_default'), $config->getSgFormContactTitle(), true);

        $this->addTextField('pageTitle', $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.pageTitle', [], 'contao_default'), $config->getSgPageTitle(), true);
    }

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

    protected function updateModuleConfiguration(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
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
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        $rootPage = PageModel::findById($config->getSgPageRoot());

        $page = PageModel::findById($formContactConfig->getSgPageForm());

        return Util::createPage($formContactConfig->getSgPageTitle(), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => ((int) $rootPage->sorting) + 128,
            'type' => 'regular',
            'robots' => 'index,follow',
            'description' => $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.pageFormDescription', [$formContactConfig->getSgPageTitle(), $config->getSgWebsiteTitle()], 'contao_default'),
            'published' => 1,
        ], null !== $page ? ['id' => $page->id] : []));
    }

    protected function createPageFormSent(): PageModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        $rootPage = PageModel::findById($config->getSgPageRoot());

        $page = PageModel::findById($formContactConfig->getSgPageFormSent());

        return Util::createPage($this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.pageFormSentTitle', [$formContactConfig->getSgPageTitle()], 'contao_default'), 0, array_merge([
            'pid' => $rootPage->id,
            'sorting' => ((int) $rootPage->sorting) + 128,
            'type' => 'regular',
            'robots' => 'index,follow',
            'description' => $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.pageFormSentDescription', [$formContactConfig->getSgPageTitle(), $config->getSgWebsiteTitle()], 'contao_default'),
            'published' => 1,
            'hide' => 1,
        ], null !== $page ? ['id' => $page->id] : []));
    }

    protected function createPages(): array
    {
        return ['form' => $this->createPageForm(), 'formSent' => $this->createPageFormSent()];
    }

    protected function createArticlePageForm(PageModel $page): ArticleModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        $article = ArticleModel::findById($formContactConfig->getSgArticleForm());

        return Util::createArticle($page, null !== $article ? ['id' => $article->id] : []);
    }

    protected function createArticlePageFormSent(PageModel $page): ArticleModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        $article = ArticleModel::findById($formContactConfig->getSgArticleFormSent());

        return Util::createArticle($page, null !== $article ? ['id' => $article->id] : []);
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
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        $headline = ContentModel::findOneById((int) $formContactConfig->getSgContentHeadlineArticleForm());
        $headline = Util::createContent($article, array_merge([
            'headline' => serialize(['unit' => 'h1', 'value' => $page->title]),
            'cssID' => ',sep-bottom',
        ], null !== $headline ? ['id' => $headline->id] : []));

        $contentForm = ContentModel::findOneById((int) $formContactConfig->getSgContentFormArticleForm());
        $contentForm = Util::createContent($article, array_merge([
            'type' => 'form',
            'form' => $form->id,
        ], null !== $contentForm ? ['id' => $contentForm->id] : []));

        return ['headline' => $headline, 'form' => $contentForm];
    }

    protected function createContentsPageFormSent(PageModel $page, ArticleModel $article): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        $headline = ContentModel::findOneById((int) $formContactConfig->getSgContentHeadlineArticleFormSent());
        $headline = Util::createContent($article, array_merge([
            'headline' => serialize(['unit' => 'h1', 'value' => $page->title]),
            'cssID' => ',sep-bottom',
        ], null !== $headline ? ['id' => $headline->id] : []));

        $text = ContentModel::findOneById((int) $formContactConfig->getSgContentTextArticleFormSent());
        $text = Util::createContent($article, array_merge([
            'text' => $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.contentTextPageFormSent', [], 'contao_default'),
        ], null !== $text ? ['id' => $text->id] : []));

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
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        $nc = NotificationModel::findOneById($formContactConfig->getSgNotification()) ?? new NotificationModel();
        $nc->tstamp = time();
        $nc->title = $formTitle;
        $nc->type = 'core_form';
        $nc->save();

        return $nc;
    }

    protected function createNotificationGatewayMessagesUser(NotificationModel $gateway): NotificationMessageModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        $nm = NotificationMessageModel::findOneById($formContactConfig->getSgNotificationMessageUser()) ?? new NotificationMessageModel();
        $nm->pid = $gateway->id;
        $nm->gateway = $config->getSgNotificationGatewayEmail();
        $nm->gateway_type = 'email';
        $nm->tstamp = time();
        $nm->title = $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.titleNotificationGatewayMessageUser', [], 'contao_default');
        $nm->published = 1;
        $nm->save();

        return $nm;
    }

    protected function createNotificationGatewayMessagesAdmin(NotificationModel $gateway): NotificationMessageModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        $nm = NotificationMessageModel::findOneById($formContactConfig->getSgNotificationMessageAdmin()) ?? new NotificationMessageModel();
        $nm->pid = $gateway->id;
        $nm->gateway = $config->getSgNotificationGatewayEmail();
        $nm->gateway_type = 'email';
        $nm->tstamp = time();
        $nm->title = $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.titleNotificationGatewayMessageAdmin', [], 'contao_default');
        $nm->published = 1;
        $nm->save();

        return $nm;
    }

    protected function createNotificationGatewayMessages(NotificationModel $gateway): array
    {
        return [
            'user' => $this->createNotificationGatewayMessagesUser($gateway),
            'admin' => $this->createNotificationGatewayMessagesAdmin($gateway),
        ];
    }

    protected function createNotificationGatewayMessagesLanguagesUser(NotificationMessageModel $gatewayMessage, string $formTitle): NotificationLanguageModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        $strText = file_get_contents(sprintf('%s/public/bundles/wemsmartgear/examples/formContact/%s/user_form.html', TL_ROOT, $this->language));

        $nl = NotificationLanguageModel::findOneById($formContactConfig->getSgNotificationMessageUserLanguage()) ?? new NotificationLanguageModel();
        $nl->pid = $gatewayMessage->id;
        $nl->tstamp = time();
        $nl->language = 'fr';
        $nl->fallback = 1;
        $nl->recipients = '##form_email##';
        $nl->gateway_type = 'email';
        $nl->email_sender_name = $config->getSgWebsiteTitle();
        $nl->email_sender_address = $config->getSgOwnerEmail();
        $nl->email_subject = $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.subjectNotificationGatewayMessageLanguageUser', [$config->getSgWebsiteTitle(), $formTitle], 'contao_default');
        $nl->email_mode = 'textAndHtml';
        $nl->email_text = $this->htmlDecoder->htmlToPlainText($strText, false);
        $nl->email_html = $strText;
        $nl->save();

        return $nl;
    }

    protected function createNotificationGatewayMessagesLanguagesAdmin(NotificationMessageModel $gatewayMessage, string $formTitle): NotificationLanguageModel
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        $strText = file_get_contents(sprintf('%s/public/bundles/wemsmartgear/examples/formContact/%s/admin_form.html', TL_ROOT, $this->language));

        $nl = NotificationLanguageModel::findOneById($formContactConfig->getSgNotificationMessageAdminLanguage()) ?? new NotificationLanguageModel();
        $nl->pid = $gatewayMessage->id;
        $nl->tstamp = time();
        $nl->language = 'fr';
        $nl->fallback = 1;
        $nl->recipients = '##admin_email##';
        $nl->gateway_type = 'email';
        $nl->email_sender_name = $config->getSgWebsiteTitle();
        $nl->email_sender_address = $config->getSgOwnerEmail();
        $nl->email_subject = $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.subjectNotificationGatewayMessageLanguageUser', [$config->getSgWebsiteTitle(), $formTitle], 'contao_default');
        $nl->email_mode = 'textAndHtml';
        $nl->email_text = $this->htmlDecoder->htmlToPlainText($strText, false);
        $nl->email_html = $strText;
        $nl->email_replyTo = '##form_email##';
        $nl->save();

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
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        $form = FormModel::findOneById($formContactConfig->getSgFormContact()) ?? new FormModel();
        $form->title = $formContactTitle;
        $form->alias = StringUtil::generateAlias($formContactTitle);
        $form->jumpTo = $page->id;
        $form->nc_notification = $notification->id;
        $form->tstamp = time();
        $form->save();

        return $form;
    }

    protected function createFormInputs(FormModel $form): array
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        $inputName = FormFieldModel::findOneById($formContactConfig->getSgFieldName()) ?? new FormFieldModel();
        $inputName->pid = $form->id;
        $inputName->sorting = 128;
        $inputName->type = 'text';
        $inputName->name = 'name';
        $inputName->label = $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.labelFormInputName', [], 'contao_default');
        $inputName->placeholder = $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.placeholderFormInputName', [], 'contao_default');
        $inputName->mandatory = 1;
        $inputName->tstamp = time();
        $inputName->save();

        $inputEmail = FormFieldModel::findOneById($formContactConfig->getSgFieldEmail()) ?? new FormFieldModel();
        $inputEmail->pid = $form->id;
        $inputEmail->sorting = 128;
        $inputEmail->type = 'text';
        $inputEmail->name = 'email';
        $inputEmail->label = $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.labelFormInputEmail', [], 'contao_default');
        $inputEmail->placeholder = $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.placeholderFormInputEmail', [], 'contao_default');
        $inputEmail->mandatory = 1;
        $inputEmail->rgxp = 'email';
        $inputEmail->tstamp = time();
        $inputEmail->save();

        $inputMessage = FormFieldModel::findOneById($formContactConfig->getSgFieldMessage()) ?? new FormFieldModel();
        $inputMessage->pid = $form->id;
        $inputMessage->sorting = 128;
        $inputMessage->type = 'textarea';
        $inputMessage->name = 'message';
        $inputMessage->label = $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.labelFormInputMessage', [], 'contao_default');
        $inputMessage->placeholder = $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.placeholderFormInputMessage', [], 'contao_default');
        $inputMessage->mandatory = 1;
        $inputMessage->tstamp = time();
        $inputMessage->save();

        $inputCaptcha = FormFieldModel::findOneById($formContactConfig->getSgFieldCaptcha()) ?? new FormFieldModel();
        $inputCaptcha->pid = $form->id;
        $inputCaptcha->sorting = 128;
        $inputCaptcha->type = 'captcha';
        $inputCaptcha->name = 'captcha';
        $inputCaptcha->label = $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.labelFormInputCaptcha', [], 'contao_default');
        $inputCaptcha->mandatory = 1;
        $inputCaptcha->tstamp = time();
        $inputCaptcha->save();

        $inputSubmit = FormFieldModel::findOneById($formContactConfig->getSgFieldSubmit()) ?? new FormFieldModel();
        $inputSubmit->pid = $form->id;
        $inputSubmit->sorting = 128;
        $inputSubmit->type = 'submit';
        $inputSubmit->name = 'submit';
        $inputSubmit->slabel = $this->translator->trans('WEMSG.FORMCONTACT.INSTALL_GENERAL.labelFormInputSubmit', [], 'contao_default');
        $inputSubmit->mandatory = 1;
        $inputSubmit->tstamp = time();
        $inputSubmit->save();

        return ['name' => $inputName, 'email' => $inputEmail, 'message' => $inputMessage, 'captcha' => $inputCaptcha, 'submit' => $inputSubmit];
    }

    protected function updateModuleConfigurationAfterGenerations(array $pages, array $articles, array $contents, NotificationModel $notification, array $notificationGatewayMessages, array $notificationGatewayMessagesLanguages, FormModel $form, array $formInputs): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
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

    protected function updateUserGroups(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        /** @var FormContactConfig */
        $formContactConfig = $config->getSgFormContact();

        // retrieve the webmaster's group and update the permissions
        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupRedactors()), $formContactConfig);
        $this->updateUserGroup(UserGroupModel::findOneById($config->getSgUserGroupAdministrators()), $formContactConfig);
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
}
