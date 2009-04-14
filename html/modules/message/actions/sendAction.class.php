<?php
if (!defined('XOOPS_ROOT_PATH')) exit();
require _MY_MODULE_PATH.'kernel/MyPageNavi.class.php';

class sendAction extends AbstractAction
{
  private $listdata;
  private $mPagenavi = null;
  
  public function __construct()
  {
    parent::__construct();
  }
  
  private function _view()
  {
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
    $this->mPagenavi->fetch();
    $this->mPagenavi->mNavi->addExtra('action', 'send');
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
      //FRONT
      if (defined('_FRONTCONTROLLER')) {
        $this->setUrl($this->url.'&action=settings');
      } else {
        $this->setUrl('index.php?action=settings');
      }
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
    //FRONT
    if (defined('_FRONTCONTROLLER')) {
      $render->setAttribute('message_url', XOOPS_URL.'/index.php?moddir='._MY_DIRNAME);
    } else {
      $render->setAttribute('message_url', 'index.php?moddir='._MY_DIRNAME);
    }
  }
}
?>