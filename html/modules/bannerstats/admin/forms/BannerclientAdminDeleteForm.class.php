<?php
/**
 * Bannerstats - Module for XCL
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    v2.5.0 Release XCL 
 * @link       http://github.com/xoopscube/
 **/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

class Bannerstats_BannerclientAdminDeleteForm extends XCube_ActionForm
{
    public function getTokenName()
    {
        return 'module.bannerstats.BannerclientAdminDeleteForm.TOKEN' . $this->get('cid');
    }

    public function prepare()
    {
        $this->mFormProperties['cid'] =new XCube_IntProperty('cid');
        $this->mFieldProperties['cid'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['cid']->setDependsByArray(['required']);
        $this->mFieldProperties['cid']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_CID);
    }

    public function load(&$obj)
    {
        $this->set('cid', $obj->get('cid'));
    }

    public function update(&$obj)
    {
        $obj->set('cid', $this->get('cid'));
    }
}
