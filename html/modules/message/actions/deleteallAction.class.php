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

class deleteallAction extends AbstractAction
{
    private string $inout = 'inbox';

    public function __construct()
    {
        parent::__construct();
    }

    public function execute()
    {
        $isAjax = $this->root->mContext->mRequest->getRequest('ajax') == 1;

        if ('in' == $this->root->mContext->mRequest->getRequest('inout')) {
            $this->inout = 'inbox';
            $redirectAction = 'index';
        } else {
            $this->inout = 'outbox';
            $redirectAction = 'send';
        }
        // Set default redirect URL for non-AJAX
        $this->setUrl('index.php?action=' . $redirectAction);

        // Fix: Get the POST data properly for AJAX requests
        $delid = $this->root->mContext->mRequest->getRequest('delmsg');
        
        // Debug: Log the received data
        error_log('Delete All Action - inout: ' . $this->inout . ', isAjax: ' . $isAjax . ', delid: ' . print_r($delid, true));

        // --- Initial Validation ---
        if (!is_array($delid) || 0 == count($delid)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => _MD_MESSAGE_DELETEMSG2]);
                exit;
            }
            $this->setErr(_MD_MESSAGE_DELETEMSG2);
            return;
        }

        // Fix the handler initialization - use xoops_getmodulehandler instead of Xoops::getHandler
        $modHand = xoops_getmodulehandler($this->inout, _MY_DIRNAME);
        $deleted_ids = [];
        $failed_ids = [];
        $errors = [];
        $success_messages = []; // Store success messages if needed

        // --- Loop and Delete ---
        foreach ($delid as $boxid) {
            $boxid = (int)$boxid; // Ensure integer
            $modObj = $modHand->get($boxid);

            if (!is_object($modObj)) {
                $errors[$boxid] = _MD_MESSAGE_ACTIONMSG1; // Record error for this ID
                $failed_ids[] = $boxid;
                continue; // Skip to the next ID
            }

            if ($modObj->get('uid') != $this->root->mContext->mXoopsUser->get('uid')) {
                $errors[$boxid] = _MD_MESSAGE_ACTIONMSG2; // Record error for this ID
                $failed_ids[] = $boxid;
                continue; // Skip to the next ID
            }

            if ($modHand->delete($modObj)) {
                $deleted_ids[] = $boxid;
                // Optional: Store individual success message if needed later
                // $success_messages[$boxid] = _MD_MESSAGE_ACTIONMSG3;
            } else {
                $errors[$boxid] = _MD_MESSAGE_ACTIONMSG4; // Record error for this ID
                $failed_ids[] = $boxid;
            }
        } // End foreach loop

        // --- Process Results ---
        $final_success = count($failed_ids) === 0 && count($deleted_ids) > 0;
        $final_message = '';

        if (count($deleted_ids) > 0) {
            // Use a generic success message or build one
             $final_message .= sprintf(_MD_MESSAGE_DELETEMSG_SUCCESS_NUM, count($deleted_ids)); // Assuming you have a constant like this
            // Fallback message:
            // $final_message .= count($deleted_ids) . " message(s) deleted successfully. ";
        }
        if (count($failed_ids) > 0) {
             $final_message .= sprintf(_MD_MESSAGE_DELETEMSG_FAIL_NUM, count($failed_ids)); // Assuming you have a constant like this
            // Fallback message:
            // $final_message .= count($failed_ids) . " message(s) failed to delete. ";
        }
         if (empty($final_message)) {
             $final_message = _MD_MESSAGE_DELETEMSG2; // No items selected or processed message
         }


        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $final_success,
                'message' => trim($final_message),
                'deleted_count' => count($deleted_ids),
                'failed_count' => count($failed_ids),
                'deleted_ids' => $deleted_ids,
                'failed_ids' => $failed_ids,
                'errors' => $errors, // Detailed errors per ID
                'inout' => $this->inout
            ]);
            exit;
        } else {
            // For non-AJAX, set a general success or error message
            // The original code set success on *each* successful delete and returned on the first error.
            // This modification provides a summary message after trying all.
            if ($final_success) {
                 $this->setErr(sprintf(_MD_MESSAGE_DELETEMSG_SUCCESS_NUM, count($deleted_ids))); // Or a more general success message
            } elseif (count($deleted_ids) > 0) {
                 // Partial success
                 $this->setErr(trim($final_message)); // Show combined message
            } else {
                 // Complete failure or nothing to delete
                 $this->setErr(count($errors) > 0 ? _MD_MESSAGE_ACTIONMSG4 : _MD_MESSAGE_DELETEMSG2); // General failure or no items
            }
            // The redirect URL is already set earlier.
            return; // Proceed to redirect
        }
    }

    public function executeView(&$render)
    {
        // No view rendering needed for delete all
    }
}
