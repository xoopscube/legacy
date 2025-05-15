<?php
/**
 *
 * @package Legacy
 * @version $Id: Legacy_Debugger.class.php,v 1.4 2008/09/25 15:11:30 kilica Exp $
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/class/errorhandler.php';

const XOOPS_DEBUG_OFF = 0;
const XOOPS_DEBUG_PHP = 1;
const XOOPS_DEBUG_MYSQL = 2;
const XOOPS_DEBUG_SMARTY = 3;

class Legacy_DebuggerManager
{
    /***
     * Create XoopsDebugger instance.
     * You must not communicate with this method directly.
     * @param $instance
     * @param $debug_mode
     */
    public static function createInstance(&$instance, $debug_mode)
    {
        if (is_object($instance)) {
            return;
        }

        switch ($debug_mode) {
            case XOOPS_DEBUG_PHP:
                $instance = new Legacy_PHPDebugger();
                break;

            case XOOPS_DEBUG_MYSQL:
                $instance = new Legacy_MysqlDebugger();
                break;

            case XOOPS_DEBUG_SMARTY:
                $instance = new Legacy_SmartyDebugger();
                break;

            case XOOPS_DEBUG_OFF:
            default:
                $instance = new Legacy_NonDebugger();
                break;
        }
    }
}

class Legacy_AbstractDebugger
{
    public function __construct()
    {
    }

    public function prepare()
    {
    }

    public function isDebugRenderSystem()
    {
        return false;
    }

    /***
     * @return string Log as html code.
     */
    public function renderLog()
    {
    }

    public function displayLog()
    {
    }
}

class Legacy_NonDebugger extends Legacy_AbstractDebugger
{
}

/***
 * @internal
 * 
 * This class works for "PHP debugging mode".
 * If you specifically need to suppress notices 
 * (though often not recommended during development), 
 * use error_reporting(E_ALL & ~E_NOTICE);
 * Similarly, use & ~E_DEPRECATED if you must suppress deprecation notices. 
 * Avoid XOR (^) for simply removing levels, 
 * as & ~ (AND NOT) is clearer and more standard for this purpose 
**/
class Legacy_PHPDebugger extends Legacy_AbstractDebugger
{
    public function prepare()
    {
        if (defined('XOOPS_ERROR_REPORTING_LEVEL')) {
            error_reporting(XOOPS_ERROR_REPORTING_LEVEL);
        } else {

            error_reporting(E_ALL);
        }
        $GLOBALS['xoopsErrorHandler'] =& XoopsErrorHandler::getInstance();
        $GLOBALS['xoopsErrorHandler']->activate(true);
    }
}

/***
 * @internal
This class works for "Mysql debugging mode".
*/
class Legacy_MysqlDebugger extends Legacy_AbstractDebugger
{
    public function prepare()
    {
        $GLOBALS['xoopsErrorHandler'] =& XoopsErrorHandler::getInstance();
        $GLOBALS['xoopsErrorHandler']->activate(true);
    }

    public function renderLog()
    {
        $xoopsLogger =& XoopsLogger::instance();
        return $xoopsLogger->dumpAll();
    }

    // Inline debug view @gigamaster
    public function displayLog()
    {
        $content = '<div id="xcl-debug-log" style="position:fixed;bottom:0;left:0;right:0;background:#111;border-top:1px solid #000;color:#eee;padding:10px;z-index:9999;max-height:50vh;overflow:auto;">';
        $content .= '<h3  style="margin: 0 0 0.5rem;"><input class="btn close" value="'._CLOSE.'" type="button" onclick="document.getElementById(\'xcl-debug-log\').style.display=\'none\';"> Debug Log</h3>';
        $content .= $this->renderLog();
        $content .= '</div>';
        echo $content;
    }
}

/***
 * @internal
This class works for "Smarty debugging mode".
*/
class Legacy_SmartyDebugger extends Legacy_AbstractDebugger
{
    public function prepare()
    {
        $GLOBALS['xoopsErrorHandler'] =& XoopsErrorHandler::getInstance();
        $GLOBALS['xoopsErrorHandler']->activate(true);
    }

    public function isDebugRenderSystem()
    {
        $root =& XCube_Root::getSingleton();
        $user =& $root->mContext->mXoopsUser;

        return is_object($user) ? $user->isAdmin(0) : false;
    }
}
