<?php
/**
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacy/class/AbstractEditAction.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/admin/forms/SmilesUploadForm.class.php';

class Legacy_SmilesUploadAction extends Legacy_Action
{
    public $mActionForm = null;
    public $mErrorMessages = [];
    public $mAllowedExts = ['gif' =>'image/gif', 'jpg' =>'image/jpeg', 'jpeg' =>'image/jpeg', 'png' =>'image/png'];

    public function prepare(&$controller, &$xoopsUser)
    {
        $this->mActionForm =new Legacy_SmilesUploadForm();
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

        $formFile = $this->mActionForm->get('upload');
        $formFileExt = $formFile->getExtension();
        $files = [];
        $smilesimages = [];

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
            if (!$this->_fetchZipSmilesImages($files, $smilesimages)) {
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
            if (!$this->_fetchTarSmilesImages($tar->files, $smilesimages)) {
                return LEGACY_FRAME_VIEW_ERROR;
            }
        }//end tar

        if (!$this->_saveSmilesImages($smilesimages)) {
            return LEGACY_FRAME_VIEW_ERROR;
        }
        return LEGACY_FRAME_VIEW_SUCCESS;
    }

    public function _fetchZipSmilesImages(&$files, &$smilesimages)
    {
        foreach ($files as $file) {
            $file_pos = strrpos($file['filename'], '/') ;
            if (false !== $file_pos) {
                $file['filename'] = substr($file['filename'], $file_pos+1);
            }
            if (!empty($file['filename']) && preg_match("/(.*)\.(gif|jpg|jpeg|png)$/i", $file['filename'], $match) && !preg_match('/[' . preg_quote('\/:*?"<>|', '/') . ']/', $file['filename'])) {
                $smilesimages[] = ['name' => $file['filename'], 'content' => $file['content']];
            }
            unset($file);
        }
        return true;
    }

    public function _fetchTarSmilesImages(&$files, &$smilesimages)
    {
        foreach ($files as $id => $info) {
            $file_pos = strrpos($info['name'], '/') ;
            if (false !== $file_pos) {
                $info['name'] = substr($info['name'], $file_pos+1);
            }
            if (!empty($info['name']) && preg_match("/(.*)\.(gif|jpg|jpeg|png)$/i", $info['name'], $match) && !preg_match('/[' . preg_quote('\/:*?"<>|', '/') . ']/', $info['name'])) {
                $smilesimages[] = ['name' => $info['name'], 'content' => $info['file']];
            }
            unset($info);
        }
        return true;
    }

    public function _saveSmilesImages(&$smilesimages)
    {
        if (0 === (is_countable($smilesimages) ? count($smilesimages) : 0)) {
            return true;
        }

        $smileshandler =& xoops_getmodulehandler('smiles');

        for ($i = 0; $i < (is_countable($smilesimages) ? count($smilesimages) : 0); $i++) {
            $ext_pos = strrpos($smilesimages[$i]['name'], '.') ;
            if (false === $ext_pos) {
                continue ;
            }
            $ext = strtolower(substr($smilesimages[$i]['name'], $ext_pos + 1)) ;
            if (empty($this->mAllowedExts[$ext])) {
                continue ;
            }
            $file_name = substr($smilesimages[$i]['name'], 0, $ext_pos) ;
            $save_file_name = uniqid('smil') . '.' . $ext ;
            $filehandle = fopen(XOOPS_UPLOAD_PATH.'/'.$save_file_name, 'w') ;
            if (! $filehandle) {
                $this->_addErrorMessage(XCube_Utils::formatString(_AD_LEGACY_ERROR_COULD_NOT_SAVE_SMILES_FILE, $file_name));
                continue ;
            }
            if (!@fwrite($filehandle, $smilesimages[$i]['content'])) {
                $this->_addErrorMessage(XCube_Utils::formatString(_AD_LEGACY_ERROR_COULD_NOT_SAVE_SMILES_FILE, $file_name));
                @fclose($filehandle) ;
                continue;
            };
            @fclose($filehandle) ;

            $smiles =& $smileshandler->create();
            $smiles->set('code', $file_name);
            $smiles->set('emotion', $file_name);
            $smiles->set('smile_url', $save_file_name);
            $smiles->set('display', 1);

            if (!$smileshandler->insert($smiles)) {
                $this->_addErrorMessage(XCube_Utils::formatString(_AD_LEGACY_ERROR_COULD_NOT_SAVE_SMILES_FILE, $file_name));
            }
            unset($smiles);
        }

        return true;
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('smiles_upload.html');
        $render->setAttribute('actionForm', $this->mActionForm);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=SmilesList');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        if (0 == count($this->mErrorMessages)) {
            $controller->executeRedirect('./index.php?action=SmilesList', 1, _AD_LEGACY_ERROR_DBUPDATE_FAILED);
        } else {
            $render->setTemplateName('smiles_upload_error.html');
            $render->setAttribute('errorMessages', $this->mErrorMessages);
        }
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=SmilesList');
    }
}
