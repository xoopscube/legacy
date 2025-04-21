<?php
/**
 *
 * @package Legacy
 * @version $Id: modifier.xoops_user_avatarize.php,v 1.3 2008/09/25 15:12:36 kilica Exp $
 * @copyright (c) 2005-2025 The XOOPSCube Project
 * @license GPL v2.0
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
        [$width, $height, $type, $attr] = getimagesize($path);
    } else {
        $src = XOOPS_URL . "/modules/user/images/no_avatar.gif";
        $path = XOOPS_UPLOAD_PATH . "/modules/user/images/no_avatar.gif";
        [$width, $height, $type, $attr] = [80, 80, IMAGETYPE_GIF, ''];
    }

    if ($tag===true) {
        $imageTag = sprintf('<img src="%s" width="%d" height="%d" alt="%s">', $src, $width, $height, $user->get('uname'));
        if (is_null($url)) {
            return $imageTag;
        } else {
            return '<a href="'.$url.'">'.$imageTag.'</a>';
        }
    } else {
        return $src;
    }
}
