<?php
/**
 * @package bannerstats
 * @version $Id: BannerclientAdminDeleteForm.class.php,v 1.1 2007/05/15 02:34:40 minahito Exp $
 */

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
        //
        // Set form properties
        //
        $this->mFormProperties['cid'] =new XCube_IntProperty('cid');
    
        //
        // Set field properties
        //
        $this->mFieldProperties['cid'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['cid']->setDependsByArray(['required']);
        $this->mFieldProperties['cid']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_LANG_CID);
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
