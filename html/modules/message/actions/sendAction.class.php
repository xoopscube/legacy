<?php
if (!defined('XOOPS_ROOT_PATH')) exit();
require _MY_MODULE_PATH.'kernel/MyPageNavi.class.php';

class sendAction extends AbstractAction
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
    $modHand = xoops_getmodulehandler('outbox', _MY_DIRNAME);
    $this->mPagenavi = new MyPageNavi($modHand);
    $this->mPagenavi->setUrl($this->url);
    $this->mPagenavi->setPagenum($pagenum);
    $this->mPagenavi->addSort('utime', 'DESC');
    $this->mPagenavi->addCriteria(new Criteria('uid', $this->root->mContext->mXoopsUser->get('uid')));
    if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
      $fromuid = intval($this->root->mContext->mRequest->getRequest('touid'));
      if ( $fromuid > 0 ) {
        $this->mPagenavi->addCriteria(new Criteria('to_uid', $fromuid));
      }
      $this->subject = $this->root->mContext->mRequest->getRequest('subject');
      if ( $this->subject != "" ) {
        $this->mPagenavi->addCriteria(new Criteria('title', '%'.$this->subject.'%', 'LIKE'));
      }
    }
    
    $this->mPagenavi->fetch();
    $this->mPagenavi->mNavi->addExtra('action', 'send');
    
    $this->select = $modHand->getReceiveUserList($this->root->mContext->mXoopsUser->get('uid'), $fromuid);
    
    $modObj = $modHand->getObjects($this->mPagenavi->getCriteria());

    foreach ($modObj as $key => $val) {
      foreach ( array_keys($val->gets()) as $var_name ) {
        $item_ary[$var_name] = $val->getShow($var_name);
      }
      $item_ary['fromname'] = $this->getLinkUnameFromId($item_ary['to_uid'], $this->root->mContext->mXoopsConfig['anonymous']);
      $this->listdata[] = $item_ary;
      unset($item_ary);
    }
  }
  
  public function execute()
  {
    if ( !$this->chk_use() ) {
      $this->setUrl('index.php?action=settings');
      $this->setErr(_MD_MESSAGE_SETTINGS_MSG5);
    } else {
      $this->_view();
    }
  }
  
  public function executeView(&$render)
  {
    $root = XCube_Root::getSingleton();
    $render->setTemplateName('message_outboxlist.html');
    $render->setAttribute('ListData', $this->listdata);
    $render->setAttribute('pageNavi', $this->mPagenavi->mNavi);
    
    $render->setAttribute('select', $this->select);
    $render->setAttribute('subject', $this->subject);
  }
}
?>
