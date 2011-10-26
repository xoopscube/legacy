<?php
/**
 *
 * @package Legacy
 * @version $Id: function.xoops_token.php,v 1.3 2008/09/25 15:12:35 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     xoops_token
 * Version:  1.0
 * Date:     Nov 14, 2005
 * Author:   minahito
 * Input:    name = token name
 *           value = token value
 *           actionform = actionform object
 * 
 * -------------------------------------------------------------
 */
function smarty_function_xoops_token($params, &$smarty)
{
	$tokenName = null;
	$tokenValue = null;

	if (isset($params['form']) && is_object($params['form'])) {
		if(is_a($params['form'], 'XCube_ActionForm')) {
			$tokenName = $params['form']->getTokenName();
			$tokenValue = $params['form']->getToken();
		}
		else {
			die('You does not set ActionForm instance to place holder.');
		}
	}
	else {
		$tokenName = $params['name'];
		$tokenValue = $params['value'];
	}
	
	if ($tokenName != null && $tokenValue != null) {
		$tokenName = htmlspecialchars($tokenName, ENT_QUOTES);
		$tokenValue = htmlspecialchars($tokenValue, ENT_QUOTES);
		
		@printf('<input type="hidden" name="%s" value="%s" />', $tokenName, $tokenValue);
	}
}

?>
