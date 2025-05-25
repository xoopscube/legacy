<?php
/**
 * Bannerstats - Module for XCL
 * LoginAction: Displays the client login form and handles login attempts.
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

require_once dirname(__DIR__) . '/class/BannerClientSession.class.php';

class Bannerstats_LoginAction
{
    private string $errorMessage = '';
    private string $username = ''; // To repopulate the form on error
    private string $moduleDirname;

    public function __construct()
    {
        $this->moduleDirname = basename(dirname(dirname(__FILE__))); // Should resolve to 'bannerstats'
    }

    public function getPageTitle(): string
    {
        // Consider loading language constants for titles, e.g., _MD_BANNERSTATS_CLIENT_LOGIN_TITLE
        return "Client Login - Banner Statistics";
    }

    /**
     * Handles POST request for login.
     * @param XCube_Controller $controller
     * @param XoopsTpl         $xoopsTpl
     * @return string|null Template name to render, or null if redirected.
     */
    public function execute(XCube_Controller $controller, XoopsTpl $xoopsTpl): ?string
    {
        $root = XCube_Root::getSingleton();

        // 1. CSRF Token Validation
        if (is_object($root->mContext) &&
            property_exists($root->mContext, 'mSecurity') &&
            is_object($root->mContext->mSecurity)) {
            if (!$root->mContext->mSecurity->check()) {
                $this->errorMessage = "Invalid security token. Please try submitting the form again."; // Use language constant
                // Log this attempt
                error_log("Bannerstats_LoginAction: CSRF token check failed for login attempt.");
                // It's often better to redirect back to the form with an error message
                // rather than just showing the error on a potentially blank page or different context.
                // For simplicity here, we'll assign the error and re-render the login form.
                $xoopsTpl->assign('errorMessage', $this->errorMessage);
                $this->prepareLoginView($xoopsTpl); // Prepare common template vars for login view
                return 'bannerstats_login.html';
            }
        } else {
            // If security context isn't available, this is a more serious configuration issue.
            // For now, we'll proceed but log it. In a production environment, you might deny the login.
            error_log("Bannerstats_LoginAction: CSRF token could not be validated because security context is unavailable.");
        }

        // 2. Get login credentials from POST
        $login = isset($_POST['login']) ? trim($_POST['login']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : ''; // Matches form input name
        
        $this->username = $login; // For re-populating form on error

        // 3. Basic validation
        if (empty($login) || empty($password)) {
            $this->errorMessage = "Login and Password are required."; // Use language constant
            $xoopsTpl->assign('errorMessage', $this->errorMessage);
            $this->prepareLoginView($xoopsTpl);
            return 'bannerstats_login.html';
        }

        // 4. Ensure BannerClientSession class is loaded (though already required at the top)
        if (!class_exists('BannerClientSession')) {
            $this->errorMessage = "System error: Session handler not available."; // Use language constant
            $xoopsTpl->assign('errorMessage', $this->errorMessage);
            error_log("Bannerstats_LoginAction: BannerClientSession class not found during execute.");
            $this->prepareLoginView($xoopsTpl);
            return 'bannerstats_login.html';
        }

        // 5. Attempt login
        if (BannerClientSession::login($login, $password)) {
            // Login successful, handle redirection
            $return_url = isset($_POST['return_url']) ? trim($_POST['return_url']) : '';
            if (!empty($return_url) && $this->isValidReturnUrl($return_url)) {
                $controller->executeForward($return_url);
            } else {
                $controller->executeForward(XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=Stats");
            }
            return null; // Indicates redirection handled
        } else {
            // Login failed
            $this->errorMessage = "Invalid login or password."; // Use language constant
            $xoopsTpl->assign('errorMessage', $this->errorMessage);
            $this->prepareLoginView($xoopsTpl);
            return 'bannerstats_login.html';
        }
    }

    /**
     * Handles GET request for the login page.
     * @param XCube_Controller $controller
     * @param XoopsTpl         $xoopsTpl
     * @return string|null Template name to render, or null if redirected.
     */
    public function getDefaultView(XCube_Controller $controller, XoopsTpl $xoopsTpl): ?string
    {
        // Ensure BannerClientSession class is loaded
        if (!class_exists('BannerClientSession')) {
            $this->errorMessage = "System error: Session manager unavailable."; // Use language constant
            $xoopsTpl->assign('errorMessage', $this->errorMessage);
            error_log("Bannerstats_LoginAction: BannerClientSession class not found during getDefaultView.");
            $this->prepareLoginView($xoopsTpl); // Prepare common vars even for this error
            return 'bannerstats_login.html';
        }

        // If already authenticated as a banner client, redirect to stats
        if (BannerClientSession::isAuthenticated()) {
            $controller->executeForward(XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=Stats");
            return null; // Indicates redirection handled
        }

        // Get error message from GET parameter if redirected (e.g., from a failed AuthenticateAction if it were used)
        $error_message_key = isset($_GET['error']) ? trim($_GET['error']) : '';
        if (!empty($error_message_key)) {
            $this->handleGetErrorMessages($error_message_key);
        }
        
        $this->prepareLoginView($xoopsTpl);
        return 'bannerstats_login.html';
    }

    /**
     * Helper function to prepare common variables for the login view.
     * @param XoopsTpl $xoopsTpl
     */
    private function prepareLoginView(XoopsTpl $xoopsTpl): void
    {
        $xoopsTpl->assign('pageTitle', $this->getPageTitle()); // Assign page title
        $xoopsTpl->assign('errorMessage', $this->errorMessage);
        $xoopsTpl->assign('username', htmlspecialchars($this->username, ENT_QUOTES));
        $xoopsTpl->assign('contactLink', XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=RequestSupport");
        
        $return_url = isset($_REQUEST['return_url']) ? trim($_REQUEST['return_url']) : ''; // From GET or POST
        $xoopsTpl->assign('return_url', htmlspecialchars($return_url, ENT_QUOTES));

        // Add CSRF token for the login form
        if (class_exists('XCube_Root')) {
            $root = XCube_Root::getSingleton();
            if (is_object($root->mContext) && 
                property_exists($root->mContext, 'mSecurity') && 
                is_object($root->mContext->mSecurity)) {
                 $token = $root->mContext->mSecurity->createToken();
                 $xoopsTpl->assign('xoops_token_name', $token->getTokenName());
                 $xoopsTpl->assign('xoops_token_value', $token->getTokenValue());
            } else {
                error_log("Bannerstats_LoginAction: Could not generate CSRF token. XCube_Security object or mSecurity property not found in context.");
                $xoopsTpl->assign('xoops_token_name', 'XOOPS_TOKEN_REQUEST'); 
                $xoopsTpl->assign('xoops_token_value', ''); 
            }
        }
    }

    /**
     * Helper function to handle error messages passed via GET.
     * @param string $error_message_key
     */
    private function handleGetErrorMessages(string $error_message_key): void
    {
        $root = XCube_Root::getSingleton();
        $languageManager = $root->mLanguageManager;
        $defaultMessages = [
            'token'    => "Invalid security token. Please try again.",
            'required' => "Login and password are required.",
            'invalid'  => "Invalid login or password."
        ];

        if ($languageManager) { 
            $languageManager->loadModuleMessageCatalog($this->moduleDirname);
            $this->errorMessage = $languageManager->getText('_MD_BANNERSTATS_ERROR_' . strtoupper($error_message_key), $defaultMessages[$error_message_key] ?? 'An unknown error occurred.');
        } else {
            $this->errorMessage = $defaultMessages[$error_message_key] ?? 'An unknown error occurred.';
        }
    }

    /**
     * Validates if the return_url is safe to redirect to.
     * @param string $url
     * @return bool
     */
    private function isValidReturnUrl(string $url): bool
    {
        if (empty($url)) {
            return false;
        }
        // Check if it's a relative URL starting with / or an absolute URL for the current site
        if ((strpos($url, '/') === 0 && strpos($url, '//') !== 0 && strpos($url, '\\\\') !== 0) || 
            (filter_var($url, FILTER_VALIDATE_URL) && strpos($url, XOOPS_URL) === 0)) {
            // Further check to prevent redirection to external sites if XOOPS_URL is just '/'
            if (XOOPS_URL === '/' && strpos($url, '//') === 0) {
                return false;
            }
            return true;
        }
        return false;
    }
}
