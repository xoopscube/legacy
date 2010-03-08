<?php
/**
 * @file
 * @package lecat
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

require_once LECAT_TRUST_PATH . '/class/AbstractAction.class.php';

/**
 * Lecat_Admin_IndexAction
**/
class Lecat_Admin_IndexAction extends Lecat_AbstractAction
{
    /**
     * getDefaultView
     * 
     * @param   void
     * 
     * @return  Enum
    **/
    public function getDefaultView()
    {
        return LECAT_FRAME_VIEW_SUCCESS;
    }

    /**
     * executeViewSuccess
     * 
     * @param   XCube_RenderTarget  &$render
     * 
     * @return  void
    **/
    public function executeViewSuccess(&$render)
    {
        $render->setTemplateName('admin.html');
        $render->setAttribute('adminMenu', $this->mModule->getAdminMenu());
    }
}

?>