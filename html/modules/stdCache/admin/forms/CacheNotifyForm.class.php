<?php
/**
 * Standard cache - Module for XCL
 * CacheNotifyForm.class.php
 *
 * Form primarily used for providing a CSRF token for admin actions
 * like testing notifications.
 *
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) XCL/PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    2.5.0 Release: XCL
 * @link       http://github.com/xoopscube/
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';

class CacheNotifyForm extends XCube_ActionForm
{
    public function getTokenName()
    {
        return 'module.stdCache.CacheNotifyForm.TOKEN';
    }

    public function getTokenErrorMessage()
    {
        return null;
    }

    public function prepare()
    {
        parent::prepare();
    }

    // new fields added to this form require validation,
    // add their validate_yourFieldName() methods here e.g.:
    /*
    public function validate_manual_timestamp()
    {
        $timestamp = $this->get('manual_timestamp');
        if ($timestamp !== null && $timestamp !== '' && !is_numeric($timestamp)) {
            $this->addErrorMessage('Manual timestamp must be a number.');
        }
    }
    */
}
