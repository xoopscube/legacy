<?php
/**
 *
 * @package Legacy
 * @version $Id: ThemeSelect.class.php,v 1.3 2008/09/25 15:12:43 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * Theme select mechanism is that base knows the method to change themes
 * without RenderSystem. So this class uses delegate to check whether the
 * specified theme is selectable. Functions should be added to this delegate in
 * constructor, because the delegate is called in preBlockFilter().
 */
class Legacy_ThemeSelect extends XCube_ActionFilter
{
    /**
     * @var XCube_Delegate
     */
    public $mIsSelectableTheme = null;
    
    public function Legacy_ThemeSelect(&$controller)
    {
        self::__construct($controller);
    }

    public function __construct(&$controller)
    {
        //
        // TODO remove
        //
        parent::__construct($controller);
        $this->mIsSelectableTheme =new XCube_Delegate();
        $this->mIsSelectableTheme->register('Legacy_ThemeSelect.IsSelectableTheme');
        
        $controller->mSetupUser->add(array(&$this, 'doChangeTheme'));
    }
    
    public function preBlockFilter()
    {
        $this->mController->mRoot->mDelegateManager->add("Site.CheckLogin.Success", array(&$this, "callbackCheckLoginSuccess"));
    }
    
    /**
     * Because this process needs sessions, this functions is added to
     * SiteLogin event.
     * 
     * @param XoopsUser $xoopsUser Must parameter, because this is added to login event.
     */
    public function doChangeTheme(&$principal, &$controller, &$context)
    {
        if (!empty($_POST['xoops_theme_select'])) {
            $xoops_theme_select = explode('!-!', $_POST['xoops_theme_select']);
            if ($this->_isSelectableTheme($xoops_theme_select[0])) {
                $this->mRoot->mContext->setThemeName($xoops_theme_select[0]);
                $_SESSION['xoopsUserTheme'] = $xoops_theme_select[0];
                $controller->executeForward($GLOBALS['xoopsRequestUri']);
            }
        } elseif (!empty($_SESSION['xoopsUserTheme']) && $this->_isSelectableTheme($_SESSION['xoopsUserTheme'])) {
            $this->mRoot->mContext->setThemeName($_SESSION['xoopsUserTheme']);
        }
    }

    public function callbackCheckLoginSuccess(&$xoopsUser)
    {
        //
        // Check Theme and set it to session.
        //
        $userTheme = $xoopsUser->get('theme');
        if (in_array($userTheme, $this->mRoot->mContext->getXoopsConfig('theme_set_allowed'))) {
            $_SESSION['xoopsUserTheme'] = $userTheme;
            $this->mRoot->mContext->setThemeName($userTheme);
        }
    }
    
    public function _isSelectableTheme($theme_name)
    {
        return in_array($theme_name, $this->mRoot->mContext->getXoopsConfig('theme_set_allowed'));
    }
}
