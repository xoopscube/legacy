<?php
/**
 * Controller for administration of openid table
 * @version $Rev$
 * @link $URL$
 */
define('OPENID_VIEW_DEFAULT', 1);
define('OPENID_ACTION_EXECUTED', 0);

class Openid_Controller_Identifier
{
    /**
     * @var string
     */
    var $_url;

    /**
     * Hadler Object
     *
     * @var Openid_Handler_Identifier
     */
    var $_handler;

    function Openid_Controller_Identifier()
    {
        require_once XOOPS_ROOT_PATH . "/modules/openid/class/handler/identifier.php";
        $this->_handler = new Openid_Handler_Identifier();
        $this->_url = XOOPS_URL . '/modules/openid/index.php';
    }

    /**
     * @return integer
     */
    function execute()
    {
        if (isset($_SERVER['HTTP_ACCEPT']) && $_SERVER['HTTP_ACCEPT'] === 'application/xrds+xml') {
            $this->xrdsAction();
        } else {
            $op = isset($_REQUEST['op']) ? $_REQUEST['op'] : 'list';
            switch ($op) {
                case 'list':
                    return OPENID_VIEW_DEFAULT;
                case 'xrds':
                case 'update':
                case 'updateok':
                case 'redirect':
                    $method = $op . 'Action';
                    $this->$method();
                    break;
                default:
                    exit(htmlspecialchars($op, ENT_QUOTES));
            }
        }
        return OPENID_ACTION_EXECUTED;
    }

    function xrdsAction()
    {
        $myurl = XOOPS_URL . '/modules/openid';
        $output =<<< EOD
<?xml version="1.0" encoding="UTF-8"?>
<xrds:XRDS xmlns:xrds="xri://\$xrds" xmlns:openid="http://openid.net/xmlns/1.0" xmlns="xri://\$xrd*(\$v*2.0)">
<XRD>
<Service priority="0">
<Type>http://specs.openid.net/auth/2.0/return_to</Type>
<URI>$myurl/finish_auth.php</URI>
</Service>
</XRD>
</xrds:XRDS>
EOD;
		// clear output buffer
		while( ob_get_level() ) {
			ob_end_clean() ;
		}

        header ('Content-type: application/xrds+xml');
		header ('Content-length: ' . strlen($output));
		echo $output;
    }

    function viewDefault()
    {
        global $xoopsUser, $xoopsModuleConfig, $xoopsTpl;
        require_once XOOPS_ROOT_PATH . '/modules/openid/class/utils.php';

        if (@$_GET['frompage']) {
            $request =& Openid_Utils::load('context');
            $request->accept('frompage', 'string', 'get');
            $xoopsTpl->assign('frompage', $request->get4show('frompage'));
        } elseif (is_object($xoopsUser)) {
            $openids = array();
            $identifiers =& $this->_handler->getByUid($xoopsUser->getVar('uid'));
            foreach ($identifiers as $identifier) {
                $openid = array();
                $openid['id'] = $identifier->get4Show('id');
                $openid['displayid'] = $identifier->get4Show('displayid');
                if ($xoopsModuleConfig['mode_policy'] == 0) {
                    $openid['mode'] = $identifier->get('omode');
                } else {
                    $openid['mode'] = ($identifier->get('omode')) ? 1 : 0;
                }
                $openid['oldmode'] = $identifier->get('omode');
                $openids[] =& $openid;
                unset($openid);
            }
            $xoopsTpl->assign('openids', $openids);
            $xoopsTpl->assign('options', $this->getModeList());
        }

        $handler_buttons =& Openid_Utils::load('buttons');
        $xoopsTpl->assign_by_ref('buttons', $handler_buttons->getObjects());

       	header('X-XRDS-Location: ' . $this->_url . '?op=xrds');

    }

    function updateAction()
    {
        global $xoopsUser, $xoopsModuleConfig;
        if (!isset($_POST['mode'])) {
            redirect_header($this->_url, 2, 'Bad operation.');
        }
        $change = array();
        $message = '';
        $lang = $this->getModeList();

        $identifiers =& $this->_handler->getByUid($xoopsUser->getVar('uid'));
        foreach ($identifiers as $identifier) {
            $id = $identifier->get('id');
            $oldmode = intval($identifier->get('omode'));
            $mode = intval(@$_POST['mode'][$id]);
            if ($xoopsModuleConfig['mode_policy'] == 0) {
                if ($mode != $oldmode) {
                    $change['mode[' . $id . ']'] = $mode;
                    $message .= $identifier->get4Show('displayid') . ': ' . $lang[$oldmode] . ' => ' . $lang[$mode] . '<br />';
                }
            } else if ($oldmode == OPENID_IDENTIFIER_INACTIVE) {
                if ($mode == OPENID_IDENTIFIER_ACTIVE) {
                    $change['mode[' . $id . ']'] = OPENID_IDENTIFIER_ACTIVE;
                    $message .= $identifier->get4Show('displayid') . ': ' . $lang[OPENID_IDENTIFIER_INACTIVE] . ' => ' . $lang[OPENID_IDENTIFIER_ACTIVE] . '<br />';
                }
            } else {
                if ($mode == OPENID_IDENTIFIER_INACTIVE) {
                    $change['mode[' . $id . ']'] = OPENID_IDENTIFIER_INACTIVE;
                    $message .= $identifier->get4Show('displayid') . ': ' . $lang[OPENID_IDENTIFIER_ACTIVE] . ' => ' . $lang[OPENID_IDENTIFIER_INACTIVE] . '<br />';
                }
            }
        }
        if ($change) {
            $change['op'] = 'updateok';
            $message = _MD_OPENID_MESSAGE_UPDATEOK . $message;
            xoops_confirm($change, $this->_url, $message);
        } else {
            redirect_header($this->_url, 2, 'No OpenID was changed.');
        }
    }

    function updateokAction()
    {
        global $xoopsUser, $xoopsModuleConfig;

        require_once XOOPS_ROOT_PATH . '/modules/openid/class/utils.php';
        if (!OpenID_Utils::validateToken()) {
            redirect_header($this->_url, 2, 'Token Error');
        }

        if (!isset($_POST['mode'])) {
            redirect_header($this->_url, 2, 'Bad operation.');
        }
        $ids = array_keys($_POST['mode']);

        $identifiers =& $this->_handler->get4Update($xoopsUser->getVar('uid'), $ids);
        $success = 0;
        $errors = '';
        foreach ($identifiers as $identifier) {
            $changed = false;
            $id = $identifier->get('id');
            $oldmode = intval($identifier->get('omode'));
            $mode = intval($_POST['mode'][$id]);
            if ($xoopsModuleConfig['mode_policy'] == 0) {
                if ($mode != $oldmode) {
                    $identifier->set('omode', $mode);
                    $changed = true;
                }
            } else if ($oldmode == OPENID_IDENTIFIER_INACTIVE) {
                if ($mode == OPENID_IDENTIFIER_ACTIVE) {
                    $identifier->set('omode', $xoopsModuleConfig['mode_policy']);
                    $changed = true;
                }
            } else {
                if ($mode == OPENID_IDENTIFIER_INACTIVE) {
                    $identifier->set('omode', OPENID_IDENTIFIER_INACTIVE);
                    $changed = true;
                } else if ($oldmode != $xoopsModuleConfig['mode_policy']) {
                    $identifier->set('omode', $xoopsModuleConfig['mode_policy']);
                    $changed = true;
                }
            }
            if ($changed) {
                if ($this->_handler->update($identifier)) {
                    $success++;
                } else {
                    $errors .= $this->_handler->getError() . '<br />';
                }
            }
        }
        if ($success > 0) {
            $message = $success . ' records are updated.';
        } else {
            $message = 'OpenID was not updated.';
        }
        if ($errors != '') {
            $message .= '<br />' . $errors;
        }
        redirect_header($this->_url, 2, $message);
    }

    /**
     * Return mode list which user can choose
     * @return array
     */
    function getModeList()
    {
        global $xoopsModuleConfig;
        if ($xoopsModuleConfig['mode_policy'] == 0) {
            $mode = array(_MD_OPENID_INACTIVE, _MD_OPENID_PRIVATE,
                _MD_OPENID_OPEN2MEMBER, _MD_OPENID_PUBLIC);
        } else {
            $mode = array(_MD_OPENID_INACTIVE, _MD_OPENID_ACTIVE);
        }
        return $mode;
    }

    /**
     * Redirect user to OpenID Identity URL
     * using only from xoops userinfo page
     */
    function redirectAction()
    {
        require_once XOOPS_ROOT_PATH . '/modules/openid/class/context.php';
        $request = new Openid_Context();
        if (!$request->accept('id', 'int', 'get')
            || !$request->accept('displayid', 'string', 'get', 'to')) {
            exit();
        }
        if ($record =& $this->_handler->get($request->get('id'))) {
            $displayid = $record->get('displayid');
            if ($request->get('displayid') == $displayid) {
                if (strpos($displayid, 'http') === 0) {
                    header('Location: ' . $displayid);//control codes are already removed.
                } else {
                    header('Location: http://xri.net/' . rawurlencode($displayid));
                }
            }
        }
    }
}
?>