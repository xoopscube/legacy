<?php
/**
 *
 * @package Legacy
 * @version $Id: Mailer.php,v 1.4 2008/09/25 15:12:43 kilica Exp $
 * @copyright (c) 2005-2024 The XOOPSCube Project
 * @license GPL v2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * This is a class for mail.
 */
class Legacy_Mailer extends PHPMailer
{
    /**
     * @type XCube_Delegate
     */
    public $mConvertLocal = null;

    public function __construct()
    {
        $this->mConvertLocal =new XCube_Delegate();
        $this->mConvertLocal->register('Legacy_Mailer.ConvertLocal');
    }

    public function prepare()
    {
        $root =& XCube_Root::getSingleton();

        $handler =& xoops_gethandler('config');
        $xoopsMailerConfig =& $handler->getConfigsByCat(XOOPS_CONF_MAILER);
        $this->reset();

        if ('' == $xoopsMailerConfig['from']) {
            $this->From = $root->mContext->mXoopsConfig['adminmail'];
        } else {
            $this->From = $xoopsMailerConfig['from'];
        }

        $this->Sender = $root->mContext->mXoopsConfig['adminmail'];

        $this->SetLanguage = LEGACY_MAIL_LANG;
        $this->CharSet = LEGACY_MAIL_CHAR;
        $this->Encoding = LEGACY_MAIL_ENCO;

        switch ($xoopsMailerConfig['mailmethod']) {
            case 'smtpauth':
                $this->IsSMTP();
                $this->SMTPAuth = true;
                $this->Host = implode(';', $xoopsMailerConfig['smtphost']);
                $this->Username = $xoopsMailerConfig['smtpuser'];
                $this->Password = $xoopsMailerConfig['smtppass'];
                break;

            case 'smtp':
                $this->IsSMTP();
                $this->SMTPAuth = false;
                $this->Host = implode(';', $xoopsMailerConfig['smtphost']);
                break;

            case 'sendmail':
                $this->IsSendmail();
                $this->Sendmail = $xoopsMailerConfig['sendmailpath'];
                break;
        }

        return true;
    }

    public function setFrom($text)
    {
        $this->From = $text;
    }

    public function setFromname($text)
    {
        $this->FromName = $this->convertLocal($text, 2);
    }

    public function setSubject($text)
    {
        $this->Subject = $this->convertLocal($text, true);
    }

    public function setBody($text)
    {
        $this->Body = $this->convertLocal($text);
    }

    public function setTo($add, $name)
    {
        $this->AddAddress($add, $this->convertLocal($name, true));
    }

    public function reset()
    {
        $this->ClearAllRecipients();
        $this->Body = '';
        $this->Subject = '';
    }

    public function convertLocal($text, $mime = false)
    {
        $this->mConvertLocal->call(new XCube_Ref($text), $mime);
        return $text;
    }
}
