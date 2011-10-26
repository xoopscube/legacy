<?php
/**
 *
 * @package Legacy
 * @version $Id: InstallerChecker.class.php,v 1.4 2008/09/25 15:12:43 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

if (!defined("LEGACY_INSTALLERCHECKER_ACTIVE"))
	define("LEGACY_INSTALLERCHECKER_ACTIVE", true);

/**
 * This filter checks whether the install-wizard directory is removed.
 * If it is not removed yet, this filter warns to remove the install-wizard
 * directory.
 */
class Legacy_InstallerChecker extends XCube_ActionFilter
{
    function preBlockFilter()
    {
    	if (LEGACY_INSTALLERCHECKER_ACTIVE == true && is_dir(XOOPS_ROOT_PATH . "/install"))
    	{
    		$root =& XCube_Root::getSingleton();
    		$root->mLanguageManager->loadModuleMessageCatalog('legacy');
    		$xoopsConfig = $root->mContext->mXoopsConfig;
    		
			require_once XOOPS_ROOT_PATH . '/class/template.php';
			$xoopsTpl =new XoopsTpl();
			$xoopsTpl->assign(array('xoops_sitename' => htmlspecialchars($xoopsConfig['sitename']),
									   'xoops_themecss' => xoops_getcss(),
									   'xoops_imageurl' => XOOPS_THEME_URL . '/' . $xoopsConfig['theme_set'] . '/',
									   'lang_message_confirm' => XCube_Utils::formatMessage(_MD_LEGACY_MESSAGE_INSTALL_COMPLETE_CONFIRM, XOOPS_ROOT_PATH . "/install"),
                                       'lang_message_warning' => XCube_Utils::formatMessage(_MD_LEGACY_MESSAGE_INSTALL_COMPLETE_WARNING, XOOPS_ROOT_PATH . "/install")
									   ));
									   
			$xoopsTpl->compile_check = true;
			
			// @todo filebase template with absolute file path
			$xoopsTpl->display(XOOPS_ROOT_PATH . '/modules/legacy/templates/legacy_install_completed.html');
			exit();
    	}
    }
}

?>
