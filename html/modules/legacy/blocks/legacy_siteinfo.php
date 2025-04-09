<?php
/**
 * legacy_siteinfo.php
 * XOOPS2
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 * @brief      This file has been modified for Legacy from XOOPS2 System module block
 */

/**
 * @param $options
 * @return array
 * */
function b_legacy_siteinfo_show($options)
{
    global $xoopsConfig, $xoopsUser;
    $xoopsDB =& Database::getInstance();
    $myts =& MyTextSanitizer::sGetInstance();
    $block = [];
    if (!empty($options[3])) {
        $block['showgroups'] = true;
        $result = $xoopsDB->query(
            'SELECT u.uid, u.uname, u.email, u.user_viewemail, u.user_avatar, g.name AS groupname FROM '
            . $xoopsDB->prefix('groups_users_link') . ' l LEFT JOIN '
            . $xoopsDB->prefix('users') . ' u ON l.uid=u.uid LEFT JOIN '
            . $xoopsDB->prefix('groups') . " g ON l.groupid=g.groupid WHERE g.group_type='Admin' ORDER BY l.groupid, u.uid");
        if ($xoopsDB->getRowsNum($result) > 0) {
            $prev_caption = '';
            $i = 0;
            while ($userinfo = $xoopsDB->fetchArray($result)) {
                if ($prev_caption != $userinfo['groupname']) {
                    $prev_caption = $userinfo['groupname'];
                    $block['groups'][$i]['name'] = $myts->htmlSpecialChars($userinfo['groupname']);
                }
                if (is_object($xoopsUser)) {
                    $block['groups'][$i]['users'][] = [
                    	'id' => $userinfo['uid'],
	                    'name' => $myts->htmlspecialchars($userinfo['uname']),
	                    'msglink' => '<a href="'
	                                 . XOOPS_URL . '/modules/message/index.php?action=new&to_userid='
                                    . $userinfo['uid'] . '"><img class="svg email" src="'
                                    . XOOPS_URL . '/images/icons/mail.svg" width="1em" height="1em" alt="message"></a>',
	                    'avatar' => XOOPS_UPLOAD_URL . '/' . $userinfo['user_avatar']];
                } else {
                    if ($userinfo['user_viewemail']) {
                        $block['groups'][$i]['users'][] = [
                        	'id' => $userinfo['uid'],
	                        'name' => $myts->htmlspecialchars($userinfo['uname']),
	                        'msglink' => '<a href="mailto:'
	                                     . $userinfo['email'] . '"><img class="svg mail" src="'
	                                     . XOOPS_URL . '/images/icons/mail.svg" width="1em" height="1em" alt="send email"></a>',
	                        'avatar' => XOOPS_UPLOAD_URL . '/' . $userinfo['user_avatar']];
                    } else {
                        $block['groups'][$i]['users'][] = [
                        	'id' => $userinfo['uid'],
	                        'name' => $myts->htmlspecialchars($userinfo['uname']),
	                        'msglink' => '&nbsp;', 'avatar' => XOOPS_UPLOAD_URL . '/' . $userinfo['user_avatar']
                        ];
                    }
                }
                $i++;
            }
        }
    } else {
        $block['showgroups'] = false;
    }
    $block['logourl'] = XOOPS_URL.'/images/'.$options[2];
    if (is_object($xoopsUser)) {
        $block['recoomendtime'] = time();
    } else {
        $block['recoomendtime'] = 0;
    }
    $block['popup_width'] = $options[0];
    $block['popup_height'] = $options[1];
    return $block;
}

function b_legacy_siteinfo_edit($options)
{
    $form = _MB_LEGACY_PWWIDTH . '&nbsp;';
    $form .= "<input type='text' name='options[]' value='".$options[0]."'>";
    $form .= '<br>' . _MB_LEGACY_PWHEIGHT . '&nbsp;';
    $form .= "<input type='text' name='options[]' value='".$options[1]."'>";
    $form .= '<br>' . sprintf(_MB_LEGACY_LOGO, XOOPS_URL . '/images/') . '&nbsp;';
    $form .= "<input type='text' name='options[]' value='".$options[2]."'>";
    $chk = '';
    $form .= '<br>' . _MB_LEGACY_SADMIN . '&nbsp;';
    if (1 == $options[3]) {
        $chk = " checked='checked'";
    }
    $form .= "<input type='radio' name='options[3]' value='1'".$chk . '>&nbsp;' . _YES . '';
    $chk = '';
    if (0 == $options[3]) {
        $chk = ' checked="checked"';
    }
    $form .= "&nbsp;<input type='radio' name='options[3]' value='0'".$chk . '>' . _NO . '';
    return $form;
}
