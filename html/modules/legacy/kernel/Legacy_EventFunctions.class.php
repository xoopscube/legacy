<?php
/**
 *
 * @package Legacy
 * @version $Id: Legacy_EventFunctions.class.php,v 1.3 2008/09/25 15:12:01 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Legacy_EventFunction
{
    public static function imageManager()
    {
        require_once XOOPS_MODULE_PATH . "/legacy/class/ActionFrame.class.php";
        
        $root =& XCube_Root::getSingleton();
        $root->mController->setupModuleContext('legacy');
    
        $moduleRunner =new Legacy_ActionFrame(false);
        
        $action = isset($_REQUEST['op']) ? ucfirst(xoops_getrequest('op')) : "List";
        $moduleRunner->setMode(LEGACY_FRAME_MODE_IMAGE);
        $moduleRunner->setActionName($action);
        
        $root->mController->mExecute->add(array(&$moduleRunner, 'execute'));

        $root->mController->execute();

        $root->mController->executeView();
    }

    public static function backend()
    {
        require_once XOOPS_MODULE_PATH . "/legacy/class/ActionFrame.class.php";
        
        $root =& XCube_Root::getSingleton();
        $root->mController->setupModuleContext('legacy');
        
        $moduleRunner =new Legacy_ActionFrame(false);
        $moduleRunner->setActionName('Backend');

        $root->mController->mExecute->add(array(&$moduleRunner, 'execute'));

        $root->mController->execute();

        $root->mController->executeView();
    }

    public static function search()
    {
        require_once XOOPS_MODULE_PATH . "/legacy/class/ActionFrame.class.php";
        
        $root =& XCube_Root::getSingleton();
        $root->mController->setupModuleContext('legacy');
        
        $moduleRunner =new Legacy_ActionFrame(false);
        $moduleRunner->setMode(LEGACY_FRAME_MODE_SEARCH);
        $moduleRunner->setActionName(ucfirst(xoops_getrequest('action')));

        $root->mController->mExecute->add(array(&$moduleRunner, 'execute'));

        $root->mController->execute();

        $root->mController->executeView();
    }
    
    public static function misc()
    {
        require_once XOOPS_LEGACY_PATH . "/class/ActionFrame.class.php";

        $root =& XCube_Root::getSingleton();
        $root->mController->setupModuleContext('legacy');
        
        $actionName = isset($_REQUEST['type']) ? ucfirst(xoops_getrequest('type')) : "Smilies";

        $moduleRunner = new Legacy_ActionFrame(false);
        $moduleRunner->setMode(LEGACY_FRAME_MODE_MISC);
        $moduleRunner->setActionName($actionName);

        $root->mController->mExecute->add(array(&$moduleRunner, 'execute'));

        $root->mController->setDialogMode(true);

        $root->mController->execute();

        $root->mController->executeView();
    }

    public static function notifications()
    {
        require_once XOOPS_LEGACY_PATH . "/class/ActionFrame.class.php";
        
        $root =& XCube_Root::getSingleton();
        $root->mController->setupModuleContext('legacy');
        
        //
        // 'Notify' is prefix to guard accessing from misc.php.
        //
        $actionName = isset($_REQUEST['op']) ? trim(xoops_getrequest('op')) : "List";
        $deleteValue = $root->mContext->mRequest->getRequest('delete');
        $cancelValue = $root->mContext->mRequest->getRequest('delete_cancel');
        if (isset($deleteValue)) {
            $actionName = "Delete";
        }
        if (isset($cancelValue)) {
            $actionName = "Cancel";
        }

        $moduleRunner = new Legacy_ActionFrame(false);
        $moduleRunner->setMode(LEGACY_FRAME_MODE_NOTIFY);
        $moduleRunner->setActionName($actionName);

        $root->mController->mExecute->add(array(&$moduleRunner, 'execute'));

        $root->mController->execute();

        $root->mController->executeView();
    }
    
    /**
     * This functions is add to 'Legacyfunction.Notifications.Select'.
     * 
     * @param XCube_RenderBuffer $render
     */
    public static function notifications_select(&$render)
    {
        require_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
        require_once XOOPS_ROOT_PATH . '/include/notification_functions.php';

        $root =& XCube_Root::getSingleton();
        $xoopsModule =& $root->mContext->mXoopsModule;
        $moduleConfig =& $root->mContext->mModuleConfig;
        $xoopsUser =& $root->mContext->mXoopsUser;

        $xoops_notification = array();
        $xoops_notification['show'] = is_object($xoopsModule) && is_object($xoopsUser) && notificationEnabled('inline') ? 1 : 0;
        
        if ($xoops_notification['show']) {
            $root->mLanguageManager->loadPageTypeMessageCatalog('notification');
            $categories =& notificationSubscribableCategoryInfo();
            $event_count = 0;
            if (!empty($categories)) {
                $notification_handler =& xoops_gethandler('notification');
                foreach ($categories as $category) {
                    $section['name'] = $category['name'];
                    $section['title'] = $category['title'];
                    $section['description'] = $category['description'];
                    $section['itemid'] = $category['item_id'];
                    $section['events'] = array();
                    $subscribed_events =& $notification_handler->getSubscribedEvents($category['name'], $category['item_id'], $xoopsModule->get('mid'), $xoopsUser->get('uid'));
                    foreach (notificationEvents($category['name'], true) as $event) {
                        if (!empty($event['admin_only']) && !$xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
                            continue;
                        }
                        if (!empty($event['invisible'])) {
                            continue;
                        }
                        $subscribed = in_array($event['name'], $subscribed_events) ? 1 : 0;
                        $section['events'][$event['name']] = array('name'=>$event['name'], 'title'=>$event['title'], 'caption'=>$event['caption'], 'description'=>$event['description'], 'subscribed'=>$subscribed);
                        $event_count ++;
                    }
                    $xoops_notification['categories'][$category['name']] = $section;
                }
                $xoops_notification['target_page'] = "notification_update.php";
                $xoops_notification['redirect_script'] = xoops_getenv('PHP_SELF');
                
                $render->setAttribute('editprofile_url', XOOPS_URL . '/edituser.php?uid=' . $xoopsUser->getShow('uid'));

                switch ($xoopsUser->getVar('notify_method')) {
                case XOOPS_NOTIFICATION_METHOD_DISABLE:
                    $render->setAttribute('user_method', _NOT_DISABLE);
                    break;
                case XOOPS_NOTIFICATION_METHOD_PM:
                    $render->setAttribute('user_method', _NOT_PM);
                    break;
                case XOOPS_NOTIFICATION_METHOD_EMAIL:
                    $render->setAttribute('user_method', _NOT_EMAIL);
                    break;
                }
            } else {
                $xoops_notification['show'] = 0;
            }
            if ($event_count == 0) {
                $xoops_notification['show'] = 0;
            }
        }
        
        $render->setAttribute('xoops_notification', $xoops_notification);
    }
    
    /**
     * This member function is added to 'User_UserViewAction.GetUserPosts'.
     * Recount posts of $xoopsUser in the comment system.
     * 
     * @static
     */
    public static function recountPost(&$posts, $xoopsUser)
    {
        $handler =& xoops_gethandler('comment');
        $criteria =new Criteria('com_uid', $xoopsUser->get('uid'));
        $posts += $handler->getCount($criteria);
    }
}
