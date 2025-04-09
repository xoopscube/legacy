<?php
/**
 * ImageAdminEditForm.class.php
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/forms/ImageUploadForm.class.php';

class Legacy_ImageAdminCreateForm extends Legacy_ImageUploadForm
{
    public $_mImgcatId = 0;

    public function getTokenName()
    {
        return 'module.legacy.ImageAdminEditForm.TOKEN' . $this->get('image_id');
    }

    public function prepare()
    {
        parent::prepare();

        //
        // Set form properties
        //
        $this->mFormProperties['image_id'] =new XCube_IntProperty('image_id');
        $this->mFormProperties['image_display'] =new XCube_BoolProperty('image_display');
        $this->mFormProperties['image_weight'] =new XCube_IntProperty('image_weight');

        //
        // Set field properties
        //
        $this->mFieldProperties['image_id'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['image_id']->setDependsByArray(['required']);
        $this->mFieldProperties['image_id']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_IMAGE_ID);

        $this->mFieldProperties['image_weight'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['image_weight']->setDependsByArray(['required']);
        $this->mFieldProperties['image_weight']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_IMAGE_WEIGHT);
    }

    public function load(&$obj)
    {
        parent::load($obj);
        $this->set('image_id', $obj->get('image_id'));
        $this->set('image_display', $obj->get('image_display'));
        $this->set('image_weight', $obj->get('image_weight'));

        $this->_mImgcatId = $obj->get('imgcat_id');
    }

    public function update(&$obj)
    {
        parent::update($obj);
        $obj->set('image_id', $this->get('image_id'));
        $obj->set('image_display', $this->get('image_display'));
        $obj->set('image_weight', $this->get('image_weight'));
    }
}

class Legacy_ImageAdminEditForm extends Legacy_ImageAdminCreateForm
{
    public function validateImgcat_id()
    {
        parent::validateImgcat_id();

        $handler =& xoops_getmodulehandler('imagecategory', 'legacy');
        $currentCategory =& $handler->get($this->_mImgcatId);

        $specificCategory =& $handler->get($this->get('imgcat_id'));
        if ($currentCategory->get('imgcat_storetype') != $specificCategory->get('imgcat_storetype')) {
            $this->set('imgcat_id', $this->_mImgcatId);
        }
    }
}
