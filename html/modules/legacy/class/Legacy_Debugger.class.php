<?php
/**
 *
 * @package Legacy
 * @version $Id: Legacy_Debugger.class.php,v 1.4 2008/09/25 15:11:30 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/class/errorhandler.php";

define("XOOPS_DEBUG_OFF",0);
define("XOOPS_DEBUG_PHP",1);
define("XOOPS_DEBUG_MYSQL",2);
define("XOOPS_DEBUG_SMARTY",3);

class Legacy_DebuggerManager
{
	/***
	Create XoopsDebugger instance.
	You must not communicate with this method directly.
	*/
	function createInstance(&$instance, $debug_mode)
	{
		if (is_object($instance)) {
			return;
		}
		
		switch($debug_mode) {
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
	function Legacy_AbstractDebugger()
	{
	}

	function prepare()
	{
	}
	
	function isDebugRenderSystem()
	{
		return false;
	}

	/***
	 * @return string Log as html code.
	 */	
	function renderLog()
	{
	}
	
	function displayLog()
	{
	}
}

class Legacy_NonDebugger extends Legacy_AbstractDebugger
{
}

/***
 * @internal
This class works for "PHP debugging mode".
*/
class Legacy_PHPDebugger extends Legacy_AbstractDebugger
{
	function prepare()
	{
		error_reporting(E_ALL);
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
	function prepare()
	{
		$GLOBALS['xoopsErrorHandler'] =& XoopsErrorHandler::getInstance();
		$GLOBALS['xoopsErrorHandler']->activate(true);
	}
	
	function renderLog()
	{
		$xoopsLogger =& XoopsLogger::instance();
		return $xoopsLogger->dumpAll();
	}
	
	function displayLog()
	{
        echo '<script type="text/javascript">
        <!--//
        debug_window = openWithSelfMain("", "xoops_debug", 680, 600, true);
        ';
        $content = '<html><head><meta http-equiv="content-type" content="text/html; charset='._CHARSET.'" /><meta http-equiv="content-language" content="'._LANGCODE.'" /><title>'.htmlspecialchars($GLOBALS['xoopsConfig']['sitename']).'</title><link rel="stylesheet" type="text/css" media="all" href="'.getcss($GLOBALS['xoopsConfig']['theme_set']).'" /></head><body>'.$this->renderLog().'<div style="text-align:center;"><input class="formButton" value="'._CLOSE.'" type="button" onclick="javascript:window.close();" /></div></body></html>';
        $lines = preg_split("/(\r\n|\r|\n)( *)/", $content);
        foreach ($lines as $line) {
            echo 'debug_window.document.writeln("'.str_replace('"', '\"', $line).'");';
        }
        echo '
        debug_window.document.close();
        //-->
        </script>';
	}
}

/***
 * @internal
This class works for "Smarty debugging mode".
*/
class Legacy_SmartyDebugger extends Legacy_AbstractDebugger
{
	function prepare()
	{
		$GLOBALS['xoopsErrorHandler'] =& XoopsErrorHandler::getInstance();
		$GLOBALS['xoopsErrorHandler']->activate(true);
	}
	
	function isDebugRenderSystem()
	{
		$root =& XCube_Root::getSingleton();
		$user =& $root->mContext->mXoopsUser;
		
		return is_object($user) ? $user->isAdmin(0) : false;
	}
}

?>
