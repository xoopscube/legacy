<?php
/**
 * Bannerstats Admin Alert Email - Module for XCL
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    v2.5.0 Release XCL 
 * @link       http://github.com/xoopscube/
 **/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_MailBuilder.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/class/handler/Banner.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/class/BannerClient.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/class/handler/BannerFinish.class.php';


class Bannerstats_AdminAlertMail extends XCube_AdminNotificationMailBuilder
{
    protected string $_templateName;
    protected string $_subjectKey; // Language constant for the subject
    protected array $_subjectArgs = []; // Arguments for sprintf for the subject
    
    /**
     * @var callable|null 
     */
    protected $_bodyVariablesCallback = null;

    public function __construct(
        string $templateName,
        string $subjectKey,
        array $subjectArgs = [],
        ?callable $bodyVariablesCallback = null
    ) {
        parent::__construct('bannerstats');
        $this->_templateName = $templateName;
        $this->_subjectKey = $subjectKey;
        $this->_subjectArgs = $subjectArgs;
        $this->_bodyVariablesCallback = $bodyVariablesCallback;
    }

    /**
     * Sets the recipient(s) for the admin notification.
     * This method overrides the parent to prioritize the module's specific admin email config.
     *
     * @param mixed $object     (e.g., Bannerstats_BannerObject)
     * @param array $moduleConfig
     */
    public function setToUsers($object, $moduleConfig)
    {
        // priority module's 'admin_alert_email' configuration
        if (!empty($moduleConfig['admin_alert_email'])) {
            $this->mMailer->setToEmails($moduleConfig['admin_alert_email']);
        } else {
            // If module-specific email is not set, fall back to the parent's behavior
            // (which typically sends to admin group or site admin email)
            $root = XCube_Root::getSingleton();
            $siteAdminEmail = $root->mContext->mXoopsConfig['adminmail'] ?? null;
            if ($siteAdminEmail) {
                $this->mMailer->setToEmails($siteAdminEmail);
            } else {

                //error_log("Bannerstats_AdminAlertMail: No admin recipient email could be determined (module config empty, site admin email empty).");
            }
            // Alternatively, to call the parent's logic
            // parent::setToUsers($object, $moduleConfig);
        }
    }

    public function setTemplateName()
    {
        $this->mMailer->setTemplate($this->_templateName);
    }

    public function setSubject($object, $xoopsConfig)
    {
        $subjectString = defined($this->_subjectKey) ? constant($this->_subjectKey) : $this->_subjectKey;
        if (!empty($this->_subjectArgs)) {
            $this->mMailer->setSubject(vsprintf($subjectString, $this->_subjectArgs));
        } else {
            $this->mMailer->setSubject($subjectString);
        }
    }

    public function setBody($object, $xoopsConfig)
    {
        parent::setBody($object, $xoopsConfig); 

        // $object is an instance of Bannerstats_BannerObject
        if (!($object instanceof Bannerstats_BannerObject)) {
            // Log an error or handle appropriately if the object is not what's expected
            // error_log("AdminAlertMail::setBody - Expected Bannerstats_BannerObject, got " . gettype($object));
            $this->mMailer->setBody("Error: Could not load banner details for this notification.");
            return;
        }

        // Assign variables common to most banner-related admin alerts
        if ($object instanceof Bannerstats_BannerObject || $object instanceof Bannerstats_BannerfinishObject) {
            $this->mMailer->assign('BANNER_ID', $object->get('bid'));
            $this->mMailer->assign('BANNER_NAME', $object->getShow('name')); // Use getShow() for display

            $clientName = Bannerstats_BannerHandler::DEFAULT_CLIENT_NAME;
            $clientEmail = 'N/A'; // Default if not found

            // load client information
            $clientToUse = null;
            if (isset($object->mClient) && $object->mClient instanceof Bannerstats_BannerclientObject) {
                $clientToUse = $object->mClient;
            } elseif (method_exists($object, 'loadClient')) {
                $object->loadClient(); // client is loaded on the banner/bannerfinish object
                if (isset($object->mClient) && $object->mClient instanceof Bannerstats_BannerclientObject) {
                    $clientToUse = $object->mClient;
                }
            } elseif (($object instanceof Bannerstats_BannerfinishObject || $object instanceof Bannerstats_BannerObject) && $object->get('cid') > 0) {
                // Fallback: load client directly if BannerfinishObject also has a 'cid'
                $clientHandler = xoops_getmodulehandler('banner', 'bannerstats');
                if ($clientHandler) {
                    $clientToUse = $clientHandler->get($object->get('cid'));
                }
            }

            if ($clientToUse instanceof Bannerstats_BannerclientObject) {
                $clientName = $clientToUse->getShow('name');
                $clientEmail = $clientToUse->get('email');
            }
            $this->mMailer->assign('CLIENT_NAME', $clientName);
            $this->mMailer->assign('CLIENT_EMAIL', $clientEmail); // This will replace {CLIENT_EMAIL}

            // Common URLs (can be overridden by callback if needed for more specific links)
            $this->mMailer->assign('CLIENT_STATS_URL', XOOPS_URL . '/modules/bannerstats/index.php?action=Stats');
            // A generic admin link; specific links should come from the callback
            $this->mMailer->assign('ADMIN_BANNER_EDIT_URL', XOOPS_URL . '/modules/bannerstats/admin/index.php?action=BannerEdit&bid=' . $object->get('bid'));
        }

        // Execute the callback for alert-specific variables
        // The callback is responsible for providing variables like IMPRESSIONS_SERVED, FINISH_REASON, etc.
        if (is_callable($this->_bodyVariablesCallback)) {
            // Pass $this->mModuleConfig if the callback needs module settings
            $customVars = call_user_func($this->_bodyVariablesCallback, $object, $this->mModuleConfig);
            if (is_array($customVars)) {
                foreach ($customVars as $key => $value) {
                    $this->mMailer->assign($key, $value);
                }
            }
        }

        // Mail body content will be generated from the .tpl file
        // using these assigned variables when $this->mMailer->send() is called.
        // No need to call $this->mMailer->setBody() with a string here if using templates.
    }
}
