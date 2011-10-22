<?php
/**
 *
 * @package Legacy
 * @version $Id: SearchShowallForm.class.php,v 1.3 2008/09/25 15:12:40 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/Legacy_Validator.class.php";

require_once XOOPS_MODULE_PATH . "/legacy/forms/SearchResultsForm.class.php";

class Legacy_SearchShowallForm extends Legacy_SearchResultsForm
{
	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['mid'] =new XCube_IntProperty('mid');
		$this->mFormProperties['andor'] =new XCube_StringProperty('andor');
		$this->mFormProperties['query'] =new XCube_StringProperty('query');
		$this->mFormProperties['start'] =new XCube_IntProperty('start');
	
		//
		// Set field properties
		//
		$this->mFieldProperties['andor'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['andor']->setDependsByArray(array('mask'));
		$this->mFieldProperties['andor']->addMessage('mask', _MD_LEGACY_ERROR_MASK, _MD_LEGACY_LANG_ANDOR);
		$this->mFieldProperties['andor']->addVar('mask', '/^(AND|OR|exact)$/i');

		$this->set('start', 0);
	}
	
	function update(&$params)
	{
		$params['queries'] = $this->mQueries;
		$params['andor'] = $this->get('andor');
		$params['maxhit'] = LEGACY_SEARCH_SHOWALL_MAXHIT;
		$params['start'] = $this->get('start');
	}
}

?>
