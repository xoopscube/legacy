<?php
/**
 *
 * @package Legacy
 * @version $Id
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
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
	if(! $params['dirname']) return;

	echo htmlspecialchars(Legacy_Utils::renderUri($params['dirname'], $params['dataname'], $params['data_id'], $params['action'], $params['query']), ENT_QUOTES);
}

?>
