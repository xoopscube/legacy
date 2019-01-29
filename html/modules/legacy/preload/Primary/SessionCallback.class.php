<?php
/**
 *
 * @package Legacy
 * @version $Id: SessionCallback.class.php,v 1.5 2008/09/25 15:12:38 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

// registration `session_write_close()` on shutdown procedure final.
// For the environment in which the object before writing session is destroyed when PHP execution end.
// ex. APC, memcached etc...
// !Fix
// ob_start(create_function('', '(session_id() && session_write_close());return false;'));
function xclCallback($session_id, $session_write_close)
{
return false;
}

ob_start("xclCallback");
// !Fix end

class Legacy_SessionCallback extends XCube_ActionFilter
{
    public function preBlockFilter()
    {
        $this->mRoot->mDelegateManager->add('XCube_Session.SetupSessionHandler', 'Legacy_SessionCallback::setupSessionHandler');
        $this->mRoot->mDelegateManager->add('XCube_Session.GetSessionCookiePath', 'Legacy_SessionCallback::getSessionCookiePath');
    }

    public static function setupSessionHandler()
    {
        $sessionHandler =& xoops_gethandler('session');
        session_set_save_handler(
            array(&$sessionHandler, 'open'),
            array(&$sessionHandler, 'close'),
            array(&$sessionHandler, 'read'),
            array(&$sessionHandler, 'write'),
            array(&$sessionHandler, 'destroy'),
            array(&$sessionHandler, 'gc'));
    }
    
    public static function getSessionCookiePath(&$cookiePath)
    {
        $parse_array = parse_url(XOOPS_URL);
        $cookiePath = @$parse_array['path'].'/';
    }
}
