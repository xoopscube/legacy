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
function b_cubeUtils_igoogle_edit($options) {
    $blockHandler =& xoops_gethandler('block');
    $blockObjects = $blockHandler->getAllBlocksByGroup(XOOPS_GROUP_ANONYMOUS);
    $blockOptionStr = '';
    foreach ($blockObjects as $blockObject ) {
        $block_type = $blockObject->getVar("block_type");
        $bid = $blockObject->getVar('bid');
        $name = "[$bid]". $blockObject->getVar("title"). " - " . $blockObject->getVar("name");
        $selected = ($options[0] == $bid) ? 'selected' : '' ;
        $blockOptionStr .= '<option value="'.$bid.'" '.$selected.'>'.$name.'</option>';
    }
    return _MB_CUBE_UTILS_BLOCKNAME.' : <select name="options[0]">'.$blockOptionStr.'</select>';
}

function b_cubeUtils_igoogle_show($options) {
    require_once dirname(dirname(__FILE__)).'/include/blockFunc.inc.php';
    $bid = intval($options[0]);
    $result = cubeUtils_GetBlock($bid);
    $result['bid'] = $bid;
    return $result;
}
?>
