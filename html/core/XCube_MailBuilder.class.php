<?php
/**
 * XCube Core Mail Builder System
 *
 * Provides a framework for modules to build and send notification emails.
 * Modules should extend these classes to implement module-specific details
 * like email subjects, body content, and recipient lists.
 * Based on RegistMailBuilder by Kazuhisa Minato aka minahito, Core developer
 * 
 * @package    XCube
 * @subpackage Mail
 * @author     Nuno Luciano aka gigamaster
 * @version    v1.1
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    BSD
 *
 * ## How to Use This in Your Module
 *
 * 1. Create a module-specific mail builder by extending one of the core builder classes
 *    (e.g., `XCube_AdminNotificationMailBuilder` or `XCube_UserNotificationMailBuilder`).
 *
 *    Example for an admin notification:
 *    ```php
 *    // /modules/yourmodule/class/YourModuleAdminMailBuilder.class.php
 *    class YourModule_AdminMailBuilder extends XCube_AdminNotificationMailBuilder
 *    {
 *        public function __construct()
 *        {
 *            // Pass your module's dirname to the parent constructor
 *            parent::__construct('yourmodule');
 *        }
 *
 *        // REQUIRED: Implement setSubject using your module's language constants
 *        public function setSubject($object, $xoopsConfig)
 *        {
 *            // _MYMOD_ADMIN_NOTIFY_SUBJECT would be defined in yourmodule's language files
 *            $this->mMailer->setSubject(
 *                sprintf(
 *                    _MYMOD_ADMIN_NOTIFY_SUBJECT,
 *                    $object->getVar('title'), // Example
 *                    $xoopsConfig['sitename']
 *                )
 *            );
 *        }
 *
 *        // OPTIONAL: Override setBody to add more specific variables
 *        public function setBody($object, $xoopsConfig)
 *        {
 *            parent::setBody($object, $xoopsConfig); // Sets common XOOPS and admin vars
 *
 *            // Add module-specific variables for your template
 *            $this->mMailer->assign('MYMODULE_FIELD', $object->getVar('my_field'));
 *            $this->mMailer->assign('MYMODULE_LINK', XOOPS_URL . '/modules/yourmodule/view.php?id=' . $object->getVar('id'));
 *
 *            // You can also override variables set by parent::setBody if needed
 *            // For example, if the default ADMIN_OBJECT_URL is not suitable:
 *            // $this->mMailer->assign('ADMIN_OBJECT_URL', XOOPS_URL . '/modules/' . $this->mModuleName . '/admin/index.php?action=custom_view&id=' . $object->getVar('id'));
 *        }
 *
 *        // OPTIONAL: Override setToUsers if the default admin group logic is not sufficient
 *        // public function setToUsers($object, $moduleConfig)
 *        // {
 *        //     // Custom logic to set recipients, or call parent for default behavior
 *        //     parent::setToUsers($object, $moduleConfig);
 *        // }
 *
 *        // OPTIONAL: Override setTemplateName if 'admin_notification.tpl' is not desired
 *        // public function setTemplateName()
 *        // {
 *        //    $this->mMailer->setTemplate('my_custom_admin_template.tpl');
 *        // }
 *    }
 *    ```
 *
 * 2. Create email templates in your module's language directory:
 *    - `/modules/yourmodule/language/english/mail_template/admin_notification.tpl`
 *    - `/modules/yourmodule/language/english/mail_template/user_notification.tpl`
 *    (Or whatever names you specify in `setTemplateName()`).
 *
 * 3. In your module's action class, use the Director and your custom Builder:
 *    ```php
 *    protected function _sendAdminNotification($object) // Example method
 *    {
 *        $root = XCube_Root::getSingleton();
 *        $xoopsConfig = $root->mContext->getXoopsConfig();
 *        // Assuming $this->mModuleConfig holds your module's preferences/configs
 *        // or pass an empty array if not used by your setToUsers or other methods.
 *        $moduleConfig = $this->mModuleConfig ?? [];
 *
 *        // Create your module-specific builder
 *        $builder = new YourModule_AdminMailBuilder();
 *
 *        // Create the director
 *        $director = new XCube_MailDirector($builder, $object, $xoopsConfig, $moduleConfig);
 *        $director->constructMail(); // Changed from contruct() to avoid PHP4 constructor conflict
 *
 *        $mailer = $builder->getResult();
 *        if (!$mailer->send()) {
 *            // Log error: $mailer->getErrors();
 *            return false;
 *        }
 *        return true;
 *    }
 *    ```
 *
 * 4. Define necessary language constants in your module (e.g., `_MYMOD_ADMIN_NOTIFY_SUBJECT`).
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * Mail Director
 * Manages the mail construction process using a specific builder.
 */
class XCube_MailDirector
{
    /**
     * @var XCube_AbstractMailBuilder
     */
    protected $_mBuilder; // protected and underscore convention

    /**
     * @var mixed The object context for the mail (e.g., a content item)
     */
    protected $_mObject;

    /**
     * @var array general configuration
     */
    protected $_mXoopsConfig;

    /**
     * @var array Module-specific configuration (optional)
     */
    protected $_mModuleConfig;

    public function __construct(XCube_AbstractMailBuilder &$builder, &$object, $xoopsConfig, $moduleConfig = [])
    {
        $this->_mBuilder = $builder;
        $this->_mObject = $object;
        $this->_mXoopsConfig = $xoopsConfig;
        $this->_mModuleConfig = $moduleConfig;
    }

    /**
     * Commands the builder to construct the mail.
     * Renamed from contruct() to constructMail() to avoid potential constructor conflicts
     * and be more descriptive.
     */
    public function constructMail()
    {
        $this->_mBuilder->setTemplateDir(); // Sets the base directory for module templates
        $this->_mBuilder->setTemplateName();   // Builder defines the specific .tpl file
        $this->_mBuilder->setToUsers($this->_mObject, $this->_mModuleConfig);
        $this->_mBuilder->setFromEmail($this->_mXoopsConfig);
        $this->_mBuilder->setSubject($this->_mObject, $this->_mXoopsConfig); // Module-specific builder implements this
        $this->_mBuilder->setBody($this->_mObject, $this->_mXoopsConfig);    // Core provides base, module extends
    }
}

/**
 * Abstract Mail Builder
 * Base class for all mail builders. Defines the interface and common functionality.
 */
abstract class XCube_AbstractMailBuilder
{
    /**
     * @var XoopsMailer
     */
    public $mMailer;

    /**
     * @var string The dirname of the module using this builder.
     */
    public $mModuleName;

    public function __construct($moduleName)
    {
        $this->mMailer = getMailer(); // Use global function
        $this->mMailer->useMail();
        if (empty($moduleName)) {
            // Optional: throw an exception or log an error if moduleName is essential
            // For now, we allow it but setTemplateDir will not work as expected.
        }
        $this->mModuleName = $moduleName;
    }

    /**
     * Sets the directory for mail templates based on the module name.
     * This is a common operation.
     */
    public function setTemplateDir()
    {
        if (empty($this->mModuleName)) {
            // Cannot set template directory without a module name.
            // Consider logging this or throwing an exception if a template is expected.
            return;
        }
        $root = XCube_Root::getSingleton();
        $language = $root->mContext->getXoopsConfig('language');
        $templatePath = XOOPS_ROOT_PATH . '/modules/' . $this->mModuleName . '/language/' . $language . '/mail_template/';
        
        // Check if directory exists, otherwise Smarty might throw errors later
        if (is_dir($templatePath)) {
            $this->mMailer->setTemplateDir($templatePath);
        } else {
            // Log error or handle - template dir not found
            // For now, mailer will use its default if not set, or error if template is set later.
            error_log("XCube_AbstractMailBuilder: Template directory not found for module '{$this->mModuleName}' at '{$templatePath}'");
        }
    }

    /**
     * Sets the "From" email address and name.
     * This is a common operation.
     */
    public function setFromEmail($xoopsConfig)
    {
        $fromEmail = defined('XOOPS_NOTIFY_FROMEMAIL') ? XOOPS_NOTIFY_FROMEMAIL : ($xoopsConfig['adminmail'] ?? ''); // XOOPS 2.0.x used XOOPS_NOTIFY_FROMEMAIL
        if (empty($fromEmail) && defined('XOOPS_NOTIFICATION_FROM_EMAIL')) $fromEmail = XOOPS_NOTIFICATION_FROM_EMAIL; // XCL 2.2+
        if (empty($fromEmail)) $fromEmail = $xoopsConfig['adminmail'] ?? 'admin@example.com';


        $fromName = defined('XOOPS_NOTIFY_FROMNAME') ? XOOPS_NOTIFY_FROMNAME : ($xoopsConfig['sitename'] ?? 'XOOPS Site'); // XOOPS 2.0.x
        if (empty($fromName) && defined('XOOPS_NOTIFICATION_FROM_NAME')) $fromName = XOOPS_NOTIFICATION_FROM_NAME; // XCL 2.2+
        if (empty($fromName)) $fromName = $xoopsConfig['sitename'] ?? 'XOOPS Site';

        $this->mMailer->setFromEmail($fromEmail);
        $this->mMailer->setFromName($fromName);
    }

    /**
     * Sets the base body variables common to most emails.
     * Module-specific builders should call `parent::setBody()` and then add their own variables.
     */
    public function setBody($object, $xoopsConfig)
    {
        $this->mMailer->assign('SITENAME', $xoopsConfig['sitename'] ?? 'Your Site');
        $this->mMailer->assign('ADMINMAIL', $xoopsConfig['adminmail'] ?? 'admin@example.com');
        $this->mMailer->assign('SITEURL', XOOPS_URL . '/');

        // Standard Mail Tags (for consistency with notification system)
        $this->mMailer->assign('X_SITENAME', $xoopsConfig['sitename'] ?? 'Your Site');
        $this->mMailer->assign('X_SITEURL', XOOPS_URL . '/');
        $this->mMailer->assign('X_ADMINMAIL', $xoopsConfig['adminmail'] ?? 'admin@example.com');
        if ($this->mModuleName) {
            $this->mMailer->assign('X_MODULE', $this->mModuleName); // Or fetch module name from handler
            $this->mMailer->assign('X_MODULE_URL', XOOPS_URL . '/modules/' . $this->mModuleName . '/');
        }
    }

    /**
     * Returns the configured XoopsMailer instance.
     * @return XoopsMailer
     */
    public function &getResult()
    {
        return $this->mMailer;
    }

    /**
     * Sets the mail template file (e.g., 'admin_notification.tpl').
     * Must be implemented by custom builders.
     */
    abstract public function setTemplateName();

    /**
     * Sets the recipients of the email.
     * Must be implemented by custom builders.
     * @param mixed $object The context object.
     * @param array $moduleConfig Module-specific configuration.
     */
    abstract public function setToUsers($object, $moduleConfig);

    /**
     * Sets the subject of the email.
     * Must be implemented by custom (module-specific) builders using their own language constants.
     * @param mixed $object The context object.
     * @param array $xoopsConfig general configuration.
     */
    abstract public function setSubject($object, $xoopsConfig);
}

/**
 * Abstract Admin Notification Mail Builder
 * Provides common defaults for admin notifications.
 * Modules should extend this and implement `setSubject()`.
 */
abstract class XCube_AdminNotificationMailBuilder extends XCube_AbstractMailBuilder
{
    /**
     * Sets the default template name for admin notifications.
     * Modules can override this if needed.
     */
    public function setTemplateName()
    {
        $this->mMailer->setTemplate('admin_notification.tpl');
    }

    /**
     * Sets the default recipients to the admin group.
     * Modules can override this or use `$moduleConfig['admin_notification_group']`.
     */
    public function setToUsers($object, $moduleConfig)
    {
        $memberHandler = xoops_gethandler('member');
        $adminGroup = null;
        if (isset($moduleConfig['admin_notification_group']) && (int)$moduleConfig['admin_notification_group'] > 0) {
            $adminGroup = $memberHandler->getGroup((int)$moduleConfig['admin_notification_group']);
        }
        
        if (!is_object($adminGroup)) { // Fallback to default admin group
            $adminGroup = $memberHandler->getGroup(XOOPS_GROUP_ADMIN);
        }

        if (is_object($adminGroup)) {
            $this->mMailer->setToGroups($adminGroup);
        } else {
            // Log error: Admin group not found.
            // Consider sending to XOOPS_ADMIN_EMAIL as a last resort if no group.
            error_log("XCube_AdminNotificationMailBuilder: Admin group not found for notifications.");
        }
    }

    /**
     * Sets common body variables for admin notifications.
     * Module-specific admin builders should call `parent::setBody()` and add their own.
     */
    public function setBody($object, $xoopsConfig)
    {
        parent::setBody($object, $xoopsConfig); // Set SITENAME, SITEURL, ADMINMAIL, X_* tags

        // Add common admin-specific variables
        if (is_object($object) && method_exists($object, 'getVar') && $this->mModuleName) {
            // A generic admin URL; module might override with a more specific action
            // Assuming 'id' or use getPrimaryKey()
            $this->mMailer->assign('ADMIN_OBJECT_URL', XOOPS_URL . '/modules/' . $this->mModuleName . '/admin/index.php?action=view&id=' . $object->getVar($object->getPrimaryKey()));
            $this->mMailer->assign('OBJECT_ID', $object->getVar($object->getPrimaryKey()));
            if (method_exists($object, 'getTitle')) { // Prefer a getTitle method if available
                 $this->mMailer->assign('OBJECT_TITLE', $object->getTitle());
            } elseif ($object->getVar('title')) {
                 $this->mMailer->assign('OBJECT_TITLE', $object->getVar('title'));
            }


            if ($object->getVar('uid')) { // If the object has a user associated (e.g., submitter)
                $memberHandler = xoops_gethandler('member');
                $user = $memberHandler->getUser($object->getVar('uid'));
                if (is_object($user)) {
                    $this->mMailer->assign('SUBMITTER_USERNAME', $user->getVar('uname'));
                    $this->mMailer->assign('SUBMITTER_EMAIL', $user->getVar('email')); // Use with caution
                }
            }
        }
    }

    // Note: setSubject() is still abstract, inherited from XCube_AbstractMailBuilder.
    // The module-specific class extending this *must* implement setSubject().
}

/**
 * Abstract User Notification Mail Builder
 * Provides common defaults for user notifications.
 * Modules should extend this and implement `setSubject()` and `setToUsers()`.
 */
abstract class XCube_UserNotificationMailBuilder extends XCube_AbstractMailBuilder
{
    /**
     * Sets the default template name for user notifications.
     * Modules can override this if needed.
     */
    public function setTemplateName()
    {
        $this->mMailer->setTemplate('user_notification.tpl');
    }

    /**
     * Sets common body variables for user notifications.
     * Module-specific user builders should call `parent::setBody()` and add their own.
     */
    public function setBody($object, $xoopsConfig)
    {
        parent::setBody($object, $xoopsConfig); // Set SITENAME, SITEURL, ADMINMAIL, X_* tags

        // Add common user-specific variables
        if (is_object($object) && method_exists($object, 'getVar') && $this->mModuleName) {
            // A generic user-facing URL; module might override
            $this->mMailer->assign('USER_OBJECT_URL', XOOPS_URL . '/modules/' . $this->mModuleName . '/index.php?action=view&id=' . $object->getVar($object->getPrimaryKey()));
            $this->mMailer->assign('OBJECT_ID', $object->getVar($object->getPrimaryKey()));
             if (method_exists($object, 'getTitle')) {
                 $this->mMailer->assign('OBJECT_TITLE', $object->getTitle());
            } elseif ($object->getVar('title')) {
                 $this->mMailer->assign('OBJECT_TITLE', $object->getVar('title'));
            }
        }
    }

    // Note: setSubject() and setToUsers() are still abstract, inherited from XCube_AbstractMailBuilder.
    // The module-specific class extending this *must* implement both.
}
