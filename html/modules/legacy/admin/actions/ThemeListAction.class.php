<?php
/**
 * ThemeListAction.class.php
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

require_once XOOPS_MODULE_PATH . '/legacy/admin/forms/ThemeSelectForm.class.php';

/***
 * @internal
 * This action shows the list of selectable themes to user.
 *
 * [Notice]
 * XOOPS Cube Legacy can have many themes with different render-systems.
 * The render-system should not control how to change the themes.
 * Because this action can't list up themes of other render-systems.
 * The action to change themes should be in Legacy. And, each render-system
 * should send theme information through delegate-mechanism.
 * Therefore, this class is a test for that. We may move this action from
 * LegacyRender module. If you want to check the concept of this strategy, see
 * ThemeSelect preload in Legacy module.
 */
class Legacy_ThemeListAction extends Legacy_Action
{
    public $mThemes = null;
    public $mObjectHandler = null;
    public $mActionForm = null;
    public $mMainTheme = null;

    public function prepare(&$controller, &$xoopsUser)
    {
        $this->_setupObject();
        $this->_setupActionForm();

        $handler =& xoops_gethandler('config');

        $criteria =new CriteriaCompo();
        $criteria->add(new Criteria('conf_name', 'theme_set'));
        $criteria->add(new Criteria('conf_catid', XOOPS_CONF));

        $configs =& $handler->getConfigs($criteria);
        $this->mMainTheme = $configs[0]->get('conf_value');
    }

    public function _setupObject()
    {
        $handler =& xoops_getmodulehandler('theme');
        $this->mThemes =& $handler->getObjects();
    }

    public function _setupActionForm()
    {
        $this->mActionForm =new Legacy_ThemeSelectForm();
        $this->mActionForm->prepare();
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        $configHandler =& xoops_gethandler('config');

        $criteria =new CriteriaCompo();
        $criteria->add(new Criteria('conf_name', 'theme_set_allowed'));
        $criteria->add(new Criteria('conf_catid', XOOPS_CONF));

        $configs =& $configHandler->getConfigs($criteria);
        $selectedThemeArr = unserialize($configs[0]->get('conf_value'));

        $this->mActionForm->load($selectedThemeArr);

        return LEGACY_FRAME_VIEW_INDEX;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        $this->mActionForm->fetch();
        $this->mActionForm->validate();

        if ($this->mActionForm->hasError()) {
            return $this->getDefaultView($controller, $xoopsUser);
        }

        //
        // save selectable themes.
        //
        $configHandler =& xoops_gethandler('config');

        $criteria =new CriteriaCompo();
        $criteria->add(new Criteria('conf_name', 'theme_set_allowed'));
        $criteria->add(new Criteria('conf_catid', XOOPS_CONF));

        $configs =& $configHandler->getConfigs($criteria);
        $t_themeArr = $this->mActionForm->getSelectableTheme();
        $configs[0]->set('conf_value', serialize($t_themeArr));
        if (!$configHandler->insertConfig($configs[0])) {
            die(); // FIXME:
        }

        //
        // save selected theme.
        //
        $themeName = $this->mActionForm->getChooseTheme();

        if (null !== $themeName) {
            $criteria =new CriteriaCompo();
            $criteria->add(new Criteria('conf_name', 'theme_set'));
            $criteria->add(new Criteria('conf_catid', XOOPS_CONF));

            $configs =& $configHandler->getConfigs($criteria);

            $configs[0]->set('conf_value', $themeName);
            if ($configHandler->insertConfig($configs[0])) {
                $controller->mRoot->mContext->setThemeName($themeName);
                $this->mMainTheme = $themeName;
            }
        }

        XCube_DelegateUtils::call('Legacy.Event.ThemeSettingChanged', $this->mMainTheme, $t_themeArr);

        return $this->getDefaultView($controller, $xoopsUser);
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('theme_list.html');
        $render->setAttribute('themes', $this->mThemes);
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('currentThemeName', $this->mMainTheme);
    }
}
