<?php
/**
 * @package user
 * @version $Id: AdminPreload.class.php,v 1.1 2007/05/15 02:35:34 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class User_AdminPreload extends XCube_ActionFilter
{
    public function preBlockFilter()
    {
        $this->mRoot->mDelegateManager->add('Legacy.Event.ThemeSettingChanged', array($this, 'doThemeSettingChanged'));
        // check pass colmun length of users table
        $db = $this->mRoot->mController->mDB;
        $sql = 'SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'.XOOPS_DB_NAME.'" AND TABLE_NAME="'.$db->prefix('users').'" AND COLUMN_NAME="pass"';
        if ($res = $db->query($sql)) {
            $type = $db->fetchRow($res);
            if (preg_replace('/[^0-9]/', '', $type[0]) < 255) {
                $sql = 'ALTER TABLE '.$db->prefix('users').' CHANGE `pass` `pass` VARCHAR(255) NOT NULL DEFAULT ""';
                $db->queryF($sql);
            }
        }
    }
    
    public function doThemeSettingChanged($mainTheme, $selectableThemes)
    {
        $root = XCube_Root::getSingleton();
        $db = $root->mController->mDB;
        $table = $db->prefix('users');
        
        $mainTheme = $db->quoteString($mainTheme);
        
        $t_conds = array();
        $t_conds[] = "theme <> " . $db->quoteString('');
        foreach ($selectableThemes as $theme) {
            $t_conds[] = "theme <> " . $db->quoteString($theme);
        }
        
        $sql = "UPDATE ${table} SET theme=${mainTheme} WHERE " . join(' AND ', $t_conds);

        $db->query($sql);
    }
}
