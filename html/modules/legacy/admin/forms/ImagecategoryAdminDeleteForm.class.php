<?php
/**
 *
 * @package Legacy
 * @version $Id: ImagecategoryAdminDeleteForm.class.php,v 1.3 2008/09/25 15:11:09 kilica Exp $
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

class Legacy_ImagecategoryAdminDeleteForm extends XCube_ActionForm
{
    public function getTokenName()
    {
        return 'module.legacy.ImagecategoryAdminDeleteForm.TOKEN' . $this->get('imgcat_id');
    }

    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['imgcat_id'] =new XCube_IntProperty('imgcat_id');

        //
        // Set field properties
        //
        $this->mFieldProperties['imgcat_id'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['imgcat_id']->setDependsByArray(['required']);
        $this->mFieldProperties['imgcat_id']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_IMGCAT_ID);
    }

    public function load(&$obj)
    {
        $this->set('imgcat_id', $obj->get('imgcat_id'));
    }

    public function update(&$obj)
    {
        $obj->set('imgcat_id', $this->get('imgcat_id'));
    }
}
