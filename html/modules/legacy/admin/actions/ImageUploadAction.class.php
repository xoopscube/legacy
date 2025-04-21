<?php
/**
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacy/class/AbstractEditAction.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/admin/forms/ImageUploadForm.class.php';

class Legacy_ImageUploadAction extends Legacy_Action
{
    public $mActionForm = null;
    public $mCategory = null;
    public $mErrorMessages = [];
    public $mAllowedExts = ['gif' =>'image/gif', 'jpg' =>'image/jpeg', 'jpeg' =>'image/jpeg', 'png' =>'image/png'];

    public function prepare(&$controller, &$xoopsUser)
    {
        $this->mActionForm =new Legacy_ImageUploadForm();
        $this->mActionForm->prepare();
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        return LEGACY_FRAME_VIEW_INPUT;
    }

    public function _addErrorMessage($msg)
    {
        $this->mErrorMessages[] = $msg;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        $form_cancel = $controller->mRoot->mContext->mRequest->getRequest('_form_control_cancel');
        if (null !== $form_cancel) {
            return LEGACY_FRAME_VIEW_CANCEL;
        }

        $this->mActionForm->fetch();
        $this->mActionForm->validate();

        if ($this->mActionForm->hasError()) {
            return $this->getDefaultView($controller, $xoopsUser);
        }

        $t_imgcat_id = $this->mActionForm->get('imgcat_id');

        $formFile = $this->mActionForm->get('upload');
        $formFileExt = $formFile->getExtension();
        $files = [];
        $targetimages = [];

        if ('zip' === strtolower($formFileExt)) {
            if (!file_exists(XOOPS_ROOT_PATH . '/class/Archive_Zip.php')) {
                return LEGACY_FRAME_VIEW_ERROR;
            }
            require_once XOOPS_ROOT_PATH . '/class/Archive_Zip.php';
            $zip = new Archive_Zip($formFile->_mTmpFileName) ;
            $files = $zip->extract(['extract_as_string' => true]) ;
            if (! is_array(@$files)) {
                return LEGACY_FRAME_VIEW_ERROR;
            }
            if (!$this->_fetchZipTargetImages($files, $targetimages)) {
                return LEGACY_FRAME_VIEW_ERROR;
            }
        }//if zip end
        else {
            require_once XOOPS_ROOT_PATH . '/class/class.tar.php';
            $tar =new tar();
            $tar->openTar($formFile->_mTmpFileName);
            if (!is_array(@$tar->files)) {
                return LEGACY_FRAME_VIEW_ERROR;
            }
            if (!$this->_fetchTarTargetImages($tar->files, $targetimages)) {
                return LEGACY_FRAME_VIEW_ERROR;
            }
        }//end tar

        if (!$this->_saveTargetImages($targetimages, $t_imgcat_id)) {
            return LEGACY_FRAME_VIEW_ERROR;
        }
        return LEGACY_FRAME_VIEW_SUCCESS;
    }

    public function _fetchZipTargetImages(&$files, &$targetimages)
    {
        foreach ($files as $file) {
            $file_pos = strrpos($file['filename'], '/') ;
            if (false !== $file_pos) {
                $file['filename'] = substr($file['filename'], $file_pos+1);
            }
            if (!empty($file['filename']) && preg_match("/(.*)\.(gif|jpg|jpeg|png)$/i", $file['filename'], $match) && !preg_match('/[' . preg_quote('\/:*?"<>|', '/') . ']/', $file['filename'])) {
                $targetimages[] = ['name' => $file['filename'], 'content' => $file['content']];
            }
            unset($file);
        }
        return true;
    }

    public function _fetchTarTargetImages(&$files, &$targetimages)
    {
        foreach ($files as $id => $info) {
            $file_pos = strrpos($info['name'], '/') ;
            if (false !== $file_pos) {
                $info['name'] = substr($info['name'], $file_pos+1);
            }
            if (!empty($info['name']) && preg_match("/(.*)\.(gif|jpg|jpeg|png)$/i", $info['name'], $match) && !preg_match('/[' . preg_quote('\/:*?"<>|', '/') . ']/', $info['name'])) {
                $targetimages[] = ['name' => $info['name'], 'content' => $info['file']];
            }
            unset($info);
        }
        return true;
    }

    public function _saveTargetImages(&$targetimages, $t_imgcat_id)
    {
        if (0 === (is_countable($targetimages) ? count($targetimages) : 0)) {
            return true;
        }

        $imgcathandler =& xoops_getmodulehandler('imagecategory', 'legacy');
        $t_category = & $imgcathandler->get($t_imgcat_id);
        $t_category_type = $t_category->get('imgcat_storetype');
        $imagehandler =& xoops_getmodulehandler('image');

        if ('file' === strtolower($t_category_type)) {
            for ($i = 0; $i < (is_countable($targetimages) ? count($targetimages) : 0); $i++) {
                $ext_pos = strrpos($targetimages[$i]['name'], '.') ;
                if (false === $ext_pos) {
                    continue ;
                }
                $ext = strtolower(substr($targetimages[$i]['name'], $ext_pos + 1)) ;
                if (empty($this->mAllowedExts[$ext])) {
                    continue ;
                }
                $file_name = substr($targetimages[$i]['name'], 0, $ext_pos) ;
                $save_file_name = uniqid('img') . '.' . $ext ;
                $filehandle = fopen(XOOPS_UPLOAD_PATH.'/'.$save_file_name, 'wb') ;
                if (! $filehandle) {
                    $this->_addErrorMessage(XCube_Utils::formatString(_AD_LEGACY_ERROR_COULD_NOT_SAVE_IMAGE_FILE, $file_name));
                    continue ;
                }
                if (!@fwrite($filehandle, $targetimages[$i]['content'])) {
                    $this->_addErrorMessage(XCube_Utils::formatString(_AD_LEGACY_ERROR_COULD_NOT_SAVE_IMAGE_FILE, $file_name));
                    @fclose($filehandle) ;
                    continue;
                };
                @fclose($filehandle) ;

                $image =& $imagehandler->create();
                $image->set('image_nicename', $file_name);
                $image->set('image_name', $save_file_name);
                $image->set('image_mimetype', $this->mAllowedExts[$ext]);
                $image->set('image_display', 1);
                $image->set('imgcat_id', $t_imgcat_id);

                if (!$imagehandler->insert($image)) {
                    $this->_addErrorMessage(XCube_Utils::formatString(_AD_LEGACY_ERROR_COULD_NOT_SAVE_IMAGE_FILE, $file_name));
                }
                unset($image);
            } //end of for
        } //end of if
        elseif ('db' === strtolower($t_category_type)) {
            foreach ($targetimages as $iValue) {
                $ext_pos = strrpos($iValue['name'], '.') ;
                if ($ext_pos === false) {
                    continue ;
                }
                $ext = strtolower(substr($iValue['name'], $ext_pos + 1)) ;
                if (empty($this->mAllowedExts[$ext])) {
                    continue ;
                }
                $file_name = substr($iValue['name'], 0, $ext_pos) ;
                $save_file_name = uniqid('img', true) . '.' . $ext ;
                //
                $image =& $imagehandler->create();
                $image->set('image_nicename', $file_name);
                $image->set('image_name', $save_file_name);
                $image->set('image_mimetype', $this->mAllowedExts[$ext]);
                $image->set('image_display', 1);
                $image->set('imgcat_id', $t_imgcat_id);
                $image->loadImageBody();
                if (!is_object($image->mImageBody)) {
                    $image->mImageBody =& $image->createImageBody();
                }
                $image->mImageBody->set('image_body', $iValue['content']);

                if (!$imagehandler->insert($image)) {
                    $this->_addErrorMessage(XCube_Utils::formatString(_AD_LEGACY_ERROR_COULD_NOT_SAVE_IMAGE_FILE, $file_name));
                }
                unset($image);
            } //end of for
        } //end of elseif
        return true;
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('image_upload.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        //image category
        $handler =& xoops_getmodulehandler('imagecategory', 'legacy');
        $cat_id = $controller->mRoot->mContext->mRequest->getRequest('imgcat_id');
        if (isset($cat_id)) {
            $this->mCategory =& $handler->get($cat_id);
            $render->setAttribute('category', $this->mCategory);
        }
        $categoryArr =& $handler->getObjects();
        $render->setAttribute('categoryArr', $categoryArr);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=ImageList&imgcat_id=' . $this->mActionForm->get('imgcat_id'));
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        if (count($this->mErrorMessages) == 0) {
            $controller->executeRedirect('./index.php?action=ImageList&imgcat_id=' . $this->mActionForm->get('imgcat_id'), 1, _AD_LEGACY_ERROR_DBUPDATE_FAILED);
        } else {
            $render->setTemplateName('image_upload_error.html');
            $render->setAttribute('errorMessages', $this->mErrorMessages);
        }
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        if ($this->mCategory) {
            $controller->executeForward('./index.php?action=ImageList&imgcat_id=' . $this->mCategory->get('imgcat_id'));
        } else {
            $controller->executeForward('./index.php?action=ImagecategoryList');
        }
    }
}
