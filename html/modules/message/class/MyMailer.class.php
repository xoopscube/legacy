<?php
if (!defined('XOOPS_ROOT_PATH')) exit();

class My_Mailer extends PHPMailer
{
  public $mConvertLocal = null;
  
  public function __construct()
  {
    $this->mConvertLocal = new XCube_Delegate();
    $this->mConvertLocal->register('Legacy_Mailer.ConvertLocal');
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
  
  public function SendmailSend($header, $body)
  {
    if ($this->Sender != "") {
      $sendmail = sprintf("%s -oi -f %s -t", $this->Sendmail, escapeshellcmd($this->Sender));
    } else {
      $sendmail = sprintf("%s -oi -t", $this->Sendmail);
    }
    if (!@$mail = popen($sendmail, "w")) {
      $this->SetError($this->Lang("execute") . $this->Sendmail);
      return false;
    }
    fputs($mail, $header);
    fputs($mail, $body);
    $result = pclose($mail) >> 8 & 0xFF;
    if ($result != 0) {
      $this->SetError($this->Lang("execute") . $this->Sendmail);
      return false;
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
    $this->Subject = $this->convertLocal($text, 1);
  }
  
  public function setBody($text)
  {
    $this->Body = $this->convertLocal($text);
  }
  
  public function setTo($add, $name)
  {
    $this->AddAddress($add, $this->convertLocal($name, 1));
  }
  
  public function reset()
  {
    $this->ClearAllRecipients();
    $this->Body = "";
    $this->Subject = "";
  }
  
  public function convertLocal($text, $mime = false)
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
      switch ($mime) {
        case '1': $text = mb_encode_mimeheader($text, LEGACY_MAIL_CHAR, 'B', $this->LE); break;
        case '2': $text = mb_encode_mimeheader($text, LEGACY_MAIL_CHAR, 'B', ""); break;
      }
    } else {
      $text = mb_convert_encoding($text, 'JIS', _CHARSET);
    }
    return $text;
  }
}
?>
