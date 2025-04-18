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
            if ($this->root->mContext->mRequest->getRequest('ajax') == 1) {
                header('Content-Type: application/json');
                echo json_encode(['error' => _MD_MESSAGE_ACTIONMSG1]);
                exit;
            }
            $this->setErr(_MD_MESSAGE_ACTIONMSG1);
            return;
        }
        if ($modObj->get('uid') != $this->root->mContext->mXoopsUser->get('uid')) {
            if ($this->root->mContext->mRequest->getRequest('ajax') == 1) {
                header('Content-Type: application/json');
                echo json_encode(['error' => _MD_MESSAGE_ACTIONMSG8]);
                exit;
            }
            $this->setErr(_MD_MESSAGE_ACTIONMSG8);
            return;
        }

        // start After $modObj is loaded and validated
        $uid = $this->root->mContext->mXoopsUser->get('uid');
        $inboxTable = $modHand->mTable;

        $key = ($this->inout === 'inbox') ? 'inbox_id' : 'outbox_id';
        $this->msgdata['param'] = ($this->inout === 'inbox') ? 'inbox' : 'outbox';
        $this->msgdata['inout_short'] = ($this->inout === 'inbox') ? 'in' : 'out';
        $currentId = $modObj->get($key);

        // Previous message (smaller ID)
        $sqlPrev = "SELECT $key FROM $inboxTable WHERE uid=$uid AND $key < $currentId ORDER BY $key DESC LIMIT 1";
        $resultPrev = $modHand->db->query($sqlPrev);
        $prevId = ($row = $modHand->db->fetchArray($resultPrev)) ? $row[$key] : null;

        // Next message (larger ID)
        $sqlNext = "SELECT $key FROM $inboxTable WHERE uid=$uid AND $key > $currentId ORDER BY $key ASC LIMIT 1";
        $resultNext = $modHand->db->query($sqlNext);
        $nextId = ($row = $modHand->db->fetchArray($resultNext)) ? $row[$key] : null;

        // Build navigation and $this->msgdata
        foreach (array_keys($modObj->gets()) as $var_name) {
            $this->msgdata[$var_name] = $modObj->getShow($var_name);
        }
        if ('inbox' == $this->inout) {
            $this->msgdata['fromname'] = $this->getLinkUnameFromId($this->msgdata['from_uid'], $this->msgdata['uname']);
        } else {
            $this->msgdata['toname'] = $this->getLinkUnameFromId($this->msgdata['to_uid'], $this->root->mContext->mXoopsConfig['anonymous']);
        }
        $this->msgdata['prev_id'] = $prevId;
        $this->msgdata['next_id'] = $nextId;
        $this->msgdata['key'] = $key;
        $this->msgdata['utime'] = $modObj->get('utime');

        // --- AJAX block: must be AFTER $this->msgdata is built ---
        if ($this->root->mContext->mRequest->getRequest('ajax') == 1) {
            header('Content-Type: application/json');
            
            // Handle AJAX lock/unlock requests
            if ('inbox' == $this->inout && 'POST' == $_SERVER['REQUEST_METHOD'] && 
                'lock' == $this->root->mContext->mRequest->getRequest('cmd')) {
                
                if (1 == (int)$this->root->mContext->mRequest->getRequest('lock')) {
                    $modObj->set('is_read', 2);  // Lock the message
                } else {
                    $modObj->set('is_read', 1);  // Unlock the message
                }
                $modHand->insert($modObj);
                
                // Update the is_read value in msgdata to reflect the change
                $this->msgdata['is_read'] = $modObj->get('is_read');
            }
            // Update message status for inbox messages if unread
            elseif ('inbox' == $this->inout && 0 == $modObj->get('is_read')) {
                $modObj->set('is_read', 1);
                $modHand->insert($modObj, true);
                // Update the is_read value in msgdata to reflect the change
                $this->msgdata['is_read'] = 1;
            }
            
            // Get the avatar URL using the same function as the Smarty modifier
            $avatarUrl = '';
            if ('inbox' == $this->inout && isset($this->msgdata['from_uid'])) {
                $handler = xoops_gethandler('user');
                $user = $handler->get(intval($this->msgdata['from_uid']));
                if (is_object($user) && $user->isActive() && ($user->get('user_avatar') != "blank.gif") && file_exists(XOOPS_UPLOAD_PATH . "/" . $user->get('user_avatar'))) {
                    $avatarUrl = XOOPS_UPLOAD_URL . "/" . $user->getShow('user_avatar');
                } else {
                    $avatarUrl = XOOPS_URL . "/modules/user/images/no_avatar.gif";
                }
            }
            
            echo json_encode([
                'subject' => $this->msgdata['title'],
                'body' => $this->msgdata['message'],
                'from' => $this->msgdata['fromname'] ?? '',
                'to' => $this->msgdata['toname'] ?? '',
                'date' => isset($this->msgdata['utime']) ? date('Y-m-d H:i', $this->msgdata['utime']) : '',
                'prev_id' => $this->msgdata['prev_id'],
                'next_id' => $this->msgdata['next_id'],
                'key' => $this->msgdata['key'],
                'inout' => $this->inout,
                'param' => $this->msgdata['param'],
                'inout_short' => $this->msgdata['inout_short'],
                'avatar_url' => $avatarUrl,
                'from_uid' => $this->msgdata['from_uid'] ?? 0,
                'is_read' => $this->msgdata['is_read'] ?? 1,
                'success' => true,
                'message' => 'lock' == $this->root->mContext->mRequest->getRequest('cmd') ? 
                    ($this->msgdata['is_read'] == 2 ? 'Message locked' : 'Message unlocked') : 
                    'Message loaded'
            ]);
            exit;
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
        $render->setAttribute('inout', $this->inout); // Previous and Next urls
    }
}
