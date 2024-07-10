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

namespace WEM\SmartgearBundle\Override;

use Contao\BackendUser;
use Contao\Config;
use Contao\CoreBundle\Exception\InternalServerErrorException;
use Contao\Database\Result;
use Contao\DataContainer;
use Contao\Email;
use Contao\Environment;
use Contao\FilesModel;
use Contao\Idna;
use Contao\Input;
use Contao\Message;
use Contao\Newsletter as ContaoNewsletter;
use Contao\NewsletterChannelModel;
use Contao\System;
use Contao\Validator;
use WEM\SmartgearBundle\Classes\StringUtil;
use WEM\SmartgearBundle\Model\Member;

/**
 * Override Newsletter Contao Backend Class.
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Newsletter extends ContaoNewsletter
{
    /**
     * Return a form to choose an existing CSV file and import it.
     */
    public function send(DataContainer $dc): string
    {
        // OVERLOAD 1 : Kill the JOIN ON because the newsletter aren't connected to channels by PID anymore
        $objNewsletter = $this->Database->prepare('SELECT n.*,n.template as "template_source",n.sender as "sender_source",n.senderName as "senderName_source" FROM tl_newsletter n WHERE n.id=?')
                                        ->limit(1)
                                        ->execute($dc->id)
        ;

        // Return if there is no newsletter
        if ($objNewsletter->numRows < 1) {
            return '';
        }

        System::loadLanguageFile('tl_newsletter_channel');

        // Set the template
        // if (!$objNewsletter->template)
        // {
        //     $objNewsletter->template = $objNewsletter->channelTemplate;
        // }

        // Set the sender address
        // if (!$objNewsletter->sender)
        // {
        //     $objNewsletter->sender = $objNewsletter->channelSender;
        // }

        // Add a new fallback, since the newsletter are not connected to channels the same way than before
        if ('' === $objNewsletter->sender) {
            $objNewsletter->sender = Config::get('adminEmail');
        }

        // Set the sender name
        // if (!$objNewsletter->senderName)
        // {
        //     $objNewsletter->senderName = $objNewsletter->channelSenderName;
        // }

        // No sender address given
        if (!$objNewsletter->sender) {
            throw new InternalServerErrorException('No sender address given. Please check the newsletter channel settings.');
        }

        $arrAttachments = [];

        // Add attachments
        if ($objNewsletter->addFile) {
            $files = StringUtil::deserialize($objNewsletter->files);

            if (!empty($files) && \is_array($files)) {
                $objFiles = FilesModel::findMultipleByUuids($files);

                if (null !== $objFiles) {
                    $projectDir = System::getContainer()->getParameter('kernel.project_dir');

                    while ($objFiles->next()) {
                        if (is_file($projectDir.'/'.$objFiles->path)) {
                            $arrAttachments[] = $objFiles->path;
                        }
                    }
                }
            }
        }

        // Replace insert tags
        $html = System::getContainer()->get('contao.insert_tag.parser')->replaceInline($objNewsletter->content ?? '');
        $text = System::getContainer()->get('contao.insert_tag.parser')->replaceInline($objNewsletter->text ?? '');

        // Convert relative URLs
        if ($objNewsletter->externalImages) {
            $html = $this->convertRelativeUrls($html);
        }

        $objSession = System::getContainer()->get('session');
        $token = Input::get('token');

        // Send newsletter
        if ($token && $token === $objSession->get('tl_newsletter_send')) {
            $referer = preg_replace('/&(amp;)?(start|mpc|token|recipient|preview)=[^&]*/', '', Environment::get('request'));

            // Preview
            if (isset($_GET['preview'])) {
                // Check the e-mail address
                if (!Validator::isEmail(Input::get('recipient', true))) {
                    $_SESSION['TL_PREVIEW_MAIL_ERROR'] = true;
                    $this->redirect($referer);
                }

                $arrRecipient['email'] = urldecode(Input::get('recipient', true));

                // Send
                $objEmail = $this->generateEmailObject($objNewsletter, $arrAttachments);

                if ($this->sendNewsletter($objEmail, $objNewsletter, $arrRecipient, $text, $html)) {
                    Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['tl_newsletter']['confirm'], 1));
                }

                $this->redirect($referer);
            }

            // OVERLOAD 3.0 : Determine the channels concerned by the newletter
            $arrChannels = StringUtil::deserialize($objNewsletter->channels);
            if (!\is_array($arrChannels) || $arrChannels === []) {
                Message::addError("La newsletter n'est connectée à aucune liste d'abonnés");
                Message::addError($GLOBALS['TL_LANG']['tl_newsletter']['notConnectedToAnyChannels']);
                $this->redirect($referer);
            }

            $strWherePid = 'pid IN('.implode(',', array_map(intval(...), $arrChannels)).')';
            $arrChannelsData = $this->prepareNewsletterChannelsData(array_map(intval(...), $arrChannels));

            // Get the total number of recipients
            // OVERLOAD 3.1 : Apply OVERLOAD 3.0
            $objTotal = $this->Database->prepare(sprintf('SELECT COUNT(DISTINCT email) AS count FROM tl_newsletter_recipients WHERE %s AND active=1', $strWherePid))
                                       ->execute()
            ;

            // Return if there are no recipients
            if ($objTotal->count < 1) {
                $objSession->set('tl_newsletter_send', null);
                Message::addError($GLOBALS['TL_LANG']['tl_newsletter']['error']);
                $this->redirect($referer);
            }

            $intTotal = $objTotal->count;

            // Get page and timeout
            $intTimeout = (Input::get('timeout') > 0) ? Input::get('timeout') : 1;
            $intStart = Input::get('start') ?: 0;
            $intPages = Input::get('mpc') ?: 10;

            // Get recipients
            // OVERLOAD 3.2 : Apply OVERLOAD 3.0
            $objRecipients = $this->Database->prepare('SELECT *, r.email FROM tl_newsletter_recipients r LEFT JOIN tl_member m ON(r.email=m.email) WHERE r.'.$strWherePid.' AND r.active=1 ORDER BY r.email')
                                            ->limit($intPages, $intStart)
                                            // ->execute($objNewsletter->pid)
                                            ->execute()
            ;

            echo '<div style="font-family:Verdana,sans-serif;font-size:11px;line-height:16px;margin-bottom:12px">';

            // Send newsletter
            if ($objRecipients->numRows > 0) {
                // Update status
                if (0 === $intStart) {
                    $this->Database->prepare("UPDATE tl_newsletter SET sent='1', date=? WHERE id=?")
                                   ->execute(time(), $objNewsletter->id)
                    ;

                    $_SESSION['REJECTED_RECIPIENTS'] = [];
                    $_SESSION['SKIPPED_RECIPIENTS'] = [];
                }

                $time = time();

                while ($objRecipients->next()) {
                    // Skip the recipient if the member is not active (see #8812)
                    if (null !== $objRecipients->id && ($objRecipients->disable || ($objRecipients->start && $objRecipients->start > $time) || ($objRecipients->stop && $objRecipients->stop <= $time))) {
                        --$intTotal;
                        echo 'Skipping <strong>'.Idna::decodeEmail($objRecipients->email).'</strong> as the member is not active<br>';
                        continue;
                    }

                    $objEmail = $this->generateEmailObject($objNewsletter, $arrAttachments);

                    // OVERLOAD 4.0 : Apply channel settings to newsletter settings
                    $objNewsletter = $this->applyChannelSettings($objNewsletter, $arrChannelsData[$objRecipients->pid]);

                    if ($this->sendNewsletter($objEmail, $objNewsletter, $objRecipients->row(), $text, $html)) {
                        echo 'Sending newsletter to <strong>'.Idna::decodeEmail($objRecipients->email).'</strong><br>';
                    } else {
                        $_SESSION['SKIPPED_RECIPIENTS'][] = $objRecipients->email;
                        echo 'Skipping <strong>'.Idna::decodeEmail($objRecipients->email).'</strong><br>';
                    }

                    // OVERLOAD 5.0 : reset newsletter settings
                    $objNewsletter = $this->removeChannelSettings($objNewsletter);
                }
            }

            echo '<div style="margin-top:12px">';

            // Redirect back home
            if ($objRecipients->numRows < 1 || ($intStart + $intPages) >= $intTotal) {
                $objSession->set('tl_newsletter_send', null);

                // Deactivate rejected addresses
                if (!empty($_SESSION['REJECTED_RECIPIENTS'])) {
                    $intRejected = \count($_SESSION['REJECTED_RECIPIENTS']);
                    Message::addInfo(sprintf($GLOBALS['TL_LANG']['tl_newsletter']['rejected'], $intRejected));
                    $intTotal -= $intRejected;

                    foreach ($_SESSION['REJECTED_RECIPIENTS'] as $strRecipient) {
                        $this->Database->prepare("UPDATE tl_newsletter_recipients SET active='' WHERE email=?")
                                       ->execute($strRecipient)
                        ;

                        System::getContainer()->get('monolog.logger.contao.error')->error('Recipient address "'.Idna::decodeEmail($strRecipient).'" was rejected and has been deactivated');
                    }
                }

                if (($intSkipped = \count($_SESSION['SKIPPED_RECIPIENTS'])) !== 0) {
                    $intTotal -= $intSkipped;
                    Message::addInfo(sprintf($GLOBALS['TL_LANG']['tl_newsletter']['skipped'], $intSkipped));
                }

                unset($_SESSION['REJECTED_RECIPIENTS'], $_SESSION['SKIPPED_RECIPIENTS']);

                Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['tl_newsletter']['confirm'], $intTotal));

                echo '<script>setTimeout(\'window.location="'.Environment::get('base').$referer.'"\',1000)</script>';
                echo '<a href="'.Environment::get('base').$referer.'">Please click here to proceed if you are not using JavaScript</a>';
            }

            // Redirect to the next cycle
            else {
                $url = preg_replace('/&(amp;)?(start|mpc|recipient)=[^&]*/', '', Environment::get('request')).'&start='.($intStart + $intPages).'&mpc='.$intPages;

                echo '<script>setTimeout(\'window.location="'.Environment::get('base').$url.'"\','.($intTimeout * 1000).')</script>';
                echo '<a href="'.Environment::get('base').$url.'">Please click here to proceed if you are not using JavaScript</a>';
            }

            echo '</div></div>';
            exit;
        }

        $strToken = md5(uniqid((string) mt_rand(), true));
        $objSession->set('tl_newsletter_send', $strToken);
        $sprintf = $objNewsletter->senderName ? $objNewsletter->senderName.' &lt;%s&gt;' : '%s';
        $this->import(BackendUser::class, 'User');

        // Preview newsletter TODO : TL_SCRIPT exist ??
        // Where TL_SCRIPT ?
        $return = Message::generate().'
<div id="tl_buttons">
<a href="'.$this->getReferer(true).'" class="header_back" title="'.StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>
<form action="'.System::getContainer()->get('request_stack')->getCurrentRequest()->get('_route').'" id="tl_newsletter_send" class="tl_form tl_edit_form" method="get">
<div class="tl_formbody_edit tl_newsletter_send">
<input type="hidden" name="do" value="'.Input::get('do').'">
<input type="hidden" name="table" value="'.Input::get('table').'">
<input type="hidden" name="key" value="'.Input::get('key').'">
<input type="hidden" name="id" value="'.Input::get('id').'">
<input type="hidden" name="token" value="'.$strToken.'">
<table class="prev_header">
  <tr class="row_0">
    <td class="col_0">'.$GLOBALS['TL_LANG']['tl_newsletter']['from'].'</td>
    <td class="col_1">'.sprintf($sprintf, Idna::decodeEmail($objNewsletter->sender)).'</td>
  </tr>
  <tr class="row_1">
    <td class="col_0">'.$GLOBALS['TL_LANG']['tl_newsletter']['subject'][0].'</td>
    <td class="col_1">'.$objNewsletter->subject.'</td>
  </tr>
  <tr class="row_2">
    <td class="col_0">'.$GLOBALS['TL_LANG']['tl_newsletter_channel']['template'][0].'</td>
    <td class="col_1">'.($objNewsletter->template ?: 'mail_default').'</td>
  </tr>'.(($arrAttachments !== [] && \is_array($arrAttachments)) ? '
  <tr class="row_3">
    <td class="col_0">'.$GLOBALS['TL_LANG']['tl_newsletter']['attachments'].'</td>
    <td class="col_1">'.implode(', ', $arrAttachments).'</td>
  </tr>' : '').'
</table>'.($objNewsletter->sendText ? '' : '
<div class="preview_html">
'.$html.'
</div>').'
<div class="preview_text">
<pre style="white-space:pre-wrap">'.$text.'</pre>
</div>

<fieldset class="tl_tbox nolegend">
<div class="w50 widget">
  <h3><label for="ctrl_mpc">'.$GLOBALS['TL_LANG']['tl_newsletter']['mailsPerCycle'][0].'</label></h3>
  <input type="text" name="mpc" id="ctrl_mpc" value="10" class="tl_text" onfocus="Backend.getScrollOffset()">'.(($GLOBALS['TL_LANG']['tl_newsletter']['mailsPerCycle'][1] && Config::get('showHelp')) ? '
  <p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['tl_newsletter']['mailsPerCycle'][1].'</p>' : '').'
</div>
<div class="w50 widget">
  <h3><label for="ctrl_timeout">'.$GLOBALS['TL_LANG']['tl_newsletter']['timeout'][0].'</label></h3>
  <input type="text" name="timeout" id="ctrl_timeout" value="1" class="tl_text" onfocus="Backend.getScrollOffset()">'.(($GLOBALS['TL_LANG']['tl_newsletter']['timeout'][1] && Config::get('showHelp')) ? '
  <p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['tl_newsletter']['timeout'][1].'</p>' : '').'
</div>
<div class="w50 widget">
  <h3><label for="ctrl_start">'.$GLOBALS['TL_LANG']['tl_newsletter']['start'][0].'</label></h3>
  <input type="text" name="start" id="ctrl_start" value="0" class="tl_text" onfocus="Backend.getScrollOffset()">'.(($GLOBALS['TL_LANG']['tl_newsletter']['start'][1] && Config::get('showHelp')) ? '
  <p class="tl_help tl_tip">'.sprintf($GLOBALS['TL_LANG']['tl_newsletter']['start'][1], $objNewsletter->id).'</p>' : '').'
</div>
<div class="w50 widget">
  <h3><label for="ctrl_recipient">'.$GLOBALS['TL_LANG']['tl_newsletter']['sendPreviewTo'][0].'</label></h3>
  <input type="text" name="recipient" id="ctrl_recipient" value="'.Idna::decodeEmail($this->User->email).'" class="tl_text" onfocus="Backend.getScrollOffset()">'.(isset($_SESSION['TL_PREVIEW_MAIL_ERROR']) ? '
  <div class="tl_error">'.$GLOBALS['TL_LANG']['ERR']['email'].'</div>' : (($GLOBALS['TL_LANG']['tl_newsletter']['sendPreviewTo'][1] && Config::get('showHelp')) ? '
  <p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['tl_newsletter']['sendPreviewTo'][1].'</p>' : '')).'
</div>
</fieldset>
</div>';

        $return .= '

<div class="tl_formbody_submit">
<div class="tl_submit_container">
<button type="submit" name="preview" class="tl_submit" accesskey="p">'.$GLOBALS['TL_LANG']['tl_newsletter']['preview'].'</button>
<button type="submit" id="send" class="tl_submit" accesskey="s" onclick="return confirm(\''.str_replace("'", "\\'", $GLOBALS['TL_LANG']['tl_newsletter']['sendConfirm']).'\')">'.$GLOBALS['TL_LANG']['tl_newsletter']['send'][0].'</button>
</div>
</div>

</form>';

        unset($_SESSION['TL_PREVIEW_MAIL_ERROR']);

        return $return;
    }

    /**
     * Compile the newsletter and send it.
     *
     * @param array $arrRecipient
     * @param string $text
     * @param string $html
     *
     */
    protected function sendNewsletter(Email $objEmail, Result $objNewsletter, $arrRecipient, $text, $html, $css = null): bool
    {
        if (\array_key_exists('id', $arrRecipient)) {
            $objMember = Member::findByPk($arrRecipient['id']);
        } elseif (\array_key_exists('email', $arrRecipient)) {
            $objMember = Member::findByEmail($arrRecipient['email']);
        }

        if ($objMember) {
            $arrRecipient = array_merge($arrRecipient, $objMember->row());
        }

        return parent::sendNewsletter($objEmail, $objNewsletter, $arrRecipient, $text, $html, $css);
    }

    protected function prepareNewsletterChannelsData(array $channelsIds): array
    {
        $arrChannels = [];
        $channels = NewsletterChannelModel::findByIds($channelsIds);
        if ($channels) {
            while ($channels->next()) {
                $arrChannels[$channels->id] = $channels->current()->row();
            }
        }

        return $arrChannels;
    }

    protected function applyChannelSettings(Result $objNewsletter, array $channelData): Result
    {
        // Set the template
        if (\array_key_exists('template', $channelData) && '' !== $channelData['template']) {
            $objNewsletter->template = $channelData['template'];
        }

        // Set the sender address
        if (\array_key_exists('sender', $channelData) && '' !== $channelData['sender']) {
            $objNewsletter->sender = $channelData['sender'];
        }

        // Add a new fallback, since the newsletter are not connected to channels the same way than before
        if ('' === $objNewsletter->sender) {
            $objNewsletter->sender = Config::get('adminEmail');
        }

        // Set the sender name
        if (\array_key_exists('senderName', $channelData) && '' !== $channelData['senderName']) {
            $objNewsletter->senderName = $channelData['senderName'];
        }

        return $objNewsletter;
    }

    protected function removeChannelSettings(Result $objNewsletter): Result
    {
        $objNewsletter->template = $objNewsletter->template_source;
        $objNewsletter->sender = $objNewsletter->sender_source;
        $objNewsletter->senderName = $objNewsletter->senderName_source;

        return $objNewsletter;
    }
}
