<?php
/**
 *
 * @package Legacy
 * @version $Id: ModuleInstallInformation.class.php,v 1.4 2008/09/25 15:12:41 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 * @brief This file declare some structure-class and stored-system readers for the installer.
 */

define('LEGACY_INSTALLINFO_STATUS_LOADED', "loaded");
define('LEGACY_INSTALLINFO_STATUS_UPDATED', "updated");
define('LEGACY_INSTALLINFO_STATUS_ORDER_UPDATED', "order_updated");
define('LEGACY_INSTALLINFO_STATUS_NEW', "new");
define('LEGACY_INSTALLINFO_STATUS_DELETED', "deleted");

/**
 * The structure which is able to keep block's informations without DB. This
 * is installer only.
 */
class Legacy_BlockInformation
{
    public $mStatus = LEGACY_INSTALLINFO_STATUS_LOADED;

    public $mFuncNum = 0;

    public $mName = "";

    public $mOptions = "";

    public $mFuncFile = "";
    public $mShowFunc = "";
    public $mEditFunc = "";
    public $mTemplate = "";

    public function Legacy_BlockInformation($funcNum, $name, $funcFile, $showFunc, $editFunc, $template, $options = null)
    {
        self::__construct($funcNum, $name, $funcFile, $showFunc, $editFunc, $template, $options);
    }

    public function __construct($funcNum, $name, $funcFile, $showFunc, $editFunc, $template, $options = null)
    {
        $this->mFuncNum = intval($funcNum);
        $this->mName = $name;
        $this->mFuncFile = $funcFile;
        $this->mShowFunc = $showFunc;
        $this->mEditFunc = $editFunc;
        $this->mTemplate = $template;
        $this->mOptions = $options;
    }

    /**
     * @return bool
     */
    public function isEqual(&$block)
    {
        if ($this->mFuncNum != $block->mFuncNum) {
            return false;
        }

        if ($this->mName != $block->mName) {
            return false;
        }

        if ($this->mFuncFile != $block->mFuncFile) {
            return false;
        }

        if ($this->mShowFunc != $block->mShowFunc) {
            return false;
        }

        if ($this->mEditFunc != $block->mEditFunc) {
            return false;
        }

        if ($this->mTemplate != $block->mTemplate) {
            return false;
        }

        return true;
    }

    public function update(&$block)
    {
        $this->mStatus = LEGACY_INSTALLINFO_STATUS_UPDATED;

        $this->mName = $block->mName;
        $this->mFuncFile = $block->mFuncFile;
        $this->mShowFunc = $block->mShowFunc;
        $this->mEditFunc = $block->mEditFunc;
        $this->mTemplate = $block->mTemplate;
    }
}

class Legacy_BlockInfoCollection
{
    public $mBlocks = array();
    public $mShowFuncs = array();
    public $mFuncFiles = array();

    public function add(&$info)
    {
        if (isset($this->mBlocks[$info->mFuncNum])) {
            return false;
        }

        $this->mBlocks[$info->mFuncNum] =& $info;
        $this->mShowFuncs[] = $info->mShowFunc;
        $this->mFuncFiles[] = $info->mFuncFile;

        ksort($this->mBlocks);

        return true;
    }

    public function &get($funcNum)
    {
        if (isset($this->mBlocks[$funcNum])) {
            return $this->mBlocks[$funcNum];
        }

        $ret = null;
        return $ret;
    }

    public function funcExists($info)
    {
        return (in_array($info->mShowFunc, $this->mShowFuncs) && in_array($info->mFuncFile, $this->mFuncFiles));
    }

    /**
     * Updates the list of blocks by comparing with $collection.
     */
    public function update(&$collection)
    {
        foreach (array_keys($this->mBlocks) as $idx) {
            $t_block =& $collection->get($this->mBlocks[$idx]->mFuncNum);
            if ($t_block == null) {
                if (!$collection->funcExists($this->mBlocks[$idx])) {
                    $this->mBlocks[$idx]->mStatus = LEGACY_INSTALLINFO_STATUS_DELETED;
                } else {
                    $this->mBlocks[$idx]->mStatus = LEGACY_INSTALLINFO_STATUS_UPDATED; // No Action.
                }
            } elseif (!$this->mBlocks[$idx]->isEqual($t_block)) {
                $this->mBlocks[$idx]->update($t_block);
            }
        }

        foreach (array_keys($collection->mBlocks) as $idx) {
            $func_num = $collection->mBlocks[$idx]->mFuncNum;
            if (!isset($this->mBlocks[$func_num])) {
                $this->add($collection->mBlocks[$idx]);
                $this->mBlocks[$func_num]->mStatus = LEGACY_INSTALLINFO_STATUS_NEW;
            }
        }
    }

    public function reset()
    {
        unset($this->mBlocks);
        $this->mBlocks = array();
    }
}

/**
 * The structure which is able to keep preference's informations without DB.
 * This is installer only.
 */
class Legacy_PreferenceInformation
{
    public $mStatus = LEGACY_INSTALLINFO_STATUS_LOADED;

    public $mOrder = 0;

    public $mName = "";

    public $mTitle = "";

    public $mDescription = "";

    public $mFormType = "";

    public $mValueType = "";

    public $mDefault = null;

    public $mOption = null;

    public function Legacy_PreferenceInformation($name, $title, $description, $formType, $valueType, $default, $order = 0)
    {
        self::__construct($name, $title, $description, $formType, $valueType, $default, $order);
    }

    public function __construct($name, $title, $description, $formType, $valueType, $default, $order = 0)
    {
        $this->mName = $name;
        $this->mTitle = $title;
        $this->mDescription = $description;
        $this->mFormType = $formType;
        $this->mValueType = $valueType;
        $this->mDefault = $default;
        $this->mOrder = intval($order);

        $this->mOption =new Legacy_PreferenceOptionInfoCollection();
    }

    /**
     * @return bool
     */
    public function isEqual(&$preference)
    {
        if ($this->mName != $preference->mName) {
            return false;
        }

        if ($this->mTitle != $preference->mTitle) {
            return false;
        }

        if ($this->mDescription != $preference->mDescription) {
            return false;
        }

        if ($this->mFormType != $preference->mFormType) {
            return false;
        }

        if ($this->mValueType != $preference->mValueType) {
            return false;
        }

        if ($this->mOrder != $preference->mOrder) {
            return false;
        }

        if (!$this->mOption->isEqual($preference->mOption)) {
            return false;
        }

        return true;
    }

    public function update(&$preference)
    {
        $this->mStatus = LEGACY_INSTALLINFO_STATUS_UPDATED;

        $this->mName = $preference->mName;
        $this->mTitle = $preference->mTitle;
        $this->mDescription = $preference->mDescription;
        $this->mFormType = $preference->mFormType;
        $this->mValueType = $preference->mValueType;
        $this->mDefault = $preference->mDefault;
        $this->mOrder = $preference->mOrder;

        unset($this->mOption);
        $this->mOption =& $preference->mOption;
    }
}

class Legacy_PreferenceInfoCollection
{
    public $mPreferences = array();

    public $mComments = array();

    public $mNotifications = array();

    public function Legacy_PreferenceInfoCollection()
    {
        self::__construct();
    }

    public function __construct()
    {
    }

    public function add(&$preference)
    {
        if ($preference->mName == 'com_rule' || $preference->mName == 'com_anonpost') {
            if (isset($this->mComments[$preference->mName])) {
                return false;
            }
            $this->mComments[$preference->mName] =& $preference;
            $this->_sort();
            return true;
        }

        if ($preference->mName == 'notification_enabled' || $preference->mName == 'notification_events') {
            if (isset($this->mNotifications[$preference->mName])) {
                return false;
            }
            $this->mNotifications[$preference->mName] =& $preference;
            $this->_sort();
            return true;
        }

        if (isset($this->mPreferences[$preference->mName])) {
            return false;
        }

        $this->mPreferences[$preference->mName] =& $preference;
        $this->_sort();

        return true;
    }

    /**
     * @private
     * Renumbers orders of preferences.
     */
    public function _sort()
    {
        $currentOrder = 0;
        foreach (array_keys($this->mPreferences) as $idx) {
            if ($this->mPreferences[$idx]->mOrder != $currentOrder) {
                $this->mPreferences[$idx]->mStatus = LEGACY_INSTALLINFO_STATUS_ORDER_UPDATED;
                $this->mPreferences[$idx]->mOrder = $currentOrder;
            }

            $currentOrder++;
        }

        foreach (array_keys($this->mComments) as $idx) {
            if ($this->mComments[$idx]->mOrder != $currentOrder) {
                $this->mComments[$idx]->mStatus = LEGACY_INSTALLINFO_STATUS_ORDER_UPDATED;
                $this->mComments[$idx]->mOrder = $currentOrder;
            }

            $currentOrder++;
        }

        foreach (array_keys($this->mNotifications) as $idx) {
            if ($this->mNotifications[$idx]->mOrder != $currentOrder) {
                $this->mNotifications[$idx]->mStatus = LEGACY_INSTALLINFO_STATUS_ORDER_UPDATED;
                $this->mNotifications[$idx]->mOrder = $currentOrder;
            }

            $currentOrder++;
        }
    }

    public function &get($name)
    {
        $ret = null;

        if (isset($this->mPreferences[$name])) {
            return $this->mPreferences[$name];
        }

        return $ret;
    }

    public function &getNotify($name)
    {
        $ret = null;

        if (isset($this->mNotifications[$name])) {
            return $this->mNotifications[$name];
        }

        return $ret;
    }

    /**
     * Updates the list of blocks by comparing with $collection.
     * @todo need delete comments' data
     * @todo need delete notifications' data
     */
    public function update(&$collection)
    {
        //
        // Preferences
        //
        foreach (array_keys($this->mPreferences) as $idx) {
            $t_preference =& $collection->get($this->mPreferences[$idx]->mName);
            if ($t_preference == null) {
                $this->mPreferences[$idx]->mStatus = LEGACY_INSTALLINFO_STATUS_DELETED;
            } elseif (!$this->mPreferences[$idx]->isEqual($t_preference)) {
                $this->mPreferences[$idx]->update($t_preference);
            }
        }

        foreach (array_keys($collection->mPreferences) as $idx) {
            $name = $collection->mPreferences[$idx]->mName;
            if (!isset($this->mPreferences[$name])) {
                $this->add($collection->mPreferences[$name]);
                $this->mPreferences[$name]->mStatus = LEGACY_INSTALLINFO_STATUS_NEW;
            }
        }

        //
        // Comments
        //
        if (count($this->mComments) > 0 && count($collection->mComments) == 0) {
            foreach (array_keys($this->mComments) as $idx) {
                $this->mComments[$idx]->mStatus = LEGACY_INSTALLINFO_STATUS_DELETED;
            }
        } elseif (count($this->mComments) == 0 && count($collection->mComments) > 0) {
            $this->mComments =& $collection->mComments;
            foreach (array_keys($this->mComments) as $idx) {
                $this->mComments[$idx]->mStatus = LEGACY_INSTALLINFO_STATUS_NEW;
            }
        }

        //
        // Notifications
        //
        foreach (array_keys($this->mNotifications) as $idx) {
            $t_preference =& $collection->getNotify($this->mNotifications[$idx]->mName);
            if ($t_preference == null) {
                $this->mNotifications[$idx]->mStatus = LEGACY_INSTALLINFO_STATUS_DELETED;
            } elseif (!$this->mNotifications[$idx]->isEqual($t_preference)) {
                $this->mNotifications[$idx]->update($t_preference);
            }
        }

        foreach (array_keys($collection->mNotifications) as $idx) {
            $name = $collection->mNotifications[$idx]->mName;
            if (!isset($this->mNotifications[$name])) {
                $this->add($collection->mNotifications[$name]);
                $this->mNotifications[$name]->mStatus = LEGACY_INSTALLINFO_STATUS_NEW;
            }
        }
    }

    public function reset()
    {
        unset($this->mPreferences);
        $this->mPreferences = array();
    }
}

class Legacy_PreferenceOptionInformation
{
    public $mName = "";
    public $mValue = "";

    public function Legacy_PreferenceOptionInformation($name, $value)
    {
        self::__construct($name, $value);
    }

    public function __construct($name, $value)
    {
        $this->mName = $name;
        $this->mValue = $value;
    }

    public function isEqual($option)
    {
        return (($this->mName == $option->mName) && ($this->mValue == $option->mValue));
    }
}

class Legacy_PreferenceOptionInfoCollection
{
    public $mOptions = array();

    public function __construct()
    {
    }

    public function add(&$option)
    {
        $this->mOptions[] = $option;
        return true;
    }

    public function isEqual(&$collection)
    {
        if (count($this->mOptions) != count($collection->mOptions)) {
            return false;
        }

        foreach (array_keys($this->mOptions) as $idx) {
            if (!$this->mOptions[$idx]->isEqual($collection->mOptions[$idx])) {
                return false;
            }
        }

        return true;
    }

    public function reset()
    {
        unset($this->mOptions);
        $this->mOptions = array();
    }
}

class Legacy_AbstractModinfoReader
{
    public function Legacy_AbstractModinfoReader()
    {
        self::__construct();
    }

    public function __construct()
    {
    }

    /**
     * @return Legacy_BlockInfoCollection
     */
    public function &loadBlockInformations()
    {
    }

    /**
     * @return Legacy_PreferenceInfoCollection
     */
    public function &loadPreferenceInformations()
    {
    }
}

/**
 * @note final class
 */
class Legacy_ModinfoX2FileReader extends Legacy_AbstractModinfoReader
{
    /**
     * @protected
     */
    public $_mDirname = null;

    public function Legacy_ModinfoX2FileReader($dirname)
    {
        self::__construct($dirname);
    }

    public function __construct($dirname)
    {
        $this->_mDirname = $dirname;
    }

    /**
     * @private
     */
    public function &_createBlockInformation($funcNum, $arr)
    {
        $showFunc = "";
        if (isset($arr['class'])) {
            $showFunc = 'cl::' . $arr['class'];
        } else {
            $showFunc = $arr['show_func'];
        }

        $editFunc = isset($arr['edit_func']) ? $arr['edit_func'] : null;
        $template = isset($arr['template']) ? $arr['template'] : null;
        $options = isset($arr['options']) ? $arr['options'] : null;

        $info =new Legacy_BlockInformation($funcNum, $arr['name'], $arr['file'], $showFunc, $editFunc, $template, $options);

        return $info;
    }

    /**
     * @todo Need guarantee of global variables.
     */
    public function &loadBlockInformations()
    {
        $collection =new Legacy_BlockInfoCollection();

        $t_filePath = XOOPS_ROOT_PATH . '/modules/' . $this->_mDirname . '/xoops_version.php';
        if (!file_exists($t_filePath)) {
            return $collection;
        }

        include $t_filePath;

        if (!isset($modversion['blocks'])) {
            return $collection;
        }

        $blockArr = $modversion['blocks'];

        //
        // Try (1) --- func_num
        //
        $successFlag = true;
        foreach ($blockArr as $idx => $block) {
            if (isset($block['func_num'])) {
                $info =& $this->_createBlockInformation($block['func_num'], $block);
                $successFlag &= $collection->add($info);
                unset($info);
            } else {
                $successFlag = false;
                break;
            }
        }

        if ($successFlag) {
            return $collection;
        }

        //
        // Try (2) --- index pattern
        //
        $collection->reset();

        $successFlag = true;
        foreach ($blockArr as $idx => $block) {
            if (is_int($idx)) {
                $info =& $this->_createBlockInformation($idx, $block);
                $successFlag &= $collection->add($info);
                unset($info);
            } else {
                $successFlag = false;
                break;
            }
        }

        if ($successFlag) {
            return $collection;
        }

        //
        // Try (3) --- automatic
        //
        $collection->reset();

        $idx = 1;
        foreach ($blockArr as $block) {
            $info =& $this->_createBlockInformation($idx++, $block);
            $successFlag &= $collection->add($info);
            unset($info);
        }

        return $collection;
    }

    public function &_createPreferenceInformation($arr)
    {
        $arr['description'] = isset($arr['description']) ? $arr['description'] : null;
        $info =new Legacy_PreferenceInformation($arr['name'], $arr['title'], $arr['description'], $arr['formtype'], $arr['valuetype'], $arr['default']);
        if (isset($arr['options'])) {
            foreach ($arr['options'] as $name => $value) {
                $option =new Legacy_PreferenceOptionInformation($name, $value);
                $info->mOption->add($option);
                unset($option);
            }
        }

        return $info;
    }

    public function _loadCommentPreferenceInfomations(&$modversion, &$collection)
    {
        if (isset($modversion['hasComments']) && $modversion['hasComments'] == true) {
            require_once XOOPS_ROOT_PATH . "/include/comment_constants.php";

            $comRule = array('name' => 'com_rule',
                             'title' => '_CM_COMRULES',
                             'description' => '',
                             'formtype' => 'select',
                             'valuetype' => 'int',
                             'default' => 1,
                             'options' => array('_CM_COMNOCOM' => XOOPS_COMMENT_APPROVENONE, '_CM_COMAPPROVEALL' => XOOPS_COMMENT_APPROVEALL, '_CM_COMAPPROVEUSER' => XOOPS_COMMENT_APPROVEUSER, '_CM_COMAPPROVEADMIN' => XOOPS_COMMENT_APPROVEADMIN)
                       );
            $info =& $this->_createPreferenceInformation($comRule);
            $collection->add($info);
            unset($info);

            $comAnonpost = array('name' => 'com_anonpost',
                                 'title' => '_CM_COMANONPOST',
                                 'description' => '',
                                 'formtype' => 'yesno',
                                 'valuetype' => 'int',
                                 'default' => 0
                           );
            $info =& $this->_createPreferenceInformation($comAnonpost);
            $collection->add($info);
            unset($info);
        }
    }

    public function _loadNotificationPreferenceInfomations(&$modversion, &$collection)
    {
        if (isset($modversion['hasNotification']) && $modversion['hasNotification'] == true) {
            require_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
            require_once XOOPS_ROOT_PATH . '/include/notification_functions.php';

            $t_options = array();
            $t_options['_NOT_CONFIG_DISABLE'] = XOOPS_NOTIFICATION_DISABLE;
            $t_options['_NOT_CONFIG_ENABLEBLOCK'] = XOOPS_NOTIFICATION_ENABLEBLOCK;
            $t_options['_NOT_CONFIG_ENABLEINLINE'] = XOOPS_NOTIFICATION_ENABLEINLINE;
            $t_options['_NOT_CONFIG_ENABLEBOTH'] = XOOPS_NOTIFICATION_ENABLEBOTH;

            $notifyEnable = array(
                'name' => 'notification_enabled',
                'title' => '_NOT_CONFIG_ENABLE',
                'description' => '_NOT_CONFIG_ENABLEDSC',
                'formtype' => 'select',
                'valuetype' => 'int',
                'default' => XOOPS_NOTIFICATION_ENABLEBOTH,
                'options' => $t_options
            );
            $info =& $this->_createPreferenceInformation($notifyEnable);
            $collection->add($info);
            unset($info);

            //
            // FIXME: doesn't work when update module... can't read back the
            //        array of options properly...  " changing to &quot;
            //

            unset($t_options);

            //
            // Get the module object to get mid.
            //
            $handler =& xoops_gethandler('module');
            $module =& $handler->getByDirname($this->_mDirname);

            $t_options = array();
            $t_categoryArr =& notificationCategoryInfo('', $module->get('mid'));
            foreach ($t_categoryArr as $t_category) {
                $t_eventArr =& notificationEvents($t_category['name'], false, $module->get('mid'));
                foreach ($t_eventArr as $t_event) {
                    if (!empty($t_event['invisible'])) {
                        continue;
                    }
                    $t_optionName = $t_category['title'] . ' : ' . $t_event['title'];
                    $t_options[$t_optionName] = $t_category['name'] . '-' . $t_event['name'];
                }
            }

            $notifyEvents = array(
                'name' => 'notification_events',
                'title' => '_NOT_CONFIG_EVENTS',
                'description' => '_NOT_CONFIG_EVENTSDSC',
                'formtype' => 'select_multi',
                'valuetype' => 'array',
                'default' => array_values($t_options),
                'options' => $t_options
            );
            $info =& $this->_createPreferenceInformation($notifyEvents);
            $collection->add($info);
            unset($info);
        }
    }

    /**
     * @note Because XoopsModule class of X2 kernel is too complex, this method
     *       parses xoops_version directly.
     * @todo Need guarantee of global variables.
     */
    public function &loadPreferenceInformations()
    {
        $collection =new Legacy_PreferenceInfoCollection();

        $t_filePath = XOOPS_ROOT_PATH . '/modules/' . $this->_mDirname . '/xoops_version.php';
        if (!file_exists($t_filePath)) {
            return $collection;
        }

        include $t_filePath;

        //
        // If the module does not have any pereferences, check comments & notifications, and return.
        //
        if (!isset($modversion['config'])) {
            $this->_loadCommentPreferenceInfomations($modversion, $collection);
            $this->_loadNotificationPreferenceInfomations($modversion, $collection);
            return $collection;
        }

        $preferenceArr = $modversion['config'];

        //
        // Try (1) --- name index pattern
        //
        $successFlag = true;
        foreach ($preferenceArr as $idx => $preference) {
            if (is_string($idx)) {
                $preference['name'] = $idx;
                $info =& $this->_createPreferenceInformation($preference);
                $successFlag &= $collection->add($info);
                unset($info);
            } else {
                $successFlag = false;
                break;
            }
        }

        //
        // Try (2) --- auto number
        //
        if (!$successFlag) {
            $collection->reset();

            foreach ($preferenceArr as $preference) {
                $info =& $this->_createPreferenceInformation($preference);
                $collection->add($info);
                unset($info);
            }
        }

        //
        // Add comments & notifications
        //
        $this->_loadCommentPreferenceInfomations($modversion, $collection);
        $this->_loadNotificationPreferenceInfomations($modversion, $collection);

        return $collection;
    }
}

class Legacy_ModinfoX2DBReader extends Legacy_AbstractModinfoReader
{
    /**
     * @protected
     */
    public $_mDirname = null;

    public function Legacy_ModinfoX2DBReader($dirname)
    {
        self::__construct($dirname);
    }

    public function __construct($dirname)
    {
        $this->_mDirname = $dirname;
    }

    public function &_createBlockInformation(&$block)
    {
        $info =new Legacy_BlockInformation($block->get('func_num'), $block->get('name'), $block->get('func_file'), $block->get('show_func'), $block->get('edit_func'), $block->get('template'), $block->get('options'));
        return $info;
    }

    public function &loadBlockInformations()
    {
        $collection =new Legacy_BlockInfoCollection();

        $handler =& xoops_getmodulehandler('newblocks', 'legacy');

        $criteria =new CriteriaCompo();
        $criteria->add(new Criteria('dirname', $this->_mDirname));
        $criteria->add(new Criteria('block_type', 'M'));

        $blockArr =& $handler->getObjects($criteria);

        foreach (array_keys($blockArr) as $idx) {
            $info =& $this->_createBlockInformation($blockArr[$idx]);
            while (!$collection->add($info)) {
                $info->mFuncNum++;
            }
        }

        return $collection;
    }

    public function &_createPreferenceInformation(&$config)
    {
        $info =new Legacy_PreferenceInformation($config->get('conf_name'), $config->get('conf_title'), $config->get('conf_desc'), $config->get('conf_formtype'), $config->get('conf_valuetype'), $config->get('conf_value'));

        $configOptionArr =& $config->getOptionItems();

        foreach (array_keys($configOptionArr) as $idx) {
            $option =new Legacy_PreferenceOptionInformation($configOptionArr[$idx]->get('confop_name'), $configOptionArr[$idx]->get('confop_value'));
            $info->mOption->add($option);
            unset($option);
        }

        return $info;
    }

    public function &loadPreferenceInformations()
    {
        $collection =new Legacy_PreferenceInfoCollection();

        $handler =& xoops_gethandler('module');
        $module =& $handler->getByDirname($this->_mDirname);

        $handler =& xoops_gethandler('config');
        $criteria =new Criteria('conf_modid', $module->get('mid'));
        $criteria->setOrder('conf_order');
        $configArr =& $handler->getConfigs($criteria);

        foreach (array_keys($configArr) as $idx) {
            $info =& $this->_createPreferenceInformation($configArr[$idx]);
            $collection->add($info);
        }

        return $collection;
    }
}
