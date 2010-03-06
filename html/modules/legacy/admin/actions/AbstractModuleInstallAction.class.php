<?php
/**
 *
 * @package Legacy
 * @version $Id: AbstractModuleInstallAction.class.php,v 1.3 2008/09/25 15:11:35 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

 if (!defined('XOOPS_ROOT_PATH')) exit();

/**
 * This is abstract class for 3 action classes that are Install, Update and
 * Uninstall.
 */
class Legacy_AbstractModuleInstallAction extends Legacy_Action
{
	/**
	 * XoopsModule instance specified.
	 */
	var $mModuleObject = null;
	var $mLog = null;
	
	var $mActionForm = null;
	
	function prepare(&$controller, &$xoopsUser)
	{
		$this->_setupActionForm();
	}
	
	function _setupActionForm()
	{
	}

	function getDefaultView(&$controller, &$xoopsUser)
	{
		$dirname = trim(xoops_getrequest('dirname'));

		$installer =& $this->_getInstaller($dirname);

		$this->mModuleObject =& $installer->loadModuleObject($dirname);

		if (!is_object($this->mModuleObject)) {
			$this->mLog =& $installer->getLog();
			return LEGACY_FRAME_VIEW_ERROR;
		}

		$this->mActionForm->load($this->mModuleObject);
		
		$this->mModuleObject->loadAdminMenu();
		$this->mModuleObject->loadInfo($dirname);
		
		return LEGACY_FRAME_VIEW_INDEX;
	}

	function execute(&$controller, &$xoopsUser)
	{
		if (isset($_REQUEST['_form_control_cancel'])) {
			return LEGACY_FRAME_VIEW_CANCEL;
		}
		
		$this->mActionForm->fetch();
		$this->mActionForm->validate();
		
		$installer =& $this->_getInstaller($this->mActionForm->get('dirname'));
		$this->mModuleObject =& $installer->loadModuleObject($this->mActionForm->get('dirname'));
		
		if ($installer->hasAgree()) {
			$this->_loadAgreement();
		}

		if ($this->mActionForm->hasError()) {
			//
			// Normal modules doesn't have licence.txt. If it has licence.txt
			// return 'INPUT' view.
			//
			if ($installer->hasAgree()) {
				return LEGACY_FRAME_VIEW_INPUT;
			}
			else {
				return LEGACY_FRAME_VIEW_INDEX;
			}
		}
		
		if (!is_object($this->mModuleObject)) {
			return LEGACY_FRAME_VIEW_ERROR;
		}

		$installer->setForceMode($this->mActionForm->get('force'));
		$installer->execute($this->mActionForm->get('dirname'));

		$this->mLog =& $installer->getLog();
		
		return LEGACY_FRAME_VIEW_SUCCESS;
	}

	/**
	 * Return a procedure for this process.
	 */
	function &_getInstaller($dirname)
	{
	}
	
	function _loadAgreement()
	{
	}

	function executeViewError(&$controller,&$xoopsUser,&$renderer)
	{
		$renderer->setTemplateName("install_wizard_error.html");
		$renderer->setAttribute('log', $this->mLog);
	}
}

?>
