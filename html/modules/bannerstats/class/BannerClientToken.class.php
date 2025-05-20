<?php
// html/modules/bannerstats/class/BannerClientToken.class.php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

// Ensure BannerClientSession is available as it's a dependency
require_once __DIR__ . '/BannerClientSession.class.php';


class BannerClientToken
{
    /**
     * Creates a token for a specific action.
     * @param string $actionIdentifier A unique name for the action (e.g., 'EmailStats', 'ChangeUrl_BID')
     * @return string The generated token, or an empty string if session token is not available.
     */
    public static function create(string $actionIdentifier): string
    {
        $sessionToken = BannerClientSession::getSessionToken();
        if (!$sessionToken) {
            // This should ideally not happen if the client is authenticated
            // and BannerClientSession is working correctly.
            return '';
        }
        // Combine session token, action identifier, and a site-specific salt for uniqueness.
        // XOOPS_SALT is a good candidate if available and consistent.
        $salt = defined('XOOPS_SALT') ? XOOPS_SALT : 'a_fallback_bannerstats_secret_salt';
        return hash_hmac('sha256', $sessionToken . $actionIdentifier, $salt);
    }

    /**
     * Validates a submitted token against an expected token for an action.
     * @param string $submittedToken The token received from the user's request.
     * @param string $actionIdentifier The unique name for the action.
     * @return bool True if the token is valid, false otherwise.
     */
    public static function validate(string $submittedToken, string $actionIdentifier): bool
    {
        $sessionToken = BannerClientSession::getSessionToken();
        if (!$sessionToken || empty($submittedToken)) {
            return false;
        }
        $salt = defined('XOOPS_SALT') ? XOOPS_SALT : 'a_fallback_bannerstats_secret_salt';
        $expectedToken = hash_hmac('sha256', $sessionToken . $actionIdentifier, $salt);
        
        return hash_equals($expectedToken, $submittedToken);
    }

    /**
     * Generates an HTML hidden input field with the token.
     * @param string $actionIdentifier The unique name for the action.
     * @return string HTML string for the hidden input.
     */
    public static function getHtml(string $actionIdentifier): string
    {
        $token = self::create($actionIdentifier);
        return "<input type='hidden' name='bstoken' value='" . htmlspecialchars($token, ENT_QUOTES) . "'>";
    }

    /**
     * Generates a URL query string part for the token.
     * @param string $actionIdentifier The unique name for the action.
     * @return string URL query string part (e.g., "bstoken=xxxxxxxx").
     */
    public static function getUrlQuery(string $actionIdentifier): string
    {
        $token = self::create($actionIdentifier);
        return "bstoken=" . rawurlencode($token);
    }
}
