<?php
/**
 * send email through PHP's "mail()" function
 * @package    class
 * @subpackage mail
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2008/09/21
 * @author     Jochen Buennagel
 * @copyright  (c) 2000-2003 Authors
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * load the base class
 */
require XOOPS_ROOT_PATH .'/class/mail/phpmailer/src/Exception.php';
require XOOPS_ROOT_PATH .'/class/mail/phpmailer/src/PHPMailer.php';
require XOOPS_ROOT_PATH .'/class/mail/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Mailer Class
 *
 * If you have problems sending mail with "mail()", you can edit the member variables to suit your setting.
 * Login to administration and edit the settings through the admin panel.
 * Administration »» Dashboard »» Settings »» Preferences »» Mail Setup
 */
class xoopsmultimailer extends PHPMailer
{

    /**
     * "from" address
     * @var   string
     * @access  private
     */
    public $From     = '';

    /**
     * "from" name
     * @var   string
     * @access  private
     */
    public $FromName   = '';

    // can be "smtp", "sendmail", or "mail"
    /**
     * Method to be used when sending the mail.
     *
     * This can be:
     * <li>mail (standard PHP function "mail()") (default)
     * <li>smtp (send through any SMTP server, SMTPAuth is supported.
     * You must set {@link $Host}, for SMTPAuth also {@link $SMTPAuth},
     * {@link $Username}, and {@link $Password}.)
     * <li>sendmail (manually set the path to your sendmail program
     * to something different than "mail()" uses in {@link $Sendmail})
     *
     * @var   string
     * @access  private
     */
    public $Mailer   = 'mail';

    /**
     * set if $Mailer is "sendmail"
     *
     * Only used if {@link $Mailer} is set to "sendmail".
     * Contains the full path to your sendmail program or replacement.
     * @var   string
     * @access  private
     */
    public $Sendmail = '/usr/sbin/sendmail';

    /**
     * SMTP Host.
     *
     * Only used if {@link $Mailer} is set to "smtp"
     * @var   string
     * @access  private
     */
    public $Host   = '';

    /**
     * Does your SMTP host require SMTPAuth authentication?
     * @var   bool
     * @access  private
     */
    public $SMTPAuth = false;

    /**
     * Username for authentication with your SMTP host.
     *
     * Only used if {@link $Mailer} is "smtp" and {@link $SMTPAuth} is TRUE
     * @var   string
     * @access  private
     */
    public $Username = '';

    /**
     * Password for SMTPAuth.
     *
     * Only used if {@link $Mailer} is "smtp" and {@link $SMTPAuth} is TRUE
     * @var   string
     * @access  private
     */
    public $Password = '';

    /**
     * Constuctor
     *
     * @access public
     * @return void
     *
     * @global  $xoopsConfig
     */
    public function __construct()
    {
        global $xoopsConfig;
        $this->ClearAllRecipients();
        $config_handler = &xoops_gethandler('config');
        $xoopsMailerConfig = &$config_handler->getConfigsByCat(XOOPS_CONF_MAILER);
        $this->From = $xoopsMailerConfig['from'];
        if ('' == $this->From) {
            $this->From = defined('XOOPS_NOTIFY_FROM_EMAIL') ? XOOPS_NOTIFY_FROM_EMAIL : $xoopsConfig['adminmail'];
        }
        $this->Sender = defined('XOOPS_NOTIFY_SENDER_EMAIL') ? XOOPS_NOTIFY_SENDER_EMAIL : $xoopsConfig['adminmail'];
        if ('smtpauth' == $xoopsMailerConfig['mailmethod']) {
            $this->Mailer = 'smtp';
            $this->SMTPAuth = true;
            $this->Host = implode(';', $xoopsMailerConfig['smtphost']);
            $this->Username = $xoopsMailerConfig['smtpuser'];
            $this->Password = $xoopsMailerConfig['smtppass'];
        } else {
            $this->Mailer = $xoopsMailerConfig['mailmethod'];
            $this->SMTPAuth = false;
            $this->Sendmail = $xoopsMailerConfig['sendmailpath'];
            $this->Host = implode(';', $xoopsMailerConfig['smtphost']);
        }
    }

    /**
     * Formats an address correctly. This overrides the default AddrFormat method
     * which does not seem to encode $FromName correctly
     * This method name is renamed from "addr_format", because method name in parent class is renamed.
     * @access private
     * @param $addr
     * @return string
     */
    //TODO: We must verify,whether we should prepare this method even now.(phpmailer is upgraded from 1.65 to 1.73)
    public function AddrFormat($addr)
    {
        if (empty($addr[1])) {
            $formatted = $addr[0];
        } else {
            $formatted = sprintf('%s <%s>', '=?' . $this->CharSet . '?B?' . base64_encode($addr[1]) . '?=', $addr[0]);
        }

        return $formatted;
    }

    /**
     * Override PHPMailer Send()
     *   Add verification whether Sender property contains correct mail format.
     */

    public function Send()
    {
        if (
            empty($this->Sender)
            || preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i", $this->Sender)
        ) {
            return parent::Send();
        }
        return false;
    }

    /**
     * Sets the language for all class error messages.  Returns false
     * if it cannot load the language file.  The default language type
     * is English.
     * @param string $lang_type Type of language (e.g. Portuguese: "br")
     * @param string $lang_path Path to the language file directory
     * @access public
     * @return bool
     */
    public function SetLanguage($lang_type = 'en', $lang_path = 'language/')
    {
        // Patch for XOOPSCube Legacy 2008/09/21
        $ext = substr($lang_path, -1, 1);
        if ('/' !== $ext && file_exists($lang_path)) {
            include($lang_path);
            $this->language = $PHPMAILER_LANG;
            return true;
        }

        return parent::SetLanguage($lang_type, $lang_path);
    }
}
