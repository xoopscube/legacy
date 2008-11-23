<?php
/**
 *
 * @package Legacy
 * @version $Id: ActionSearchForm.class.php,v 1.4 2008/09/25 15:11:16 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH."/core/XCube_ActionForm.class.php";

class Legacy_ActionSearchForm extends XCube_ActionForm 
{
	var $mState = null;
	
	function prepare()
	{
		$this->mFormProperties['keywords']=new XCube_StringProperty('keywords');

		// set fields
		$this->mFieldProperties['keywords']=new XCube_FieldProperty($this);
		$this->mFieldProperties['keywords']->setDependsByArray(array('required'));
		$this->mFieldProperties['keywords']->addMessage("required",_AD_LEGACY_ERROR_SEARCH_REQUIRED);
	}

	function fetch()
	{
		parent::fetch();
		$this->set('keywords', trim($this->get('keywords')));
	}
}	
