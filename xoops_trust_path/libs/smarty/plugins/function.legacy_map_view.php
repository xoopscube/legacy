<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     legacy_map_view
 * Version:  1.0
 * Date:     Apr 18, 2011
 * Author:   HIKAWA Kilica
 * Purpose:  Show map
 * Input:    string dirname: filter data by this dirname
 *           string dataname: filter data by this dataname
 *           int    data_id: filter data by user in this array
 *           string template:   template name
 * Examples: {legacy_map_view dirname=map dataname=place data_id=$object->get('page_id') template="lemap_place_inc.html}
 * -------------------------------------------------------------
 */
function smarty_function_legacy_map_view($params, &$smarty)
{
    $dirname = $params['dirname'] ?? null;
    $dataname = $params['dataname'] ?? null;
    $dataId = $params['data_id'] ?? null;
    $template = $params['template'] ?? 'legacy_inc_map_view.html';

    $places = [];
    XCube_DelegateUtils::call('Legacy_Map.GetPlaces',
        new XCube_Ref($places),
        $dirname,
        $dataname,
        $dataId
    );

    //render template
    $render = new XCube_RenderTarget();
    $render->setTemplateName($template);
    $render->setAttribute('legacy_buffertype',XCUBE_RENDER_TARGET_TYPE_MAIN);
    $render->setAttribute('places', $places);
    XCube_Root::getSingleton()->getRenderSystem('Legacy_RenderSystem')->render($render);

    echo $render->getResult();
}

?>
