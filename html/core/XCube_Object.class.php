<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_Object.class.php,v 1.3 2008/10/12 04:30:27 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/bsd_licenses.txt Modified BSD license
 *
 */

function S_PUBLIC_VAR($definition)
{
    $t_str = explode(' ', trim($definition));
    return array('name' => trim($t_str[1]), 'type' => trim($t_str[0]));
}

class XCube_Object
{
    /**
     * Member property
     */
    public $mProperty = array();
    
    /**
     * @static
     * @return array
     */
    public function isArray()
    {
        return false;
    }
    
    /**
     * Return member property information. This member function is called in
     * the initialize of object and service. This member function has to be
     * a static function.
     *
     * @static
     * @return array
     */
    public function getPropertyDefinition()
    {
    }
    // !Fix PHP7
    public function __construct()
    //public function XCube_Object()
    {
        $fileds = $this->getPropertyDefinition();
        foreach ($fileds as $t_field) {
            $this->mProperty[$t_field['name']] = array(
                'type' => $t_field['type'],
                'value' => null
            );
        }
    }
    
    /**
     * Initialize. If the exception raises, return false.
     */
    public function prepare()
    {
    }
    
    public function toArray()
    {
        $retArray = array();
        
        foreach ($this->mProperty as $t_key => $t_value) {
            $retArray[$t_key] = $t_value['value'];
        }
        
        return $retArray;
    }
    
    public function loadByArray($vars)
    {
        foreach ($vars as $t_key => $t_value) {
            if (isset($this->mProperty[$t_key])) {
                $this->mProperty[$t_key]['value'] = $t_value;
            }
        }
    }
}

class XCube_ObjectArray
{
    public function isArray()
    {
        return true;
    }
    
    /**
     * @static
     * @return string
     */
    public function getClassName()
    {
    }
}
