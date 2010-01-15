<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Legacy_DhtmlTextArea extends XCube_ActionFilter
{
	/**
	 * @public
	 */
	function preBlockFilter()
	{
        $this->mRoot->mDelegateManager->add('Site.DhtmlTextArea.Show','Legacy_DhtmlTextArea::render',XCUBE_DELEGATE_PRIORITY_FINAL);
	}

	/**
	 *	@public
	*/
	function render(&$html, $id, $name, $name, $value, $rows, $cols)
	{
		if (!XC_CLASS_EXISTS('xoopsformelement')) {
			require_once XOOPS_ROOT_PATH . "/class/xoopsformloader.php";
		}
	
		$form =& new XoopsFormDhtmlTextArea($name, $name, $value, $rows, $cols);
		$form->setId($id);
		if ($class != null) {
			$form->setClass($class);
		}
		
		$html = $form->render();
	}
}

?>
