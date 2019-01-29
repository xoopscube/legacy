<?php
/**
 *
 * @package Legacy
 * @version $Id: group_permission.php,v 1.3 2008/09/25 15:11:29 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class LegacyGroup_permissionObject extends XoopsSimpleObject
{
    public function LegacyGroup_permissionObject()
    {
        self::__construct();
    }

    public function __construct()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->mVars = $initVars;
            return;
        }
        $this->initVar('gperm_id', XOBJ_DTYPE_INT, '', true);
        $this->initVar('gperm_groupid', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('gperm_itemid', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('gperm_modid', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('gperm_name', XOBJ_DTYPE_STRING, '', true, 50);
        $initVars=$this->mVars;
    }
}

class LegacyGroup_permissionHandler extends XoopsObjectGenericHandler
{
    public $mTable = "group_permission";
    public $mPrimary = "gperm_id";
    public $mClass = "LegacyGroup_permissionObject";
    
    /**
     * Gets array of roles by array of group ID.
     * @param int $mid
     * @param array $groups
     * @return array
     */
    public function getRolesByModule($mid, $groups)
    {
        $retRoles = array();
        
        $sql = "SELECT gperm_name FROM " . $this->mTable . " WHERE gperm_modid=" . intval($mid) . " AND gperm_itemid=0 AND ";
        $groupSql = array();
        
        foreach ($groups as $gid) {
            $groupSql[] = "gperm_groupid=" . intval($gid);
        }
        
        $sql .= "(" . implode(' OR ', $groupSql) . ")";
        
        $result = $this->db->query($sql);
        
        if (!$result) {
            return $retRoles;
        }
        
        while ($row = $this->db->fetchArray($result)) {
            $retRoles[] = $row['gperm_name'];
        }
        
        return $retRoles;
    }
}
