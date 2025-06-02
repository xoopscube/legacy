<?php
/**
 * NotifyListAction.class.php
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/include/notification_functions.php';

require_once XOOPS_MODULE_PATH . '/legacy/forms/NotifyDeleteForm.class.php';

/***
 * @internal
 * List up notifications. This action is like notifications.php (when $op is
 * 'list').
 */
class Legacy_NotifyListAction extends Legacy_Action
{
    public $mModules = [];
    public $mActionForm = null;

    public function prepare(&$controller, &$xoopsUser)
    {
        $root =& $controller->mRoot;
        $root->mLanguageManager->loadPageTypeMessageCatalog('notification');
        $root->mLanguageManager->loadModuleMessageCatalog('legacy');

        $this->mActionForm =new Legacy_NotifyDeleteForm();
        $this->mActionForm->prepare();
    }

    public function hasPermission(&$controller, &$xoopsUser)
    {
        return is_object($xoopsUser);
    }

     public function getDefaultView(&$contoller, &$xoopsUser)
    {
        $criteria =new Criteria('not_uid', $xoopsUser->get('uid'));
        $criteria->setSort('not_modid, not_category, not_itemid');

        $handler =& xoops_gethandler('notification');
        $notificationArr =& $handler->getObjects($criteria);

        $moduleHandler =& xoops_gethandler('module');

        $prev_modid = -1;
        $prev_category = -1;
        $prev_item = -1;

        // Variables to store titles/names and validity
        $current_module_name = '';
        $current_module_lookup_func = '';

        $current_category_title = ''; // hold title or empty
        $is_current_category_title_valid = false; // Flag for validity

        foreach ($notificationArr as $notify) {
            $t_modid = $notify->get('not_modid');

            $module =& $moduleHandler->get($t_modid);
            if (!is_object($module)) {
                continue;
            }

            if ($t_modid != $prev_modid) {
                $prev_modid = $t_modid;
                $prev_category = -1; // Reset category when module changes
                $prev_item = -1;     // Reset item when module changes

                $current_module_name = $module->getShow('name'); // Store module name

                $this->mModules[$t_modid] = [
                    'id' => $t_modid,
                    'name' => $current_module_name,
                    'categories' => []
                ];

                $notifyConfig = $module->getInfo('notification');
                $current_module_lookup_func = ''; // Reset for new module
                if (is_array($notifyConfig) && !empty($notifyConfig['lookup_file'])) {
                    $t_filepath = XOOPS_ROOT_PATH . '/modules/' . $module->get('dirname') . '/' . $notifyConfig['lookup_file'];
                    if (file_exists($t_filepath)) {
                        require_once $t_filepath;
                        if (is_array($notifyConfig) && !empty($notifyConfig['lookup_func']) && function_exists($notifyConfig['lookup_func'])) {
                            $current_module_lookup_func = $notifyConfig['lookup_func'];
                        }
                    }
                }
            }

            $t_category = $notify->get('not_category');
            if ($t_category != $prev_category) {
                $prev_category = $t_category;
                $prev_item = -1; // Reset item when category changes

                $categoryInfo = notificationCategoryInfo($t_category, $t_modid);
                if (is_array($categoryInfo) && isset($categoryInfo['title'])) {
                    $current_category_title = $categoryInfo['title'];
                    $is_current_category_title_valid = true;
                } else {
                    $current_category_title = ''; // No valid title
                    $is_current_category_title_valid = false;
                }
            }

            $t_item = $notify->get('not_itemid');
            if ($t_item != $prev_item) {
                $prev_item = $t_item;
                $item_name_for_structure = '[' . _NOT_NAMENOTAVAILABLE . ']';
                $item_url_for_structure = '';

                if (!empty($current_module_lookup_func)) {
                    if (is_callable($current_module_lookup_func)) {
                        $itemInfo = call_user_func($current_module_lookup_func, $t_category, $t_item);
                        if (is_array($itemInfo)) {
                            $item_name_for_structure = isset($itemInfo['name']) ? $itemInfo['name'] : $item_name_for_structure;
                            $item_url_for_structure = isset($itemInfo['url']) ? $itemInfo['url'] : $item_url_for_structure;
                        }
                    }
                }
                $this->mModules[$t_modid]['categories'][$t_category]['items'][$t_item] = [
                    'id' => $t_item,
                    'name' => $item_name_for_structure,
                    'url' => $item_url_for_structure,
                    'notifications' => []
                ];
            }

            // Event title can change notification, fetch and check
            $eventInfo = notificationEventInfo($t_category, $notify->get('not_event'), $notify->get('not_modid'));
            $current_event_title = '';
            $is_current_event_title_valid = false; // Flag for validity
            if (is_array($eventInfo) && isset($eventInfo['title'])) {
                $current_event_title = $eventInfo['title'];
                $is_current_event_title_valid = true;
            }

            $this->mModules[$t_modid]['categories'][$t_category]['items'][$t_item]['notifications'][] = [
                'id' => $notify->get('not_id'),
                'module_id' => $notify->get('not_modid'),
                'module_name' => $current_module_name, // Pass module name
                'category' => $notify->get('not_category'),
                'category_title' => $current_category_title,
                'is_category_title_valid' => $is_current_category_title_valid, // Pass validity flag
                'item_id' => $notify->get('not_itemid'),
                'event' => $notify->get('not_event'),
                'event_title' => $current_event_title,
                'is_event_title_valid' => $is_current_event_title_valid, // Pass validity flag
                'user_id' => $notify->get('not_uid')
            ];
        }
        return LEGACY_FRAME_VIEW_INDEX;
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('legacy_notification_list.html');

        $render->setAttribute('modules', $this->mModules);
        $render->setAttribute('currentUser', $xoopsUser);
        $render->setAttribute('actionForm', $this->mActionForm);
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=NotifyList');
    }
}
