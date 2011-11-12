<?php
/**
 * Discovery and redirect a user to OP for authentication.
 * @version $Rev$
 * @link $URL$
 */
// Save original 'REQUEST_URI'.
define('ORIGINAL_REQUEST_URI', @$_SERVER['REQUEST_URI']);

require '../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/modules/openid/class/utils.php';
Openid_Utils::reset();

$request =& Openid_Utils::load('context');
$isExistFrompage = $request->accept('frompage', 'string', 'request');
$error = null;

do {
	if (! $request->accept('openid_identifier', 'string', 'request')
		||
		! preg_match('/[.@=]/', $request->get('openid_identifier'))
	) {
        $error = 'Expected an OpenID URL.';
        break;
    }

    //Get OP Endpoint URL
    /* @var $library Openid_Library */
    $library =& Openid_Utils::load('library');
    if (!$library->discover($request->get('openid_identifier'))) {
        $error = $library->getError();
        break;
    }

    //execute post-discovery filter if any extension exist
    $extension =& Openid_Utils::load('extension');
    $ret = $extension->execute('preFilter', $library->getAuthRequest());
    if ($ret === true) {
        //skip filter
    } else if ($ret === false) {
    	$error = $extension->getError();
        break;
    } else {
        //Check OP Endpoint URL
        $filter =& Openid_Utils::load('filter');
        if (!$filter->validateEndpoint($library->getEndpoint())) {
            $error = _MD_OPENID_ERROR_MAYNOT . $filter->getError();
            break;
        }
    }

    // Redirect the user to the OpenID server for authentication.
    // Store the token for this authentication so we can verify the
    // response.
    if ($library->buildRedirect()) {
        if ($isExistFrompage) {
            $parsed = parse_url(XOOPS_URL);
            setcookie('openid_frompage', $request->get('frompage'),
                    time() + 300, @$parsed['path'] . '/');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($library->isOpenidType2()) {
                // For OpenID 2, use a javascript form
                // to send a POST request to the server.
                echo $library->autoSubmitHTML();
            } else {
                // For OpenID 1, send a redirect.
                header('Location: ' . $library->getRedirectUrl());
            }
        } else {
            //Confirm the redirecting to endpoint
            if ($library->isOpenidType2()) {
                $redirect = $library->getSubmitForm(_GO);
            } else {
                $redirect = '<form method="get" action="' . $library->getRedirectUrl() . '">'
                          . '<input type="submit" value="' . _GO . '" /></form>';
            }
            echo '<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=' . _CHARSET . '" />
<title>' . _MD_OPENID_MESSAGE_REDIRECT_TITLE . '</title>
</head>
<body>' . sprintf(_MD_OPENID_MESSAGE_CONFIRM,
                htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES),
                htmlspecialchars($request->get4show('openid_identifier'), ENT_QUOTES),
                htmlspecialchars($library->getEndpoint(), ENT_QUOTES)
            ) . '<br /><br />' . $redirect . '
<input type="button" value="' . _CANCEL . '" onclick="javascript:history.back()" />
</body></html>';
        }
        exit();
    } else {
        $error = $library->getError();
    }
} while (false);

// Display form for inputting "User-supplied Identifier" again.
$url = XOOPS_URL . '/modules/openid/index.php';
if ($isExistFrompage) {
    $url .= '?frompage=' . rawurlencode($request->get('frompage'));
}
redirect_header($url, 5, $error);
?>