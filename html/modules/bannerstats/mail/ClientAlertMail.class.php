<?php
/**
 * Bannerstats Client Alert Email - Module for XCL
 * Constructor for client alert emails
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


class Bannerstats_ClientAlertMail extends XCube_UserNotificationMailBuilder
{
    protected string $_clientEmail;
    protected string $_clientName;
    protected string $_templateName;
    protected string $_subjectKey;
    protected array $_subjectArgs = [];

    /**
     * @var callable|null
     */
    protected $_bodyVariablesCallback = null;

    /**
     * Constructor for client alert emails.
     *
     * @param string   $clientEmail           The client's email address.
     * @param string   $clientName            The client's name.
     * @param string   $templateName          The name of the mail template file (e.g., 'client_low_impressions.tpl').
     * @param string   $subjectKey            The language constant key for the email subject.
     * @param array    $subjectArgs           Optional arguments for sprintf, to be used with the subject string.
     * @param callable|null $bodyVariablesCallback Add specific template variables. Signature: function($contextObject): array
     */
    public function __construct(
        string $clientEmail,
        string $clientName,
        string $templateName,
        string $subjectKey,
        array $subjectArgs = [],
        ?callable $bodyVariablesCallback = null 
    ) {
        parent::__construct('bannerstats'); 
        $this->_clientEmail = $clientEmail;
        $this->_clientName = $clientName;
        $this->_templateName = $templateName;
        $this->_subjectKey = $subjectKey;
        $this->_subjectArgs = $subjectArgs;
        $this->_bodyVariablesCallback = $bodyVariablesCallback;
    }

    /**
     * Sets the recipient of the email (banner client)
     * This method is called by the XCube_MailDirector
     *
     * @param mixed $object
     * @param array $moduleConfig
     */
    public function setToUsers($object, $moduleConfig)
    {
        if (!empty($this->_clientEmail)) {
            $this->mMailer->setToEmails($this->_clientEmail, $this->_clientName);
        } else {
            $bannerId = ($object && ($object instanceof Bannerstats_BannerObject || $object instanceof Bannerstats_BannerfinishObject)) ? $object->get('bid') : 'N/A';
            //error_log("Bannerstats_ClientAlertMail: No client email provided for notification. Banner ID context: " . $bannerId);
        }
    }

    /**
     * Sets the template file name for the email
     * This method is called by the XCube_MailDirector
     */
    public function setTemplateName()
    {
        $this->mMailer->setTemplate($this->_templateName);
    }

    /**
     * Sets the subject of the email
     * This method is called by the XCube_MailDirector
     *
     * @param mixed $object
     * @param array $xoopsConfig
     */
    public function setSubject($object, $xoopsConfig)
    {
        $subjectString = defined($this->_subjectKey) ? constant($this->_subjectKey) : $this->_subjectKey;
        if (!empty($this->_subjectArgs)) {
            $this->mMailer->setSubject(vsprintf($subjectString, $this->_subjectArgs));
        } else {
            $this->mMailer->setSubject($subjectString);
        }
    }

    /**
     * Sets the body of the email by assigning variables to the template
     * This method is called by the XCube_MailDirector
     *
     * @param mixed $object
     * @param array $xoopsConfig
     */
    public function setBody($object, $xoopsConfig)
    {
        parent::setBody($object, $xoopsConfig); 
        // Sets common vars and user vars (X_SITENAME, X_UNAME etc.)
        // Assign common client and banner-related variables
        $this->mMailer->assign('CLIENT_NAME', $this->_clientName); // Passed in constructor

        if ($object instanceof Bannerstats_BannerObject || $object instanceof Bannerstats_BannerfinishObject) {
            $this->mMailer->assign('BANNER_ID', $object->get('bid'));
            $this->mMailer->assign('BANNER_NAME', $object->getShow('name'));
            // View Statistics (General)
            $this->mMailer->assign('CLIENT_STATS_URL', XOOPS_URL . '/modules/bannerstats/index.php?action=Stats');
            // Request Support
            $this->mMailer->assign('CLIENT_SUPPORT', XOOPS_URL . '/modules/bannerstats/index.php?action=RequestSupport');
        }

        // Call the custom callback to assign more specific template variables
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
