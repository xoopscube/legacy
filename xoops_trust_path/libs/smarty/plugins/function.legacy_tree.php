<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:	 function
 * Name:	 legacy_tree
 * Version:  1.2
 * Date:	 Mar 28, 2008 / Feb 19, 2010
 * Author:	 HIKAWA Kilica
 * Purpose:  format category tree object
 * Input:	 tree=Legacy_AbstractCategoryObject object[]
 *			 control=bool	:display control(edit,delete,add child) or not
 *			 dirname=string
 *			 className=string
 * Examples: {legacy_tree tree=$cattree control=false dirname=$dirname className=legacy_tree}
 * -------------------------------------------------------------
 */
 
function smarty_function_legacy_tree($params, &$smarty)
{
	$tree = $params['tree'];
	$control = $params['control'];
	$dirname = $params['dirname'];
	$className = $params['className'] ? $params['className'] : 'tree';
	$template = isset($params['template']) ? $params['template'] : 'legacy_inc_tree.html';

	//render template
	$render = new XCube_RenderTarget();
	$render->setTemplateName($template);
	$render->setAttribute('legacy_buffertype',XCUBE_RENDER_TARGET_TYPE_MAIN);
	$render->setAttribute('tree', $tree);
	$render->setAttribute('dirname', $dirname);
	$render->setAttribute('className', $className);
	XCube_Root::getSingleton()->getRenderSystem('Legacy_RenderSystem')->render($render);

	echo $render->getResult();
}
?>
