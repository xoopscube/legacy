<?php
/**
 *
 * @package Legacy
 * @version $Id: ModuleInstallForm.class.php,v 1.3 2008/09/25 15:11:20 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";

class Legacy_ModuleInstallForm extends XCube_ActionForm
{
	function getTokenName()
	{
		return "module.legacy.ModuleInstallForm.TOKEN." . $this->get('dirname');
	}

	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['dirname'] =new XCube_StringProperty('dirname');
		$this->mFormProperties['force'] =new XCube_BoolProperty('force');
	}

	function load(&$obj)
	{
		$this->set('dirname', $obj->get('dirname'));
	}

	function update(&$obj)
	{
		$obj->set('dirname', $this->get('dirname'));
	}
}

?>
