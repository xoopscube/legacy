<?php
/**
 * NotifyListAction.class.php
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
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
        foreach ($notificationArr as $notify) {
            $t_modid = $notify->get('not_modid');

            $module =& $moduleHandler->get($t_modid);
            if (!is_object($module)) {
                continue;
            }

            if ($t_modid != $prev_modid) {
                $prev_modid = $t_modid;
                $prev_category = -1;
                $prev_item = -1;

                $this->mModules[$t_modid] = [
                    'id' => $t_modid,
                    'name' => $module->getShow('name'),
                    'categories' => []
                ];


                //
                // [ToDo] (Original)
                // note, we could auto-generate the url from the id
                // and category info... (except when category has multiple
                // subscription scripts defined...)
                // OR, add one more option to xoops_version 'view_from'
                // which tells us where to redirect... BUT, e.g. forums, it
                // still wouldn't give us all the required info... e.g. the
                // topic ID doesn't give us the ID of the forum which is
                // a required argument...

                //
                // Get the lookup function, if exists
                //
                $notifyConfig = $module->getInfo('notification');
                $lookupFunc = '';
                if (!empty($notifyConfig['lookup_file'])) {
                    $t_filepath = XOOPS_ROOT_PATH . '/modules/' . $module->get('dirname') . '/' . $notifyConfig['lookup_file'];
                    if (file_exists($t_filepath)) {
                        require_once $t_filepath;
                        if (!empty($notifyConfig['lookup_func']) && function_exists($notifyConfig['lookup_func'])) {
                            $lookupFunc = $notifyConfig['lookup_func'];
                        }
                    }
                }
            }

            $t_category = $notify->get('not_category');
            if ($t_category != $prev_category) {
                $prev_category = $t_category;
                $prev_item = -1;
                $categoryInfo =& notificationCategoryInfo($t_category, $t_modid);
            }

            $t_item = $notify->get('not_itemid');
            if ($t_item != $prev_item) {
                $prev_item = $t_item;
                if (!empty($lookupFunc)) {
                    $itemInfo = $lookupFunc($t_category, $t_item);
                } else {
                    $itemInfo = ['name' => '[' . _NOT_NAMENOTAVAILABLE . ']', 'url' => ''];
                }
                $this->mModules[$t_modid]['categories'][$t_category]['items'][$t_item] = [
                    'id' => $t_item,
                    'name' => $itemInfo['name'],
                    'url' => $itemInfo['url'],
                    'notifications' => []
                ];
            }

            $eventInfo =& notificationEventInfo($t_category, $notify->get('not_event'), $notify->get('not_modid'));
            $this->mModules[$t_modid]['categories'][$t_category]['items'][$t_item]['notifications'][] = [
                'id' => $notify->get('not_id'),
                'module_id' => $notify->get('not_modid'),
                'category' => $notify->get('not_category'),
                'category_title' => $categoryInfo['title'],
                'item_id' => $notify->get('not_itemid'),
                'event' => $notify->get('not_event'),
                'event_title' => $eventInfo['title'],
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
