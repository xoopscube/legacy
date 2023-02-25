<?php
/**
 *
 * @package Legacy
 * @version $Id: theme.php,v 1.4 2008/09/25 15:11:21 kilica Exp $
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/include/comment_constants.php';

class LegacyThemeObject extends XoopsSimpleObject
{
    public function LegacyThemeObject()
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
        $this->initVar('name', XOBJ_DTYPE_STRING, '', true, 191);
        $this->initVar('dirname', XOBJ_DTYPE_STRING, '', true, 191);
        $this->initVar('screenshot', XOBJ_DTYPE_STRING, '', false, 191);
        $this->initVar('description', XOBJ_DTYPE_STRING, '', false, 191);
        $this->initVar('format', XOBJ_DTYPE_STRING, '', true, 191);
        $this->initVar('render_system', XOBJ_DTYPE_STRING, '', true, 191);
        $this->initVar('themeoptions', XOBJ_DTYPE_STRING, '', true, 191);
        $this->initVar('version', XOBJ_DTYPE_STRING, '', true, 32);
        $this->initVar('author', XOBJ_DTYPE_STRING, '', true, 64);
        $this->initVar('url', XOBJ_DTYPE_STRING, '', true, 191);
        $this->initVar('license', XOBJ_DTYPE_STRING, '', true, 191);

        // For TYPO
        $this->initVar('licence', XOBJ_DTYPE_STRING, '', true, 191);
        $initVars = $this->mVars;
    }
}

class LegacyThemeHandler extends XoopsObjectHandler
{
    public $_mResults = [];

    /**
     * @var XCube_Delegate
     */
    public $mGetInstalledThemes = null;

    public function LegacyThemeHandler(&$db)
    {
        self::__construct($db);
    }

    public function __construct(&$db)
    {
        $this->mGetInstalledThemes =new XCube_Delegate();
        $this->mGetInstalledThemes->register('LegacyThemeHandler.GetInstalledThemes');
    }

    public function &create()
    {
        $to = new LegacyThemeObject();
        return $to;
    }

    public function &get($name)
    {
        $ret = null;
        $this->_makeCache();

        foreach (array_keys($this->_mResults) as $key) {
            if ($this->_mResults[$key]->get('dirname') == $name) {
                return $this->_mResults[$key];
            }
        }

        return $ret;
    }

    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $this->_makeCache();
        return $this->_mResults;
    }

    /**
     * Create cache at $this->mResult by Delegate, if cache is empty.
     */
    public function _makeCache()
    {
        if (0 === count($this->_mResults)) {
            $t_themeArr = [];
            $this->mGetInstalledThemes->call(new XCube_Ref($t_themeArr));

            foreach ($t_themeArr as $theme) {
                $obj =& $this->create();
                $obj->assignVars(
                    [
                        'name'          => $theme->mName,
                        'dirname'       => $theme->mDirname,
                        'screenshot'    => $theme->mScreenShot,
                        'description'   => $theme->mDescription,
                        'format'        => $theme->mFormat,
                        'render_system' => $theme->mRenderSystemName,
                        'themeoptions'  => $theme->mThemeOptions,
                        'version'       => $theme->mVersion,
                        'author'        => $theme->mAuthor,
                        'url'           => $theme->mUrl,
                        'license'       => $theme->mLicence
                    ]
                );
                $this->_mResults[] =& $obj;
                unset($obj);
            }
        }
    }

    public function insert(&$obj, $force = false)
    {
        return false;
    }

    public function delete(&$obj, $force = false)
    {
        return false;
    }
}
