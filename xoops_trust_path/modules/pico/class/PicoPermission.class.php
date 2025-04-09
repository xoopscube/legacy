<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

require_once dirname( __DIR__ ) . '/include/common_functions.php';

// singleton
class PicoPermission {
    // Define all possible permissions with defaults
    private const DEFAULT_PERMISSIONS = [
        'can_read' => false,
        'can_readfull' => false,
        'can_post' => false,
        'can_edit' => false,
        'can_delete' => false,
        'can_makesubcategory' => false,
        'post_auto_approved' => false,
        'is_moderator' => false
    ];

    public $db = null;  // Database instance
    public $uid = 0; // intval
    public $permissions = []; // [dirname][permission_id] or [dirname]['is_module_admin']
    protected $defaultPermissions;

    public function __construct() {
        global $xoopsUser;

        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->uid = is_object($xoopsUser) ? $xoopsUser->getVar('uid') : 0;
        $this->defaultPermissions = self::DEFAULT_PERMISSIONS;
    }

    public static function &getInstance(): \PicoPermission {
        static $instance;
        if ( ! isset( $instance ) ) {
            $instance = new PicoPermission();
        }

        return $instance;
    }

    public function getPermissions($mydirname) 
    {
        if (!isset($this->permissions[$mydirname])) {
            $permissions = $this->queryPermissions($mydirname) ?? [];
            
            // Initialize each category's permissions
            foreach ($permissions as $catId => &$catPerms) {
                if (!is_array($catPerms)) {
                    $catPerms = [];
                }
                $catPerms = $this->initializePermissions($catPerms);
            }
            unset($catPerms); // Break reference
            
            // Ensure root category exists with defaults
            if (!isset($permissions[0])) {
                $permissions[0] = self::DEFAULT_PERMISSIONS;
            }
            
            $this->permissions[$mydirname] = $permissions;
        }

        return $this->permissions[$mydirname];
    }

    /**
     * Initialize permissions with defaults
     */
    protected function initializePermissions(array $permissions): array 
    {
        // Ensure all default permissions exist
        $result = [];
        foreach (self::DEFAULT_PERMISSIONS as $key => $default) {
            $result[$key] = isset($permissions[$key]) ? (bool)$permissions[$key] : $default;
        }
        return $result;
    }

    /**
     * Get permission value safely
     */
    public function getPermissionValue(string $key, ?array $permissions = null): bool 
    {
        $permissions = $permissions ?? [];
        return !empty($permissions[$key] ?? self::DEFAULT_PERMISSIONS[$key] ?? false);
    }

    public function queryPermissions( $mydirname ): ?array {
        $user = null;
        $ret = [];

        if ( $this->uid > 0 ) {
            $user_handler = &xoops_gethandler( 'user' );
            $user         = &$user_handler->get( $this->uid );
        }

        $is_module_admin = false;
        if ( is_object( @$user ) ) {
            // is_module_admin
            $module_handler = &xoops_gethandler( 'module' );
            $moduleObj      = &$module_handler->getByDirname( $mydirname );
            if ( is_object( $moduleObj ) && $user->isAdmin( $moduleObj->getVar( 'mid' ) ) ) {
                $is_module_admin = true;
            }
        }

        if ( is_object( @$user ) ) {
            $groups = $user->getGroups();
            if ( ! empty( $groups ) ) {
                $whr = "`uid`=$this->uid || `groupid` IN (" . implode( ',', $groups ) . ')';
            } else {
                $whr = "`uid`=$this->uid";
            }
        } else {
            $whr = '`groupid`=' . (int) XOOPS_GROUP_ANONYMOUS;
        }

        $sql    = 'SELECT cat_id,permissions FROM ' . $this->db->prefix( $mydirname . '_category_permissions' ) . " WHERE ($whr)";
        $result = $this->db->query( $sql );
        if ( $result ) {
            while ( [$cat_id, $serialized_permissions] = $this->db->fetchRow( $result ) ) {
                $permissions = pico_common_unserialize( $serialized_permissions );
                if (!is_array($permissions)) {
                    $permissions = [];
                }
                // Initialize permissions with defaults
                $permissions = $this->initializePermissions($permissions);
                
                if (isset($ret[$cat_id]) && is_array($ret[$cat_id])) {
                    foreach ($permissions as $perm_name => $value) {
                        $ret[$cat_id][$perm_name] = $ret[$cat_id][$perm_name] ?? false;
                        $ret[$cat_id][$perm_name] |= $value;
                    }
                } else {
                    $ret[$cat_id] = $permissions;
                }
            }
        }

        if ( empty( $ret ) ) {
            return [ 0 => [], 'is_module_admin' => $is_module_admin ];
        }

        return $ret + [ 'is_module_admin' => $is_module_admin ];
    }

    public function getUidsFromCatid( $mydirname, $cat_id, $permission_type = '' ): array {
        // prepare $type
        $whr_type = $permission_type ? "permissions LIKE '%" . $permission_type . "\";i:1%'" : '1';

        // get permission_id
        $cat_id = (int) $cat_id;
        $sql    = 'SELECT cat_permission_id FROM ' . $this->db->prefix( $mydirname . '_categories' ) . " WHERE cat_id=$cat_id";
        [ $permission_id ] = $this->db->fetchRow( $this->db->query( $sql ) );

        // uid
        $uids   = [];
        $sql    = 'SELECT uid FROM ' . $this->db->prefix( $mydirname . '_category_permissions' ) . " WHERE cat_id=$permission_id AND uid IS NOT NULL AND ($whr_type)";
        $result = $this->db->query( $sql );
        while ( [$uid] = $this->db->fetchRow( $result ) ) {
            $uids[] = $uid;
        }

        // groupid * groups_users_link
        $sql    = 'SELECT distinct g.uid FROM ' . $this->db->prefix( $mydirname . '_category_permissions' ) . ' x , ' . $this->db->prefix( 'groups_users_link' ) . " g WHERE x.groupid=g.groupid AND x.cat_id=$permission_id AND x.groupid IS NOT NULL AND ($whr_type)";
        $result = $this->db->query( $sql );
        while ( [$uid] = $this->db->fetchRow( $result ) ) {
            $uids[] = $uid;
        }
        $uids = array_unique( $uids );

        return $uids;
    }

    public function validatePermissions(array $permissions): array {
        return array_merge($this->defaultPermissions, $permissions);
    }
}
