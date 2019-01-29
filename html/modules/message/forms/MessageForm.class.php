<?php
/**
 * @license http://www.gnu.org/licenses/gpl.txt GNU GENERAL PUBLIC LICENSE Version 3
 * @author Marijuana
 */
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
require_once XOOPS_ROOT_PATH.'/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH.'/legacy/class/Legacy_Validator.class.php';

class MessageForm extends XCube_ActionForm
{
    public $fuid = 0;
  
    public function __construct()
    {
        // ! call parent::__construct() instead of parent::Controller()
        parent::__construct();
        //parent::XCube_ActionForm();
    }
  
    public function getTokenName()
    {
        return 'module.message.NewMessage.TOKEN';
    }
  
    private function set_Property($key, $classname = 'XCube_StringProperty')
    {
        $this->mFormProperties[$key] = new $classname($key);
    }
  
    public function prepare()
    {
        $this->set_Property('uname');
        $this->set_Property('title');
        $this->set_Property('Legacy_Event_User_Preview');
        $this->set_Property('Legacy_Event_User_Submit');
        $this->set_Property('note', 'XCube_TextProperty');
    
        $this->mFieldProperties['uname'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['uname']->setDependsByArray(array('required', 'maxlength'));
        $this->mFieldProperties['uname']->addMessage('required', _MD_MESSAGE_FORMERROR1);
        $this->mFieldProperties['uname']->addMessage('maxlength', _MD_MESSAGE_FORMERROR2);
        $this->mFieldProperties['uname']->addVar('maxlength', '30');
    
        $this->mFieldProperties['title'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['title']->setDependsByArray(array('required', 'maxlength'));
        $this->mFieldProperties['title']->addMessage('required', _MD_MESSAGE_FORMERROR3);
        $this->mFieldProperties['title']->addMessage('maxlength', _MD_MESSAGE_FORMERROR4);
        $this->mFieldProperties['title']->addVar('maxlength', '100');
    
        $this->mFieldProperties['note'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['note']->setDependsByArray(array('required'));
        $this->mFieldProperties['note']->addMessage('required', _MD_MESSAGE_FORMERROR5);
    }
  
    public function validateUname()
    {
        if ($this->get('uname') != "") {
            $uname = mb_strcut($this->get('uname'), 0, 30);
            $userhand = xoops_gethandler('user');
            $criteria = new CriteriaCompo(new Criteria('uname', $uname));
            $uobj = $userhand->getObjects($criteria);
            if (isset($uobj) && is_array($uobj) && count($uobj) == 1) {
                $this->fuid = $uobj[0]->get('uid');
            } else {
                $this->fuid = 0;
                $this->addErrorMessage(_MD_MESSAGE_FORMERROR6);
            }
            $this->set('uname', $uname);
        }
    }
  
    public function getShow($name, $type = 'toShow')
    {
        if (isset($this->mFormProperties[$name])) {
            $root = XCube_Root::getSingleton();
            $textFilter = $root->getTextFilter();
            return $textFilter->$type($this->mFormProperties[$name]->getValue(null));
        }
        return "";
    }
  
    public function update(&$obj)
    {
        $root = XCube_Root::getSingleton();
        $obj->set('uid', $this->fuid);
        $obj->set('from_uid', $root->mContext->mXoopsUser->get('uid'));
        $obj->set('title', $this->get('title'));
        $obj->set('message', $this->get('note'));
        $obj->set('utime', time());
    }
  
    public function setRes(&$obj)
    {
        $title = $obj->get('title', 'n');
        if (!preg_match("/^Re:/i", $title)) {
            $title = 'Re: '.$title;
        }
    
        $userhand = xoops_gethandler('user');
        $uobj = $userhand->get($obj->get('from_uid'));
        if (is_object($uobj)) {
            $this->set('uname', $uobj->get('uname'));
            $this->set('title', $title);
            $this->set('note', '[quote]'.$obj->get('message').'[/quote]');
            return true;
        }
        return false;
    }
  
    public function setUser(&$user)
    {
        $this->set('uname', $user->get('uname'));
    }
}
