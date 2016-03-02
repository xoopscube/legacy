<?php
/**
 * @package user
 * @version $Id: UserDataDownloadAction.class.php,v 1.1 2007/05/15 02:34:42 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . "/user/class/AbstractListAction.class.php";

class User_UserDataDownloadAction extends User_Action
{
    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('users');
        return $handler;
    }

    public function _getBaseUrl()
    {
        return "./index.php?action=UserDataDownload";
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName("user_data_download.html");
        $member_handler =& xoops_gethandler('member');
        $user_count = $member_handler->getUserCount();
        $render->setAttribute('user_count', $user_count);
    }
    
    public function getDefaultView(&$controller, &$xoopsUser)
    {
        return USER_FRAME_VIEW_INDEX;
    }
    
    
    /// export CSV file
    public function execute(&$controller, &$xoopsUser)
    {
        $filename = sprintf('%s_User_data_List.csv', $GLOBALS['xoopsConfig']['sitename']);
        $text = '';
        $field_line = '';
        
        $user_handler =& $this->_getHandler();
        $criteria = new CriteriaElement();
        $criteria->setSort('uid');
        $users = $user_handler->getObjects($criteria);
        if (!$users || count($users)==0) {
            return USER_FRAME_VIEW_INDEX;
        }
        foreach ($users[0]->gets() as $key=>$var) {
            $_f = '_MD_USER_LANG_'.strtoupper($key);
            $field_line .= (defined($_f) ? constant($_f) : $key).",";
        }
        $field_line .= "\n";
        
        foreach ($users as $u) {
            $user_data = '';
            foreach ($u->gets() as $key=>$value) {
                switch ($key) {
                  case 'user_regdate':
                  case 'last_login':
                    $value = $value ? formatTimestamp($value, 'Y/n/j H:i') : '';
                    break;
                  default:
                }
                if (preg_match('/[,"\r\n]/', $value)) {
                    $value = preg_replace('/"/', "\"\"", $value);
                    $value = "\"$value\"";
                }
                $user_data .= $value . ',';
            }
            $text .= trim($user_data, ',')."\n";
        }
        $text = $field_line.$text;
        
        /// japanese 
        if (strncasecmp($GLOBALS['xoopsConfig']['language'], 'ja', 2)===0) {
            if (_CHARSET !== 'UTF-8') {
                mb_convert_variables('UTF-8', _CHARSET, $text);
            }
            $text = pack('C*', 0xEF, 0xBB, 0xBF) . $text;
        }
        
        if (preg_match('/firefox/i', xoops_getenv('HTTP_USER_AGENT'))) {
            header("Content-Type: application/x-csv");
        } else {
            header("Content-Type: application/vnd.ms-excel");
        }
        
        
        header("Content-Disposition: attachment ; filename=\"{$filename}\"") ;
        exit($text);
    }
}
