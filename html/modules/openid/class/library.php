<?php
/**
 * Class for handling PHP OpenID library by JanRain, Inc.
 *
 * @version $Rev$
 * @link $URL$
 */
class OpenID_Library
{
    /**
     * @var array
     */
    var $pape_policy_uris;

    /**
     * @var array
     */
    var $sregFields;

    /**
     * Object for auth request to OP
     *
     * @var Auth_OpenID_AuthRequest
     */
    var $_auth_request;

    /**
     * @var string
     */
    var $form_html;

    /**
     * @var string
     */
    var $redirect_url;

    /**
     * constructor
     */
    function OpenID_Library()
    {
        if (!@$GLOBALS['xoopsModuleConfig']['openid_rand_souce']) {
            define('Auth_OpenID_RAND_SOURCE', null);
        } else if (!@is_readable($GLOBALS['xoopsModuleConfig']['openid_rand_souce'])) {
            redirect_header(XOOPS_URL, 2, 'Please set rand_source on admin panel');
        } else {
            define('Auth_OpenID_RAND_SOURCE', $GLOBALS['xoopsModuleConfig']['openid_rand_souce']);
        }
        if (@$GLOBALS['xoopsModuleConfig']['curl_cainfo_file']) {
        	$cainfo = str_replace('XOOPS_ROOT_PATH', XOOPS_ROOT_PATH, $GLOBALS['xoopsModuleConfig']['curl_cainfo_file']);
            define('Auth_OpenID_CURLOPT_CAINFO_FILE', $cainfo);
        }

        if (version_compare(PHP_VERSION, '5.0.0', '>=')) {
        	$path_extra = XOOPS_ROOT_PATH . '/modules/openid/class/php5-openid';
        } else {
        	$path_extra = XOOPS_ROOT_PATH . '/modules/openid/class/php-openid';
        }
        $path = ini_get('include_path');
        $path = $path_extra . PATH_SEPARATOR . $path;
        ini_set('include_path', $path);

        $this->doIncludes();

        // @todo Set on module config
        $this->sregFields = array(
            'nickname',
            'fullname',
            'email',
            'timezone'
        );
        $this->pape_policy_uris = array(
              PAPE_AUTH_MULTI_FACTOR_PHYSICAL,
              PAPE_AUTH_MULTI_FACTOR,
              PAPE_AUTH_PHISHING_RESISTANT
        );
    }

    /**
     * This is where the example will store its OpenID information.
     * You should change this path if you want the example store to be
     * created elsewhere.  After you're done playing with the example
     * script, you'll have to remove this directory manually.
     * @return Auth_OpenID_Store
     */
    function &getStore()
    {
        require_once 'xoopsDBconnection.php';
        require_once 'ExMySQLStore.php';

        $connection = new OpenID_XoopsDBconnection($GLOBALS['xoopsDB']);
        $store = new OpenID_ExMySQLStore($connection,
                                $GLOBALS['xoopsDB']->prefix('openid_assoc'),
                                $GLOBALS['xoopsDB']->prefix('openid_nonce')
                                );
        return $store;
    }

    /**
     * Get Return URL
     *
     * @return string
     */
    function getReturnTo()
    {
        if (defined('OPENID_RETURN_PATH')) {
            return OPENID_RETURN_PATH;
        } else {
            return $this->getMyUrlPath() . '/finish_auth.php';
        }
    }

    /**
     * Get Trust Path
     *
     * @return string
     */
    function getTrustRoot()
    {
        return XOOPS_URL . '/modules/openid/';
    }

    /**
     * Get Error message
     *
     * @return string
     */
    function getError()
    {
        return $this->_error;
    }

    function doIncludes()
    {
        /**
         * Require the OpenID consumer code.
         */
        require_once "Auth/OpenID/Consumer.php";

        /**
         * Require the Simple Registration extension API.
         */
        require_once "Auth/OpenID/SReg.php";

        /**
         * Require the PAPE extension module.
         */
        require_once "Auth/OpenID/PAPE.php";
    }

    /**
     * Create a consumer object using the store object created
     * earlier.
     * @return Auth_OpenID_Consumer
     */
    function &getConsumer() {
        $store =& $this->getStore();

        require_once 'exConsumer.php';
        $consumer = new OpenID_ExConsumer($store);
        return $consumer;
    }

    /**
     * Discover OP Endpoint URL
     *
     * @param string $openid_identifier
     * @return boolean
     */
    function discover($openid_identifier)
    {
        $consumer =& $this->getConsumer();

        // Begin the OpenID authentication process.
        $this->_auth_request = $consumer->begin($openid_identifier);

        // No auth request means we can't begin OpenID.
        if (!$this->_auth_request) {
            $this->_error = "Authentication error; not a valid OpenID.";
            return false;
        }
        return true;
    }

    /**
     * Get authentication request
     *
     * @return Auth_OpenID_AuthRequest
     */
    function getAuthRequest()
    {
        return $this->_auth_request;
    }

    /**
     * Get OP End point URL
     *
     * @return string
     */
    function getEndpoint()
    {
        return $this->_auth_request->endpoint->server_url;
    }

    /**
     * Build auth request
     *
     * @return mixed
     */
    function buildRedirect()
    {
        $sreg_request = Auth_OpenID_SRegRequest::build(
                                         // Required
                                         null,
                                         // Optional
                                         $this->sregFields);

        if ($sreg_request) {
            $this->_auth_request->addExtension($sreg_request);
        }

        $pape_request = new Auth_OpenID_PAPE_Request($this->pape_policy_uris);
        if ($pape_request) {
            $this->_auth_request->addExtension($pape_request);
        }

        $this->_message = $this->_auth_request->getMessage($this->getTrustRoot(), $this->getReturnTo());
        if (Auth_OpenID::isFailure($this->_message)) {
            $this->_error = "Could not redirect to server: " . $this->_message->message;
            return false;
        } else {
            return true;
        }
    }

    /**
     * Return does IDP support OpenID type 2.0 OR not
     *
     * @return boolean
     */
    function isOpenidType2()
    {
        return !$this->_auth_request->shouldSendRedirect();
    }

    /**
     * Return a html document that will autosubmit the request to the IDP.
     *
     * @return string
     */
    function autoSubmitHTML()
    {
        return Auth_OpenID::autoSubmitHTML($this->getSubmitForm("Continue"));
    }

    /**
     * Return submited form html for OpenID 2
     *
     * @param string $submit_text
     * @return string
     */
    function getSubmitForm($submit_text)
    {
        return $this->_message->toFormMarkup($this->getEndpoint(), null, $submit_text);
    }

    /**
     * Return redirect url for OpenID 1
     *
     * @return string
     */
    function getRedirectUrl()
    {
        return $this->_message->toURL($this->getEndpoint());
    }

    /**
     * Get User Claimed ID
     *
     * @return string
     */
    function getClaimedId()
    {
        return $this->_auth_request->endpoint->claimed_id;
    }

    /**
     * Check the server's response
     *
     * @param object $container
     * @return boolean
     */
    function finish_auth(&$container)
    {
        $consumer =& $this->getConsumer();

        // Complete the authentication process using the server's
        // response.
        $return_to = $this->getReturnTo();
        $response = $consumer->complete($return_to);

        // Check the response status.
        if ($response->status == Auth_OpenID_SUCCESS) {
            // This means the authentication succeeded; extract the
            // identity URL and Simple Registration data (if it was
            // returned).
            $container->set('claimed_id',  $response->identity_url);
            $container->set('displayId',   $response->getDisplayIdentifier());
            $container->set('local_id',    $response->endpoint->getLocalID());
            $container->set('endpoint',    $response->endpoint->server_url);
            $container->set('canonicalID', $response->endpoint->canonicalID);

            $sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
            if ($sreg_resp) {
                $sreg = $sreg_resp->contents();
                foreach ($this->sregFields as $field) {
                    if (!empty($sreg[$field])) {
                        $container->set($field, $sreg[$field]);
                    }
                }
            }
            return true;

        } else if ($response->status == Auth_OpenID_CANCEL) {
            // This means the authentication was cancelled.
            $this->_error = 'Verification cancelled.';

        } else if ($response->status == Auth_OpenID_FAILURE) {
            // Authentication failed; display the error message.
            $this->_error = "OpenID authentication failed: " . $response->message;
        }
        return false;
    }

    function getMyUrlPath()
    {
        $checkPathInfo = TRUE;
        $base = preg_replace('#^(https?://[^/]+).*$#i', '$1', XOOPS_URL);
        if (empty($_SERVER['_REQUEST_URI'])) {
            if (empty($_SERVER['REQUEST_URI'])) {
                if (! ($request_uri = $_SERVER['PHP_SELF'])) {
                    $request_uri = $_SERVER['SCRIPT_NAME'];
                    $checkPathInfo = FALSE;
                }
            } else {
                $request_uri = $_SERVER['REQUEST_URI'];
            }
        } else {
            $request_uri = $_SERVER['_REQUEST_URI'];
        }
        $urlInfo = parse_url($base . $request_uri);
        $path = isset($urlInfo['path'])? $urlInfo['path'] : '';
        if ($checkPathInfo && isset($_SERVER['PATH_INFO'])) {
            $path = preg_replace('/' . preg_quote($_SERVER['PATH_INFO'], '/') . '$/', '', $path);
        }
        if (substr($path, -1) === '/') {
            $path .= 'index.php';
        }
        return $base . dirname($path);
    }

    function cleanupStore()
    {
        $store =& $this->getStore();
        $ret = $store->cleanup();
        return $ret[0] . ' nonces deleted.<br />' . $ret[1] . ' associations deleted.';
    }
}
?>