<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     legacy_map_edit
 * Version:  1.0
 * Date:     Apr 18, 2011
 * Author:   HIKAWA Kilica
 * Purpose:  Show map for edit
 * Input:    string dirname: filter data by this dirname
 *           string dataname: filter data by this dataname
 *           int    data_id: filter data by user in this array
 *           string geocode: input field's id for geocoding
 *           string template:   template name
 * Examples: {legacy_map_edit dirname=map dataname=place data_id=$object->get('page_id') template="lemap_place_inc.html}
 * -------------------------------------------------------------
 */
function smarty_function_legacy_map_edit($params, &$smarty)
{
    $geocode = null;
    $dirname = $params['dirname'] ?? null;
    $dataname = $params['dataname'] ?? null;
    $dataId = $params['data_id'] ?? null;
    $addressId = $params['geocode'] ?? null;
    $template = $params['template'] ?? 'legacy_inc_map_edit.html';

    $places = [];
    XCube_DelegateUtils::call('Legacy_Map.GetPlaces',
        new XCube_Ref($places),
        $dirname,
        $dataname,
        $dataId
    );

    $root = XCube_Root::getSingleton();
    $latitude = $root->mContext->mRequest->getRequest('latitude');
    $longitude = $root->mContext->mRequest->getRequest('longitude');

    $request = null;
    if(isset($latitude) && isset($longitude)){
        $request = ['latitude'=>$latitude, 'longitude'=>$longitude, 'zoom'=>10];
    }

    //render template
    $render = new XCube_RenderTarget();
    $render->setTemplateName($template);
    $render->setAttribute('legacy_buffertype',XCUBE_RENDER_TARGET_TYPE_MAIN);
    $render->setAttribute('places', $places);
    $render->setAttribute('geocode', $geocode);
    $render->setAttribute('request', $request);
    XCube_Root::getSingleton()->getRenderSystem('Legacy_RenderSystem')->render($render);

    echo $render->getResult();
}

?>
