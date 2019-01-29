<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_Property.class.php,v 1.7 2008/10/12 04:30:27 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/bsd_licenses.txt Modified BSD license
 *
 */

/**
 * @public
 * @brief [Abstract] Defines a interface for the property class group.
 * 
 * XCube_PropertyInterface is designed to work in XCube_ActionForm or XCube_Service (in the near future).
 * Therefore only sub-classes of them should call constructors of XCube_Property classes.
 */
class XCube_PropertyInterface
{
    /**
     * @public
     * @brief Constructor.
     * @param $name string - A name of this property.
     */
    // !Fix PHP7
    public function __construct($name)
    //public function XCube_PropertyInterface($name)
    {
    }

    /**
     * @public
     * @brief [Abstract] Sets $value as raw value to this property. And the value is casted by the property's type'.
     * @param $value mixed
     */
    public function set($value)
    {
    }
    
    /**
     * @public
     * @brief [Abstract] Gets the value of this property.
     * @return mixed
     */
    public function get()
    {
    }

    /**
     * @deprecated
     */
    public function setValue($arg0 = null, $arg1 = null)
    {
        $this->set($arg0, $arg1);
    }
    
    /**
     * @deprecated
     */
    public function getValue($arg0 = null)
    {
        return $this->get($arg0);
    }
    
    /**
     * @public
     * @brief [Abstract] Gets a value indicating whether this object expresses Array.
     * @return bool
     */
    public function isArray()
    {
    }
    
    /**
     * @public
     * @brief [Abstract] Gets a value indicating whether this object is null.
     * @return bool
     */
    public function isNull()
    {
    }
    
    /**
     * @public
     * @brief [Abstract] Gets a value as integer.
     * @return int
     */
    public function toNumber()
    {
    }
    
    /**
     * @public
     * @brief [Abstract] Gets a value as string.
     * @return string
     */
    public function toString()
    {
    }

    /**
     * @public
     * @brief [Abstract] Gets a value as encoded HTML code.
     * @return string - HTML
     * @deprecated
     */
    public function toHTML()
    {
    }
    
    /**
     * @public
     * @brief [Abstract] Gets a value indicating whether this object has a fetch control.
     * @return bool
     */
    public function hasFetchControl()
    {
    }

    /**
     * @public [Abstract] Fetches values.
     * @param $form XCube_ActionForm
     * @return void
     */
    public function fetch(&$form)
    {
    }
}

/**
 * @public
 * @brief [Abstract] The base class which implements XCube_PropertyInterface, for all properties.
 */
class XCube_AbstractProperty extends XCube_PropertyInterface
{
    /**
     * @protected
     * @brief string
     */
    public $mName = null;
    
    /**
     * @protected
     * @brief string
     */
    public $mValue = null;

    /**
     * @public
     * @brief Constructor.
     * @param $name string - A name of this property.
     */
    // !Fix PHP7
    public function __construct($name)
    //public function XCube_AbstractProperty($name)
    {
        // ! call parent::__construct() instead of parent::Controller()
        parent::__construct($name);
        //parent::XCube_PropertyInterface($name);
        $this->mName = $name;
    }
    
    /**
     * @public
     * @brief Sets $value as raw value to this property. And the value is casted by the property's type'.
     * @param $value mixed
     */
    public function set($value)
    {
        $this->mValue = $value;
    }
    
    /**
     * @public
     * @brief Gets the value of this property.
     * @return mixed
     */
    public function get($index = null)
    {
        return $this->mValue;
    }
    
    /**
     * @public
     * @brief Gets a value indicating whether this object expresses Array.
     * @return bool
     * 
     * @remarks
     *     This class is a base class for none-array properties, so a sub-class of this
     *     does not override this method.
     */
    public function isArray()
    {
        return false;
    }
    
    /**
     * @public
     * @brief Gets a value indicating whether this object is null.
     * @return bool
     */
    public function isNull()
    {
        return (strlen(trim($this->mValue)) == 0);
    }
    
    /**
     * @public
     * @brief Gets a value as integer.
     * @return int
     */
    public function toNumber()
    {
        return $this->mValue;
    }
    
    /**
     * @public
     * @brief Gets a value as string.
     * @return string
     */
    public function toString()
    {
        return $this->mValue;
    }
    
    /**
     * @public
     * @brief Gets a value as encoded HTML code.
     * @return string - HTML
     * @deprecated
     */
    public function toHTML()
    {
        return htmlspecialchars($this->toString(), ENT_QUOTES);
    }
    
    /**
     * @public
     * @brief Gets a value indicating whether this object has a fetch control.
     * @return bool
     */
    public function hasFetchControl()
    {
        return false;
    }
}

/**
 * @public
 * @brief [Abstract] Defines common array property class which implements XCube_PropertyInterface.
 * 
 * This class is a kind of template-class --- XCube_GenericArrayProperty<T>.
 * Developers should know about sub-classes of XCube_AbstractProperty.
 */
class XCube_GenericArrayProperty extends XCube_PropertyInterface
{
    /**
     * @protected
     * @brief string
     */
    public $mName = null;

    /**
     * @protected
     * @brief XCube_AbstractProperty[] - std::map<mixed_key, mixed_value>
     */
    public $mProperties = array();
    
    /**
     * @protected
     * @brief string - <T>
     * 
     * If this class is XCube_GenericArrayProperty<T>, mPropertyClassName is <T>.
     */
    public $mPropertyClassName = null;
    
    /**
     * @public
     * @brief Constructor.
     * @param $classname string - <T>
     * @param $name string - A name of the property.
     */
    // !Fix PHP7
    public function __construct($classname, $name)
    //public function XCube_GenericArrayProperty($classname, $name)
    {
        $this->mPropertyClassName = $classname;
        $this->mName = $name;
    }
    
    /**
     * @public
     * @brief Sets a value. And the value is casted by the property's type'.
     * 
     *   This member function has two signatures.
     * 
     * \par set(something[] values);
     *    Fetches values from the array.
     * 
     * \par set(mixed key, mixed value);
     *    Set values with index 'key'.
     */
    public function set($arg1, $arg2 = null)
    {
        if (is_array($arg1) && $arg2 == null) {
            $this->reset();
            foreach ($arg1 as $t_key => $t_value) {
                $this->_set($t_key, $t_value);
            }
        } elseif ($arg1===null && $arg2===null) {    //ex) all checkbox options are off
            $this->reset();
        } elseif ($arg1 !== null && $arg2 !== null) {
            $this->_set($arg1, $arg2);
        }
    }
    
    /**
     * @internal
     * @todo Research this method.
     */
    public function add($arg1, $arg2 = null)
    {
        if (is_array($arg1) && $arg2 == null) {
            foreach ($arg1 as $t_key => $t_value) {
                $this->_set($t_key, $t_value);
            }
        } elseif ($arg1 !== null && $arg2 !== null) {
            $this->_set($arg1, $arg2);
        }
    }
    
    /**
     * @private
     * @brief This member function helps set().
     * @param string $index
     * @param mixed $value
     * @return void
     */
    public function _set($index, $value)
    {
        if (!isset($this->mProperties[$index])) {
            $this->mProperties[$index] = new $this->mPropertyClassName($this->mName);
        }
        $this->mProperties[$index]->set($value);
    }
    
    /**
     * @public
     * @brief Gets values of this property.
     * @param $index mixed - If $indes is null, gets array (std::map<mixed_key, mixed_value>).
     * @return mixed
     */
    public function get($index = null)
    {
        if ($index === null) {
            $ret = array();
            
            foreach ($this->mProperties as $t_key => $t_value) {
                $ret[$t_key] = $t_value->get();
            }
            
            return $ret;
        }
        
        return isset($this->mProperties[$index]) ? $this->mProperties[$index]->get() : null;
    }
    
    /**
     * @protected
     * @brief Resets all properties of this.
     */
    public function reset()
    {
        unset($this->mProperties);
        $this->mProperties = array();
    }
    
    /**
     * @public
     * @brief Gets a value indicating whether this object expresses Array.
     * @return bool
     * 
     * @remarks
     *     This class is a base class for array properties, so a sub-class of this
     *     does not override this method.
     */
    public function isArray()
    {
        return true;
    }
    
    /**
     * @public
     * @brief Gets a value indicating whether this object is null.
     * @return bool
     */
    public function isNull()
    {
        return (count($this->mProperties) == 0);
    }
    
    /**
     * @public
     * @brief Gets a value as integer --- but, gets null always.
     * @return int
     */
    public function toNumber()
    {
        return null;
    }
    
    /**
     * @public
     * @brief Gets a value as string --- but, gets 'Array' always.
     * @return string
     */
    public function toString()
    {
        return 'Array';
    }
    
    /**
     * @public
     * @brief Gets a value as encoded HTML code --- but, gets 'Array' always.
     * @return string - HTML
     * @deprecated
     */
    public function toHTML()
    {
        return htmlspecialchars($this->toString(), ENT_QUOTES);
    }
    
    /**
     * @public
     * @brief Gets a value indicating whether this object has a fetch control.
     * @return bool
     */
    public function hasFetchControl()
    {
        return false;
    }
}

/**
 * @internal
 * @deprecated
 */
class XCube_AbstractArrayProperty extends XCube_GenericArrayProperty
{
    // !Fix PHP7
    public function __construct($name)
    //public function XCube_AbstractArrayProperty($name)
    {
        // ! call parent::__construct() instead of parent::Controller()
        parent::__construct($this->mPropertyClassName, $name);
        //parent::XCube_GenericArrayProperty($this->mPropertyClassName, $name);
    }
}

/**
 * @public
 * @brief Represents bool property. 
 */
class XCube_BoolProperty extends XCube_AbstractProperty
{
    public function set($value)
    {
        $this->mValue = (int)$value ? 1 : 0;
    }
}

/**
 * @public
 * @brief Represents bool[] property. XCube_GenericArrayProperty<XCube_BoolProperty>.
 * @see XCube_BoolProperty
 */
class XCube_BoolArrayProperty extends XCube_GenericArrayProperty
{
    // !Fix PHP7
    public function __construct($name)
    //public function XCube_BoolArrayProperty($name)
    {
        // ! call parent::__construct() instead of parent::Controller()
        parent::__construct("XCube_BoolProperty", $name);
        //parent::XCube_GenericArrayProperty("XCube_BoolProperty", $name);
    }
}

/**
 * @public
 * @brief Represents int property. 
 */
class XCube_IntProperty extends XCube_AbstractProperty
{
    public function set($value)
    {
        $this->mValue = trim($value)!==''?(int)$value:null;
    }
}

/**
 * @public
 * @brief Represents int[] property. XCube_GenericArrayProperty<XCube_IntProperty>.
 * @see XCube_IntProperty
 */
class XCube_IntArrayProperty extends XCube_GenericArrayProperty
{
    // !Fix PHP7
    public function __construct($name)
    //public function XCube_IntArrayProperty($name)
    {
        // ! call parent::__construct() instead of parent::Controller()
        parent::__construct("XCube_IntProperty", $name);
        //parent::XCube_GenericArrayProperty("XCube_IntProperty", $name);
    }
}

/**
 * @public
 * @brief Represents float property. 
 */
class XCube_FloatProperty extends XCube_AbstractProperty
{
    public function set($value)
    {
        $this->mValue = trim($value)!== ''?(float)$value:null;
    }
}

/**
 * @public
 * @brief Represents float[] property. XCube_GenericArrayProperty<XCube_FloatProperty>.
 * @see XCube_FloatProperty
 */
class XCube_FloatArrayProperty extends XCube_GenericArrayProperty
{
    // !Fix PHP7
    public function __construct($name)
    //public function XCube_FloatArrayProperty($name)
    {
        // ! call parent::__construct() instead of parent::Controller()
        parent::__construct("XCube_FloatProperty", $name);
        //parent::XCube_GenericArrayProperty("XCube_FloatProperty", $name);
    }
}

/**
 * @public
 * @brief Represents string property. 
 * 
 * This class shows the property of string. Check whether a request includes control
 * code. If it does, stop own process.
 */
class XCube_StringProperty extends XCube_AbstractProperty
{
    public function set($value)
    {
        // if (preg_match_all("/[\\x00-\\x1f]/", $value, $matches, PREG_PATTERN_ORDER)) {
        // 	die("Get control code :" . ord($matches[0][0]));
        // }

        $this->mValue = preg_replace("/[\\x00-\\x1f]/", '', $value);
    }
    
    public function toNumber()
    {
        return (int)$this->mValue;
    }
}

/**
 * @public
 * @brief Represents string[] property. XCube_GenericArrayProperty<XCube_StringProperty>.
 * @see XCube_StringProperty
 */
class XCube_StringArrayProperty extends XCube_GenericArrayProperty
{
    // !Fix PHP7
    public function __construct($name)
    //public function XCube_StringArrayProperty($name)
    {
        // ! call parent::__construct() instead of parent::Controller()
        parent::__construct("XCube_StringProperty", $name);
        //parent::XCube_GenericArrayProperty("XCube_StringProperty", $name);
    }
}

/**
 * @public
 * @brief Represents string property which allows CR and LF.
 *  
 *  This class shows the property of text. Check whether a request includes control
 * code. If it does, stop own process.
 */
class XCube_TextProperty extends XCube_AbstractProperty
{
    public function set($value)
    {
        $matches = array();
        
        // if (preg_match_all("/[\\x00-\\x08]|[\\x0b-\\x0c]|[\\x0e-\\x1f]/", $value, $matches,PREG_PATTERN_ORDER)) {
        // 	die("Get control code :" . ord($matches[0][0]));
        // }

        $this->mValue = preg_replace("/[\\x00-\\x08]|[\\x0b-\\x0c]|[\\x0e-\\x1f]/", '', $value);
    }
    
    public function toNumber()
    {
        return (int)$this->mValue;
    }
}

/**
 * @public
 * @brief Represents string[] property which allows CR and LF. XCube_GenericArrayProperty<XCube_TextProperty>.
 * @see XCube_TextProperty
 */
class XCube_TextArrayProperty extends XCube_GenericArrayProperty
{

    // !Fix PHP7
    public function __construct($name)
    //public function XCube_TextArrayProperty($name)
    {
        // ! call parent::__construct() instead of parent::Controller()
        parent::__construct("XCube_TextProperty", $name);
        //parent::XCube_GenericArrayProperty("XCube_TextProperty", $name);
    }
}

/**
 * @public
 * @brief Represents the special property which handles uploaded file.
 * @see XCube_FormFile
 */
class XCube_FileProperty extends XCube_AbstractProperty
{
    /**
     * @protected
     * @brief mixed - ID for XCube_FileArrayProperty.
     * 
     * friend XCube_FileArrayProperty;
     */
    public $mIndex = null;
    // !Fix PHP7
    public function __construct($name)
    //public function XCube_FileProperty($name)
    {
        // ! call parent::__construct() instead of parent::Controller()
        parent::__construct($name);
        //parent::XCube_AbstractProperty($name);
        $this->mValue = new XCube_FormFile($name);
    }
    
    public function hasFetchControl()
    {
        return true;
    }
    
    public function fetch(&$form)
    {
        if (!is_object($this->mValue)) {
            return false;
        }
        
        if ($this->mIndex !== null) {
            $this->mValue->mKey = $this->mIndex;
        }
        
        $this->mValue->fetch();
        
        if (!$this->mValue->hasUploadFile()) {
            $this->mValue = null;
        }
    }
    
    public function isNull()
    {
        if (!is_object($this->mValue)) {
            return true;
        }
        
        return !$this->mValue->hasUploadFile();
    }
    
    public function toString()
    {
        return null;
    }
    
    public function toNumber()
    {
        return null;
    }
}

/**
 * @public
 * @brief Represents the special property[] which handles uploaded file. XCube_GenericArrayProperty<XCube_FileProperty>.
 * @see XCube_FileProperty
 */
class XCube_FileArrayProperty extends XCube_GenericArrayProperty
{
    // !Fix PHP7
    public function __construct($name)
    //public function XCube_FileArrayProperty($name)
    {
        // ! call parent::__construct() instead of parent::Controller()
        parent::__construct("XCube_FileProperty", $name);
        //parent::XCube_GenericArrayProperty("XCube_FileProperty", $name);
    }
    
    public function hasFetchControl()
    {
        return true;
    }
    
    public function fetch(&$form)
    {
        unset($this->mProperties);
        $this->mProperties = array();
        if (isset($_FILES[$this->mName]) && is_array($_FILES[$this->mName]['name'])) {
            foreach ($_FILES[$this->mName]['name'] as $_key => $_val) {
                $this->mProperties[$_key] = new $this->mPropertyClassName($this->mName);
                $this->mProperties[$_key]->mIndex = $_key;
                $this->mProperties[$_key]->fetch($form);
            }
        }
    }
}

/**
 * @public
 * @brief This is extended XCube_FileProperty and limits uploaded files by image files.
 * @see XCube_FormImageFile
 */
class XCube_ImageFileProperty extends XCube_FileProperty
{
    // !Fix PHP7
    public function __construct($name)
    //public function XCube_ImageFileProperty($name)
    {
        // ! call parent::__construct() instead of parent::Controller()
        parent::__construct($name);
        //parent::XCube_AbstractProperty($name);
        $this->mValue = new XCube_FormImageFile($name);
    }
}

/**
 * @public
 * @brief  XCube_GenericArrayProperty<XCube_ImageFileProperty>.
 * @see XCube_ImageFileProperty
 */
class XCube_ImageFileArrayProperty extends XCube_FileArrayProperty
{
    // !Fix PHP7
    public function __construct($name)
    //public function XCube_ImageFileArrayProperty($name)
    {
        // ! call parent::__construct() instead of parent::Controller()
        parent::__construct("XCube_ImageFileProperty", $name);
        //parent::XCube_GenericArrayProperty("XCube_ImageFileProperty", $name);
    }
}
