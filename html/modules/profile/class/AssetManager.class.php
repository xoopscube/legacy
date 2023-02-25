<?php
/**
 * @package    profile
 * @version    XCL 2.3.1
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     Kilica
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Profile_AssetManager
{
    public $mDirname = 'profile';
    public $mAssetList = [];
    public $_mCache = [];

    /**
     * @private
     */
    public function __construct()
    {
    }

    /**
     * @public
     */
    public static function &getSingleton()
    {
        static $instance;

        if (!is_object($instance)) {
            $instance = new Profile_AssetManager();
        }

        return $instance;
    }

    /**
     * @public
     * @param $type
     * @param $name
     * @return |null |null
     */
    public function &create($type, $name)
    {
        $instance = null;

        // TODO:Insert your creation code.

        // fallback
        if (null === $instance) {
            $instance =& $this->_fallbackCreate($type, $name);
        }

        $this->_mCache[$type][$name] =& $instance;

        return $instance;
    }

    /**
     * @private
     * @param $type
     * @param $name
     * @return |null |null
     */
    public function &_fallbackCreate($type, $name)
    {
        if (isset($this->mAssetList[$type][$name])) {
            $className = $this->mAssetList[$type][$name]['class'];
            if (isset($this->mAssetList[$type][$name]['absPath'])) {
                $filePath = $this->mAssetList[$type][$name]['absPath'];
            } else {
                $filePath = XOOPS_MODULE_PATH . '/' . $this->mDirname . '/' . $this->mAssetList[$type][$name]['path'];
            }

            $instance =& $this->_createInstance($className, $filePath);
        } else {
            switch ($type) {
                case 'filter':
                    $instance =& $this->_createFilter($name);
                    break;
                case 'form':
                    $instance =& $this->_createActionForm($name);
                    break;
                case 'handler':
                    $instance =& $this->_createHandler($name);
                    break;
            }
        }

        return $instance;
    }

    /**
     * @public
     * @param $type
     * @param $name
     * @return mixed|null
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
     * @param $name
     * @return
     */
    public function &_createHandler($name)
    {
        return xoops_getmodulehandler($name, $this->mDirname);
    }

    /**
     * @private
     * @param $name
     * @return null
     */
    public function &_createFilter($name)
    {
        $entity = $name;
        $isAdmin = false;
        $adminToken = '';

        if (preg_match("/^admin\.([a-z\_]+)$/i", $name, $matches)) {
            $entity = $matches[1];
            $isAdmin = true;
            $adminToken = 'Admin_';
        }

        $filePath = $this->_getBasePath($isAdmin) . '/forms/' . ucfirst($entity) . 'FilterForm.class.php';
        $className = ucfirst($this->mDirname) . "_${adminToken}" . ucfirst($entity) . 'FilterForm';

        $instance =& $this->_createInstance($className, $filePath);

        return $instance;
    }

    /**
     * @private
     * @param $name
     * @return null
     */
    public function &_createActionForm($name)
    {
        $mode = '';
        $entity = $name;
        $isAdmin = false;
        $adminToken = '';

        if (preg_match("/^admin\.([a-z\_]+)$/i", $name, $matches)) {
            $entity = $matches[1];
            $isAdmin = true;
            $adminToken = 'Admin_';
        }

        if (preg_match("/^([^\_]+)\_(.+)$/", $entity, $matches)) {
            $mode = $matches[1];
            $entity = $matches[2];
        }

        $className = ucfirst($this->mDirname) . "_${adminToken}" . ucfirst($entity) . ucfirst($mode) . 'Form';
        $filePath = $this->_getBasePath($isAdmin) . '/forms/' . ucfirst($entity) . ucfirst($mode) . 'Form.class.php';

        $instance =& $this->_createInstance($className, $filePath);

        return $instance;
    }

    /**
     * @private
     * @param $className
     * @param $filePath
     * @return null
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
     * @param bool $isAdmin
     * @return string
     */
    public function _getBasePath($isAdmin = false)
    {
        $filePath = XOOPS_MODULE_PATH . '/' . $this->mDirname;
        if ($isAdmin) {
            $filePath .= '/admin';
        }

        return $filePath;
    }
}
