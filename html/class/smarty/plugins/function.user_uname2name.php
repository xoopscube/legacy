<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     user_uname2name
 * Version:  1.0
 * Date:     Aug 29, 2007
 * Author:   HIKAWA Kilica
 * Purpose:  Change uname to name
 * Input:    
 * 
 * Examples: {user_uname2name uname=$uname}
 * -------------------------------------------------------------
 */

function smarty_function_user_uname2name($params, &$smarty)
{
	$handler = xoops_gethandler('user');
	$user =& $handler->getObjects(new Criteria('uname',$params['uname']));

	if(! count($user)>0){
		return "guest";
	}

	if($user[0]->getShow('name')){
		return $user[0]->getShow('name');
	}
	else{
		return $params['uname'];
	}
}

?>
