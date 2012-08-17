<?php
    /**
     *
     * @package Legacy
     * @version $Id: InstallerChecker.class.php,v 1.5 2012/08/11 09:32:20 yoshis Exp $
     * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/>
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
    // Remove install folder.
    // Returns TRUE on success or FALSE on failure.
    protected function _rglob($pattern = '*', $flags = 0, $path = '')
    {
        $paths = glob($path . '*', GLOB_MARK | GLOB_ONLYDIR | GLOB_NOSORT);
        $files = glob($path . $pattern, $flags);
        foreach ($paths as $path) {
            $files = array_merge($files, $this->_rglob($pattern, $flags, $path));
        }
        return $files;
    }

    private function remove_install_folder($folderName)
    {
        if (is_dir($folderName) || is_writable($folderName)) {
            $folders = array($folderName);
            $files = $this->_rglob("*", 0, $folderName);
            // delete files
            foreach ($files as $path) {
                if (dirname($path) != XOOPS_ROOT_PATH) {
                    if (!in_array(dirname($path), $folders)) {
                        $folders[] = dirname($path);
                    }
                }
                if (!is_dir($path) && is_writable($path)) {
                    unlink($path);
                }
            }
            // delete folders
            for ($i = count($folders) - 1; $i >= 0; $i--) {
                if (is_dir($folders[$i])) {
                    if (!is_writable($folders[$i])) {
                        chmod($folders[$i], 0777);
                    }
                    rmdir($folders[$i]);
                }
            }
        }
    }

    private function rename_install_folder($folderName)
    {
        if (is_dir($folderName)) {
            rename($folderName, $folderName . mt_rand());
        }
    }

    function preBlockFilter()
    {
        if (LEGACY_INSTALLERCHECKER_ACTIVE == true && is_dir(XOOPS_ROOT_PATH . "/install")) {
            $this->remove_install_folder(XOOPS_ROOT_PATH . "/install");
            $this->rename_install_folder(XOOPS_ROOT_PATH . "/install");
            if (is_dir(XOOPS_ROOT_PATH . "/install")) {
                $root =& XCube_Root::getSingleton();
                $root->mLanguageManager->loadModuleMessageCatalog('legacy');
                $xoopsConfig = $root->mContext->mXoopsConfig;
                require_once XOOPS_ROOT_PATH . '/class/template.php';
                $xoopsTpl = new XoopsTpl();
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
}