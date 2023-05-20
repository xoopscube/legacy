<?php
/**
 * @package Legacy
 * @author  Kazuhisa Minato aka minahito, Core developer
 * @version $Id: Mailjob_linkAdminDeletesForm.class.php,v 1.2 2007/06/07 05:27:37 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';

class User_Mailjob_linkAdminDeletesForm extends XCube_ActionForm
{
    public function getTokenName()
    {
        return 'module.user.Mailjob_linkAdminDeletesForm.TOKEN.' . $this->get('mailjob_id');
    }
    
    /**
     * For displaying the confirm-page, don't show CSRF error.
     * Always return null.
     */
    public function getTokenErrorMessage()
    {
        return null;
    }
    
    public function prepare()
    {
        // set properties
        $this->mFormProperties['mailjob_id']=new XCube_IntProperty('mailjob_id');
        $this->mFormProperties['uid']=new XCube_IntArrayProperty('uid');
    }
}
