<?php

declare(strict_types=1);

namespace WEM\SmartgearBundle\Classes\Utils\Notification;

use Terminal42\NotificationCenterBundle\NotificationType\NotificationTypeInterface;
use Terminal42\NotificationCenterBundle\Token\Definition\EmailTokenDefinition;
use Terminal42\NotificationCenterBundle\Token\Definition\Factory\TokenDefinitionFactoryInterface;
use Terminal42\NotificationCenterBundle\Token\Definition\FileTokenDefinition;
use Terminal42\NotificationCenterBundle\Token\Definition\HtmlTokenDefinition;
use Terminal42\NotificationCenterBundle\Token\Definition\TextTokenDefinition;
use Terminal42\NotificationCenterBundle\Token\Definition\TokenDefinitionInterface;

class TicketCreationNotificationType implements NotificationTypeInterface
{
    public const NAME = 'ticket_creation';

    public function __construct(private readonly TokenDefinitionFactoryInterface $factory)
    {
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getTokenDefinitions(): array
    {
        return [
            $this->factory->create(EmailTokenDefinition::class, 'email_sender_name', 'form.email_sender_name'),
            $this->factory->create(EmailTokenDefinition::class, 'email_sender_address', 'form.email_sender_address'),
            $this->factory->create(TextTokenDefinition::class, 'email_subject', 'form.email_subject'),
            $this->factory->create(EmailTokenDefinition::class, 'recipients', 'form.recipients'),
            $this->factory->create(HtmlTokenDefinition::class, 'email_text', 'form.email_text'),
            $this->factory->create(TextTokenDefinition::class, 'email_html', 'form.email_html'),
            $this->factory->create(EmailTokenDefinition::class, 'email_replyTo', 'form.email_replyTo'),
            $this->factory->create(FileTokenDefinition::class, 'attachment_tokens', 'form.attachment_tokens'),

        ];
    }


    private function createDefinition(string $definitionClass, string $tokenName): TokenDefinitionInterface
    {
        return $this->factory->create($definitionClass, $tokenName, self::NAME.'.'.$tokenName);
    }

//$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['smartgear']['ticket_creation'] = [
//'email_sender_name' => ['email_sender_name'],
//'email_sender_address' => ['sg_owner_email'],
//'email_subject' => ['ticket_subject', 'sg_title'],
//'recipients' => ['support_email', 'sg_owner_email'],
//'email_text' => ['ticket_*', 'sg_owner_name'],
//'email_html' => ['ticket_*', 'sg_owner_name'],
//'email_replyTo' => ['sg_owner_email'],
//'attachment_tokens' => ['ticket_file'],
//];
}