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

class BannerClientSession
{
    private const SESSION_AUTH_KEY = 'bannerstats_client_auth';
    private const SESSION_CID_KEY = 'cid';
    private const SESSION_LOGIN_KEY = 'login';
    private const SESSION_TOKEN_KEY = 'token';

    /**
     * Attempts to log in a banner client.
     *
     * @param string $username The client's login username
     * @param string $password The client's plain-text password
     * @return bool True on successful login, false otherwise
     */
    public static function login(string $username, string $password): bool
    {
        global $xoopsDB;

        $username = trim($username);

        if (empty($username) || empty($password)) {
            return false;
        }

        $clientTable = $xoopsDB->prefix('bannerclient');
        $sql = sprintf(
            'SELECT cid, passwd FROM %s WHERE login = %s',
            $clientTable,
            $xoopsDB->quoteString($username)
        );

        $result = $xoopsDB->query($sql);
        if ($result && $row = $xoopsDB->fetchArray($result)) {
            if (password_verify($password, $row['passwd'])) {
                if (session_status() == PHP_SESSION_ACTIVE) {
                    session_regenerate_id(true);
                }

                $_SESSION[self::SESSION_AUTH_KEY] = [
                    self::SESSION_CID_KEY => (int)$row['cid'],
                    self::SESSION_LOGIN_KEY => $username,
                    self::SESSION_TOKEN_KEY => bin2hex(random_bytes(16))
                ];
                return true;
            }
        }
        return false;
    }

    /**
     * Logs out the current banner client.
     */
    public static function logout(): void
    {
        unset($_SESSION[self::SESSION_AUTH_KEY]);
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /**
     * Checks if a banner client is currently authenticated
     *
     * @return bool True if authenticated
     */
    public static function isAuthenticated(): bool
    {
        return isset($_SESSION[self::SESSION_AUTH_KEY][self::SESSION_CID_KEY]) &&
               $_SESSION[self::SESSION_AUTH_KEY][self::SESSION_CID_KEY] > 0;
    }

    /**
     * Gets the ID of the currently authenticated banner client
     *
     * @return int|null client ID if authenticated
     */
    public static function getClientId(): ?int
    {
        if (self::isAuthenticated()) {
            return (int)$_SESSION[self::SESSION_AUTH_KEY][self::SESSION_CID_KEY];
        }
        return null;
    }

    /**
     * Gets the login name of the currently authenticated banner client
     *
     * @return string|null client login name if authenticated
     */
    public static function getClientLogin(): ?string
    {
        if (self::isAuthenticated()) {
            return (string)$_SESSION[self::SESSION_AUTH_KEY][self::SESSION_LOGIN_KEY];
        }
        return null;
    }

    /**
     * Gets the session token for the currently authenticated banner client
     * This can be used as part of a CSRF protection mechanism.
     *
     * @return string|null session token if authenticated
     */
    public static function getSessionToken(): ?string
    {
        if (self::isAuthenticated()) {
            return (string)$_SESSION[self::SESSION_AUTH_KEY][self::SESSION_TOKEN_KEY];
        }
        return null;
    }

    /**
     * Gets the name of the currently authenticated banner client from the database
     *
     * @return string|null client's name if authenticated
     */
    public static function getClientName(): ?string
    {
        $cid = self::getClientId();
        if ($cid === null) {
            return null;
        }

        global $xoopsDB;
        $clientTable = $xoopsDB->prefix('bannerclient');
        $sql = sprintf(
            'SELECT name FROM %s WHERE cid = %d',
            $clientTable,
            $cid
        );

        $result = $xoopsDB->query($sql);
        if ($result && $row = $xoopsDB->fetchArray($result)) {
            return $row['name'];
        }
        return null;
    }

    /**
     * Gets the email of the currently authenticated banner client
     *
     * @return string|null client's email if authenticated
     */
    public static function getClientEmail(): ?string
    {
        $cid = self::getClientId();
        if ($cid === null) {
            return null;
        }

        global $xoopsDB;
        $clientTable = $xoopsDB->prefix('bannerclient');
        $sql = sprintf(
            'SELECT email FROM %s WHERE cid = %d',
            $clientTable,
            $cid
        );

        $result = $xoopsDB->query($sql);
        if ($result && $row = $xoopsDB->fetchArray($result)) {
            return $row['email'];
        }
        return null;
    }
}
