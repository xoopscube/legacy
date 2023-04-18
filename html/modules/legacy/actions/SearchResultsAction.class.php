<?php
/**
 * SearchAction.class.php
 * @package    Legacy
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacy/forms/SearchResultsForm.class.php';

const LEGACY_SEARCH_RESULT_MAXHIT = 5;
const LEGACY_SEARCH_SHOWALL_MAXHIT = 20;


class Legacy_SearchResultsAction extends Legacy_Action
{
    public $mActionForm = null;
    public $mSearchResults = [];
    public $mModules = [];

    public $mConfig = [];

    public function prepare(&$controller, &$xoopsUser)
    {
        $root =& $controller->mRoot;
        $root->mLanguageManager->loadPageTypeMessageCatalog('search');
        $root->mLanguageManager->loadModuleMessageCatalog('legacy');

        $handler =& xoops_gethandler('config');
        $this->mConfig =& $handler->getConfigsByCat(XOOPS_CONF_SEARCH);

        $this->_setupActionForm();
    }

    public function _setupActionForm()
    {
        $this->mActionForm =new Legacy_SearchResultsForm($this->mConfig['keyword_min']);
        $this->mActionForm->prepare();
    }

    public function hasPermission(&$controller, &$xoopsUser)
    {
        // Avoid strict check !
        if (1 != $this->mConfig['enable_search']) {
            $controller->executeRedirect(XOOPS_URL . '/', 3, _MD_LEGACY_ERROR_SEARCH_NOT_ENABLED);
            return false;
        }
        return true;
    }

    public function _getMaxHit()
    {
        return LEGACY_SEARCH_RESULT_MAXHIT;
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        $params = [];
        $root =& $controller->mRoot;
        $service =& $root->mServiceManager->getService('LegacySearch');

        if (is_object($service)) {
            $client =& $root->mServiceManager->createClient($service);
            $this->mModules = $client->call('getActiveModules', []);
        }

        $this->mActionForm->fetch();
        $this->mActionForm->validate();

        if ($this->mActionForm->hasError()) {
            return LEGACY_FRAME_VIEW_INDEX;
        }

        //
        // TODO ErrorHandling
        //
        if (is_object($service)) {
            $this->mActionForm->update($params);

            $handler =& xoops_gethandler('module');
            foreach ($this->_getSelectedMids() as $mid) {
                $t_module =& $handler->get($mid);
                if (is_object($t_module)) {
                    $module = [];

                    $module['mid'] = $mid;
                    $module['name'] = $t_module->get('name');

                    $params['mid'] = $mid;
                    $module['results'] = $this->_doSearch($client, $xoopsUser, $params);

                    if ((is_countable($module['results']) ? count($module['results']) : 0) > 0) {
                        // @todo @gigamaster $module['has_more'] = (count($module['results']) >= $this->_getMaxHit()) ? true : false;
                        $module['has_more'] = (is_countable($module['results']) ? count($module['results']) : 0) >= $this->_getMaxHit();
                        $this->mSearchResults[] = $module;
                    }
                }
            }
        } else {
            return LEGACY_FRAME_VIEW_ERROR;
        }

        return LEGACY_FRAME_VIEW_INDEX;
    }

    public function _doSearch(&$client, &$xoopsUser, &$params)
    {
        $root =& XCube_Root::getSingleton();
        $timezone = $root->mContext->getXoopsConfig('server_TZ') * 3600;

        $results = $client->call('searchItems', $params);

        return $results;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        return $this->getDefaultView($controller, $xoopsUser);
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName($this->_getTemplateName());

        $render->setAttribute('actionForm', $this->mActionForm);

        $render->setAttribute('searchResults', $this->mSearchResults);
        $render->setAttribute('moduleArr', $this->mModules);

        //
        // If the request include $mids, setAttribute it. If it don't include,
        // setAttribute $mid or $this->mModules.
        //
        $render->setAttribute('selectedMidArr', $this->_getSelectedMids());
        $render->setAttribute('searchRuleMessage', @sprintf(_SR_KEYTOOSHORT, $this->mConfig['keyword_min']));
    }

    public function _getTemplateName()
    {
        return 'legacy_search_results.html';
    }

    public function _getSelectedMids()
    {
        $ret = $this->mActionForm->get('mids');
        if (!(is_countable($ret) ? count($ret) : 0)) {
            foreach ($this->mModules as $module) {
                $ret[] = $module['mid'];
            }
        }

        return $ret;
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward(XOOPS_URL . '/');
    }
}
