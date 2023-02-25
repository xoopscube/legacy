<?php
/**
 *
 * @package Legacy
 * @version $Id: ImageUploadForm.class.php,v 1.4 2008/09/25 15:12:40 kilica Exp $
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

class Legacy_ImageUploadForm extends XCube_ActionForm
{
    public $mOldFileName = null;
    public $_mIsNew = null;
    public $mFormFile = null;

    public function getTokenName()
    {
        return 'module.legacy.ImageUploadForm.TOKEN' . $this->get('imgcat_id');
    }

    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['image_name'] =new XCube_ImageFileProperty('image_name');
        $this->mFormProperties['image_nicename'] =new XCube_StringProperty('image_nicename');
        $this->mFormProperties['imgcat_id'] =new XCube_IntProperty('imgcat_id');

        //
        // Set field properties
        //
        $this->mFieldProperties['image_name'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['image_name']->setDependsByArray(['extension']);
        $this->mFieldProperties['image_name']->addVar('extension', 'jpg,gif,png');

        $this->mFieldProperties['image_nicename'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['image_nicename']->setDependsByArray(['required']);
        $this->mFieldProperties['image_nicename']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _MD_LEGACY_LANG_IMAGE_NICENAME);

        $this->mFieldProperties['imgcat_id'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['imgcat_id']->setDependsByArray(['required', 'objectExist']);
        $this->mFieldProperties['imgcat_id']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _MD_LEGACY_LANG_IMGCAT_ID);
        $this->mFieldProperties['imgcat_id']->addMessage('objectExist', _MD_LEGACY_ERROR_OBJECTEXIST, _MD_LEGACY_LANG_IMGCAT_ID);
        $this->mFieldProperties['imgcat_id']->addVar('handler', 'imagecategory');
        $this->mFieldProperties['imgcat_id']->addVar('module', 'legacy');

        // Fix the bug #1769768
        // https://sourceforge.net/tracker/?func=detail&aid=1769768&group_id=159211&atid=943471
        // The action form should not load language files and should be given resources
        // from outside. However, the ideal fix needs changing much message catalogs
        // including code which I can not edit. So I put the following code as an
        // exception.
        $root =& XCube_Root::getSingleton();
        $root->mLanguageManager->loadModuleAdminMessageCatalog('legacy');
    }

    /**
     * Check the permission of uploading.
     */
    public function validateImgcat_id()
    {
        $imgcat_id = $this->get('imgcat_id');
        if (null !== $imgcat_id) {
            $root =& XCube_Root::getSingleton();
            $xoopsUser =& $root->mController->mRoot->mContext->mXoopsUser;

            $groups = [];
            if (is_object($xoopsUser)) {
                $groups =& $xoopsUser->getGroups();
            } else {
                $groups = [XOOPS_GROUP_ANONYMOUS];
            }

            $handler =& xoops_getmodulehandler('imagecategory', 'legacy');
            $imgcat =& $handler->get($imgcat_id);
            if (is_object($imgcat) && !$imgcat->hasUploadPerm($groups)) {
                $this->addErrorMessage(_MD_LEGACY_ERROR_PERMISSION);
            }
        }
    }

    public function validateImage_name()
    {
        $formFile = $this->get('image_name');

        if (null == $formFile && $this->_mIsNew) {
            $this->addErrorMessage(_MD_LEGACY_ERROR_YOU_MUST_UPLOAD);
        }
    }

    public function validate()
    {
        parent::validate();

        $handler =& xoops_getmodulehandler('imagecategory', 'legacy');
        $category =& $handler->get($this->get('imgcat_id'));

        $formFile = $this->get('image_name');

        if (null !== $formFile && is_object($category)) {
            //
            // Imagefile width & height check.
            //
            if ($formFile->getWidth() > $category->get('imgcat_maxwidth') || $formFile->getHeight() > $category->get('imgcat_maxheight')) {
                $this->addErrorMessage(XCube_Utils::formatString(_AD_LEGACY_ERROR_IMG_SIZE, $category->get('imgcat_maxwidth'), $category->get('imgcat_maxheight')));
            }

            //
            // Check file size
            //
            if ($formFile->getFilesize() > $category->get('imgcat_maxsize')) {
                $this->addErrorMessage(XCube_Utils::formatString(_AD_LEGACY_ERROR_IMG_FILESIZE, $category->get('imgcat_maxsize')));
            }
        }
    }

    public function load(&$obj)
    {
        $this->set('image_nicename', $obj->get('image_nicename'));
        $this->set('imgcat_id', $obj->get('imgcat_id'));

        $this->_mIsNew = $obj->isNew();
        $this->mOldFileName = $obj->get('image_name');
    }

    public function update(&$obj)
    {
        $obj->set('image_nicename', $this->get('image_nicename'));
        $obj->set('image_display', true);
        $obj->set('imgcat_id', $this->get('imgcat_id'));

        $handler =& xoops_getmodulehandler('imagecategory', 'legacy');
        $category =& $handler->get($this->get('imgcat_id'));

        $this->mFormFile = $this->get('image_name');

        if (null !== $this->mFormFile) {
            $this->mFormFile->setRandomToBodyName('img');

            $filename = $this->mFormFile->getBodyName();
            $this->mFormFile->setBodyName(substr($filename, 0, 24));

            $obj->set('image_name', $this->mFormFile->getFileName());
            $obj->set('image_mimetype', $this->mFormFile->getContentType());

            //
            // To store db
            //
            if ('db' == $category->get('imgcat_storetype')) {
                $obj->loadImageBody();
                if (!is_object($obj->mImageBody)) {
                    $obj->mImageBody =& $obj->createImageBody();
                }

                //
                // Access to private member property.
                //
                $obj->mImageBody->set('image_body', file_get_contents($this->mFormFile->_mTmpFileName));
            }
        }
    }
}
