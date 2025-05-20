<?php
/**
 * @package bannerstats
 * @version $Id: BannerfinishAdminDeleteForm.class.php,v 1.1 2007/05/15 02:34:40 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

class Bannerstats_BannerfinishAdminDeleteForm extends XCube_ActionForm
{
    public function getTokenName()
    {
        return 'module.bannerstats.BannerfinishAdminDeleteForm.TOKEN' . $this->get('bid');
    }

    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['bid'] =new XCube_IntProperty('bid');
    
        //
        // Set field properties
        //
        $this->mFieldProperties['bid'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['bid']->setDependsByArray(['required']);
        $this->mFieldProperties['bid']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_LANG_BID);
    }

    public function load(&$obj)
    {
        $this->set('bid', $obj->get('bid'));
    }

    public function update(&$obj)
    {
        $obj->set('bid', $this->get('bid'));
    }
}
