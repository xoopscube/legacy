<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_Session.class.php,v 1.4 2008/10/12 04:30:27 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/bsd_licenses.txt Modified BSD license
 *
 */

class XCube_Session
{
    /**
     * @public
     * @brief [READ ONLY] string
     */
    public $mSessionName = '';

    /**
     * @public
     * @brief [READ ONLY] int
     */
    public $mSessionLifetime = 0;

    /**
     * @public
     * @brief [READ ONLY] XCube_Delegate
     */
    public $mSetupSessionHandler = null;

    /**
     * @public
     * @brief [READ ONLY] XCube_Delegate
     */
    public $mGetSessionCookiePath = null;

    // !Fix PHP7
    public function __construct($sessionName='', $sessionExpire=0)
    //public function XCube_Session($sessionName='', $sessionExpire=0)
    {
        $this->setParam($sessionName, $sessionExpire);

        $this->mSetupSessionHandler = new XCube_Delegate();
        $this->mSetupSessionHandler->register('XCube_Session.SetupSessionHandler');

        $this->mGetSessionCookiePath = new XCube_Delegate();
        $this->mGetSessionCookiePath->register('XCube_Session.GetSessionCookiePath');
    }
    
    /**
     * @public
     */
    public function setParam($sessionName='', $sessionExpire=0)
    {
        $allIniArray = ini_get_all();

        if ($sessionName !='') {
            $this->mSessionName = $sessionName;
        } else {
            $this->mSessionName = $allIniArray['session.name']['global_value'];
        }
        
        if (!empty($sessionExpire)) {
            $this->mSessionLifetime = 60 * $sessionExpire;
        } else {
            $this->mSessionLifetime = $allIniArray['session.cookie_lifetime']['global_value'];
        }
    }

    /**
     * @public
     */
    public function start()
    {
        $this->mSetupSessionHandler->call();

        session_name($this->mSessionName);
        session_set_cookie_params($this->mSessionLifetime, $this->_cookiePath());

        session_start();

        if (!empty($this->mSessionLifetime) && isset($_COOKIE[$this->mSessionName])) {
            // Refresh lifetime of Session Cookie
            $session_params = session_get_cookie_params();
            !$session_params['domain'] and $session_params['domain'] = null;
            $session_cookie_params = array(
                $this->mSessionName, session_id(), time() + $this->mSessionLifetime, $this->_cookiePath(),
                $session_params['domain'], $session_params['secure']
                );
            if (isset($session_params['httponly'])) {
                $session_cookie_params[] = $session_params['httponly'];
            }
            call_user_func_array('setcookie', $session_cookie_params);
        }
    }

    /**
     * @public
     */
    public function destroy($forceCookieClear = false)
    {
        // If current session name is not same as config value.
        // Session cookie should be clear
        // (This case will occur when session config params are changed in preference screen.)
        $currentSessionName = session_name();
        if (isset($_COOKIE[$currentSessionName])) {
            if ($forceCookieClear || ($currentSessionName != $this->mSessionName)) {
                // Clearing Session Cookie
                setcookie($currentSessionName, '', time() - 86400, $this->_cookiePath());
            }
        }
        session_destroy();
    }

    /**
     * @public
     */
    public function regenerate()
    {
        $oldSessionID = session_id();
        session_regenerate_id();
        $newSessionID = session_id();
        session_id($oldSessionID);
        $this->destroy();
        $oldSession = $_SESSION;
        session_id($newSessionID);
        $this->start();
        $_SESSION = array();
        foreach (array_keys($oldSession) as $key) {
            $_SESSION[$key] = $oldSession[$key];
        }
    }

    /**
     * @public
     */
    public function rename()
    {
        if (session_name() != $this->mSessionName) {
            $oldSessionID = session_id();
            $oldSession = $_SESSION;
            $this->destroy();
            session_id($oldSessionID);
            $this->start();
            $_SESSION = array();
            foreach (array_keys($oldSession) as $key) {
                $_SESSION[$key] = $oldSession[$key];
            }
        }
    }

    /**
     * @private
     */
    public function _cookiePath()
    {
        static $sessionCookiePath = null;
        if (empty($sessionCookiePath)) {
            $this->mGetSessionCookiePath->call(new XCube_Ref($sessionCookiePath));
            if (empty($sessionCookiePath)) {
                $sessionCookiePath = '/';
            }
        }
        return $sessionCookiePath;
    }
}
