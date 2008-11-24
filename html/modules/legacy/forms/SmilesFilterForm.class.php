<?php
/**
 *
 * @package Legacy
 * @version $Id: SmilesFilterForm.class.php,v 1.3 2008/09/25 15:12:40 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/class/AbstractFilterForm.class.php";

define('SMILES_SORT_KEY_ID', 1);
define('SMILES_SORT_KEY_CODE', 2);
define('SMILES_SORT_KEY_SMILE_URL', 3);
define('SMILES_SORT_KEY_EMOTION', 4);
define('SMILES_SORT_KEY_DISPLAY', 5);
define('SMILES_SORT_KEY_MAXVALUE', 5);

define('SMILES_SORT_KEY_DEFAULT', SMILES_SORT_KEY_ID);

class Legacy_SmilesFilterForm extends Legacy_AbstractFilterForm
{
	var $mSortKeys = array(
		SMILES_SORT_KEY_ID => 'id',
		SMILES_SORT_KEY_CODE => 'code',
		SMILES_SORT_KEY_SMILE_URL => 'smile_url',
		SMILES_SORT_KEY_EMOTION => 'emotion',
		SMILES_SORT_KEY_DISPLAY => 'display'
	);

	function getDefaultSortKey()
	{
		return SMILES_SORT_KEY_ID;
	}

	function fetch()
	{
		parent::fetch();
	
		if (isset($_REQUEST['target'])) {
			$this->mNavi->addExtra('target', xoops_getrequest('target'));
		}
		
		$this->_mCriteria->addSort($this->getSort(), $this->getOrder());
	}
}

?>
