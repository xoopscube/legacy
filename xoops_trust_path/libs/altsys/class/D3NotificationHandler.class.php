<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * Notification handler for D3 modules
 * @package    Altsys
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

require_once XOOPS_ROOT_PATH . '/include/notification_functions.php';

class D3NotificationHandler
{

    /**
     * @param null $conn
     * @return \D3NotificationHandler
     */
    public static function getInstance($conn = null)
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new self();
        }

        return $instance;
    }


    /**
     * @param        $mydirname
     * @param string $mytrustdirname
     * @return mixed|string
     */
    public function getMailTemplateDir($mydirname, string $mytrustdirname = '')
    {
        global $xoopsConfig;

        $mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname;

        $mytrustdirpath = XOOPS_TRUST_PATH . '/modules/' . $mytrustdirname;

        $language = empty($xoopsConfig['language']) ? 'english' : $xoopsConfig['language'];

        $search_paths = [
            "$mydirpath/language/$language/mail_template/",
            "$mytrustdirpath/language/$language/mail_template/",
            "$mydirpath/language/english/mail_template/",
            "$mytrustdirpath/language/english/mail_template/",
        ];

        $mail_template_dir = "$mytrustdirpath/language/english/mail_template/";

        foreach ($search_paths as $path) {
            if (file_exists($path)) {
                $mail_template_dir = $path;

                break;
            }
        }

        return $mail_template_dir;
    }


    /**
     * @param       $mydirname
     * @param       $mytrustdirname
     * @param       $category
     * @param       $item_id
     * @param       $event
     * @param array $extra_tags
     * @param array $user_list
     * @param null  $omit_user_id
     * @return bool|void
     */
    public function triggerEvent($mydirname, $mytrustdirname, $category, $item_id, $event, array $extra_tags = [], array $user_list = [], $omit_user_id = null)
    {
        $module_handler =& xoops_gethandler('module');

        $module =& $module_handler->getByDirname($mydirname);

        $notification_handler = xoops_gethandler('notification');

        $mail_template_dir = $this->getMailTemplateDir($mydirname, $mytrustdirname);

        // calling a delegate before
        if (class_exists('XCube_DelegateUtils')) {
            $force_return = false;

            //Gigamaster fixed deprecated XCube_DelegateUtils::raiseEvent(). Use call()
	            XCube_DelegateUtils::call(
                'D3NotificationHandler.Trigger',
                new XCube_Ref($category),
                new XCube_Ref($event),
                new XCube_Ref($item_id),
                new XCube_Ref($extra_tags),
                new XCube_Ref($module),
                new XCube_Ref($user_list),
                new XCube_Ref($omit_user_id),
                $module->getInfo('notification'),
                new XCube_Ref($force_return),
                new XCube_Ref($mail_template_dir),
                $mydirname,
                $mytrustdirname
            );

            if ($force_return) {
                return;
            }
        }

        $mid = $module->getVar('mid');

        // Check if event is enabled

        $configHandler =& xoops_getHandler('config');

        $mod_config =& $configHandler->getConfigsByCat(0, $mid);

    // calling a delegate before
    if (class_exists('XCube_DelegateUtils')) {

        //Gigamaster fixed deprecated XCube_DelegateUtils::raiseEvent(). Use call()
	        XCube_DelegateUtils::call(
        	'D3NotificationHandler.Trigger',
	        new XCube_Ref($category),
	        new XCube_Ref($event),
	        new XCube_Ref($item_id),
	        new XCube_Ref($extra_tags),
	        new XCube_Ref($module),
	        new XCube_Ref($user_list),
	        new XCube_Ref($omit_user_id),
	        $module->getInfo('notification'),
	        new XCube_Ref($force_return),
	        new XCube_Ref($mail_template_dir),
	        $mydirname,
	        $mytrustdirname) ;
        if ($force_return) {
            return ;
        }
    }

        $mid = $module->getVar('mid') ;

    // Check if event is enabled
    $config_handler =& xoops_gethandler('config');
        $mod_config =& $config_handler->getConfigsByCat(0, $mid);
        if (empty($mod_config['notification_enabled'])) {
            return false;
        }
        $category_info =& notificationCategoryInfo($category, $mid);
        $event_info =& notificationEventInfo($category, $event, $mid);
        if (!in_array(notificationGenerateConfig($category_info, $event_info, 'option_name'), $mod_config['notification_events']) && empty($event_info['invisible'])) {
            return false;
        }

        if (!isset($omit_user_id)) {
            global $xoopsUser;
            if (!empty($xoopsUser)) {
                $omit_user_id = $xoopsUser->getVar('uid');
            } else {
                $omit_user_id = 0;
            }
        }
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('not_modid', (int)$mid));
        $criteria->add(new Criteria('not_category', $category));
        $criteria->add(new Criteria('not_itemid', (int)$item_id));
        $criteria->add(new Criteria('not_event', $event));
        $mode_criteria = new CriteriaCompo();
        $mode_criteria->add(new Criteria('not_mode', XOOPS_NOTIFICATION_MODE_SENDALWAYS), 'OR');
        $mode_criteria->add(new Criteria('not_mode', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE), 'OR');
        $mode_criteria->add(new Criteria('not_mode', XOOPS_NOTIFICATION_MODE_SENDONCETHENWAIT), 'OR');
        $criteria->add($mode_criteria);

        $notifications = &$notification_handler->getObjects($criteria);

        if (empty($notifications)) {
            return;
        }

        // Add some tag substitutions here

        $tags = [];

        // {X_ITEM_NAME} {X_ITEM_URL} {X_ITEM_TYPE} from lookup_func are disabled
        $tags['X_MODULE'] = $module->getVar('name', 'n');

        $tags['X_MODULE_URL'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/';
        $tags['X_NOTIFY_CATEGORY'] = $category;
        $tags['X_NOTIFY_EVENT'] = $event;

        $template = $event_info['mail_template'] . '.tpl';
        $subject = $event_info['mail_subject'];

        if ($user_list) {
            $user_list = array_flip(array_unique($user_list));
        }
        foreach ($notifications as $notification) {
            $send_uid = $notification->getVar('not_uid');
            if ((empty($omit_user_id) || $send_uid != $omit_user_id)
                && (!$user_list || isset($user_list[$send_uid]))) {
                // user-specific tags
                // $tags['X_UNSUBSCRIBE_URL'] = 'TODO';

                // TODO: don't show unsubscribe link if it is 'one-time' ??

                $tags['X_UNSUBSCRIBE_URL'] = XOOPS_URL . '/notifications.php';

                $tags = array_merge($tags, $extra_tags);

                $notification->notifyUser($mail_template_dir, $template, $subject, $tags);
            }
        }
    }
}
