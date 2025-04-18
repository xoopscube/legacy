<?php
/**
 * Message module for private messages and forward to email
 * 
 * @package    Message
 * @version    2.5.0
 * @author     Nuno Luciano aka gigamaster, 2020 XCL23
 * @author     Osamu Utsugi aka Marijuana
 * @copyright  (c) 2005-2025 The XOOPSCube Project, Authors
 * @license    GPL 3.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require _MY_MODULE_PATH.'kernel/MyPageNavi.class.php';

class indexAction extends AbstractAction
{
    private ?array $listdata = null;
    private ?\MyPageNavi $mPagenavi = null;
    private $select;
    private $subject = '';
    private $status = '';
    private $mService;

    private function _view()
    {
        $fromuid = 0;
        $setting = $this->getSettings();
        if ($setting->get('pagenum') > 0) {
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
        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            $fromuid = (int)$this->root->mContext->mRequest->getRequest('fromuid');
            if ($fromuid > 0) {
                $this->mPagenavi->addCriteria(new Criteria('from_uid', $fromuid));
            }
            $this->subject = $this->root->mContext->mRequest->getRequest('subject');
            if ('' !== $this->subject) {
                $this->mPagenavi->addCriteria(new Criteria('title', '%'.$this->subject.'%', 'LIKE'));
            }
            $this->status = $this->root->mContext->mRequest->getRequest('status');
            if ('' !== $this->status) {
                $this->status = (int)$this->status;
                $this->mPagenavi->addCriteria(new Criteria('is_read', $this->status));
            }
        }
        $this->mPagenavi->fetch();
        $this->select = $modHand->getSendUserList($this->root->mContext->mXoopsUser->get('uid'), $fromuid);
        $modObj = $modHand->getObjects($this->mPagenavi->getCriteria());

        foreach ($modObj as $key => $val) {
            foreach (array_keys($val->gets()) as $var_name) {
                $item_ary[$var_name] = $val->getShow($var_name);
            }
            $item_ary['fromname'] = $this->getLinkUnameFromId($item_ary['from_uid'], $item_ary['uname']);
            $this->listdata[] = $item_ary;
            unset($item_ary);
        }
        // service UserSearch
        $this->mService = $this->root->mServiceManager->getService('UserSearch');
    }

    public function execute()
    {
        if ($this->chk_use()) {
            $this->_view();
        } else {
            $this->setUrl('index.php?action=settings');
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
        $render->setAttribute('status', $this->status);
        $render->setAttribute('UserSearch', $this->mService);
        $render->setAttribute('message_url', XOOPS_URL.'/modules/message/index.php');
    }
}
