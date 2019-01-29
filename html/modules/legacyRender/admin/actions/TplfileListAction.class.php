<?php
/**
 * @package legacyRender
 * @version $Id: TplfileListAction.class.php,v 1.1 2007/05/15 02:34:17 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . "/legacyRender/class/AbstractListAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacyRender/admin/forms/TplfileFilterForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacyRender/admin/forms/TplfileSetFilterForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacyRender/admin/forms/TplfileUploadForm.class.php";

class LegacyRender_TplfileListAction extends LegacyRender_AbstractListAction
{
    /**
     * A instance of action form for uploading.
     * @var LegacyRender_TplfileUploadForm
     */
    public $mActionForm = null;
    
    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        LegacyRender_AbstractListAction::prepare($controller, $xoopsUser, $moduleConfig);
        $this->mActionForm =new LegacyRender_TplfileUploadForm();
        $this->mActionForm->prepare();
    }
    
    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('tplfile');
        return $handler;
    }

    public function &_getFilterForm()
    {
        $filter = isset($_REQUEST['tpl_tplset']) ? new LegacyRender_TplfileSetFilterForm($this->_getPageNavi(), $this->_getHandler())
                                                 : new LegacyRender_TplfileFilterForm($this->_getPageNavi(), $this->_getHandler());
        return $filter;
    }
    
    public function _getBaseUrl()
    {
        return "./index.php?action=TplfileList";
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        $this->mFilter =& $this->_getFilterForm();
        $this->mFilter->fetch();
        
        $handler =& $this->_getHandler();
        
        $criteria = $this->mFilter->getCriteria();
        
        if (isset($_REQUEST['tpl_tplset'])) {
            $this->mObjects =& $handler->getObjectsWithOverride($criteria, xoops_getrequest('tpl_tplset'));
        } else {
            $this->mObjects =& $handler->getObjects($criteria);
        }
    
        return LEGACYRENDER_FRAME_VIEW_INDEX;
    }

    /**
     * This member function processes the uploaded file.
     */
    public function execute(&$controller, &$xoopsUser)
    {
        require_once XOOPS_ROOT_PATH . "/class/template.php";
        
        $this->mActionForm->fetch();
        $this->mActionForm->validate();
        
        if ($this->mActionForm->hasError()) {
            return $this->getDefaultView($controller, $xoopsUser);
        }
        
        $formFileArr = $this->mActionForm->get('upload');

        //
        // Set tpl_module and tpl_tplset of the last object to the following variable for redirect.
        //
        $last_tplset = null;
        $last_module = null;
        
        $handler =& xoops_getmodulehandler('tplfile');
        
        $successFlag = true;
        
        foreach (array_keys($formFileArr) as $key) {
            $formFile =& $formFileArr[$key];
            
            $obj =& $handler->get($key);
            if ($obj == null) {
                continue;
            }

            //
            // If $obj belongs to 'default' template-set, kick!
            //			
            if ($obj->get('tpl_tplset') == 'default') {
                continue;
            }

            $obj->loadSource();
            
            $last_tplset = $obj->get('tpl_tplset');
            $last_module = $obj->get('tpl_module');
            
            //
            // [Warning] Access to a private property of XCube_FormFile.
            //
            if ($formFile != null) {
                $source = file_get_contents($formFile->_mTmpFileName);
                $obj->Source->set('tpl_source', $source);
                $obj->set('tpl_lastmodified', time());
                $obj->set('tpl_lastimported', time());
                
                $successFlag &= $handler->insert($obj);
                
                $xoopsTpl =new XoopsTpl();
                $xoopsTpl->clear_cache('db:' . $obj->get('tpl_file'));
                $xoopsTpl->clear_compiled_tpl('db:' . $obj->get('tpl_file'));
            }
        
            unset($obj);
            unset($formFile);
        }
        
        $errorMessage = $successFlag ? _AD_LEGACYRENDER_MESSAGE_UPLOAD_TEMPLATE_SUCCESS : _AD_LEGACYRENDER_ERROR_DBUPDATE_FAILED;
        
        //
        // No good exmaple ;)
        // Because some local variables are used, jump directly without the return value of view status.
        //
        $controller->executeRedirect("index.php?action=TplfileList&tpl_tplset=${last_tplset}&tpl_module=${last_module}", 1, $errorMessage);
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        $controller->mRoot->mDelegateManager->add('Legacy.Event.Explaceholder.Get.LegacyRenderPagenaviHidden', 'LegacyRender_TplfileListAction::renderHiddenControl');
        
        $render->setTemplateName("tplfile_list.html");
        
        //
        // Load override file.
        //
        if ($this->mFilter->mTplset != null && $this->mFilter->mTplset->get('tplset_name') != 'default') {
            foreach (array_keys($this->mObjects) as $key) {
                $this->mObjects[$key]->loadOverride($this->mFilter->mTplset->get('tplset_name'));
            }
        }
        
        $render->setAttribute('objects', $this->mObjects);
        $render->setAttribute('pageNavi', $this->mFilter->mNavi);
        $render->setAttribute('filterForm', $this->mFilter);
        $render->setAttribute('actionForm', $this->mActionForm);
        
        if ($this->mFilter->mTplset != null) {
            $render->setAttribute('targetTplset', $this->mFilter->mTplset->get('tplset_name'));
        }
        
        $render->setAttribute('targetModule', xoops_getrequest('tpl_module'));
        
        //
        // TODO We must fetch only module objects that has templates.
        // 
        // fetch module objects, assign to template for pull-down menu.
        //
        $moduleHandler =& xoops_gethandler('module');
        $modules =& $moduleHandler->getObjects();
        $render->setAttribute('modules', $modules);

        $handler =& xoops_getmodulehandler('tplset');
        $tplsets =& $handler->getObjects();
        $render->setAttribute('tplsets', $tplsets);
    }
    
    // !Fix static function call for PHP 7.x
    public static function renderHiddenControl(&$buf, $params)
    // public function renderHiddenControl(&$buf, $params)
    {
        if (isset($params['pagenavi']) && is_object($params['pagenavi'])) {
            $navi =& $params['pagenavi'];
            $mask = isset($params['mask']) ? $params['mask'] : null;
            
            foreach ($navi->mExtra as $key => $value) {
                if ($key != $mask) {
                    $value = htmlspecialchars($value, ENT_QUOTES);
                    $buf .= "<input type=\"hidden\" name=\"${key}\" value=\"${value}\" />";
                }
            }
        }
    }
}
