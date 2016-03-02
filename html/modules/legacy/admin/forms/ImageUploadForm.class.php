<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/Legacy_Validator.class.php";

class Legacy_ImageUploadForm extends XCube_ActionForm
{
    public $mOldFileName = null;
    public $_mIsNew = null;
    public $mFormFile = null;
    public $_allowExtensions = array('tar', 'tar.gz', 'tgz', 'gz', 'zip');

    public function getTokenName()
    {
        return "module.legacy.ImageUploadForm.TOKEN";
    }

    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['imgcat_id'] =new XCube_IntProperty('imgcat_id');
        $this->mFormProperties['upload'] =new XCube_FileProperty('upload');
    
        //
        // Set field properties
        //
        $this->mFieldProperties['imgcat_id'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['imgcat_id']->setDependsByArray(array('required'));
        $this->mFieldProperties['imgcat_id']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_IMGCAT_ID);
        $this->mFieldProperties['upload'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['upload']->setDependsByArray(array('required'));
        $this->mFieldProperties['upload']->addMessage('required', _AD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_IMAGE_UPLOAD_FILE);
    }
    
    public function validateImgcat_id()
    {
        $handler =& xoops_getmodulehandler('imagecategory', 'legacy');
        $imgcat_id = $this->get('imgcat_id');
        if (!$imgcat_id || !$handler->get($imgcat_id)) {
            $this->addErrorMessage(_AD_LEGACY_LANG_IMGCAT_WRONG);
        } else {
            $root =& XCube_Root::getSingleton();
            $xoopsUser =& $root->mController->mRoot->mContext->mXoopsUser;
            
            $groups = array();
            if (is_object($xoopsUser)) {
                $groups =& $xoopsUser->getGroups();
            } else {
                $groups = array(XOOPS_GROUP_ANONYMOUS);
            }
            $imgcat =& $handler->get($imgcat_id);
            if (is_object($imgcat) && !$imgcat->hasUploadPerm($groups)) {
                $this->addErrorMessage(_MD_LEGACY_ERROR_PERMISSION);
            }
        }
    }

    public function validateUpload()
    {
        $formFile = $this->get('upload');
        if ($formFile != null) {
            $flag = false;
            foreach ($this->_allowExtensions as $ext) {
                $flag |= preg_match("/" . str_replace(".", "\.", $ext) . "$/", $formFile->getFileName());
            }
            
            if (!$flag) {
                $this->addErrorMessage(_AD_LEGACY_ERROR_EXTENSION_IS_WRONG);
            }
        }
    }
}
