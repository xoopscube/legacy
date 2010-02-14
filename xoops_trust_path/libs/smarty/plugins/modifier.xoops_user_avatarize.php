<?php
/**
 *
 * @package Legacy
 * @version $Id: modifier.xoops_user_avatarize.php,v 1.3 2008/09/25 15:12:36 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     xoops_user_avatarize
 * Purpose:  Return avatar url by $uid.
 * Input:    uid: user id
 * -------------------------------------------------------------
 */
function smarty_modifier_xoops_user_avatarize($uid, $tag=false, $url=null)
{
	$handler =& xoops_gethandler('user');
	$user =& $handler->get(intval($uid));
	if (is_object($user) && $user->isActive() && ($user->get('user_avatar') != "blank.gif") && file_exists(XOOPS_UPLOAD_PATH . "/" . $user->get('user_avatar'))) {
		$src = XOOPS_UPLOAD_URL . "/" . $user->getShow('user_avatar');
		$path = XOOPS_UPLOAD_PATH . "/" . $user->getShow('user_avatar');
	}
	else{
		$src = XOOPS_URL . "/modules/user/images/no_avatar.gif";
		$path = XOOPS_UPLOAD_PATH . "/modules/user/images/no_avatar.gif";
	}

	if($tag===true){
		list($width, $height, $type, $attr) = getimagesize($path);
		$imageTag = sprintf('<img src="%s" width="%d" height="%d" alt="%s" />', $src, $width, $height, $user->get('uname'));
		if(is_null($url)){
			return $imageTag;
		}
		else{
			return '<a href="'.$url.'">'.$imageTag.'</a>';
		}
	}
	else{
		return $src;
	}
}

?>
