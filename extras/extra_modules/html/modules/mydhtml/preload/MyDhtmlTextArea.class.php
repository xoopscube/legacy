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
	function preBlockFilter()
	{
		$this->mRoot->mDelegateManager->reset('Site.TextareaEditor.BBCode.Show');
		$this->mRoot->mDelegateManager->add('Site.TextareaEditor.BBCode.Show',array(&$this, 'render'));
	}

	/**
	 *	@public
	*/
	function render(&$html, $id, $caption, $name, $value, $rows, $cols)
	{
		//$form =& new XoopsFormDhtmlTextArea($name, $name, $value, $rows, $cols);		
		$form = array('name'=>$name, 'value'=>$value, 'rows'=>$rows, 'cols'=>$cols, 'id'=>$id);
		$root =& XCube_Root::getSingleton();
		$renderSystem =& $root->getRenderSystem(XOOPSFORM_DEPENDENCE_RENDER_SYSTEM);
		
		$renderTarget =& $renderSystem->createRenderTarget('main');
	
		$renderTarget->setAttribute('legacy_module', 'mydhtml');
		$renderTarget->setTemplateName("mydhtml_textarea.html");
		$renderTarget->setAttribute("element", $form);
	//var_dump($renderTarget->getAttribute("element"));die();
		$renderSystem->render($renderTarget);
	
		$html = $renderTarget->getResult();
	}
}

?>
