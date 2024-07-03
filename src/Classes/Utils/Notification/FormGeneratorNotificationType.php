<?php

declare(strict_types = 1);

use Terminal42\NotificationCenterBundle\NotificationType\NotificationTypeInterface;
use Terminal42\NotificationCenterBundle\Token\Definition\Factory\TokenDefinitionFactoryInterface;
use Terminal42\NotificationCenterBundle\Token\Definition\FileTokenDefinition;
use Terminal42\NotificationCenterBundle\Token\Definition\HtmlTokenDefinition;
use Terminal42\NotificationCenterBundle\Token\Definition\TextTokenDefinition;
use Terminal42\NotificationCenterBundle\Token\Definition\TokenDefinitionInterface;

class FormGeneratorNotificationType implements NotificationTypeInterface
{
    public const NAME = 'core_form';

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
            $this->factory->create(TextTokenDefinition::class, 'useful_data', 'form.useful_data'),
            $this->factory->create(TextTokenDefinition::class, 'useful_data_filled', 'form.useful_data_filled'),
            $this->factory->create(HtmlTokenDefinition::class, 'useful_data', 'form.useful_data'),
            $this->factory->create(HtmlTokenDefinition::class, 'useful_data_filled', 'form.useful_data_filled'),
            $this->factory->create(FileTokenDefinition::class, 'useful_data', 'form.useful_data'),
            $this->factory->create(FileTokenDefinition::class, 'useful_data_filled', 'form.useful_data_filled'),
        ];
    }

    private function createDefinition(string $definitionClass, string $tokenName): TokenDefinitionInterface
    {
        return $this->factory->create($definitionClass, $tokenName, self::NAME.'.'.$tokenName);
    }


//    /*
// * NC hooks
// */
//$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['contao']['core_form']['email_text'][] = 'useful_data';
//$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['contao']['core_form']['email_text'][] = 'useful_data_filled';
//$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['contao']['core_form']['email_html'][] = 'useful_data';
//$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['contao']['core_form']['email_html'][] = 'useful_data_filled';
//$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['contao']['core_form']['file_content'][] = 'useful_data';
//$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['contao']['core_form']['file_content'][] = 'useful_data_filled';
//

}