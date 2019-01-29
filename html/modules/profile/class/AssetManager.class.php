<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Profile_AssetManager
{
    public $mDirname = "profile";
    public $mAssetList = array();
    public $_mCache = array();

    /**
     * @private
     */
    // !Fix deprecated constructor for PHP 7.x
    public function __construct()
    //public function Profile_AssetManager()
    {
    }

    /**
     * @public
     */
    // !Fix non-static function called statically in file /modules/profile/preload/AssetPreload.class.php line 43
    public static function &getSingleton()
    // public function &getSingleton()
    {
        static $instance;
    
        if (!is_object($instance)) {
            $instance = new Profile_AssetManager();
        }
    
        return $instance;
    }

    /**
     * @public
     */
    public function &create($type, $name)
    {
        $instance = null;
    
        // TODO:Insert your creation code.

        // fallback
        if ($instance === null) {
            $instance =& $this->_fallbackCreate($type, $name);
        }
    
        $this->_mCache[$type][$name] =& $instance;
    
        return $instance;
    }

    /**
     * @private
     */
    public function &_fallbackCreate($type, $name)
    {
        if (isset($this->mAssetList[$type][$name])) {
            $className = $this->mAssetList[$type][$name]['class'];
            if (isset($this->mAssetList[$type][$name]['absPath'])) {
                $filePath = $this->mAssetList[$type][$name]['absPath'];
            } else {
                $filePath = XOOPS_MODULE_PATH . "/" . $this->mDirname . "/" . $this->mAssetList[$type][$name]['path'];
            }
            
            $instance =& $this->_createInstance($className, $filePath);
        } else {
            switch ($type) {
                case "filter":
                    $instance =& $this->_createFilter($name);
                    break;
                case "form":
                    $instance =& $this->_createActionForm($name);
                    break;
                case "handler":
                    $instance =& $this->_createHandler($name);
                    break;
            }
        }
    
        return $instance;
    }

    /**
     * @public
     */
    public function &load($type, $name)
    {
        if (isset($this->_mCache[$type][$name])) {
            return $this->_mCache[$type][$name];
        }
    
        return $this->create($type, $name);
    }

    /**
     * @private
     */
    public function &_createHandler($name)
    {
        return xoops_getmodulehandler($name, $this->mDirname);
    }

    /**
     * @private
     */
    public function &_createFilter($name)
    {
        $entity = $name;
        $isAdmin = false;
        $adminToken = "";
    
        if (preg_match("/^admin\.([a-z\_]+)$/i", $name, $matches)) {
            $entity = $matches[1];
            $isAdmin = true;
            $adminToken = "Admin_";
        }
    
        $filePath = $this->_getBasePath($isAdmin) . "/forms/" . ucfirst($entity) . "FilterForm.class.php";
        $className = ucfirst($this->mDirname) . "_${adminToken}" . ucfirst($entity) . "FilterForm";
    
        $instance =& $this->_createInstance($className, $filePath);
    
        return $instance;
    }

    /**
     * @private
     */
    public function &_createActionForm($name)
    {
        $mode = "";
        $entity = $name;
        $isAdmin = false;
        $adminToken = "";
    
        if (preg_match("/^admin\.([a-z\_]+)$/i", $name, $matches)) {
            $entity = $matches[1];
            $isAdmin = true;
            $adminToken = "Admin_";
        }
    
        if (preg_match("/^([^\_]+)\_(.+)$/", $entity, $matches)) {
            $mode = $matches[1];
            $entity = $matches[2];
        }
    
        $className = ucfirst($this->mDirname) . "_${adminToken}" . ucfirst($entity) . ucfirst($mode) . "Form";
        $filePath = $this->_getBasePath($isAdmin) . "/forms/" . ucfirst($entity) . ucfirst($mode) . "Form.class.php";
    
        $instance =& $this->_createInstance($className, $filePath);
    
        return $instance;
    }

    /**
     * @private
     */
    public function &_createInstance($className, $filePath)
    {
        $instance = null;
    
        if (class_exists($className)) {
            $instance =new $className();
            return $instance;
        }
    
        if (!file_exists($filePath)) {
            return $instance;
        }
    
        require_once $filePath;
    
        if (class_exists($className)) {
            $instance =new $className();
        }
    
        return $instance;
    }

    /**
     * @private
     */
    public function _getBasePath($isAdmin = false)
    {
        $filePath = XOOPS_MODULE_PATH . "/" . $this->mDirname;
        if ($isAdmin) {
            $filePath .= "/admin";
        }
    
        return $filePath;
    }
}
