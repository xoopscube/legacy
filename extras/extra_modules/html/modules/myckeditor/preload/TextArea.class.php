<?php
/**
 * @file
 * @package myckeditor
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Myckeditor_TextArea extends XCube_ActionFilter
{
	/**
	 * @public
	 */
	function preBlockFilter()
	{
		$this->mRoot->mDelegateManager->reset('Site.TextareaEditor.HTML.Show');
		$this->mRoot->mDelegateManager->add('Site.TextareaEditor.HTML.Show',array(&$this, 'render'));
	}

	/**
	 *	@public
	*/
	public function render(&$html, $params)
	{
		$root =& XCube_Root::getSingleton();
		$renderSystem =& $root->getRenderSystem(XOOPSFORM_DEPENDENCE_RENDER_SYSTEM);
		
		$renderTarget =& $renderSystem->createRenderTarget('main');
	
		$renderTarget->setAttribute('legacy_module', 'myckeditor');
		$renderTarget->setTemplateName("myckeditor_textarea.html");
		$renderTarget->setAttribute("element", $params);
	
		$renderSystem->render($renderTarget);
	
		$html = $renderTarget->getResult();
	
		$this->_addScript($params);
	}

	protected function _addScript(/*** string[] ***/ $params)
	{
		$root = XCube_Root::getSingleton();
		$jQuery = $root->mContext->getAttribute('headerScript');
		$jQuery->addScript('var ckconfig_'.$params['id'].' = {toolbar:[["Source", "-", "Bold", "Italic", "-", "NumberedList", "BulletedList", "-", "Link", "Unlink"],["UIColor"]]};');
		$jQuery->addScript('$("textarea#'.$params['id'].'").ckeditor(ckconfig_'.$params['id'].');');
		//$jQuery->addScript('CKEDITOR.replace("ckeditor");');
		$jQuery->addLibrary('/modules/myckeditor/ckeditor/ckeditor.js');
		$jQuery->addLibrary('/modules/myckeditor/ckeditor/adapters/jquery.js');
	}
}

?>
