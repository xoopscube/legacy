<?php
/**
 * @package legacyRender
 * @version $Id: TplsetUploadAction.class.php,v 1.1 2007/05/15 02:34:17 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacyRender/class/AbstractEditAction.class.php';
require_once XOOPS_MODULE_PATH . '/legacyRender/admin/forms/TplsetUploadForm.class.php';

class LegacyRender_TplsetUploadAction extends LegacyRender_Action
{
    public $mActionForm = null;
    public $mErrorMessages = [];

    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        $this->mActionForm =new LegacyRender_TplsetUploadForm();
        $this->mActionForm->prepare();
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        return LEGACYRENDER_FRAME_VIEW_INPUT;
    }

    public function _addErrorMessage($msg)
    {
        $this->mErrorMessages[] = $msg;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        if (null != xoops_getrequest('_form_control_cancel')) {
            return LEGACYRENDER_FRAME_VIEW_CANCEL;
        }

        $this->mActionForm->fetch();
        $this->mActionForm->validate();

        if ($this->mActionForm->hasError()) {
            return $this->getDefaultView($controller, $xoopsUser);
        }

        require_once XOOPS_ROOT_PATH . '/class/class.tar.php';
        $tar =new tar();

        $formFile = $this->mActionForm->get('upload');

        //
        // [Warn] access private member directly
        // TODO We should define the access method because we oftern
        //      access private member of XCube_FormFile.
        //
        $tar->openTar($formFile->_mTmpFileName);

        if (!is_array($tar->files)) {
            return LEGACYRENDER_FRAME_VIEW_ERROR;
        }

        $tplsetName = null;
        foreach ($tar->files as $id => $info) {
            $infoArr = explode('/', str_replace("\\", '/', $info['name']));
            $tplsetName = $this->mActionForm->get('tplset_name');
            if (null == $tplsetName) {
                $tplsetName = trim($infoArr[0]);
            }

            if (null != $tplsetName) {
                break;
            }
        }

        //
        // Check tplset name.
        //
        if (null == $tplsetName || preg_match('/[' . preg_quote('\/:*?"<>|', '/') . ']/', $tplsetName)) {
            $this->_addErrorMessage(_AD_LEGACYRENDER_ERROR_TPLSET_NAME_WRONG);
            return LEGACYRENDER_FRAME_VIEW_ERROR;
        }

        $handler =& xoops_getmodulehandler('tplset');
        if (0 != $handler->getCount(new Criteria('tplset_name', $tplsetName))) {
            $this->_addErrorMessage(XCube_Utils::formatString(_AD_LEGACYRENDER_ERROR_TPLSET_ALREADY_EXISTS, $tplsetName));
            return LEGACYRENDER_FRAME_VIEW_ERROR;
        }

        $tplset =& $handler->create();
        $tplset->set('tplset_name', $tplsetName);
        if (!$handler->insert($tplset)) {
            $this->_addErrorMessage(_AD_LEGACYRENDER_ERROR_COULD_NOT_SAVE_TPLSET);
            return LEGACYRENDER_FRAME_VIEW_ERROR;
        }

        $themeimages = [];
        if (!$this->_fetchTemplateFiles($tar, $tplset, $themeimages)) {
            return LEGACYRENDER_FRAME_VIEW_ERROR;
        }

        if (!$this->_fetchImageset($tar, $tplset, $themeimages)) {
            return LEGACYRENDER_FRAME_VIEW_ERROR;
        }

        return LEGACYRENDER_FRAME_VIEW_SUCCESS;
    }

    public function _fetchTemplateFiles(&$tar, &$tplset, &$themeimages)
    {
        $handler =& xoops_getmodulehandler('tplfile');
        foreach ($tar->files as $id => $info) {
            $infoArr = explode('/', str_replace("\\", '/', $info['name']));
            if (isset($infoArr[3]) && 'blocks' == trim($infoArr[3])) {
                $default =& $handler->find('default', 'block', null, trim($infoArr[2]), trim($infoArr[4]));
            } elseif ((!isset($infoArr[4]) || '' == trim($infoArr[4])) && 'templates' == $infoArr[1]) {
                $default =& $handler->find('default', 'module', null, trim($infoArr[2]), trim($infoArr[3]));
            } elseif (isset($infoArr[3]) && 'images' == trim($infoArr[3])) {
                $infoArr[2] = trim($infoArr[2]);
                if (preg_match("/(.*)\.(gif|jpg|jpeg|png)$/i", $infoArr[2], $match)) {
                    $themeimages[] = ['name' => $infoArr[2], 'content' => $info['file']];
                }
            }
            if ((is_countable($default) ? count($default) : 0) > 0) {
                $tplfile =& $default[0]->createClone($tplset->get('tplset_name'));
                $tplfile->Source->set('tpl_source', $info['file']);
                $tplfile->set('tpl_lastimported', time());

                if (!$handler->insert($tplfile)) {
                    $this->_addErrorMessage(XCube_Utils::formatString(_AD_LEGACYRENDER_ERROR_COULD_NOT_SAVE_TPLFILE, $tplfile->get('tpl_file')));
                }
                unset($default);
            }
            unset($info);
        }

        return true;
    }

    public function _fetchImageset(&$tar, &$tplset, &$themeimages)
    {
        if (0 == (is_countable($themeimages) ? count($themeimages) : 0)) {
            return true;
        }

        $handler =& xoops_gethandler('imageset');
        $imgset =& $handler->create();
        $imgset->set('imgset_name', $tplset->get('tplset_name'));
        $imgset->set('imgset_refid', 0);

        if (!$handler->insert($imgset)) {
            $this->_addErrorMessage(XCube_Utils::formatString(_AD_LEGACYRENDER_ERROR_COULD_NOT_SAVE_IMAGESET, $tplset->get('tplset_name')));
            return false;
        }

        if (!$handler->linktplset($imgset->get('imgset_id'), $tplset->get('tplset_name'))) {
            $this->_addErrorMessage(_AD_LEGACYRENDER_ERROR_COULD_NOT_SAVE_LINKTPLSET);
            return false;
        }

        $handler =& xoops_gethandler('imagesetimg');
        for ($i = 0; $i < (is_countable($themeimages) ? count($themeimages) : 0); $i++) {
            if (isset($themeimages[$i]['name']) && '' != $themeimages[$i]['name']) {
                $image =& $handler->create();
                $image->set('imgsetimg_file', $themeimages[$i]['name']);
                $image->set('imgsetimg_imgset', $imgset->get('imgset_id'));
                $image->set('imgsetimg_body', $themeimages[$i]['content'], true);
                if (!$handler->insert($image)) {
                    $this->_addErrorMessage(XCube_Utils::formatString(_AD_LEGACYRENDER_ERROR_COULD_NOT_SAVE_IMAGE_FILE, $image->get('imgsetimg_file')));
                }
                unset($image);
            }
        }

        return true;
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('tplset_upload.html');
        $render->setAttribute('actionForm', $this->mActionForm);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=TplsetList');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        if (0 == count($this->mErrorMessages)) {
            $controller->executeRedirect('./index.php?action=TplsetList', 1, _AD_LEGACYRENDER_ERROR_DBUPDATE_FAILED);
        } else {
            $render->setTemplateName('tplset_upload_error.html');
            $render->setAttribute('errorMessages', $this->mErrorMessages);
        }
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=TplsetList');
    }
}
