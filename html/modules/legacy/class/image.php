<?php
/**
 *
 * @package Legacy
 * @version $Id: image.php,v 1.3 2008/09/25 15:11:33 kilica Exp $
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class LegacyImageObject extends XoopsSimpleObject
{
    public $mImageCategory = null;
    public $_mImageCategoryLoadedFlag = false;
    public $mImageBody = null;
    public $_mImageBodyLoadedFlag = false;

    public function __construct()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->mVars = $initVars;
            return;
        }
        $this->initVar('image_id', XOBJ_DTYPE_INT, '', false);
        $this->initVar('image_name', XOBJ_DTYPE_STRING, '', true, 30);
        $this->initVar('image_nicename', XOBJ_DTYPE_STRING, '', true, 191);
        $this->initVar('image_mimetype', XOBJ_DTYPE_STRING, '', true, 30);
        $this->initVar('image_created', XOBJ_DTYPE_INT, time(), true);
        $this->initVar('image_display', XOBJ_DTYPE_BOOL, '1', true);
        $this->initVar('image_weight', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('imgcat_id', XOBJ_DTYPE_INT, '0', true);
        $initVars=$this->mVars;
    }

    public function loadImagecategory()
    {
        if (false === $this->_mImageCategoryLoadedFlag) {
            $handler =& xoops_getmodulehandler('imagecategory', 'legacy');
            $this->mImageCategory =& $handler->get($this->get('imgcat_id'));
            $this->_mImageCategoryLoadedFlag = true;
        }
    }

    public function loadImagebody()
    {
        if (false === $this->_mImageBodyLoadedFlag) {
            $handler =& xoops_getmodulehandler('imagebody', 'legacy');
            $this->mImageBody =& $handler->get($this->get('image_id'));
            $this->_mImageBodyLoadedFlag = true;
        }
    }

    public function &createImagebody()
    {
        $handler =& xoops_getmodulehandler('imagebody', 'legacy');
        $obj =& $handler->create();
        $obj->set('image_id', $this->get('image_id'));
        return $obj;
    }
}

class LegacyImageHandler extends XoopsObjectGenericHandler
{
    public $mTable = 'image';
    public $mPrimary = 'image_id';
    public $mClass = 'LegacyImageObject';

    public function insert(&$obj, $force = false)
    {
        if (parent::insert($obj, $force)) {
            if (is_object($obj->mImageBody)) {
                $obj->mImageBody->set('image_id', $obj->get('image_id'));
                $handler =& xoops_getmodulehandler('imagebody', 'legacy');
                return $handler->insert($obj->mImageBody, $force);
            }

            return true;
        }

        return false;
    }

    /**
     *
     * Delete object and image file.
     *
     * @param LegacyImageObject $obj
     * @param bool              $force
     * @return bool
     */
    public function delete(&$obj, $force = false)
    {
        $obj->loadImagebody();

        if (parent::delete($obj, $force)) {
            $filepath = XOOPS_UPLOAD_PATH . '/' . $obj->get('image_name');
            if (file_exists($filepath)) {
                @unlink($filepath);
            }

            if (is_object($obj->mImageBody)) {
                $handler =& xoops_getmodulehandler('imagebody', 'legacy');
                $handler->delete($obj->mImageBody, $force);
            }

            return true;
        }

        return false;
    }
}
