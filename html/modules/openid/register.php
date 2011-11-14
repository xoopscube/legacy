<?php
/**
 * Regster new user OR link to existing user
 * @version $Rev$ $Date$
 * @link $URL$
 */
require '../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/modules/openid/class/utils.php';
require_once XOOPS_ROOT_PATH . '/modules/openid/class/context.php';

$openid = @unserialize(rawurldecode($_SESSION['openid_response']));
if (!is_object($openid)) {
    Openid_Utils::redirect('Bad operation !');
}

$post = new Openid_Context();
$post->accept('uname');
$post->accept('omode', 'int');

$member =& Openid_Utils::load('member');

$user = $op = $error = false;
switch (@$_POST['op']) {
    case 'register':
        if ($xoopsModuleConfig['allow_register']) {
            $post->accept('email');
            $post->accept('timezone_offset');
            $user =& $member->register($openid, $post);
            $op = 'register';
        } else {
            //This case is an unjust request. But this config option may has changed just now.
            $error = _MD_OPENID_NOREGISTER;
        }
        break;
    case 'link':
        $post->accept('pass');
        $user =& $member->checkLogin($post);
        break;
    default:
        Openid_Utils::reset();
        exit($xoopsConfig['debug_mode'] ? (string)__LINE__ : '');
}

if (is_object($user)) {
    if ($xoopsModuleConfig['mode_policy'] == 0) {
        $openid->set('omode', $post->get('omode'));
    } else {
        $openid->set('omode', $xoopsModuleConfig['mode_policy']);
    }
    $identifier_handler =& Openid_Utils::load('identifier');
    if ($identifier_handler->register($openid, $user->getVar('uid'))) {
        $member->loginSuccess($user);
    } else {
        if ($op == 'register') {
            //rollback
            $member->deleteUser($user);
        }
        Openid_Utils::redirect($identifier_handler->getError());
    }
} else {
    require XOOPS_ROOT_PATH . '/header.php';
    $xoopsOption['template_main'] = 'openid_new_user.html';
    $xoopsTpl->assign('error', @$error ? $error : $member->getError());
    $xoopsTpl->assign('unam', $post->get4Show('uname'));

    if ($op == 'register') {
        $email = $post->get4Show('email');
        $timezone_offset = $post->get4Show('timezone_offset');
    } else {
        $email = $openid->get4Show('email');
    	$timezone_offset = $xoopsConfig['default_TZ'];
    }
    $xoopsTpl->assign('email', $email);
    $xoopsTpl->assign('timezone_offset', $timezone_offset);

    require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
    $xoopsTpl->assign('timezones', XoopsLists::getTimeZoneList());
    $xoopsTpl->assign('allow_register', $xoopsModuleConfig['allow_register']);
    if ($xoopsModuleConfig['mode_policy'] == 0) {
        $options = array(1 => _MD_OPENID_PRIVATE,
                    2 => _MD_OPENID_OPEN2MEMBER,
                    3 => _MD_OPENID_PUBLIC);
        $xoopsTpl->assign('options', $options);
        $xoopsTpl->assign('omode', $post->get('omode'));
    }
    require XOOPS_ROOT_PATH . '/footer.php';
}
?>