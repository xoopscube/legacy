<?php
/**
 * @package user
 * @author  Kazuhisa Minato aka minahito, Core developer
 * @version $Id: RanksAdminDeleteForm.class.php,v 1.2 2007/06/07 05:27:37 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';

class User_RanksAdminDeleteForm extends XCube_ActionForm
{
    public function getTokenName()
    {
        return 'module.user.RanksAdminDeleteForm.TOKEN' . $this->get('rank_id');
    }

    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['rank_id'] =new XCube_IntProperty('rank_id');

        //
        // Set field properties
        //
        $this->mFieldProperties['rank_id'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['rank_id']->setDependsByArray(['required']);
        $this->mFieldProperties['rank_id']->addMessage('required', _MD_USER_ERROR_REQUIRED, _MD_USER_LANG_RANK_ID);
    }

    public function load(&$obj)
    {
        $this->set('rank_id', $obj->get('rank_id'));
    }

    public function update(&$obj)
    {
        $obj->setVar('rank_id', $this->get('rank_id'));
    }
}
