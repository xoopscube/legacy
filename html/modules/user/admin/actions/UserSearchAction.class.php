<?php
/**
 * @package user
 * @version $Id: UserSearchAction.class.php,v 1.1 2007/05/15 02:34:41 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . "/user/admin/forms/UserSearchForm.class.php";

class User_UserSearchAction extends User_Action
{
    public $mActionForm = null;
    
    // !Fix compatibility with User_Action::prepare(&$controller, &$xoopsUser, $moduleConfig)
    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    // public function prepare(&$controller, &$xoopsUser)
    {
        $this->mActionForm =new User_UserSearchForm();
        $this->mActionForm->prepare();
    }
    
    public function getDefaultView(&$controller, &$xoopsUser)
    {
        $this->mActionForm->fetch();
        
        return USER_FRAME_VIEW_INPUT;
    }
    
    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName("user_search.html");
        $render->setAttribute("actionForm", $this->mActionForm);
        
        $groupHandler =& xoops_gethandler('group');
        $groups =& $groupHandler->getObjects(null, true);
        
        $groupOptions = array();
        foreach ($groups as $gid => $group) {
            $groupOptions[$gid] = $group->getVar('name');
        }

        $matchOptions = array();
        $matchArray = array(XOOPS_MATCH_START => _STARTSWITH, XOOPS_MATCH_END => _ENDSWITH, XOOPS_MATCH_EQUAL => _MATCHES, XOOPS_MATCH_CONTAIN => _CONTAINS);
        foreach ($matchArray as $key => $value) {
            $matchOptions[$key] = $value;
        }

        $render->setAttribute('groupOptions', $groupOptions);
        $render->setAttribute('matchOptions', $matchOptions);

        $member_handler =& xoops_gethandler('member');
        $active_total = $member_handler->getUserCount(new Criteria('level', 0, '>'));
        $inactive_total = $member_handler->getUserCount(new Criteria('level', 0));
        $render->setAttribute('activeUserTotal', $active_total);
        $render->setAttribute('inactiveUserTotal', $inactive_total);
        $render->setAttribute('UserTotal', $active_total+$inactive_total);
    }
}
