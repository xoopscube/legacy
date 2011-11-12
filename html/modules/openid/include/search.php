<?php
/**
 * Function for Xoops Global Search
 * This is used only on userinfo page.
 *
 * @version $Rev$
 * @link $URL$
 *
 * @param array $queryarray
 * @param string $andor
 * @param int $limit
 * @param int $offset
 * @param int $userid
 * @return array
 */
function openid_search($queryarray, $andor, $limit, $offset, $userid)
{
    global $xoopsUserIsAdmin, $xoopsUser;
    if ( ! $userid ) {
        return array();
    }

    require_once XOOPS_ROOT_PATH . '/modules/openid/class/handler/identifier.php';

    $moduleHandler =& xoops_gethandler('module');
    $module =& $moduleHandler->getByDirname('openid');
    $configHandler =& xoops_gethandler('config');
    $config =& $configHandler->getConfigsByCat(0, $module->getVar('mid'));

    if ($xoopsUserIsAdmin) {
        $threshold = OPENID_IDENTIFIER_INACTIVE;
    } else if (is_object(@$xoopsUser)) {
        if ($xoopsUser->getVar('uid') == $userid) {
            $threshold = OPENID_IDENTIFIER_INACTIVE;
        } else if ($config['mode_policy'] == 0) {
            $threshold = OPENID_IDENTIFIER_OPEN2MEMBER;
        } else if ($config['mode_policy'] == OPENID_IDENTIFIER_PRIVATE) {
            return array();
        } else {
            $threshold = OPENID_IDENTIFIER_ACTIVE;
        }
    } else {
        if ($config['mode_policy'] == 0) {
            $threshold = OPENID_IDENTIFIER_PUBLIC;
        } else if ($config['mode_policy'] < OPENID_IDENTIFIER_PUBLIC) {
            return array();
        } else {
            $threshold = OPENID_IDENTIFIER_ACTIVE;
        }
    }

    $identifier_handler = new Openid_Handler_Identifier();
    $records =& $identifier_handler->getByUid($userid, $limit, $offset, $threshold);

    $ret = array();
    $i = 0;
    foreach ($records as $record) {
        $ret[$i] = array(
            'image' => 'images/openid_small_logo_white.gif',
            'title' => $record->get4Show('displayid'),
            'time'  => $record->get('utime')
        );
        if ($record->get('omode') == OPENID_IDENTIFIER_INACTIVE) {
            $ret[$i]['title'] .= ' (( inactive ))';
        }
        if (!is_object(@$xoopsUser) || $xoopsUser->getVar('uid') != $userid) {
            $ret[$i]['link'] = '?op=redirect&amp;id=' . $record->get4show('id')
                             . '&amp;to=' . rawurlencode($record->get('displayid'));
        }
        $i++;
    }
    return $ret;
}
?>