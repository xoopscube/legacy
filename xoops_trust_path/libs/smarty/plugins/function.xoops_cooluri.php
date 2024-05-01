<?php
/**
 *
 * @package Legacy
 * @version $Id
 * @copyright (c) 2005-2024 The XOOPSCube Project
 * @license GPL v2.0
 *
 */

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:	 function
 * Name:	 xoops_cooluri
 * Version:  1.0
 * Date:	 May 1, 2010
 * Author:	 kilica
 * Purpose:  create uri
 * Input:	 string		dirname	*required
 *			 string		dataname
 *			 int		data_id
 *			 string		action
 *			 string		query
 *
 * Examples: {xoops_cooluri dirname=lenews dataname=story data_id=6 action=edit query='cat_id=3&mode=admin'}
 * -------------------------------------------------------------
 */

function smarty_function_xoops_cooluri($params, &$smarty)
{
    if (! $params['dirname']) {
        return;
    }
    $dirname = $params['dirname'];
    $dataname = $params['dataname'] ?? null;
    $dataId = $params['data_id'] ?? 0;
    $action = $params['action'] ?? null;
    $query = $params['query'] ?? null;

    echo htmlspecialchars(Legacy_Utils::renderUri($dirname, $dataname, $dataId, $action, $query), ENT_QUOTES);
}
