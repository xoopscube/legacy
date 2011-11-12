<?php
/**
 * receive a certification result from OP, and login complete
 * @version $Rev$
 * @link $URL$
 */
ini_set('mbstring.http_input', 'pass');

// Save original 'REQUEST_URI'.
$_SERVER['_REQUEST_URI'] = @ $_SERVER['REQUEST_URI'];

require '../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/modules/openid/class/utils.php';

// Cancel XOOPS error handler
restore_error_handler();

/* @var $library Openid_Library */
$library =& Openid_Utils::load('library');
/* @var $response Openid_Context */
$response =& Openid_Utils::load('context');

// Run Once & break out at 'break;' statement
do {
    // Check the server's response.
    if (!$library->finish_auth($response)) {
        // This means the authentication failed.
        $error = $library->getError();
        break;
    }
    $response->set('gid', $GLOBALS['xoopsModuleConfig']['default_group']);

    //execute post-authentication filter if any extension exist
    /* @var $extension Openid_Extension */
    $extension =& Openid_Utils::load('extension');
    $ret = $extension->execute('postFilter', $response);
    if ($ret === true) {
        //skip filter
    } else if ($ret === false) {
        $error = $extension->getError();
        break;
    } else {
        // Check OP-Endpoint with the whitelist and the blacklist.
        /* @var $filter Openid_Filter */
        $filter =& Openid_Utils::load('filter');
        if (!$filter->postFilter($response)) {
            $error = _MD_OPENID_ERROR_MAYNOT . $filter->getError();
            break;
        }
    }

    // If the filtering succeeded.
    /* @var $identifier_handler Openid_Identifier */
    $identifier_handler =& Openid_Utils::load('identifier');
    $record =& $identifier_handler->getByClaimedID($response->get4sql('claimed_id'));
    $uid = false;

    if ($record) {
        // He is already registered into the map.
        if ($record->get('omode') == 0) {
            $error = 'This OpenID is not valid.';
            break;
        }
        $uid = $record->get('uid');
        $message = 'You have already logged in.';
    } else {
        // If Local ID is regstered OR User has logged in,
        // OpenID is immediately connected to the existing user.
        if ($record =& $identifier_handler->getByLocalID($response->get4Sql('local_id'))) {
            // This means OP-Local Identifier is already useed
            $uid = $record->get('uid');
            $mode = $record->get('omode');
        } else if (is_object($xoopsUser)) {
            $uid = $xoopsUser->getVar('uid');
        }

        if ($uid) {
            if ($xoopsModuleConfig['mode_policy'] != 0) {
                $mode = $xoopsModuleConfig['mode_policy'];
            } else if (!@$mode) {
                $mode = OPENID_IDENTIFIER_PUBLIC;
            }
            $response->set('omode', $mode);
            if ($identifier_handler->register($response, $uid)) {
                $message = 'OpenID is added successfully.';
            } else {
                $error = 'Fail in registration of OpenID<br />' . $identifier_handler->getError();
                break;
            }
        }
    }

    /* @var $member Openid_Member */
    $member =& Openid_Utils::load('member');
    if ($uid) {
        if (is_object($xoopsUser)) {
            if ($xoopsUser->getVar('uid') == $uid) {
                Openid_Utils::redirect($message, true);
            } else {
            	//This is an undesirable case. However, it is not possible to refuse.
                $member->logout($xoopsUser);
            }
        }
        if ($user =& $member->getUser($uid)) {
            $member->loginSuccess($user);
        } else {
            $error = 'This OpenID may be invalid.<br />' . $member->getError();
            break;
        }
    }

    // Display regsteration panel
    $_SESSION['openid_response'] = rawurlencode(serialize($response));
    $displayId = $response->get4Show('displayId');

    Openid_Utils::loadEncoder();

    $xoopsOption['template_main'] = 'openid_new_user.html';
    require XOOPS_ROOT_PATH . '/header.php';

    $xoopsTpl->assign('displayId', $displayId);
    if (strpos($displayId, 'http') === 0) {
        $uname = Openid_Encoder::fromUtf8($response->get4Show('nickname'));
    } else {
        $uname = $displayId;
    }
    $xoopsTpl->assign('unam', $uname);

    if ($xoopsModuleConfig['allow_register']) {
        $xoopsTpl->assign('allow_register', true);
        $xoopsTpl->assign('email', $response->get4Show('email'));

        require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
        $xoopsTpl->assign('timezones', XoopsLists::getTimeZoneList());

        $timezone_offset = $xoopsConfig['default_TZ'];
        $sreg_tz = $response->get('timezone');
        if ($sreg_tz && function_exists('timezone_abbreviations_list')) {
            $timezones = timezone_abbreviations_list();
            foreach ($timezones as $timezone) {
                foreach ($timezone as $tz) {
                    if ($tz['timezone_id'] == $sreg_tz) {
                        $timezone_offset = $tz['offset'] / 3600;
                        break;
                    }
                }
            }
        }
        $xoopsTpl->assign('timezone_offset', $timezone_offset);
        if (!$member->validateUname($uname)) {
            $xoopsTpl->assign('error', $member->getError());
        }
    } else {
        $xoopsTpl->assign('allow_register', false);
    }

    if ($xoopsModuleConfig['mode_policy'] == 0) {
        $options = array(1 => _MD_OPENID_PRIVATE,
                    2 => _MD_OPENID_OPEN2MEMBER,
                    3 => _MD_OPENID_PUBLIC);
        $xoopsTpl->assign('options', $options);
        $xoopsTpl->assign('omode', OPENID_IDENTIFIER_PUBLIC);
    }
    require XOOPS_ROOT_PATH . '/footer.php';
    exit();

} while (false);

// Redirect user to OpenID login form for inputting "User-supplied Identifier" again.
/* @var $cookie Openid_Context */
$cookie = Openid_Utils::load('context');
$cookie->accept('openid_frompage', 'string', 'cookie');
Openid_Utils::reset();

$url = XOOPS_URL . '/modules/openid/?verify=1';
if ($frompage = $cookie->get('openid_frompage')) {
    $url .= '&amp;frompage=' . rawurlencode($frompage);
}
redirect_header($url, 5, $error);
?>