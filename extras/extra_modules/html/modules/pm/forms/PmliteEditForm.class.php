<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH."/core/XCube_ActionForm.class.php";

/***
 * @internal
 * This class is generated by makeActionForm tool.
 * 
 * This class was generated in the old days. We should to check it.
 * @abstract
 */
class Pm_PmliteEditForm extends XCube_ActionForm
{
    public $mState = null;
    
    public function getTokenName()
    {
        return "module.pm.PmliteEditForm.TOKEN";
    }

    public function prepare()
    {
        $this->mFormProperties['subject'] = new XCube_StringProperty('subject');
        $this->mFormProperties['message'] = new XCube_TextProperty('message');

        // set fields
        $this->mFieldProperties['subject'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['subject']->setDependsByArray(array('required', 'maxlength'));
        $this->mFieldProperties['subject']->addMessage("required", _MD_PM_ERROR_REQUIRED, _MD_PM_LANG_SUBJECT);
        $this->mFieldProperties['subject']->addMessage('maxlength', _MD_PM_ERROR_MAXLENGTH, _MD_PM_LANG_SUBJECT, '255');
        $this->mFieldProperties['subject']->addVar('maxlength', '255');

        $this->mFieldProperties['message'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['message']->setDependsByArray(array('required'));
        $this->mFieldProperties['message']->addMessage("required", _MD_PM_ERROR_REQUIRED, _MD_PM_LANG_MESSAGE);

        if (is_object($this->mState)) {
            $this->mState->prepare($this);
        }
    }
    
    /***
     * @internal
     * @attention
     *     Only Pm_PmliteEditFormReplyState should call this method. 
     */
    public function setToUserByUid($uid)
    {
        $this->set('to_userid', $uid);
    }
    
    public function fetch()
    {
        parent::fetch();

        if (is_object($this->mState)) {
            $this->mState->fetch($this);
        }
    }
    
    public function changeStateReply()
    {
        $this->mState =& new Pm_PmliteEditFormReplyState();
    }
    
    public function resetToUser()
    {
    }
}

class Pm_PmliteComboEditForm extends Pm_PmliteEditForm
{
    public function prepare()
    {
        parent::prepare();
        $this->mFormProperties['to_userid'] = new XCube_IntProperty('to_userid');
    }
    
    public function setToUserByUid($uid)
    {
        $this->set('to_userid', $uid);
    }

    /**
     * Check existence of the target user.
     */
    public function validateTo_userid()
    {
        if ($this->get('to_userid')) {
            $handler =& xoops_gethandler('user');
            $user =& $handler->get($this->get('to_userid'));

            if (!(is_object($user) && $user->isActive())) {
                $this->set('to_userid', 0);
                $this->addErrorMessage(_MD_PM_ERROR_USERNOEXIST);
            }
        }
    }

    public function update(&$obj)
    {
        $obj->set('to_userid', $this->get('to_userid'));
        $obj->set('subject', $this->get('subject'));
        $obj->set('msg_text', $this->get('message'));
    }
    
    public function resetToUser()
    {
        $this->set('to_userid', 0);
    }
}

class Pm_PmliteDirectEditForm extends Pm_PmliteEditForm
{
    public $_mUid;

    public function prepare()
    {
        parent::prepare();
        $this->mFormProperties['to_uname'] = new XCube_StringProperty('to_uname');
    }

    public function setToUserByUid($uid)
    {
        $handler =& xoops_gethandler('user');
        $user =& $handler->get($uid);

        if (is_object($user) && $user->isActive()) {
            $this->set('to_uname', $user->get('uname'));
        }
    }
    
    public function fetch()
    {
        parent::fetch();
        
        if (xoops_getrequest('to_userid') != null && xoops_getrequest('to_uname') == null) {
            $this->setToUserByUid(xoops_getrequest('to_userid'));
        }
    }
    
    /***
     * @internal
     * Check existence of the target user.
     */
    public function validateTo_uname()
    {
        if ($this->get('to_uname')) {
            $handler =& xoops_gethandler('user');
            $criteria =& new Criteria("uname", $this->get("to_uname"));
            $userArr =& $handler->getObjects($criteria);

            if (count($userArr) > 0 && is_object($userArr[0]) && $userArr[0]->isActive()) {
                $this->_mUid = $userArr[0]->get('uid');
            } else {
                $this->addErrorMessage(_MD_PM_ERROR_PLZTRYAGAIN);
            }
        } else {
            $this->addErrorMessage(_MD_PM_ERROR_PLZTRYAGAIN);
        }
    }

    public function update(&$obj)
    {
        $obj->set('to_userid', $this->_mUid);
        $obj->set('subject', $this->get('subject'));
        $obj->set('msg_text', $this->get('message'));
    }
    
    public function resetToUser()
    {
        $this->_mUid = 0;
    }
}

/***
 * @internal
 * This shows reply-mode for ActionForm of pmlite.
 */
class Pm_PmliteEditFormReplyState
{
    /**
     * @param $form PmliteEditForm
     */
    public function prepare(&$form)
    {
        $form->mFormProperties['msg_id'] = new XCube_IntProperty('msg_id');
    }

    /***
     * @internal
     * @param $form PmliteEditForm
     */
    public function fetch(&$form)
    {
        if ($form->get('msg_id')) {
            $handler =& xoops_gethandler('privmessage');
            $pm =& $handler->get($form->get('msg_id'));
            if (is_object($pm)) {
                $root =& XCube_Root::getSingleton();
                $currentUser =& $root->mContext->mXoopsUser;
                if ($pm->get('to_userid') == $currentUser->get('uid')) {
                    $form->setToUserByUid($pm->get('from_userid'));
                    
                    //
                    // Create subject
                    //
                    if (preg_match("/^Re\[(\d+)\]:(.*)/", $pm->get('subject'), $matches)) {
                        $form->set('subject', "Re[" . $matches[1] . "]: " . $matches[2]);
                    } elseif (preg_match("/^Re:(.*)/", $pm->get('subject'), $matches)) {
                        $form->set('subject', "Re[2]: " . $matches[1]);
                    } else {
                        $form->set('subject', "Re: " . $pm->get('subject'));
                    }

                    // TODO Use Active Check
                    $handler =& xoops_gethandler('user');
                    $user =& $handler->get($pm->get('from_userid'));
                    
                    if (!(is_object($user) && $user->isActive())) {
                        $this->addErrorMessage(_MD_PM_ERROR_USERNOEXIST);
                    } else {
                        $message = "[quote]\n";
                        $message .= sprintf(_MD_PM_ERROR_USERWROTE, $user->get('uname')) . "\n";
                        $message .= $pm->get("msg_text") . "\n";
                        $message .= "[/quote]\n";
                        $form->set('message', $message);
                    }
                    return;
                }
            }
        }

        //
        // Reset
        //
        $form->set('msg_id', 0);
        $form->resetToUser();
    }
}
