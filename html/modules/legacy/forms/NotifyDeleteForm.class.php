<?php
/**
 *
 * @package Legacy
 * @version $Id: NotifyDeleteForm.class.php,v 1.3 2008/09/25 15:12:39 kilica Exp $
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

class Legacy_NotifyDeleteForm extends XCube_ActionForm
{
    public $mNotifiyIds = [];
    public $mFatalError = false;

    public function getTokenName()
    {
        return 'POST' == $_SERVER['REQUEST_METHOD'] ? 'module.legacy.NotifyDeleteForm.TOKEN' : null;
    }

    public function fetch()
    {
        parent::fetch();

        $root =& XCube_Root::getSingleton();
        $t_arr = $root->mContext->mRequest->getRequest('del_not');

        if (!is_array($t_arr)) {
            $this->addErrorMessage(_MD_LEGACY_LANG_ERROR);
            $this->mFatalError = true;
            return;
        }

        foreach ($t_arr as $t_modid => $t_idArr) {
            if (!is_array($t_idArr)) {
                $this->addErrorMessage(_MD_LEGACY_LANG_ERROR);
                $this->mFatalError = true;
                return;
            }
            foreach ($t_idArr as $t_id) {
                $this->mNotifiyIds[] = ['modid' => (int)$t_modid, 'id' => (int)$t_id];
            }
        }
    }
}
