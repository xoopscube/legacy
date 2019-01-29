<?php
/**
 * @package legacyRender
 * @version $Id: ThemeSelect.class.php,v 1.1 2007/05/15 02:35:28 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class LegacyRender_ThemeSelect extends XCube_ActionFilter
{
    public function LegacyRender_ThemeSelect(&$controller)
    {
        self::__construct($controller);
    }

    public function __construct(&$controller)
    {
        parent::__construct($controller);
        $controller->mRoot->mDelegateManager->add('Legacy_ThemeSelect.IsSelectableTheme', 'LegacyRender_ThemeSelect::isSelectableTheme');
        $controller->mRoot->mDelegateManager->add('LegacyThemeHandler.GetInstalledThemes', 'LegacyRender_DelegateFunctions::getInstalledThemes', XOOPS_ROOT_PATH . "/modules/legacyRender/kernel/DelegateFunctions.class.php");
    }
    
    public function isSelectableTheme(&$flag, $theme_name)
    {
        $handler =& xoops_getmodulehandler('theme', 'legacyRender');
        $themeArr =& $handler->getObjects(new Criteria('name', $theme_name));
        
        if (count($themeArr) == 1 && $themeArr[0]->get('enable_select')) {
            $flag = true;
        }
    }
}
