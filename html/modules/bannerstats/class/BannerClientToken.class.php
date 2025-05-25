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

require_once __DIR__ . '/BannerClientSession.class.php';


class BannerClientToken
{
    public static function create(string $actionIdentifier): string
    {
        $sessionToken = BannerClientSession::getSessionToken();
        if (!$sessionToken) {
            return '';
        }
        $salt = defined('XOOPS_SALT') ? XOOPS_SALT : 'a_fallback_bannerstats_secret_salt';
        return hash_hmac('sha256', $sessionToken . $actionIdentifier, $salt);
    }

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

    public static function getHtml(string $actionIdentifier): string
    {
        $token = self::create($actionIdentifier);
        return "<input type='hidden' name='bstoken' value='" . htmlspecialchars($token, ENT_QUOTES) . "'>";
    }

    public static function getUrlQuery(string $actionIdentifier): string
    {
        $token = self::create($actionIdentifier);
        return "bstoken=" . rawurlencode($token);
    }
}
