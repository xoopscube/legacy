<?php
/**
 * @file
 * @package mydhtml
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Mydhtml_JQueryLoad extends XCube_ActionFilter
{
	/**
	 * @public
	 */
	function preBlockFilter()
	{
		/*
		if (!$this->mRoot->mContext->hasAttribute('module.mydhtml.HasSetAssetManager')) {
			$delegate =& new XCube_Delegate();
			$delegate->register('Module.mydhtml.Event.GetAssetManager');
			$delegate->add(array(&$this, 'getManager'));
			$this->mRoot->mContext->setAttribute('module.mydhtml.HasSetAssetManager', true);
		}
		*/
        $this->mRoot->mDelegateManager->add('Site.JQuery.AddFunction',array(&$this, 'addScript'));
	}

	/*
		This is sample jQuery script.
		Just use this ONLY on trial server !!!!
	*/
	function addScript(&$jQuery)
	{
		$jQuery->addScript('$(".mydhtml").sMarkUp("bbcode", 300);');
		$jQuery->addLibrary('/modules/mydhtml/templates/smarkup/jquery.smarkup.js');
		$jQuery->addLibrary('/modules/mydhtml/templates/smarkup/smarkup.js');
		$jQuery->addLibrary('/modules/mydhtml/templates/smarkup/conf/bbcode/conf.js');
		$jQuery->addStylesheet('/modules/mydhtml/templates/smarkup/skins/style.css');
		$jQuery->addStylesheet('/modules/mydhtml/templates/smarkup/skins/default/style.css');
	}
}

?>
