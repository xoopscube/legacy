<?php

function smarty_function_legacy_tag_select($params, &$smarty)
{
	$tDirname = $params['tDirname'];
	$dirname = isset($params['dirname']) ? $params['dirname'] : null;
	$dataname = isset($params['dataname']) ? $params['dataname'] : null;
	$uidList = isset($params['uidList']) ? $params['uidList'] : null;
	$tags = isset($params['tags']) ? $params['tags'] : null;	//selected tags
	$template = isset($params['template']) ? $params['template'] : 'legacy_inc_tag_select.html';
	$cloud = array();

	XCube_DelegateUtils::call('Legacy_Tag.'.$tDirname.'.GetTagCloudSrc',
		new XCube_Ref($cloud),
		$tDirname,
		$dirname,
		$dataname,
		$uidList
	);

	//render template
	$render = new XCube_RenderTarget();
	$render->setTemplateName($template);
	$render->setAttribute('legacy_buffertype',XCUBE_RENDER_TARGET_TYPE_MAIN);
	$render->setAttribute('cloud', $cloud);
	$render->setAttribute('tags', $tags);
	XCube_Root::getSingleton()->getRenderSystem('Legacy_RenderSystem')->render($render);

	echo $render->getResult();
}

?>
