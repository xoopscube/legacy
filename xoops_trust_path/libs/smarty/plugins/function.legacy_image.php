<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:	 function
 * Name:	 legacy_image
 * Version:  1.0
 * Date:	 Jun 29, 2011
 * Author:	 HIKAWA Kilica
 * Purpose:  show image html tag
 * Input:	 string	dirname(*): target module's dirname
 *           string dataname(*): target table's name
 *			 int	data_id(*): target table's primary key
 *           int	num: target image number
 *           int	size: thumbnail number
 * Examples: {legacy_image dirname=score dataname=page datsa_id=$object->get('page_id')}
 * -------------------------------------------------------------
 */
function smarty_function_legacy_image($params, &$smarty)
{
	if(!defined(LEGACY_IMAGE_DUMMY_EXT)) define('LEGACY_IMAGE_DUMMY_EXT', 'gif');

	$dirname = $params['dirname'];
	$dataname = $params['dataname'];
	$dataId = $params['data_id'];
	$num = isset($params['num']) ? $params['num'] : 1;
	$size = isset($params['size']) ? $params['size'] : 0;
    $returnUri = isset($params['returnUri']) ? true : false;

    $imageObjs = array();
    XCube_DelegateUtils::call('Legacy_Image.GetImageObjects', new XCube_Ref($imageObjs), $dirname, $dataname, $dataId, $num);

	//display dummy image
    if(! $imageObj=array_shift($imageObjs)){
    	$imageObj = null;
	    XCube_DelegateUtils::call('Legacy_Image.CreateImageObject', new XCube_Ref($imageObj));
	    $imageObj->set('dirname', $dirname);
	    $imageObj->set('dataname', $dataname);
    }

   	echo $returnUri ? $imageObj->getFileUrl($size) : $imageObj->makeImageTag($size);
}

?>
