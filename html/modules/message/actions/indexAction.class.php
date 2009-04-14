<?php
if (!defined('XOOPS_ROOT_PATH')) exit();
require _MY_MODULE_PATH.'kernel/MyPageNavi.class.php';

class indexAction extends AbstractAction
{
  private $listdata;
  private $mPagenavi = null;
  private $select;
  private $subject = "";
  
  public function __construct()
  {
    parent::__construct();
  }
  
  private function _view()
  {
    $fromuid = 0;
    $setting = $this->getSettings();
    if ( $setting->get('pagenum') > 0 ) {
      $pagenum = $setting->get('pagenum');
    } else {
      $pagenum = $this->root->mContext->mModuleConfig['pagenum'];
    }
    $modHand = xoops_getmodulehandler('inbox', _MY_DIRNAME);
    $this->mPagenavi = new MyPageNavi($modHand);
    $this->mPagenavi->setUrl($this->url);
    $this->mPagenavi->setPagenum($pagenum);
    $this->mPagenavi->addSort('utime', 'DESC');
    $this->mPagenavi->addCriteria(new Criteria('uid', $this->root->mContext->mXoopsUser->get('uid')));
    if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
      $fromuid = intval($this->root->mContext->mRequest->getRequest('fromuid'));
      if ( $fromuid > 0 ) {
        $this->mPagenavi->addCriteria(new Criteria('from_uid', $fromuid));
      }
      $this->subject = $this->root->mContext->mRequest->getRequest('subject');
      if ( $this->subject != "" ) {
        $this->mPagenavi->addCriteria(new Criteria('title', '%'.$this->subject.'%', 'LIKE'));
      }
    }
    $this->mPagenavi->fetch();
    $this->select = $modHand->getSendUserList($this->root->mContext->mXoopsUser->get('uid'), $fromuid);
    $modObj = $modHand->getObjects($this->mPagenavi->getCriteria());

    foreach ($modObj as $key => $val) {
      foreach ( array_keys($val->gets()) as $var_name ) {
        $item_ary[$var_name] = $val->getShow($var_name);
      }
      $item_ary['fromname'] = $this->getLinkUnameFromId($item_ary['from_uid'], $item_ary['uname']);
      $this->listdata[] = $item_ary;
      unset($item_ary);
    }
  }
  
  public function execute()
  {
    if ( $this->chk_use() ) {
      $this->_view();
    } else {
      //FRONT
      if (defined('_FRONTCONTROLLER')) {
        $this->setUrl($this->url.'&action=settings');
      } else {
        $this->setUrl('index.php?action=settings');
      }
      $this->setErr(_MD_MESSAGE_SETTINGS_MSG5);
    }
  }
  
  public function executeView(&$render)
  {
    $render->setTemplateName('message_inboxlist.html');
    $render->setAttribute('ListData', $this->listdata);
    $render->setAttribute('pageNavi', $this->mPagenavi->mNavi);
    $render->setAttribute('select', $this->select);
    $render->setAttribute('subject', $this->subject);
    //FRONT
    if (defined('_FRONTCONTROLLER')) {
      $render->setAttribute('message_url', XOOPS_URL.'/index.php?moddir='._MY_DIRNAME);
    } else {
      $render->setAttribute('message_url', 'index.php?moddir='._MY_DIRNAME);
    }
  }
}
?>