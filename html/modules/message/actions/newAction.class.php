<?php
if (!defined('XOOPS_ROOT_PATH')) exit();

require _MY_MODULE_PATH.'forms/MessageForm.class.php';

class newAction extends AbstractAction
{
  private $mActionForm;
  
  public function __construct()
  {
    parent::__construct();
    $this->mActionForm = new MessageForm();
    $this->mActionForm->prepare();
  }
  
  public function execute()
  {
    if ( !$this->chk_use() ) {
      $this->setUrl('index.php?action=settings');
      $this->setErr(_MD_MESSAGE_SETTINGS_MSG5);
    } else {
      $inboxid = intval($this->root->mContext->mRequest->getRequest('res'));
      $to_userid = intval($this->root->mContext->mRequest->getRequest('to_userid'));
      
      if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
        $this->mActionForm->fetch();
        $this->mActionForm->validate();
        if ($this->mActionForm->hasError()) {
          $this->errMsg = $this->mActionForm->getErrorMessages();
        } elseif ( !$this->chk_use($this->mActionForm->fuid) || !$this->chk_deny($this->mActionForm->fuid) ) {
          $this->errMsg = _MD_MESSAGE_SETTINGS_MSG6;
        } elseif ( $this->mActionForm->get('Legacy_Event_User_Submit') != "" ) {
          $this->isError = true;
          $modHand = xoops_getmodulehandler('inbox', _MY_DIRNAME);
          $modObj = $modHand->create();
          $this->mActionForm->update($modObj);
          if ( !$modHand->insert($modObj) ) {
            $this->errMsg = _MD_MESSAGE_ACTIONMSG5;
          } else {
            $this->usemail();
            $modHand->deleteDays($this->root->mContext->mModuleConfig['savedays'], $this->root->mContext->mModuleConfig['dletype']);
            if ( !$this->update_outbox($modObj) ) {
              $this->errMsg = _MD_MESSAGE_ACTIONMSG6;
            } else {
              $this->errMsg = _MD_MESSAGE_ACTIONMSG7;
            }
          }
        }
      } elseif ( $inboxid > 0 ) {
        $modHand = xoops_getmodulehandler('inbox', _MY_DIRNAME);
        $modObj = $modHand->get($inboxid);
        if ( is_object($modObj) && $modObj->get('from_uid') > 0 && $modObj->get('uid') == $this->root->mContext->mXoopsUser->get('uid') ) {
          if ( !$this->mActionForm->setRes($modObj) ) {
            $this->errMsg = _MD_MESSAGE_ACTIONMSG9;
          }
        }
      } elseif ( $to_userid > 0 ) {
        $userhand = xoops_gethandler('user');
        $user = $userhand->get($to_userid);
        $this->mActionForm->setUser($user);
      }
    }
  }
  
  private function chk_deny($uid)
  {
    $fromid = $this->root->mContext->mXoopsUser->get('uid');
    $modObj = $this->getSettings($uid);
    $blacklist = $modObj->get('blacklist');
    if ( $blacklist == "" ) {
      return true;
    } elseif ( strpos($blacklist, ',') !== false ) {
      $lists = explode(',', $blacklist);
      if ( !in_array($fromid, $lists) ) {
        return true;
      }
    } elseif ( $blacklist != $fromid ) {
      return true;
    }
    return false;
  }
  
  /*
  private function usemail($orgi = false)
  {
    $setting = $this->getSettings($this->mActionForm->fuid);
    if ( $setting->get('tomail') == 1 ) {
      $userhand = xoops_gethandler('user');
      $user = $userhand->get($this->mActionForm->fuid);
      require_once XOOPS_ROOT_PATH.'/class/mail/phpmailer/class.phpmailer.php';
      require_once _MY_MODULE_PATH.'class/MyMailer.class.php';
      $mailer = new My_Mailer();
      $mailer->prepare();
      $mailer->setFromname($this->root->mContext->mXoopsConfig['sitename']);
      $mailer->setFrom($this->root->mContext->mXoopsConfig['adminmail']);
      $mailer->setTo($user->get('email'), $user->get('uname'));
      
      $mailer->setSubject(_MD_MESSAGE_MAILSUBJECT);
      $mailer->setBody($this->getMailBody($setting->get('viewmsm')));
      
      $mailer->Send();
    }
  }
  */
  
  private function usemail()
  {
    $setting = $this->getSettings($this->mActionForm->fuid);
    if ( $setting->get('tomail') == 1 ) {
      $userhand = xoops_gethandler('user');
      $user = $userhand->get($this->mActionForm->fuid);
      
      $mailer = $this->getMailer();
      $mailer->setFromName($this->root->mContext->mXoopsConfig['sitename']);
      $mailer->setFromEmail($this->root->mContext->mXoopsConfig['adminmail']);
      $mailer->setToEmails($user->get('email'));
      $mailer->setSubject(_MD_MESSAGE_MAILSUBJECT);
      $mailer->setBody($this->getMailBody($setting->get('viewmsm')));
      $mailer->send();
    }
  }

  private function getMailBody($body = 0)
  {
    $tpl = new Smarty();
    $tpl->_canUpdateFromFile = true;
    $tpl->compile_check = true;
    $tpl->template_dir = _MY_MODULE_PATH.'language/'.$this->root->mLanguageManager->mLanguageName.'/';
    $tpl->cache_dir = XOOPS_CACHE_PATH;
    $tpl->compile_dir = XOOPS_COMPILE_PATH;
    
    $tpl->assign('sitename', $this->root->mContext->mXoopsConfig['sitename']);
    $tpl->assign('uname', $this->root->mContext->mXoopsUser->get('uname'));
    if ( $body == 1 ) {
      $tpl->assign('note', $this->mActionForm->get('note'));
    } else {
      $tpl->assign('note', XCube_Utils::formatString(_MD_MESSAGE_MAILBODY, XOOPS_URL.'/'));
    }
    $tpl->assign('siteurl', XOOPS_URL.'/');
    return $tpl->fetch(_MY_MODULE_PATH.'language/'.$this->root->mLanguageManager->mLanguageName.'/invitation.tpl');
  }
  
  private function update_outbox(&$obj)
  {
    $outHand = xoops_getmodulehandler('outbox');
    $outHand->deleteDays($this->root->mContext->mModuleConfig['savedays']);
    $outObj = $outHand->create();
    $outObj->set('uid', $obj->get('from_uid'));
    $outObj->set('to_uid', $obj->get('uid'));
    $outObj->set('title', $obj->get('title'));
    $outObj->set('message', $obj->get('message'));
    $outObj->set('utime', $obj->get('utime'));
    return $outHand->insert($outObj);
  }
  
  public function executeView(&$render)
  {
    $render->setTemplateName('message_new.html');
    $render->setAttribute('mActionForm', $this->mActionForm);
    $render->setAttribute('errMsg', $this->errMsg);
  }
}
?>
