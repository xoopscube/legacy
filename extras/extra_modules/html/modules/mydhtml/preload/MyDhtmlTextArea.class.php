<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Mydhtml_MyDhtmlTextArea extends XCube_ActionFilter
{
	/**
	 * @public
	 */
	public function preBlockFilter()
	{
		$this->mRoot->mDelegateManager->reset('Site.TextareaEditor.BBCode.Show');
		$this->mRoot->mDelegateManager->add('Site.TextareaEditor.BBCode.Show',array(&$this, 'render'));
	}

	protected function _addScript($params)
	{
		$jQuery = XCube_Root::getSingleton()->mContext->getAttribute('headerScript');
		$jQuery->addScript('$("textarea#'.$params['id'].'").sMarkUp("bbcode", 300);');
		$jQuery->addLibrary('/modules/mydhtml/templates/smarkup/jquery.smarkup.js');
		$jQuery->addLibrary('/modules/mydhtml/templates/smarkup/smarkup.js');
		$jQuery->addLibrary('/modules/mydhtml/templates/smarkup/conf/bbcode/conf.js');
		$jQuery->addStylesheet('/modules/mydhtml/templates/smarkup/skins/style.css');
		$jQuery->addStylesheet('/modules/mydhtml/templates/smarkup/skins/default/style.css');
	}

	/**
	 *	@public
	*/
	public function render(&$html, $params)
	{
		//$form =& new XoopsFormDhtmlTextArea($name, $name, $value, $rows, $cols);		
		$root =& XCube_Root::getSingleton();
		$renderSystem =& $root->getRenderSystem(XOOPSFORM_DEPENDENCE_RENDER_SYSTEM);
		
		$renderTarget =& $renderSystem->createRenderTarget('main');
	
		$renderTarget->setAttribute('legacy_module', 'mydhtml');
		$renderTarget->setTemplateName("mydhtml_textarea.html");
		$renderTarget->setAttribute("element", $params);
	
		$renderSystem->render($renderTarget);
	
		$html = $renderTarget->getResult();
		$this->_addScript($params);
	}
}

?>
