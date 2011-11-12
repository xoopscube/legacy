<?php
/**
 * @brief This is all bells and whistles. This preload improve reports of the standard debugger.
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/modules/legacy/class/Legacy_Debugger.class.php";

class DevelopPHPDebugger extends XCube_ActionFilter
{
	function preFilter()
	{
		$this->mController->mSetupDebugger->add("DevelopPHPDebugger::myFactory");
	}
	
	function myFactory(&$instance, $debug_mode)
	{
		switch($debug_mode) {
			case XOOPS_DEBUG_PHP:
				$instance = new My_PHPDebugger();
				break;

			case XOOPS_DEBUG_MYSQL:
				$instance = new My_MysqlDebugger();
				break;

			case XOOPS_DEBUG_SMARTY:
				$instance = new My_SmartyDebugger();
				break;
			
			case XOOPS_DEBUG_OFF:
			default:
				$instance = new Legacy_NonDebugger();
				break;
		}
	}
}

class My_PHPDebugger extends Legacy_PHPDebugger
{
	function prepare()
	{
		error_reporting(E_ALL);
	}
}

class My_MysqlDebugger extends Legacy_MysqlDebugger
{
	function prepare()
	{
	}
}

class My_SmartyDebugger extends Legacy_SmartyDebugger
{
	function prepare()
	{
	}
}


?>