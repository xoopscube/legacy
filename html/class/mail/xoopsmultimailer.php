<?php
// $Id: xoopsmultimailer.php,v 1.4 2008/09/21 06:45:39 minahito Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Jochen B—¬nagel (job@buennagel.com)                               //
// URL:  http://www.xoops.org                        //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
/**
 * @package   class
 * @subpackage  mail
 * 
 * @filesource 
 *
 * @author    Jochen B—¬nagel <jb@buennagel.com>
 * @copyright copyright (c) 2000-2003 The XOOPS Project (http://www.xoops.org)
 *
 * @version   $Revision: 1.4 $ - $Date: 2008/09/21 06:45:39 $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * load the base class
 */
require_once(XOOPS_ROOT_PATH.'/class/mail/phpmailer/class.phpmailer.php');

/**
 * Mailer Class.
 * 
 * At the moment, this does nothing but send email through PHP's "mail()" function,
 * but it has the abiltiy to do much more.
 * 
 * If you have problems sending mail with "mail()", you can edit the member variables
 * to suit your setting. Later this will be possible through the admin panel.
 *
 * @todo    Make a page in the admin panel for setting mailer preferences.
 * 
 * @package   class
 * @subpackage  mail
 *
 * @author    Jochen Buennagel  <job@buennagel.com>
 * @copyright (c) 2000-2003 The Xoops Project - www.xoops.org
 * @version   $Revision: 1.4 $ - changed by $Author: minahito $ on $Date: 2008/09/21 06:45:39 $
 */
class xoopsmultimailer extends PHPMailer
{

    /**
   * "from" address
   * @var   string
   * @access  private
   */
  public $From     = "";
  
  /**
   * "from" name
   * @var   string
   * @access  private
   */
  public $FromName   = "";

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
  public $Mailer   = "mail";

  /**
   * set if $Mailer is "sendmail"
   * 
   * Only used if {@link $Mailer} is set to "sendmail".
   * Contains the full path to your sendmail program or replacement.
   * @var   string
   * @access  private
   */
  public $Sendmail = "/usr/sbin/sendmail";

  /**
   * SMTP Host.
   * 
   * Only used if {@link $Mailer} is set to "smtp"
   * @var   string
   * @access  private
   */
  public $Host   = "";

  /**
   * Does your SMTP host require SMTPAuth authentication?
   * @var   boolean
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
  public $Username = "";

  /**
   * Password for SMTPAuth.
   * 
   * Only used if {@link $Mailer} is "smtp" and {@link $SMTPAuth} is TRUE
   * @var   string
   * @access  private
   */
  public $Password = "";
  
  /**
   * Constuctor
   * 
   * @access public
   * @return void 
   * 
   * @global  $xoopsConfig
   */
  public function XoopsMultiMailer()
  {
      global $xoopsConfig;
      $this->ClearAllRecipients();
      $config_handler =& xoops_gethandler('config');
      $xoopsMailerConfig =& $config_handler->getConfigsByCat(XOOPS_CONF_MAILER);
      $this->From = $xoopsMailerConfig['from'];
      if ($this->From == '') {
          $this->From = defined('XOOPS_NOTIFY_FROM_EMAIL')? XOOPS_NOTIFY_FROM_EMAIL : $xoopsConfig['adminmail'];
      }
      $this->Sender = defined('XOOPS_NOTIFY_SENDER_EMAIL')? XOOPS_NOTIFY_SENDER_EMAIL : $xoopsConfig['adminmail'];
      if ($xoopsMailerConfig["mailmethod"] == "smtpauth") {
          $this->Mailer = "smtp";
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
     * @return string
     */
     //TODO: We must verify,whether we should prepare this method even now.(phpmailer is upgraded from 1.65 to 1.73)
    public function AddrFormat($addr)
    {
        if (empty($addr[1])) {
            $formatted = $addr[0];
        } else {
            $formatted = sprintf('%s <%s>', '=?'.$this->CharSet.'?B?'.base64_encode($addr[1]).'?=', $addr[0]);
        }

        return $formatted;
    }
    
   /**
     * Override PHPMailer Send()
     *   Add verification whether Sender property contains correct mail format.
     */
    
    public function Send()
    {
        if (empty($this->Sender)
            || preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i", $this->Sender)) {
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
        // Patch for XOOPS Cube Legacy 2008/09/21
        $ext = substr($lang_path, -1, 1);
        if ($ext != '/' && file_exists($lang_path)) {
            include($lang_path);
            $this->language = $PHPMAILER_LANG;
            return true;
        }
        
        return parent::SetLanguage($lang_type, $lang_path);
    }
}
