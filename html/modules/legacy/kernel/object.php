<?php
/**
 *
 * @package Legacy
 * @version $Id: object.php,v 1.3 2008/09/25 15:12:02 kilica Exp $
 * @copyright (c) 2005-2022 XOOPS Cube Project
 * @license GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * This class implements the interface of XoopsObjectInterface, and it gives developers
 * 'TYPE SAFE' to avoid type errors trying to perform an operation on the wrong type of data.
 * The instance can have only five data type that are : BOOL, INT, FLOAT, STRING and TEXT.
 * You can not sanitize values by cleanVars() that is the function of XoopsObject.
 * However, all set functions give you 'TYPE SAFE'.
 * You should use this class with your favorite ActionForm.
 *
 * "Check values by actionform, set values to XoopsSimpleObject"
 *
 * This class was defined for the "extending life expectancy plan" of Xoops2.
 * It's not a rule you're forced to use. PHP supports the following data types:
 * String, Integer, Float, Boolean, Array, Object, NULL, Resource.
 */
class XoopsSimpleObject extends AbstractXoopsObject
{
    public $mVars = [];
    public $mIsNew = true;
    public $mDirname = null;

    public function __construct()
    {
    }

    public function setNew()
    {
        $this->mIsNew = true;
    }

    public function unsetNew()
    {
        $this->mIsNew = false;
    }

    public function isNew()
    {
        return $this->mIsNew;
    }

    public function initVar($key, $dataType, $value = null, $required = false, $size = null)
    {
        static $_mAllowType = [XOBJ_DTYPE_BOOL =>XOBJ_DTYPE_BOOL, XOBJ_DTYPE_INT =>XOBJ_DTYPE_INT, XOBJ_DTYPE_FLOAT =>XOBJ_DTYPE_FLOAT, XOBJ_DTYPE_STRING =>XOBJ_DTYPE_STRING, XOBJ_DTYPE_TEXT =>XOBJ_DTYPE_TEXT];

        if (!$_mAllowType[$dataType]) {
            die();    // TODO
        }

        $this->mVars[$key] = [
            'data_type' => $dataType,
            'value' => null,
            'required' => $required ? true : false,
            'maxlength' => $size ? (int)$size : null
        ];

        $this->assignVar($key, $value);
    }

    public function assignVar($key, $value)
    {
        $vars = &$this->mVars[$key];
        if (!isset($vars)) {
            return;
        }

        switch ($vars['data_type']) {
            case XOBJ_DTYPE_BOOL:
                $vars['value'] = $value ? 1 : 0;
                return;

            case XOBJ_DTYPE_INT:
                $vars['value'] = null !== $value ? (int)$value : null;
                return;

            case XOBJ_DTYPE_FLOAT:
                $vars['value'] = null !== $value ? (float)$value : null;
                return;

            case XOBJ_DTYPE_STRING:
                $len = $vars['maxlength'];
                $vars['value'] = (null !== $len && strlen($value) > $len) ? xoops_substr($value, 0, $len, null) : $value;
                return;

            case XOBJ_DTYPE_TEXT:
                $vars['value'] = $value;
                return;
        }
    }

    public function assignVars($values)
    {
        foreach ($values as $key => $value) {
            $this->assignVar($key, $value);
        }
    }

    public function set($key, $value)
    {
        $this->assignVar($key, $value);
    }

    public function get($key)
    {
        return $this->mVars[$key]['value'];
    }

    public function gets()
    {
        $ret = [];

        foreach ($this->mVars as $key => $value) {
            $ret[$key] = $value['value'];
        }

        return $ret;
    }

    public function setVar($key, $value)
    {
        $this->assignVar($key, $value);
    }

    public function setVars($values)
    {
        $this->assignVars($values);
    }

    /**
     * @param $key
     * @return string|null
     * @deprecated
     */
    public function getVar($key)
    {
        return $this->getShow($key);
    }

    /**
     * Return HTML strings for displaying only by HTML.
     * The second parameter doesn't exist.
     * @param $key
     * @return string|null
     */
    public function getShow($key)
    {
        $value = null;
        $vars = $this->mVars[$key];

        switch ($vars['data_type']) {
            case XOBJ_DTYPE_BOOL:
            case XOBJ_DTYPE_INT:
            case XOBJ_DTYPE_FLOAT:
                return $vars['value'];

            case XOBJ_DTYPE_STRING:
                $root =& XCube_Root::getSingleton();
                $textFilter =& $root->getTextFilter();
                return $textFilter->toShow($vars['value']);

            case XOBJ_DTYPE_TEXT:
                $root =& XCube_Root::getSingleton();
                $textFilter =& $root->getTextFilter();
                return $textFilter->toShowTarea($vars['value'], 0, 1, 1, 1, 1);
        }

        return $value;
    }

    public function getTypeInformations()
    {
        $ret = [];
        foreach (array_keys($this->mVars) as $key) {
            $ret[$key] = $this->mVars[$key]['data_type'];
        }

        return $ret;
    }

    /**
     * getPurifiedHtml
     *
     * @param string      $key
     * @param string|null $encoding
     * @param string|null $doctype
     *
     * @return	string
    **/
// TODO version 2.3.0
//     public function getPurifiedHtml(/*** string ***/ $key, /*** string ***/ $encoding=null, /*** string ***/ $doctype=null)

    public function getPurifiedHtml( string $key, string $encoding=null, string $doctype=null)
    {
        $root = XCube_Root::getSingleton();
        $textFilter = $root->getTextFilter();
        return $textFilter->purifyHtml($this->get($key), $encoding, $doctype);
    }

    /**
     * getDirname
     *
     * @param	void
     *
     * @return	string
    **/
    public function getDirname()
    {
        return $this->mDirname;
    }
}
