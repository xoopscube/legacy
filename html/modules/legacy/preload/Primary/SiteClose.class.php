<?php
/**
 *
 * @package Legacy
 * @version $Id: SiteClose.class.php,v 1.5 2008/09/25 15:12:38 kilica Exp $
 * @copyright Copyright 2005-2023 XOOPS Cube Project  <https://github.com/xoopscube/>
 * @license   GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/***
 * The action filter for the site close procedure.
 */
class Legacy_SiteClose extends XCube_ActionFilter
{
    public function preBlockFilter()
    {
        if (1 == $this->mRoot->mContext->getXoopsConfig('closesite')) {

            $this->mController->mSetupUser->add('Legacy_SiteClose::callbackSetupUser', XCUBE_DELEGATE_PRIORITY_FINAL);
            $this->mRoot->mDelegateManager->add('Site.CheckLogin.Success', [&$this, 'callbackCheckLoginSuccess']);
        }
    }

    /**
     * Checks whether the site is closed now, and whether all required modules
     * have been installed. This function is called through delegates.
     * @param $principal
     * @param $controller
     * @param $context
     * @see preBlockFilter()
     */
    public static function callbackSetupUser(&$principal, &$controller, &$context)
    {
        $accessAllowFlag = false;
        $xoopsConfig = $controller->mRoot->mContext->getXoopsConfig();

        if (!empty($_POST['xoops_login'])) {
            $controller->checkLogin();
            return;
        } elseif ('logout' == @$_GET['op']) { // GIJ
            $controller->logout();
            return;
        } elseif (is_object($context->mXoopsUser)) {
            foreach ($context->mXoopsUser->getGroups() as $group) {
                if (in_array($group, $xoopsConfig['closesite_okgrp'], true) || XOOPS_GROUP_ADMIN == $group) {
                    $accessAllowFlag = true;
                    break;
                }
            }
        }

        if (!$accessAllowFlag) {
            require_once XOOPS_ROOT_PATH . '/class/template.php';
            $xoopsTpl =new XoopsTpl();
            $xoopsTpl->assign(
                [
                    'xoops_sitename'    => htmlspecialchars($xoopsConfig['sitename']),
                    'xoops_isuser'      => is_object($context->mXoopsUser), //GIJ
                    'xoops_themecss'    => xoops_getcss(),
                    'xoops_imageurl'    => XOOPS_THEME_URL . '/' . $xoopsConfig['theme_set'] . '/',
                    'theme_css'         => getcss(),
                    'theme_url'         => XOOPS_THEME_URL . '/' . $xoopsConfig['theme_set'],
                    'lang_login'        => _LOGIN,
                    'lang_username'     => _USERNAME,
                    'lang_password'     => _PASSWORD,
                    'lang_siteclosemsg' => $xoopsConfig['closesite_text']
                ]
            );

            $xoopsTpl->compile_check = true;
            $xclTplClose = XOOPS_ROOT_PATH . '/themes/' . $xoopsConfig['theme_set'] . '/templates/legacy/legacy_site_closed.html';
            // Theme filebase template with absolute file path
            if (file_exists($xclTplClose)) {
                $xoopsTpl->display($xclTplClose);
            }
		    else {
                $xoopsTpl->display(XOOPS_ROOT_PATH . '/modules/legacy/templates/legacy_site_closed.html');
                }

            exit();
        }
    }

    /**
     * When the user logs in successfully, checks whether the user belongs to
     * the special group which is allowed to log in. This function is called
     * through delegates.
     * @var XoopsUser &$xoopsUser
     * @see preBlockFilter
     */
    public function callbackCheckLoginSuccess(&$xoopsUser)
    {
        //
        // This check is not needed. :)
        //
        if (!is_object($xoopsUser)) {
            return;
        }

        // Site close
        if ($this->mRoot->mContext->getXoopsConfig('closesite')) {
            $accessAllowed = false;

            foreach ($xoopsUser->getGroups() as $group) {
                if (in_array($group, $this->mRoot->mContext->getXoopsConfig('closesite_okgrp'), true) || (XOOPS_GROUP_ADMIN === $group)) {
                    $accessAllowed = true;
                    break;
                }
            }

            if (!$accessAllowed) {
                $this->mController->executeRedirect(XOOPS_URL . '/', 1, _NOPERM);
            }
        }
    }
}
