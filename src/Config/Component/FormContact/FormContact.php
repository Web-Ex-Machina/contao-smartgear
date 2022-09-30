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

namespace WEM\SmartgearBundle\Config\Component\FormContact;

use WEM\SmartgearBundle\Classes\Config\ConfigModuleInterface;

class FormContact implements ConfigModuleInterface
{
    public const ARCHIVE_MODE_EMPTY = '';
    public const ARCHIVE_MODE_ARCHIVE = 'archive';
    public const ARCHIVE_MODE_KEEP = 'keep';
    public const ARCHIVE_MODE_DELETE = 'delete';
    public const ARCHIVE_MODES_ALLOWED = [
        self::ARCHIVE_MODE_EMPTY,
        self::ARCHIVE_MODE_ARCHIVE,
        self::ARCHIVE_MODE_KEEP,
        self::ARCHIVE_MODE_DELETE,
    ];
    public const DEFAULT_PAGE_TITLE = 'Contact';
    public const DEFAULT_FEED_TITLE = 'Contact';
    public const DEFAULT_ARCHIVE_MODE = self::ARCHIVE_MODE_EMPTY;

    /** @var bool */
    protected $sgInstallComplete = false;
    /** @var string */
    protected $sgFormContactTitle = self::DEFAULT_FEED_TITLE;
    /** @var string */
    protected $sgPageTitle = self::DEFAULT_PAGE_TITLE;
    /** @var int */
    protected $sgPageForm;
    /** @var int */
    protected $sgPageFormSent;
    /** @var int */
    protected $sgArticleForm;
    /** @var int */
    protected $sgArticleFormSent;
    /** @var int */
    protected $sgContentHeadlineArticleForm;
    /** @var int */
    protected $sgContentFormArticleForm;
    /** @var int */
    protected $sgContentHeadlineArticleFormSent;
    /** @var int */
    protected $sgContentTextArticleFormSent;
    /** @var int */
    protected $sgFormContact;
    /** @var int */
    protected $sgFieldName;
    /** @var int */
    protected $sgFieldEmail;
    /** @var int */
    protected $sgFieldMessage;
    /** @var int */
    protected $sgFieldCaptcha;
    /** @var int */
    protected $sgFieldSubmit;
    /** @var int */
    protected $sgNotification;
    /** @var int */
    protected $sgNotificationMessageUser;
    /** @var int */
    protected $sgNotificationMessageAdmin;
    /** @var int */
    protected $sgNotificationMessageUserLanguage;
    /** @var int */
    protected $sgNotificationMessageAdminLanguage;
    /** @var bool */
    protected $sgArchived = false;
    /** @var int */
    protected $sgArchivedAt = 0;
    /** @var string */
    protected $sgArchivedMode = self::DEFAULT_ARCHIVE_MODE;

    public function reset(): self
    {
        $this->setSgInstallComplete(false)
            ->setSgFormContactTitle(self::DEFAULT_FEED_TITLE)
            ->setSgPageTitle(self::DEFAULT_PAGE_TITLE)
            ->setSgPageForm(null)
            ->setSgPageFormSent(null)
            ->setSgArticleForm(null)
            ->setSgArticleFormSent(null)
            ->setSgContentHeadlineArticleForm(null)
            ->setSgContentFormArticleForm(null)
            ->setSgContentHeadlineArticleFormSent(null)
            ->setSgContentTextArticleFormSent(null)
            ->setSgFormContact(null)
            ->setSgFieldName(null)
            ->setSgFieldEmail(null)
            ->setSgFieldMessage(null)
            ->setSgFieldCaptcha(null)
            ->setSgFieldSubmit(null)
            ->setSgNotification(null)
            ->setSgNotificationMessageUser(null)
            ->setSgNotificationMessageAdmin(null)
            ->setSgNotificationMessageUserLanguage(null)
            ->setSgNotificationMessageAdminLanguage(null)
            ->setSgArchived(false)
            ->setSgArchivedAt(0)
            ->setSgArchivedMode(self::DEFAULT_ARCHIVE_MODE)
        ;

        return $this;
    }

    public function import(\stdClass $json): self
    {
        $this->setSgInstallComplete($json->installComplete ?? false)
            ->setSgFormContactTitle($json->faq_title ?? self::DEFAULT_FEED_TITLE)
            ->setSgPageTitle($json->page_title ?? self::DEFAULT_PAGE_TITLE)
            ->setSgPageForm($json->contao->pages->form ?? null)
            ->setSgPageFormSent($json->contao->pages->formSent ?? null)
            ->setSgArticleForm($json->contao->articles->form ?? null)
            ->setSgArticleFormSent($json->contao->articles->formSent ?? null)
            ->setSgContentHeadlineArticleForm($json->contao->contents->articleForm->headline ?? null)
            ->setSgContentFormArticleForm($json->contao->contents->articleForm->form ?? null)
            ->setSgContentHeadlineArticleFormSent($json->contao->contents->articleFormSent->headline ?? null)
            ->setSgContentTextArticleFormSent($json->contao->contents->articleFormSent->text ?? null)
            ->setSgFormContact($json->contao->formContact ?? null)
            ->setSgFieldName($json->contao->fields->name ?? null)
            ->setSgFieldEmail($json->contao->fields->email ?? null)
            ->setSgFieldMessage($json->contao->fields->message ?? null)
            ->setSgFieldCaptcha($json->contao->fields->captcha ?? null)
            ->setSgFieldSubmit($json->contao->fields->submit ?? null)
            ->setSgNotification($json->contao->notification ?? null)
            ->setSgNotificationMessageUser($json->contao->notificationMessages->user ?? null)
            ->setSgNotificationMessageAdmin($json->contao->notificationMessages->admin ?? null)
            ->setSgNotificationMessageUserLanguage($json->contao->notificationMessagesLanguages->user ?? null)
            ->setSgNotificationMessageAdminLanguage($json->contao->notificationMessagesLanguages->admin ?? null)
            ->setSgArchived($json->archived->status ?? false)
            ->setSgArchivedAt($json->archived->at ?? 0)
            ->setSgArchivedMode($json->archived->mode ?? self::DEFAULT_ARCHIVE_MODE)
        ;

        return $this;
    }

    public function export(): \stdClass
    {
        $json = new \stdClass();
        $json->installComplete = $this->getSgInstallComplete();

        $json->faq_title = $this->getSgFormContactTitle();
        $json->page_title = $this->getSgPageTitle();

        $json->contao = new \stdClass();
        $json->contao->notification = $this->getSgNotification();
        $json->contao->formContact = $this->getSgFormContact();

        $json->contao->pages = new \stdClass();
        $json->contao->pages->form = $this->getSgPageForm();
        $json->contao->pages->formSent = $this->getSgPageFormSent();

        $json->contao->articles = new \stdClass();
        $json->contao->articles->form = $this->getSgArticleForm();
        $json->contao->articles->formSent = $this->getSgArticleFormSent();

        $json->contao->contents = new \stdClass();
        $json->contao->contents->articleForm = new \stdClass();
        $json->contao->contents->articleForm->headline = $this->getSgContentHeadlineArticleForm();
        $json->contao->contents->articleForm->form = $this->getSgContentFormArticleForm();

        $json->contao->contents->articleFormSent = new \stdClass();
        $json->contao->contents->articleFormSent->headline = $this->getSgContentHeadlineArticleFormSent();
        $json->contao->contents->articleFormSent->text = $this->getSgContentTextArticleFormSent();

        $json->contao->fields = new \stdClass();
        $json->contao->fields->name = $this->getSgFieldName();
        $json->contao->fields->email = $this->getSgFieldEmail();
        $json->contao->fields->message = $this->getSgFieldMessage();
        $json->contao->fields->captcha = $this->getSgFieldCaptcha();
        $json->contao->fields->submit = $this->getSgFieldSubmit();

        $json->contao->notificationMessages = new \stdClass();
        $json->contao->notificationMessages->user = $this->getSgNotificationMessageUser();
        $json->contao->notificationMessages->admin = $this->getSgNotificationMessageAdmin();

        $json->contao->notificationMessagesLanguages = new \stdClass();
        $json->contao->notificationMessagesLanguages->user = $this->getSgNotificationMessageUserLanguage();
        $json->contao->notificationMessagesLanguages->admin = $this->getSgNotificationMessageAdminLanguage();

        $json->archived = new \stdClass();
        $json->archived->status = $this->getSgArchived();
        $json->archived->at = $this->getSgArchivedAt();
        $json->archived->mode = $this->getSgArchivedMode();

        return $json;
    }

    public function getContaoModulesIds(): array
    {
        if (!$this->getSgInstallComplete()
        && (\in_array($this->getSgArchivedMode(), [self::ARCHIVE_MODE_EMPTY, self::ARCHIVE_MODE_DELETE], true))
        ) {
            return [];
        }

        return [];
    }

    public function getContaoPagesIds(): array
    {
        if (!$this->getSgInstallComplete()
        && (\in_array($this->getSgArchivedMode(), [self::ARCHIVE_MODE_EMPTY, self::ARCHIVE_MODE_DELETE], true))
        ) {
            return [];
        }

        return [$this->getSgPageForm(), $this->getSgPageFormSent()];
    }

    public function getContaoContentsIds(): array
    {
        if (!$this->getSgInstallComplete()
        && (\in_array($this->getSgArchivedMode(), [self::ARCHIVE_MODE_EMPTY, self::ARCHIVE_MODE_DELETE], true))
        ) {
            return [];
        }

        return [
            $this->getSgContentHeadlineArticleForm(),
            $this->getSgContentFormArticleForm(),
            $this->getSgContentHeadlineArticleFormSent(),
            $this->getSgContentTextArticleFormSent(),
        ];
    }

    public function getContaoArticlesIds(): array
    {
        if (!$this->getSgInstallComplete()
        && (\in_array($this->getSgArchivedMode(), [self::ARCHIVE_MODE_EMPTY, self::ARCHIVE_MODE_DELETE], true))
        ) {
            return [];
        }

        return [$this->getSgArticleForm(), $this->getSgArticleFormSent()];
    }

    public function getContaoFoldersIds(): array
    {
        return [];
    }

    public function getContaoUsersIds(): array
    {
        return [];
    }

    public function getContaoUserGroupsIds(): array
    {
        return [];
    }

    public function getContaoMembersIds(): array
    {
        return [];
    }

    public function getContaoMemberGroupsIds(): array
    {
        return [];
    }

    public function resetContaoModulesIds(): void
    {
    }

    public function resetContaoPagesIds(): void
    {
        $this->setSgPageForm(null);
        $this->setSgPageFormSent(null);
    }

    public function resetContaoContentsIds(): void
    {
        $this->setSgContentFormArticleForm(null);
        $this->setSgContentHeadlineArticleForm(null);
        $this->setSgContentHeadlineArticleFormSent(null);
        $this->setSgContentTextArticleFormSent(null);
    }

    public function resetContaoArticlesIds(): void
    {
        $this->setSgArticleForm(null);
        $this->setSgArticleFormSent(null);
    }

    public function resetContaoFoldersIds(): void
    {
    }

    public function resetContaoUsersIds(): void
    {
    }

    public function resetContaoUserGroupsIds(): void
    {
    }

    public function resetContaoMembersIds(): void
    {
    }

    public function resetContaoMemberGroupsIds(): void
    {
    }

    public function getSgInstallComplete(): bool
    {
        return $this->sgInstallComplete;
    }

    public function setSgInstallComplete(bool $sgInstallComplete): self
    {
        $this->sgInstallComplete = $sgInstallComplete;

        return $this;
    }

    public function getSgArchived(): bool
    {
        return $this->sgArchived;
    }

    public function setSgArchived(bool $sgArchived): self
    {
        $this->sgArchived = $sgArchived;

        return $this;
    }

    public function getSgArchivedAt(): int
    {
        return $this->sgArchivedAt;
    }

    public function setSgArchivedAt(int $sgArchivedAt): self
    {
        $this->sgArchivedAt = $sgArchivedAt;

        return $this;
    }

    public function getSgArchivedMode(): string
    {
        return $this->sgArchivedMode;
    }

    public function setSgArchivedMode(string $sgArchivedMode): self
    {
        if (!\in_array($sgArchivedMode, static::ARCHIVE_MODES_ALLOWED, true)) {
            throw new \InvalidArgumentException(sprintf('Invalid archive mode "%s" given', $sgArchivedMode));
        }
        $this->sgArchivedMode = $sgArchivedMode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSgFormContactTitle(): string
    {
        return $this->sgFormContactTitle;
    }

    /**
     * @param mixed $sgFormContactTitle
     */
    public function setSgFormContactTitle(string $sgFormContactTitle): self
    {
        $this->sgFormContactTitle = $sgFormContactTitle;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSgPageTitle(): string
    {
        return $this->sgPageTitle;
    }

    /**
     * @param mixed $sgPageTitle
     */
    public function setSgPageTitle(string $sgPageTitle): self
    {
        $this->sgPageTitle = $sgPageTitle;

        return $this;
    }

    public function getSgPageForm()
    {
        return $this->sgPageForm;
    }

    public function setSgPageForm(?int $sgPageForm): self
    {
        $this->sgPageForm = $sgPageForm;

        return $this;
    }

    public function getSgPageFormSent(): ?int
    {
        return $this->sgPageFormSent;
    }

    public function setSgPageFormSent(?int $sgPageFormSent): self
    {
        $this->sgPageFormSent = $sgPageFormSent;

        return $this;
    }

    public function getSgArticleForm(): ?int
    {
        return $this->sgArticleForm;
    }

    public function setSgArticleForm(?int $sgArticleForm): self
    {
        $this->sgArticleForm = $sgArticleForm;

        return $this;
    }

    public function getSgArticleFormSent(): ?int
    {
        return $this->sgArticleFormSent;
    }

    public function setSgArticleFormSent(?int $sgArticleFormSent): self
    {
        $this->sgArticleFormSent = $sgArticleFormSent;

        return $this;
    }

    public function getSgContentHeadlineArticleForm(): ?int
    {
        return $this->sgContentHeadlineArticleForm;
    }

    public function setSgContentHeadlineArticleForm(?int $sgContentHeadlineArticleForm): self
    {
        $this->sgContentHeadlineArticleForm = $sgContentHeadlineArticleForm;

        return $this;
    }

    public function getSgContentFormArticleForm(): ?int
    {
        return $this->sgContentFormArticleForm;
    }

    public function setSgContentFormArticleForm(?int $sgContentFormArticleForm): self
    {
        $this->sgContentFormArticleForm = $sgContentFormArticleForm;

        return $this;
    }

    public function getSgContentHeadlineArticleFormSent(): ?int
    {
        return $this->sgContentHeadlineArticleFormSent;
    }

    public function setSgContentHeadlineArticleFormSent(?int $sgContentHeadlineArticleFormSent): self
    {
        $this->sgContentHeadlineArticleFormSent = $sgContentHeadlineArticleFormSent;

        return $this;
    }

    public function getSgContentTextArticleFormSent(): ?int
    {
        return $this->sgContentTextArticleFormSent;
    }

    public function setSgContentTextArticleFormSent(?int $sgContentTextArticleFormSent): self
    {
        $this->sgContentTextArticleFormSent = $sgContentTextArticleFormSent;

        return $this;
    }

    public function getSgFormContact(): ?int
    {
        return $this->sgFormContact;
    }

    public function setSgFormContact(?int $sgFormContact): self
    {
        $this->sgFormContact = $sgFormContact;

        return $this;
    }

    public function getSgFieldName(): ?int
    {
        return $this->sgFieldName;
    }

    public function setSgFieldName(?int $sgFieldName): self
    {
        $this->sgFieldName = $sgFieldName;

        return $this;
    }

    public function getSgFieldEmail(): ?int
    {
        return $this->sgFieldEmail;
    }

    public function setSgFieldEmail(?int $sgFieldEmail): self
    {
        $this->sgFieldEmail = $sgFieldEmail;

        return $this;
    }

    public function getSgFieldMessage(): ?int
    {
        return $this->sgFieldMessage;
    }

    public function setSgFieldMessage(?int $sgFieldMessage): self
    {
        $this->sgFieldMessage = $sgFieldMessage;

        return $this;
    }

    public function getSgFieldCaptcha(): ?int
    {
        return $this->sgFieldCaptcha;
    }

    public function setSgFieldCaptcha(?int $sgFieldCaptcha): self
    {
        $this->sgFieldCaptcha = $sgFieldCaptcha;

        return $this;
    }

    public function getSgFieldSubmit(): ?int
    {
        return $this->sgFieldSubmit;
    }

    public function setSgFieldSubmit(?int $sgFieldSubmit): self
    {
        $this->sgFieldSubmit = $sgFieldSubmit;

        return $this;
    }

    public function getSgNotification(): ?int
    {
        return $this->sgNotification;
    }

    public function setSgNotification(?int $sgNotification): self
    {
        $this->sgNotification = $sgNotification;

        return $this;
    }

    public function getSgNotificationMessageUser(): ?int
    {
        return $this->sgNotificationMessageUser;
    }

    public function setSgNotificationMessageUser(?int $sgNotificationMessageUser): self
    {
        $this->sgNotificationMessageUser = $sgNotificationMessageUser;

        return $this;
    }

    public function getSgNotificationMessageAdmin(): ?int
    {
        return $this->sgNotificationMessageAdmin;
    }

    public function setSgNotificationMessageAdmin(?int $sgNotificationMessageAdmin): self
    {
        $this->sgNotificationMessageAdmin = $sgNotificationMessageAdmin;

        return $this;
    }

    public function getSgNotificationMessageUserLanguage(): ?int
    {
        return $this->sgNotificationMessageUserLanguage;
    }

    public function setSgNotificationMessageUserLanguage(?int $sgNotificationMessageUserLanguage): self
    {
        $this->sgNotificationMessageUserLanguage = $sgNotificationMessageUserLanguage;

        return $this;
    }

    public function getSgNotificationMessageAdminLanguage(): ?int
    {
        return $this->sgNotificationMessageAdminLanguage;
    }

    public function setSgNotificationMessageAdminLanguage(?int $sgNotificationMessageAdminLanguage): self
    {
        $this->sgNotificationMessageAdminLanguage = $sgNotificationMessageAdminLanguage;

        return $this;
    }
}
