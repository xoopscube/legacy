<?php
/**
 * SearchShowallbyuserAction.class.php
 * @package    Legacy
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacy/actions/SearchShowallAction.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/forms/SearchShowallbyuserForm.class.php';


class Legacy_SearchShowallbyuserAction extends Legacy_SearchShowallAction
{
    public function _setupActionForm()
    {
        $this->mActionForm =new Legacy_SearchShowallbyuserForm(0);
        $this->mActionForm->prepare();
    }

    public function _getTemplateName()
    {
        return 'legacy_search_showallbyuser.html';
    }

    public function _getSelectedMids()
    {
        $ret = [];
        $ret[] = $this->mActionForm->get('mid');

        return $ret;
    }

    public function _doSearch(&$client, &$xoopsUser, &$params)
    {
        return $client->call('searchItemsOfUser', $params);
    }

    public function _getMaxHit()
    {
        return LEGACY_SEARCH_SHOWALL_MAXHIT;
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewIndex($controller, $xoopsUser, $render);

        $handler =& xoops_gethandler('user');
        $user =& $handler->get($this->mActionForm->get('uid'));

        $render->setAttribute('targetUser', $user);

        $prevStart = $this->mActionForm->get('start') - LEGACY_SEARCH_SHOWALL_MAXHIT;
        if ($prevStart < 0) {
            $prevStart = 0;
        }

        $render->setAttribute('prevStart', $prevStart);
        $render->setAttribute('nextStart', $this->mActionForm->get('start') + LEGACY_SEARCH_SHOWALL_MAXHIT);
    }
}
