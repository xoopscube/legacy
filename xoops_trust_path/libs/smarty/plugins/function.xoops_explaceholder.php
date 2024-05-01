<?php
/**
 *
 * @package Legacy
 * @version $Id: function.xoops_explaceholder.php,v 1.3 2008/09/25 15:12:36 kilica Exp $
 * @copyright (c) 2005-2024 The XOOPSCube Project
 * @license https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 *
 */

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     xoops_explaceholder
 * Version:  1.0
 * Date:     Oct 12, 2006
 * Author:   minahito
 * Purpose:  Extended place holder
 * Input:    control =
 *
 * Examples: <{xoops_explaceholder control=sp_pagenavi pagenavi=$pagenavi}>
 * -------------------------------------------------------------
 */
function smarty_function_xoops_explaceholder($params, &$smarty)
{
    $buf = null;

    if (isset($params['control'])) {
        XCube_DelegateUtils::call('Legacy.Event.Explaceholder.Get.' . $params['control'], new XCube_Ref($buf), $params);

        if ($buf === null) {
            XCube_DelegateUtils::call('Legacy.Event.Explaceholder.Get', new XCube_Ref($buf), $params['control'], $params);
        }
    }

    return $buf;
}
