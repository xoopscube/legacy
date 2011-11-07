<?php
/**
 *
 * @package CubeUtils
 * @version $Id: xoops_version.php 1294 2008-01-31 05:32:20Z nobunobu $
 * @copyright Copyright 2006-2008 NobuNobuXOOPS Project <http://sourceforge.net/projects/nobunobuxoops/>
 * @author NobuNobu <nobunobu@nobunobu.com>
 * @license http://www.gnu.org/licenses/gpl.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
function b_cubeUtils_whatsnew_edit($options) {
    return _MB_CUBE_UTILS_NUMOFCONTENT.' : <input type="text" name="options[0]" value="'.intval($options[0]).'" />';
}

function b_cubeUtils_whatsnew_show($options) {
    $mGetRSSItems = new XCube_Delegate();
    $mGetRSSItems->register('Legacy_BackendAction.GetRSSItems');
    $items = array();
    $mGetRSSItems->call(new XCube_Ref($items));

    $max_item = intval($options[0]);
    if (empty($max_item)) $max_item = 5;

    $sortArr = array();
    $n = 0;
    foreach ($items as $item) {
        $i = intval($item['pubdate']);
        for (; isset($sortArr[$i]) ; $i++);
        $sortArr[$i] = $item;
    }
    krsort($sortArr);
    $result['items'] = array_slice($sortArr,0,$max_item);
    return $result;
}
?>
