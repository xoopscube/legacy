<?php
/**
 * Message module for private messages and forward to email
 * @package    Message
 * @version    2.3.3
 * @author     Other authors Nuno Luciano aka gigamaster, 2020 XCL23
 * @author     Osamu Utsugi aka Marijuana
 * @copyright  (c) 2005-2023 The XOOPSCube Project, Authors
 * @license    GPL 3.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class viewAction extends AbstractAction
{
    private string $inout = 'inbox';
    private ?array $msgdata = null;
    private $mService;

    public function execute()
    {
        if ('in' == $this->root->mContext->mRequest->getRequest('inout')) {
            $this->inout = 'inbox';
        } else {
            $this->inout = 'outbox';
        }

        $boxid = (int)$this->root->mContext->mRequest->getRequest($this->inout);
        $modHand = xoops_getmodulehandler($this->inout);
        $modObj = $modHand->get($boxid);
        if (!is_object($modObj)) {
            $this->setErr(_MD_MESSAGE_ACTIONMSG1);
            return;
        }

        if ($modObj->get('uid') != $this->root->mContext->mXoopsUser->get('uid')) {
            $this->setErr(_MD_MESSAGE_ACTIONMSG8);
            return;
        }

        if ('inbox' == $this->inout) {
            if ('POST' == $_SERVER['REQUEST_METHOD']) {
                if ('lock' == $this->root->mContext->mRequest->getRequest('cmd')) {
                    if (1 == (int)$this->root->mContext->mRequest->getRequest('lock')) {
                        $modObj->set('is_read', 2);
                    } else {
                        $modObj->set('is_read', 1);
                    }
                    $modHand->insert($modObj);
                } elseif ('mail' == $this->root->mContext->mRequest->getRequest('cmd')) {
                    $this->send_mail($modObj);
                }
            } elseif (0 == $modObj->get('is_read')) {
                $modObj->set('is_read', 1);
                $modHand->insert($modObj, true);
            }
        }
        /**
         * @ Version XCL 2.3.x
         * Outbox message forward to email
         */
        if ('outbox' == $this->inout) {
            if ('POST' == $_SERVER['REQUEST_METHOD']) {
                if ('mail' == $this->root->mContext->mRequest->getRequest('cmd')) {
                    $this->send_mail($modObj);
                }
            }
        }

        foreach (array_keys($modObj->gets()) as $var_name) {
            $this->msgdata[$var_name] = $modObj->getShow($var_name);
        }
        if ('inbox' == $this->inout) {
            $this->msgdata['fromname'] = $this->getLinkUnameFromId($this->msgdata['from_uid'], $this->msgdata['uname']);
        } else {
            $this->msgdata['toname'] = $this->getLinkUnameFromId($this->msgdata['to_uid'], $this->root->mContext->mXoopsConfig['anonymous']);
        }
        // service UserSearch
        $this->mService = $this->root->mServiceManager->getService('UserSearch');
    }

    private function send_mail(&$obj)
    {
        $mailer = $this->getMailer();
        $mailer->setFromName($this->root->mContext->mXoopsConfig['sitename']);
        $mailer->setFromEmail($this->root->mContext->mXoopsConfig['adminmail']);
        $mailer->setToEmails($this->root->mContext->mXoopsUser->get('email'));
        $mailer->setSubject($obj->get('title'));
        $mailer->setBody($obj->get('message'));
        $mailer->send();
    }

    public function executeView(&$render)
    {
        if ('inbox' == $this->inout) {
            $render->setTemplateName('message_inboxview.html');
        } else {
            $render->setTemplateName('message_outboxview.html');
        }
        $render->setAttribute('msgdata', $this->msgdata);
        $render->setAttribute('UserSearch', $this->mService);
        $render->setAttribute('message_url', XOOPS_URL.'/modules/message/index.php');
    }
}
