<?php
if (!defined('XOOPS_ROOT_PATH')) exit();
if ( !defined('LEGACY_MAIL_LANG') ) {
  define('LEGACY_MAIL_LANG', _LANGCODE);
  define('LEGACY_MAIL_CHAR', _CHARSET);
  define('LEGACY_MAIL_ENCO', '7bit');
}

class My_Mailer extends PHPMailer
{
  public $mConvertLocal = null;
  
  public function __construct()
  {
    $this->mConvertLocal = new XCube_Delegate();
    $this->mConvertLocal->register('Legacy_Mailer.ConvertLocal');
    $this->LE ="\n";
    $this->prepare();
  }
  
  public function prepare()
  {
    $root = XCube_Root::getSingleton();
    $handler = xoops_gethandler('config');
    $xoopsMailerConfig = $handler->getConfigsByCat(XOOPS_CONF_MAILER);
    $this->reset();
    
    if ($xoopsMailerConfig['from'] == '') {
      $this->From = $root->mContext->mXoopsConfig['adminmail'];
    } else {
      $this->From = $xoopsMailerConfig['from'];
    }
    
    $this->Sender = $root->mContext->mXoopsConfig['adminmail'];
    $this->SetLanguage(LEGACY_MAIL_LANG, XOOPS_ROOT_PATH.'/class/mail/phpmailer/language/');
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
  
  public function setFromEmail($text)
  {
    $this->From = $text;
  }
  
  public function setFromName($text)
  {
    $this->FromName = $this->convertLocal($this->SecureHeader($text), true);
  }
  
  public function setSubject($text)
  {
    $this->Subject = $text;
  }
  
  public function setBody($text)
  {
    $search = array("\r\n", "\r", "\n");
    $replace = array("\n", "\n", $this->LE);
    $text = str_replace($search, $replace, $text);
    $this->Body = $this->convertLocal($text);
  }
  
  public function setToEmails($email)
  {
    $this->AddAddress($email, "");
  }
  
  public function setTo($add, $name = "")
  {
    $this->AddAddress($add, $name);
  }
  
  public function reset()
  {
    $this->ClearAllRecipients();
    $this->Body = "";
    $this->Subject = "";
  }

  public function send()
  {
    parent::Send();
  }

  public function EncodeHeader($str, $position = 'text')
  {
    if ( $position == 'text' ) {
      return $this->convertLocal($str, true);
    } else {
      return parent::EncodeHeader($str, $position);
    }
  }
  
  private function convertLocal($text, $mime = false)
  {
    if ( _LANGCODE == 'ja' ) {
      $text = $this->_Japanese_convLocal($text, $mime);
    } else {
      $this->mConvertLocal->call(new XCube_Ref($text), $mime);
    }
    return $text;
  }
  
  private function _Japanese_convLocal($text, $mime)
  {
    if ( $mime ) {
      $text = mb_encode_mimeheader($text, LEGACY_MAIL_CHAR, 'B', $this->LE);
    } else {
      $text = mb_convert_encoding($text, 'JIS', _CHARSET);
    }
    return $text;
  }
}
?>
