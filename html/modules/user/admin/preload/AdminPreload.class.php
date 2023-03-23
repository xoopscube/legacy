<?php
/**
 * @package user
 * @author     Nobuhiro YASUTOMI, PHP8
 * @version $Id: AdminPreload.class.php,v 1.1 2007/05/15 02:35:34 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class User_AdminPreload extends XCube_ActionFilter
{
    public function preBlockFilter()
    {
        $this->mRoot->mDelegateManager->add('Legacy.Event.ThemeSettingChanged', [$this, 'doThemeSettingChanged']);
    }
    
    public function doThemeSettingChanged($mainTheme, $selectableThemes)
    {
        $root = XCube_Root::getSingleton();
        $db = $root->mController->mDB;
        $table = $db->prefix('users');
        
        $mainTheme = $db->quoteString($mainTheme);
        
        $t_conds = [];
        $t_conds[] = 'theme <> ' . $db->quoteString('');
        foreach ($selectableThemes as $theme) {
            $t_conds[] = 'theme <> ' . $db->quoteString($theme);
        }
        
        $sql = "UPDATE {$table} SET theme={$mainTheme} WHERE " . implode(' AND ', $t_conds);

        $db->query($sql);
    }
}
