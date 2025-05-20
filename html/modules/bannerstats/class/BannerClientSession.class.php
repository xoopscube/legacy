<?php
// html/modules/bannerstats/class/BannerClientSession.class.php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class BannerClientSession
{
    /**
     * The key used to store banner client authentication data in the $_SESSION.
     */
    private const SESSION_AUTH_KEY = 'bannerstats_client_auth';

    /**
     * The key within the session data for storing the client's ID.
     */
    private const SESSION_CID_KEY = 'cid';

    /**
     * The key within the session data for storing the client's login name.
     */
    private const SESSION_LOGIN_KEY = 'login';

    /**
     * The key within the session data for storing a simple session token for CSRF.
     */
    private const SESSION_TOKEN_KEY = 'token';

    /**
     * Attempts to log in a banner client.
     *
     * @param string $username The client's login username.
     * @param string $password The client's password.
     * @return bool True on successful login, false otherwise.
     */
    public static function login(string $username, string $password): bool
    {
        global $xoopsDB;

        $username = trim($username);
        // Note: The original banners.php does not trim the password.
        // Consider if trimming password is desired or if it should match exact input.
        // $password = trim($password); 

        if (empty($username) || empty($password)) {
            return false;
        }

        // It's good practice to ensure the ClientHandler class is available
        // if we were to use it here for authentication.
        // However, for direct DB access as in the original banners.php:
        $clientTable = $xoopsDB->prefix('bannerclient');
        $sql = sprintf(
            'SELECT cid, passwd FROM %s WHERE login = %s',
            $clientTable,
            $xoopsDB->quoteString($username)
        );

        $result = $xoopsDB->query($sql);
        if ($row = $xoopsDB->fetchArray($result)) {
            // --- IMPORTANT SECURITY NOTE ---
            // The original banners.php compares passwords in plain text.
            // This is highly insecure. For a production system, you should:
            // 1. Migrate existing passwords in the 'bannerclient' table to be hashed
            //    using password_hash().
            // 2. Use password_verify() here for comparison.
            // Example (if passwords were hashed):
            // if (password_verify($password, $row['passwd'])) { ... }

            // For now, replicating the original plain text comparison:
            if ($password === $row['passwd']) {
                // Regenerate session ID on successful login to prevent session fixation.
                if (session_status() == PHP_SESSION_ACTIVE) {
                    session_regenerate_id(true);
                }

                $_SESSION[self::SESSION_AUTH_KEY] = [
                    self::SESSION_CID_KEY => (int)$row['cid'],
                    self::SESSION_LOGIN_KEY => $username,
                    self::SESSION_TOKEN_KEY => bin2hex(random_bytes(16)) // Simple session token for CSRF
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
        // Optionally, regenerate session ID on logout as well for added security.
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /**
     * Checks if a banner client is currently authenticated.
     *
     * @return bool True if authenticated, false otherwise.
     */
    public static function isAuthenticated(): bool
    {
        return isset($_SESSION[self::SESSION_AUTH_KEY][self::SESSION_CID_KEY]) &&
               $_SESSION[self::SESSION_AUTH_KEY][self::SESSION_CID_KEY] > 0;
    }

    /**
     * Gets the ID of the currently authenticated banner client.
     *
     * @return int|null The client ID if authenticated, null otherwise.
     */
    public static function getClientId(): ?int
    {
        if (self::isAuthenticated()) {
            return (int)$_SESSION[self::SESSION_AUTH_KEY][self::SESSION_CID_KEY];
        }
        return null;
    }

    /**
     * Gets the login name of the currently authenticated banner client.
     *
     * @return string|null The client login name if authenticated, null otherwise.
     */
    public static function getClientLogin(): ?string
    {
        if (self::isAuthenticated()) {
            return (string)$_SESSION[self::SESSION_AUTH_KEY][self::SESSION_LOGIN_KEY];
        }
        return null;
    }

    /**
     * Gets the session token for the currently authenticated banner client.
     * This can be used as part of a CSRF protection mechanism.
     *
     * @return string|null The session token if authenticated, null otherwise.
     */
    public static function getSessionToken(): ?string
    {
        if (self::isAuthenticated()) {
            return (string)$_SESSION[self::SESSION_AUTH_KEY][self::SESSION_TOKEN_KEY];
        }
        return null;
    }

        // --- NEW METHODS TO ADD ---

    /**
     * Gets the name of the currently authenticated banner client from the database.
     *
     * @return string|null The client's name if authenticated and found, null otherwise.
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
     * Gets the email of the currently authenticated banner client from the database.
     *
     * @return string|null The client's email if authenticated and found, null otherwise.
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
    // --- END NEW METHODS ---
}
