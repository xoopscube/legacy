<?php
/**
 * handling class for xoops-user
 * @version $Rev$
 * @link $URL$
 */
class OpenID_Member
{
    /**
     * @var string
     */
    var $_error;
    
    /**
     * @var array
     */
    var $_configUser;

    function loginSuccess(&$user)
    {
        if (!$user->isNew()) {
            $user->setVar('last_login', time());
            $member_handler =& xoops_gethandler('member');
            $member_handler->insertUser($user, true);
        }
        $_SESSION['xoopsUserId'] = $user->getVar('uid');
        $_SESSION['xoopsUserGroups'] = $user->getGroups();
        $user_theme = $user->getVar('theme');
        if (in_array($user_theme, $GLOBALS['xoopsConfig']['theme_set_allowed'])) {
            $_SESSION['xoopsUserTheme'] = $user_theme;
        }

        Openid_Utils::redirect(_MD_OPENID_LOGGINGU, true);
    }

    function validateUname($uname)
    {
        $config_handler =& xoops_gethandler('config');
        if (!defined('XOOPS_CUBE_LEGACY')) {
            $this->_configUser =& $config_handler->getConfigsByCat(XOOPS_CONF_USER);
        } else {
            $module_handler =& xoops_gethandler('module');
            $module =& $module_handler->getByDirname('user');
            $this->_configUser =& $config_handler->getConfigsByCat(0, $module->getVar('mid'));
        }

        $member_handler =& xoops_gethandler('member');
        $criteria = new CriteriaCompo(new Criteria('uname', $uname));
        if ($member_handler->getUserCount($criteria) > 0) {
            $this->_error = '"' . $uname . '": ' . _MD_OPENID_ERROR_NICKNAMETAKEN;
            return false;
        }

        if (strlen($uname) > $this->_configUser['maxuname']) {
            $this->_error = str_replace(array('{0}', '{1}'), array(_MD_OPENID_SCREEN_NAME, $this->_configUser['maxuname']), _MD_OPENID_ERROR_MAXLENGTH);
            return false;
        }
        if (strlen($uname) < $this->_configUser['minuname'] ) {
            $this->_error = str_replace(array('{0}', '{1}'), array(_MD_OPENID_SCREEN_NAME, $this->_configUser['minuname']), _MD_OPENID_ERROR_MINLENGTH);
            return false;
        }

        // Check allow uname string pattern.
        $regex = "";
        switch($this->_configUser['uname_test_level']) {
            case 0:
                $regex = "/[^a-zA-Z0-9_-]/";
                $ifMatch = _MD_OPENID_ERROR_ALFABET_ONLY;
                break;
            case 1:
                $regex = "/[^a-zA-Z0-9_<>,\.$%#@!'\"-]/";
                $ifMatch = _MD_OPENID_ERROR_SINGLE_ONLY;
                break;
            case 2:
            	if (preg_match('/[\000-\040\177]/', $uname)) {
                    exit();
                }
                break;
        }
        if ($regex && preg_match($regex, $uname)) {
            $this->_error = $ifMatch;
            return false;
        }
        // Check bad uname patterns.
        foreach($this->_configUser['bad_unames'] as $t_uname) {
            if(!empty($t_uname) && preg_match("/${t_uname}/i", $uname)) {
                $this->_error = _MD_OPENID_ERROR_NAMERESERVED;
                return false;
            }
        }
        return true;
    }

    function validateEmail(&$email)
    {
        if (strlen($email) > 0) {
            if (!checkEmail($email)) {
                $this->_error = _MD_OPENID_ERROR_INVALID_EMAIL;
            	return false;
            }
            foreach ($this->_configUser['bad_emails'] as $t_email) {
                if (!empty($t_email) && preg_match("/${t_email}/i", $email)) {
                    $this->_error = _MD_OPENID_ERROR_BAD_EMAIL;
                    return false;
                }
            }
        } else {
            $email = '@OpenID';
        }
        return true;
    }

    function getError()
    {
        return $this->_error;
    }

    function &getUser($uid)
    {
        $member_handler =& xoops_gethandler('member');
        $user =& $member_handler->getUser($uid);
        if (false != $user && $user->getVar('level') > 0) {
            return $user;
        } else {
            $this->_error = _MD_OPENID_ERROR_USER_NOT_IDENTIFIED;
            $ret = false;
            return $ret;
        }
    }

    function &register(&$openid, &$post)
    {
        $ret = false;
        $uname = $post->get('uname');
        $email = $post->get('email');
        if (!$this->validateUname($uname) || !$this->validateEmail($email)) {
            return $ret;
        }

        $member_handler =& xoops_gethandler('member');
        $user =& $member_handler->createUser();
        $user->setVar('uname', $uname);
        $user->setVar('email', $email);
        $user->setVar('user_regdate', time());
        $user->setVar('pass', '*');
        $user->setVar('user_mailok', 0);
        $user->setVar('level', 1);
        $user->setVar('user_avatar', 'blank.gif');
        $user->setVar('last_login', time());

        $tz = $post->get('timezone_offset');
        if ($tz === FALSE) {
            $user->setVar('timezone_offset', $GLOBALS['xoopsConfig']['default_TZ']);
        } else {
            $user->setVar('timezone_offset', $tz);
        }
        if ($member_handler->insertUser($user, true)) {
            // Now, add the user to the group.
            foreach ($openid->get('gid') as $gid) {
                $member_handler->addUserToGroup($gid, $user->getVar('uid'));
            }
            return $user;
        } else {
            Openid_Utils::redirect(_MD_OPENID_ERROR_REGISTERNG);
        }
    }

    function &checkLogin(&$post)
    {
        $member_handler =& xoops_gethandler('member');
        $user =& $member_handler->loginUser($post->get4sql('uname'), $post->get4sql('pass'));
        if (false != $user && $user->getVar('level') > 0) {
            return $user;
        } else {
            $this->_error = _MD_OPENID_ERROR_INCORRECTLOGIN;
            $ret = false;
            return $ret;
        }
    }

    /**
     * Get user objects by userid array
     *
     * @param array $uids
     * @return array
     */
    function &getUsers($uids)
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('uid', '(' . implode(',', $uids) . ')', 'IN'));
        $criteria->setSort('uid');

        $member_handler =& xoops_gethandler('member');
        $users =& $member_handler->getUsers($criteria, true);
        return $users;
    }

    /**
     * Get groups by user id
     *
     * @param int $uid
     * @param string $asString
     * @return mixed string OR array
     */
    function getGroups($uid, $asString = FALSE)
    {
        static $grouplist = NULL;

        $menber =& xoops_gethandler('member');

        if (is_null($grouplist)) {
            $grouplist = $menber->getGroupList();
        }
        foreach($menber->getGroupsByUser($uid) as $gid) {
            $ret[$gid] = $grouplist[$gid];
        }
        if ($asString) {
            $ret = join($asString, $ret);
        }

        return $ret;
    }

    function logout(&$xoopsUser)
    {
        // Reset session
        $_SESSION = array();

        // clear entry from online users table
        if (is_object($xoopsUser)) {
            $onlineHandler =& xoops_gethandler('online');
            $onlineHandler->destroy($xoopsUser->get('uid'));
        }
    }

    function deleteUser(&$user)
    {
        $menber =& xoops_gethandler('member');
        $menber->deleteUser($user);
    }
}
?>