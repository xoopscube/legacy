<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:	 function
 * Name:	 legacy_comment
 * Version:  1.0
 * Date:	 Dec 14, 2010
 * Author:	 HIKAWA Kilica
 * Purpose:  show comments to the given data and show comment form.
 * Input:	 string	cDirname(*): comment module's dirname
 *			 string	dirname: client module's dirname
 *			 string	dataname: client module's dataname
 *			 int	data_id: client module's primary key
 *			 string	template:	template name
 * Examples: {legacy_comment cDirname=tag dirname=news dataname=story data_id=3}
 * -------------------------------------------------------------
 */
function smarty_function_legacy_comment($params, &$smarty)
{
	$cDirname = $params['cDirname'];
	$dirname = isset($params['dirname']) ? $params['dirname'] : null;
	$dataname = isset($params['dataname']) ? $params['dataname'] : null;
	$dataId = isset($params['data_id']) ? $params['data_id'] : 0;
	$categoryId = isset($params['category_id']) ? $params['category_id'] : 0;
	$comments = null;

	XCube_DelegateUtils::call('Legacy_Comment.'.$cDirname.'.GetComments',
		new XCube_Ref($comments),
		$cDirname,
		$dirname,
		$dataname,
		$dataId,
		$categoryId,
		$params
	);

	$template = isset($params['template']) ? $params['template'] : $comments['template'];

	//render template
	$render = new XCube_RenderTarget();
	$render->setTemplateName($template);
	$render->setAttribute('legacy_buffertype',XCUBE_RENDER_TARGET_TYPE_MAIN);
	$render->setAttribute('comments', $comments);
	XCube_Root::getSingleton()->getRenderSystem('Legacy_RenderSystem')->render($render);

	echo $render->getResult();
}


?>
