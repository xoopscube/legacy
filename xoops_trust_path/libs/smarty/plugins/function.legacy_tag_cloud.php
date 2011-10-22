<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:	 function
 * Name:	 legacy_tag_cloud
 * Version:  1.0
 * Date:	 Dec 14, 2010
 * Author:	 HIKAWA Kilica
 * Purpose:  get tag cloud html source from tag list
 * Input:	 string	tDirname(*): tag module's dirname
 *			 string	dirname: filter data by this dirname
 *			 string	dataname: filter data by this dataname
 *			 int[]	uidList: filter data by user in this array
 *			 int	max: maximum font size in the cloud (%)
 *			 int	min: minimum font size in the cloud (%)
 *			 string	template:	template name
 * Examples: {legacy_tag_cloud tDirname=tag dirname=news}
 * -------------------------------------------------------------
 */
function smarty_function_legacy_tag_cloud($params, &$smarty)
{
	$tDirname = $params['tDirname'];
	$dirname = isset($params['dirname']) ? $params['dirname'] : null;
	$dataname = isset($params['dataname']) ? $params['dataname'] : null;
	$uidList = isset($params['uidList']) ? $params['uidList'] : null;
	$max = isset($params['max']) ? $params['max'] : 200;	//font size(%)
	$min = isset($params['min']) ? $params['min'] : 80;	//font size(%)
	$template = isset($params['template']) ? $params['template'] : 'legacy_inc_tag_cloud.html';
	$cloud = array();

	XCube_DelegateUtils::call('Legacy_Tag.'.$tDirname.'.GetTagCloudSrc',
		new XCube_Ref($cloud),
		$tDirname,
		$dirname,
		$dataname,
		$uidList
	);

	$sizeArr = _smarty_function_legacy_tag_cloud_get_size($cloud, $max, $min);

	//render template
	$render = new XCube_RenderTarget();
	$render->setTemplateName($template);
	$render->setAttribute('legacy_buffertype',XCUBE_RENDER_TARGET_TYPE_MAIN);
	$render->setAttribute('dirname', $tDirname);
	$render->setAttribute('cloud', $cloud);
	$render->setAttribute('sizeArr', $sizeArr);
	XCube_Root::getSingleton()->getRenderSystem('Legacy_RenderSystem')->render($render);

	echo $render->getResult();
}

function _smarty_function_legacy_tag_cloud_get_size(/*** array **/ $tagList, /*** int ***/ $max, /*** int ***/ $min)
{
	// get the largest and smallest array values
	if(count($tagList)>0){
		$maxQty = max(array_values($tagList));
		$minQty = min(array_values($tagList));
	}

	// find the range of values
	$spread = $maxQty - $minQty;
	if ($spread<=0) { // we don't want to divide by zero
	    $spread = 1;
	}

	// determine the font-size increment
	// this is the increase per tag quantity (times used)
	$step = ($max - $min)/($spread);
	
	// loop through our tag array
	$sizeArr = array();
	foreach ($tagList as $key => $value) {
	    // calculate CSS font-size
	    // find the $value in excess of $min_qty
	    // multiply by the font-size increment ($size)
	    // and add the $min_size set above
	    $sizeArr[$key] = $min + (($value - $minQty) * $step);
	}
	return $sizeArr;
}
?>
