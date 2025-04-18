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

class deleteAction extends AbstractAction
{
    // No need for $mActionForm property here
    private string $inout = 'inbox';

    public function __construct()
    {
        if (method_exists('AbstractAction', '__construct')) {
            parent::__construct();
        }
    }

    public function execute()
    {
        $isAjax = $this->root->mContext->mRequest->getRequest('ajax') == 1;

        if ('in' == $this->root->mContext->mRequest->getRequest('inout')) {
            $this->inout = 'inbox';
        } else {
            $this->inout = 'outbox';
        }
        
        // Remove duplicate handler initialization
        $modHand = xoops_getmodulehandler($this->inout, _MY_DIRNAME);
        $boxid = (int)$this->root->mContext->mRequest->getRequest($this->inout);
        $modObj = $modHand->get($boxid);

        // --- Validation ---
        if (!is_object($modObj)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => _MD_MESSAGE_ACTIONMSG1]);
                exit; // Add exit here
            }
            $this->setErr(_MD_MESSAGE_ACTIONMSG1);
            return;
        }

        if ($modObj->get('uid') != $this->root->mContext->mXoopsUser->get('uid')) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => _MD_MESSAGE_ACTIONMSG2]);
                exit;
            }
            $this->setErr(_MD_MESSAGE_ACTIONMSG2);
            return;
        }

        // --- Deletion Attempt ---
        if ($modHand->delete($modObj)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true, 
                    'message' => _MD_MESSAGE_ACTIONMSG3, 
                    'deleted_id' => $boxid, 
                    'inout' => $this->inout
                ]);
                exit;
            }
            $this->setErr(_MD_MESSAGE_ACTIONMSG3);
        } else {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => _MD_MESSAGE_ACTIONMSG4]);
                exit;
            }
            $this->setErr(_MD_MESSAGE_ACTIONMSG4);
        }
    }

    /**
     * This method is typically used for rendering templates.
     * Since delete actions often redirect or return JSON,
     * it might remain empty or be removed if AbstractAction allows.
     */
    public function executeView(&$render)
    {
        // No view rendering needed for delete, especially for AJAX
    }
}
