<?php
/**
 * @file
 * @package legacy
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Legacy_TextareaEditor extends XCube_ActionFilter
{
    /**
     * @public
     */
    function preBlockFilter()
    {
        $this->mRoot->mDelegateManager->add('Site.TextareaEditor.BBCode.Show','Legacy_TextareaEditor::renderBBCode',XCUBE_DELEGATE_PRIORITY_FINAL);
        $this->mRoot->mDelegateManager->add('Site.TextareaEditor.HTML.Show','Legacy_TextareaEditor::renderHTML',XCUBE_DELEGATE_PRIORITY_FINAL);
        $this->mRoot->mDelegateManager->add('Site.TextareaEditor.None.Show','Legacy_TextareaEditor::renderBBCode',XCUBE_DELEGATE_PRIORITY_FINAL);
    }

    /**
     *  @public
    */
    function renderBBCode(&$html, $id, $name, $name, $value, $rows, $cols)
    {
        if (!XC_CLASS_EXISTS('xoopsformelement')) {
            require_once XOOPS_ROOT_PATH . "/class/xoopsformloader.php";
        }
    
        $form =& new XoopsFormDhtmlTextArea($name, $name, $value, $rows, $cols);
        $form->setId($id);
        if ($class != null) {
            $form->setClass($class);
        }
        
        $html = $form->render();
    }

    /**
     *  @public
    */
    function renderHtml(&$html, $params)
    {
        if(! empty($params['name'])){
            require_once XOOPS_ROOT_PATH.'/common/fckeditor/fckeditor.php';
        
            // FCK editor options $params.
            $basepath = isset($params['basepath']) ? htmlspecialchars(trim($params['basepath']), ENT_QUOTES) : "/common/fckeditor/";
            $toolbar = isset($params['toolbar']) ? htmlspecialchars(trim($params['toolbar']), ENT_QUOTES) : "Default";
            $skin = isset($params['skin']) ? htmlspecialchars(trim($params['skin']), ENT_QUOTES) : "default";
        
            ob_start();
            $oFCKeditor = new FCKeditor(trim($params['name'])) ;
            $oFCKeditor->BasePath = XOOPS_URL. $basepath;
            $oFCKeditor->Value = isset($params['value']) ? $params['value'] : null;
            $oFCKeditor->Height = isset($params['height']) ? htmlspecialchars(trim($params['height']), ENT_QUOTES) : "400px";
            $oFCKeditor->Width = isset($params['width']) ? htmlspecialchars(trim($params['width']), ENT_QUOTES) : "400px";
            // options
            if (isset($toolbar)) $oFCKeditor->ToolbarSet = $toolbar ;
            if (isset($skin)) $oFCKeditor->Config['SkinPath'] =  XOOPS_URL.
$basepath. 'editor/skins/'. $skin. '/' ;
            $oFCKeditor->Create() ;
            $html = ob_get_contents();
            ob_end_clean();
        }
    }

}

?>
