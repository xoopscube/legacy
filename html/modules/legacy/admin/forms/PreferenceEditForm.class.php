<?php
/**
 *
 * @package Legacy
 * @version $Id: PreferenceEditForm.class.php,v 1.3 2008/09/25 15:11:12 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";

class Legacy_PreferenceEditForm extends XCube_ActionForm
{
    public $mKeyName = "confcat_id";
    public $mKeyValue = 0;
    
    public function Legacy_PreferenceEditForm($category)
    {
        self::__construct($category);
    }

    public function __construct($category)
    {
        // ! call parent::__construct() instead of parent::Controller()
        parent::__construct();
        //parent::XCube_ActionForm();
        $this->mKeyValue = $category->get('confcat_id');
    }
    
    public function getTokenName()
    {
        return "module.legacy.PreferenceEditForm.TOKEN" . $this->getCategoryId();
    }
    
    public function getCategoryId()
    {
        return $this->mKeyValue;
    }

    public function getModuleId()
    {
        return 0;
    }

    public function prepare()
    {
        //
        // Set form properties
        //
        $configArr = array();
        if (func_num_args()) {
            $configArr = func_get_arg(0);
        }
        foreach ($configArr as $config) {
            switch ($config->get('conf_valuetype')) {
                case 'text':
                case 'string':
                    if ($config->get('conf_formtype') == 'textarea') {
                        $this->mFormProperties[$config->get('conf_name')] =new XCube_TextProperty($config->get('conf_name'));
                    } else {
                        $this->mFormProperties[$config->get('conf_name')] =new XCube_StringProperty($config->get('conf_name'));
                    }
                    $this->set($config->get('conf_name'), $config->get('conf_value'));
                    break;

                case 'float':
                    $this->mFormProperties[$config->get('conf_name')] =new XCube_FloatProperty($config->get('conf_name'));
                    $this->set($config->get('conf_name'), $config->get('conf_value'));
                    
                    $this->mFieldProperties[$config->get('conf_name')] =new XCube_FieldProperty($this);
                    $this->mFieldProperties[$config->get('conf_name')]->setDependsByArray(array('required'));
                    $this->mFieldProperties[$config->get('conf_name')]->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, $config->get('conf_title'));
                    break;

                case 'int':
                    $this->mFormProperties[$config->get('conf_name')] =new XCube_IntProperty($config->get('conf_name'));
                    $this->set($config->get('conf_name'), $config->get('conf_value'));
                    
                    $this->mFieldProperties[$config->get('conf_name')] =new XCube_FieldProperty($this);
                    $this->mFieldProperties[$config->get('conf_name')]->setDependsByArray(array('required'));
                    $this->mFieldProperties[$config->get('conf_name')]->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, $config->get('conf_title'));
                    break;
                    
                case 'other':
                    $this->mFormProperties[$config->get('conf_name')] =new XCube_StringProperty($config->get('conf_name'));
                    $this->set($config->get('conf_name'), $config->get('conf_value'));
                    break;

                case 'array':
                    if ($config->get('conf_formtype') == 'textarea') {
                        $this->mFormProperties[$config->get('conf_name')] =new XCube_StringProperty($config->get('conf_name'));
                        $this->set($config->get('conf_name'), implode("|", unserialize($config->get('conf_value'))));
                    } else {
                        $this->mFormProperties[$config->get('conf_name')] =new XCube_StringArrayProperty($config->get('conf_name'));
                        $t_arr = unserialize($config->get('conf_value'));
                        if (is_array($t_arr)) {
                            foreach ($t_arr as $_key => $_value) {
                                $this->set($config->get('conf_name'), $_key, $_value);
                            }
                        }
                    }
                    break;
                case 'encrypt':
                    if ($config->get('conf_formtype') == 'textarea') {
                        $this->mFormProperties[$config->get('conf_name')] =new XCube_TextProperty($config->get('conf_name'));
                    } else {
                        $this->mFormProperties[$config->get('conf_name')] =new XCube_StringProperty($config->get('conf_name'));
                    }
                    $this->set($config->get('conf_name'), XCube_Utils::decrypt($config->get('conf_value')));
                    break;
            }
        }
    }

    public function getImploadValue($key)
    {
        $value = $this->get($key);
        return is_array($value) ? implode("|", $value) : $value;
    }
    
    public function update(&$configArr)
    {
        foreach (array_keys($configArr) as $key) {
            $value = $this->get($configArr[$key]->get('conf_name'));
            
            if ($configArr[$key]->get('conf_valuetype') == 'array') {
                if (is_array($value)) {
                    $configArr[$key]->set('conf_value', serialize($value));
                } else {
                    $configArr[$key]->set('conf_value', serialize(explode("|", $value)));
                }
            } elseif ($configArr[$key]->get('conf_valuetype') == 'encrypt') {
                $configArr[$key]->set('conf_value', XCube_Utils::encrypt($value));
            } else {
                $configArr[$key]->set('conf_value', $value);
            }
        }
    }
}

class Legacy_ModulePreferenceEditForm extends Legacy_PreferenceEditForm
{
    public $mKeyName = "confmod_id";

    public function Legacy_ModulePreferenceEditForm(&$module)
    {
        self::__construct($module);
    }

    public function __construct(&$module)
    {
        // ! call parent::__construct() instead of parent::Controller()
        parent::__construct($module);       
        //parent::XCube_ActionForm();
        $this->mKeyValue = $module->get('mid');
    }
    
    public function getTokenName()
    {
        return "module.legacy.ModulePreferenceEditForm.TOKEN" . $this->getModuleId();
    }
    
    public function getCategoryId()
    {
        return 0;
    }

    public function getModuleId()
    {
        return $this->mKeyValue;
    }
}
