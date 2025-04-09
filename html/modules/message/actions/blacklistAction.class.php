<?php
/**
 * Message module for private messages and forward to email
 * @package    Message
 * @version    2.5.0
 * @author     Other authors Nuno Luciano aka gigamaster, 2020 XCL23
 * @author     Osamu Utsugi aka Marijuana
 * @copyright  (c) 2005-2024 The XOOPSCube Project, Authors
 * @license    GPL 3.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class blacklistAction extends AbstractAction
{
    private $mActionForm;
    private array $blackuser = [];
    private $mService;

    public function execute()
    {
        $this->setUrl('index.php?action=blacklist');
        $modobj = $this->getSettings();
        $uid = (int)$this->root->mContext->mRequest->getRequest('uid');
        if (0 !== $uid) {  //Add
            $this->addblklist($modobj, $uid);
        } else {
            switch ($this->root->mContext->mRequest->getRequest('cmd')) {
            case 'add':
            $modHand = xoops_getmodulehandler('settings', _MY_DIRNAME);
            $uid = $modHand->getuidTouname($this->root->mContext->mRequest->getRequest('uname'));
            if ($uid > 0) {
                $this->addblklist($modobj, $uid);
            } else {
                $this->setErr(_MD_MESSAGE_SETTINGS_MSG19);
            }
            break;
            case 'del': //Delete
                $this->delblklist($modobj);
                break;
            default:
                if ('' !== $modobj->get('blacklist')) {
                    $blusers = explode(',', $modobj->get('blacklist'));
                    foreach ($blusers as $bluid) {
                        $this->blackuser[$bluid] = $this->getLinkUnameFromId($bluid);
                    }
                }
            }
        }
        // service UserSearch
        $this->mService = $this->root->mServiceManager->getService('UserSearch');
    }

    private function delblklist($modobj)
    {
        $deluid = $this->root->mContext->mRequest->getRequest('deluid');
        if (!is_array($deluid) || 0 === count($deluid)) {
            $this->setErr(_MD_MESSAGE_DELETEMSG2);
            return;
        }

        $adduid = false;
        if (!empty($deluid) && is_array($deluid)) {
            $modHand = xoops_getmodulehandler('settings', _MY_DIRNAME);
            $lists = explode(',', $modobj->get('blacklist'));
            foreach ($lists as $auid) {
                if (!in_array($auid, $deluid, true) && $modHand->chkUser($auid)) {
                    $adduid[] = $auid;
                }
            }
            if (is_array($adduid)) {
                $modobj->set('blacklist', implode(',', $adduid));
            } else {
                $modobj->set('blacklist', '');
            }
            if ($modHand->insert($modobj)) {
                $this->setErr(_MD_MESSAGE_SETTINGS_MSG16);
            } else {
                $this->setErr(_MD_MESSAGE_SETTINGS_MSG17);
            }
        }
    }

    private function addblklist($modobj, $uid)
    {
        $modHand = xoops_getmodulehandler('settings', _MY_DIRNAME);
        $blackuser = $this->getLinkUnameFromId($uid);
        $lists = explode(',', $modobj->get('blacklist'));
        if (in_array($uid, $lists, true)) {
            $this->setErr(XCube_Utils::formatString(_MD_MESSAGE_SETTINGS_MSG14, $blackuser));
            return;
        }
        if ('' === $lists[0]) {
            $modobj->set('blacklist', $uid);
        } else {
            $lists[] = $uid;
            $modobj->set('blacklist', implode(',', $lists));
        }
        if ($modHand->insert($modobj, true)) {
            $this->setErr(XCube_Utils::formatString(_MD_MESSAGE_SETTINGS_MSG12, $blackuser));
        } else {
            $this->setErr(XCube_Utils::formatString(_MD_MESSAGE_SETTINGS_MSG13, $blackuser));
        }
    }

    public function executeView(&$render)
    {
        $render->setTemplateName('message_blaclist.html');
        $render->setAttribute('blackuser', $this->blackuser);
        $render->setAttribute('UserSearch', $this->mService);
        $render->setAttribute('message_url', XOOPS_URL.'/modules/message/index.php');
    }
}
