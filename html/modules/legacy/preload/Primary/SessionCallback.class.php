<?php
/**
 *
 * @package Legacy
 * @version $Id: SessionCallback.class.php,v 1.5 2008/09/25 15:12:38 kilica Exp $
 * @copyright Copyright 2005-2025 XOOPS Cube Project  <https://github.com/xoopscube/>
 * @license   GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

// Registers session_write_close() to execute during the shutdown procedure.
// This ensures session data is properly saved when PHP execution ends,
// especially in environments where objects might be destroyed prematurely
// (e.g., when using APC, memcached, etc.).
function xclCallback($buffer)
{
    if (session_id()) {
        session_write_close();
    }
    return $buffer;
}

ob_start('xclCallback');

class Legacy_SessionCallback extends XCube_ActionFilter
{
    public function preBlockFilter()
    {
        $this->mRoot->mDelegateManager->add('XCube_Session.SetupSessionHandler', 'Legacy_SessionCallback::setupSessionHandler');
        $this->mRoot->mDelegateManager->add('XCube_Session.GetSessionCookiePath', 'Legacy_SessionCallback::getSessionCookiePath');
    }

    public static function setupSessionHandler()
    {
        $sessionHandler = xoops_gethandler('session');
        // PHP 8.4+: object implementing SessionHandlerInterface
        session_set_save_handler($sessionHandler, true);
    }

    public static function getSessionCookiePath(&$cookiePath)
    {
        $parse_array = parse_url(XOOPS_URL);
        $cookiePath = isset($parse_array['path']) ? $parse_array['path'] : '';
        $cookiePath .= '/';
    }
}
