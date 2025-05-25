<?php
/**
 * Bannerstats - Module for XCL
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

// Ensure the core XCube_MailBuilder is loaded
require_once XOOPS_ROOT_PATH . '/core/XCube_MailBuilder.class.php';

// Dependent module classes
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
     * @param mixed $object      The context object (e.g., Bannerstats_BannerObject) passed from the Director.
     * @param array $moduleConfig The Bannerstats module configuration array.
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

                error_log("Bannerstats_AdminAlertMail: No admin recipient email could be determined (module config empty, site admin email empty).");
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

        if ($object instanceof Bannerstats_BannerObject || $object instanceof Bannerstats_BannerfinishObject) {
            $this->mMailer->assign('banner_bid', $object->get('bid'));
            $this->mMailer->assign('banner_name', $object->getShow('name'));

            $clientName = Bannerstats_BannerHandler::DEFAULT_CLIENT_NAME;
            if (isset($object->mClient) && $object->mClient instanceof Bannerstats_BannerclientObject && $object->mClient->get('name')) {
                $clientName = $object->mClient->getShow('name');
            } elseif ($object instanceof Bannerstats_BannerObject && method_exists($object, 'loadClient')) {
                $object->loadClient();
                if (isset($object->mClient) && $object->mClient instanceof Bannerstats_BannerclientObject && $object->mClient->get('name')) {
                    $clientName = $object->mClient->getShow('name');
                }
            }
            $this->mMailer->assign('client_name', $clientName);

            $this->mMailer->assign('stats_link', XOOPS_URL . '/modules/bannerstats/index.php?action=Stats');
            $this->mMailer->assign('admin_banner_link', XOOPS_URL . '/modules/bannerstats/admin/index.php?action=BannerEdit&bid=' . $object->get('bid'));
        }

        if (is_callable($this->_bodyVariablesCallback)) {
            $customVars = call_user_func($this->_bodyVariablesCallback, $object);
            if (is_array($customVars)) {
                foreach ($customVars as $key => $value) {
                    $this->mMailer->assign($key, $value);
                }
            }
        }
    }
}
