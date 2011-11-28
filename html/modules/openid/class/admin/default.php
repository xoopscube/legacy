<?php
/**
 * Controller for default page
 * @version $Rev$
 * @link $URL$
 */
if (!class_exists('Openid_Admin_Controller')) {
    exit();
}
class Openid_Admin_Default extends Openid_Admin_Controller
{
    var $_allowed = array('list');
    var $_allowedAction = array('updateCert');
    var $_template = 'db:openid_admin_default.html';
    var $default_crt = '/uploads/cacert.pem';
    var $work_txt = '/uploads/certdata.txt';

    function Openid_Admin_Default()
    {
        $this->_url = XOOPS_URL . '/modules/openid/admin/index.php?controller=default';
    }

    function _list($view)
    {
        global $xoopsModuleConfig;
        if (@$xoopsModuleConfig['curl_cainfo_file']) {
            $crt = str_replace('XOOPS_ROOT_PATH', XOOPS_ROOT_PATH, $xoopsModuleConfig['curl_cainfo_file']);
            if (!file_exists($crt)) {
                $view->assign('no_cainfo', true);
            }
        }

        if (@$xoopsModuleConfig['openid_rand_souce']) {
            if (!@is_readable($xoopsModuleConfig['openid_rand_souce'])) {
                $view->assign('no_rand_source', true);
            }
        }

        if (! function_exists('openssl_open')) {
        	$view->assign('no_ssl', true);
        }
    }

    function updateCertAction()
    {
        global $xoopsModuleConfig;
        if (@$xoopsModuleConfig['curl_cainfo_file']) {
            $crt = str_replace('XOOPS_ROOT_PATH', XOOPS_ROOT_PATH, $xoopsModuleConfig['curl_cainfo_file']);
        } else {
            $crt = XOOPS_ROOT_PATH . $this->default_crt;
        }
        if (file_exists($crt)) {
            if (!@is_writable($crt)) {
                redirect_header($this->_url, 2, _AD_OPENID_ERROR_NOT_WRITABLE);
            }
        } elseif (file_exists(dirname($crt))) {
            if (!@is_writable(dirname($crt))) {
                redirect_header($this->_url, 2, _AD_OPENID_ERROR_NOT_WRITABLE_DIR);
            }
        } else {
            redirect_header($this->_url, 2, _AD_OPENID_ERROR_NOT_EXIST_DIR);
        }

        require_once XOOPS_ROOT_PATH . '/modules/openid/class/UpdateCert.php';
        $ret = UpdateCert::download_convert_cert($crt, XOOPS_ROOT_PATH . $this->work_txt);
        if ($ret === true) {
            $msg = 'Success.';
            if (!@$xoopsModuleConfig['curl_cainfo_file']) {
                $config_handler =& xoops_gethandler('config');
                $criteria = new CriteriaCompo(new Criteria('conf_modid', @$GLOBALS['xoopsModule']->getVar('mid')));
                $criteria->add(new Criteria('conf_name', 'curl_cainfo_file'));
                $configs = $config_handler->getConfigs($criteria);
                if ($configs) {
                    $config = $configs[0];
                    $config->set('conf_value', $crt);
                    if (!$config_handler->insertConfig($config)) {
                        $msg = _AD_OPENID_ERROR_NOT_UPDATE_CONFIG . $crt;
                    }
                }
            }
        } else {
            $msg = _AD_OPENID_ERROR_NOT_CONECT;
        }
        redirect_header($this->_url, 3, $msg);
    }
}