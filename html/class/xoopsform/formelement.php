<?php
/**
 * Abstract base class for form elements
 * @package    kernel
 * @subpackage form
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

class XoopsFormElement
{

    /**#@+
     * @access private
     */
    /**
     * "name" attribute of the element
     * @var string
     */
    public $_name;

    /**
     * caption of the element
     * @var	string
     */
    public $_caption;

    /**
     * Accesskey for this element
     * @var	string
     */
    public $_accesskey = '';

    /**
     * HTML class for this element
     * @var	string
     */
    public $_class = '';

    /**
     * hidden?
     * @var	bool
     */
    public $_hidden = false;

    /**
     * extra attributes to go in the tag
     * @var	string
     */
    public $_extra = '';

    /**
     * required field?
     * @var	bool
     */
    public $_required = false;

    /**
     * description of the field
     * @var	string
     */
    public $_description = '';

    /**
     * specified id for the form.
     * @var string
     */
    public $_id = null;
    /**#@-*/


    /**
     * constructor
     *
     */
    public function __construct()
    {
        exit('This class cannot be instantiated!');
    }
    public function XoopsFormElement()
    {
        return self::__construct();
    }

    /**
     * Is this element a container of other elements?
     *
     * @return	bool false
     */
    public function isContainer()
    {
        return false;
    }

    /**
     * set the "name" attribute for the element
     *
     * @param	string  $name   "name" attribute for the element
     */
    public function setName($name)
    {
        $this->_name = trim($name);
    }

    /**
     * get the "name" attribute for the element
     *
     * @param	bool    encode?
     * @return	string  "name" attribute
     */
    public function getName($encode=true)
    {
        if (false != $encode) {
            return str_replace(array("'", '&amp;'), array('&#039;', '&'), htmlspecialchars($this->_name));
        }
        return $this->_name;
    }

    /**
     * Set $id to member property.
     * @param string $id
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * get the "id" attribute for the element. If ID is empty, return getName().
     */
    public function getId()
    {
        return null != $this->_id ? $this->_id : $this->getName();
    }

    /**
     * set the "accesskey" attribute for the element
     *
     * @param	string  $key   "accesskey" attribute for the element
     */
    public function setAccessKey($key)
    {
        $this->_accesskey = trim($key);
    }
    /**
     * get the "accesskey" attribute for the element
     *
     * @return 	string  "accesskey" attribute value
     */
    public function getAccessKey()
    {
        return $this->_accesskey;
    }
    /**
     * If the accesskey is found in the specified string, underlines it
     *
     * @param	string  $str   String where to search the accesskey occurence
     * @return 	string  Enhanced string with the 1st occurence of accesskey underlined
     */
    public function getAccessString($str)
    {
        $access = $this->getAccessKey();
        if (!empty($access) && (false !== ($pos = strpos($str, $access)))) {
            return substr($str, 0, $pos) . '<span style="text-decoration:underline">' . $str[$pos] . '</span>' . substr($str, $pos+1);
        }
        return $str;
    }

    /**
     * set the "class" attribute for the element
     *
     * @param $class
     */
    public function setClass($class)
    {
        $class = trim($class);
        if (empty($class)) {
            $this->_class = '';
        } else {
            $this->_class .= (empty($this->_class) ? '' : ' ') . $class;
        }
    }
    /**
     * get the "class" attribute for the element
     *
     * @return 	string  "class" attribute value
     */
    public function getClass()
    {
        return $this->_class;
    }

    /**
     * set the caption for the element
     *
     * @param	string  $caption
     */
    public function setCaption($caption)
    {
        $this->_caption = trim($caption);
    }

    /**
     * get the caption for the element
     *
     * @return	string
     */
    public function getCaption()
    {
        return $this->_caption;
    }

    /**
     * set the element's description
     *
     * @param	string  $description
     */
    public function setDescription($description)
    {
        $this->_description = trim($description);
    }

    /**
     * get the element's description
     *
     * @return	string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * flag the element as "hidden"
     *
     */
    public function setHidden()
    {
        $this->_hidden = true;
    }

    /**
     * Find out if an element is "hidden".
     *
     * @return	bool
     */
    public function isHidden()
    {
        return $this->_hidden;
    }

    public function isBreak()
    {
        return false;
    }

    /**
     * Add extra attributes to the element.
     *
     * This string will be inserted verbatim and unvalidated in the
     * element's tag. Know what you are doing!
     *
     * @param string $extra
     * @param bool   $replace If true, passed string will replace current content otherwise it will be appended to it
     * @return    string New content of the extra string
     */
    public function setExtra($extra, $replace = false)
    {
        if ($replace) {
            $this->_extra = ' ' . trim($extra);
        } else {
            $this->_extra .= ' ' . trim($extra);
        }
        return $this->_extra;
    }

    /**
     * Get the extra attributes for the element
     *
     * @return	string
     */
    public function getExtra()
    {
        if (isset($this->_extra)) {
            return $this->_extra;
        }
    }

    /**
     * Generates output for the element.
     *
     * This method is abstract and must be overwritten by the child classes.
     * @abstract
     */
    public function render()
    {
    }

    /**
     * Gets a message for javascript in the specific member function of XoopsForm class.
     */
    public function getMessageForJS()
    {
        $eltcaption = trim($this->getCaption());
        $eltmsg = empty($eltcaption) ? sprintf(_FORM_ENTER, $eltname) : sprintf(_FORM_ENTER, $eltcaption);
        $eltmsg = str_replace('"', '\"', stripslashes($eltmsg));

        return $eltmsg;
    }
}
