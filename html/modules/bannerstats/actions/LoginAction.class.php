<?php
// html/modules/bannerstats/actions/LoginAction.class.php
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
// Corrected path:
require_once dirname(__DIR__) . '/class/BannerClientSession.class.php';

class Bannerstats_LoginAction
{
    private string $errorMessage = '';
    private string $username = '';

    public function getPageTitle(): string
    {
        return "Client Login - Banner Statistics";
    }

    /**
     * Handles POST request for login.
     * @return string|null Template name to render, or null if redirected.
     */
    public function execute(): ?string
    {
        global $xoopsTpl; // Make $xoopsTpl available

        $login = isset($_POST['login']) ? trim($_POST['login']) : '';
        $pass = isset($_POST['pass']) ? trim($_POST['pass']) : '';
        $this->username = $login; // For re-populating form on error

        if (empty($login) || empty($pass)) {
            $this->errorMessage = "Login and Password are required.";
            if (is_object($xoopsTpl)) {
                $xoopsTpl->assign('errorMessage', $this->errorMessage);
                $xoopsTpl->assign('username', htmlspecialchars($this->username, ENT_QUOTES));
            }
            // $xoopsTpl->display('db:bannerstats_login.html'); // REMOVE THIS
            return 'bannerstats_login.html'; // Indicate to render login form again
        }

        // Ensure BannerClientSession class is loaded if not already by autoloader or previous require
        if (!class_exists('BannerClientSession')) {
            $this->errorMessage = "System error: Session handler not available.";
             if (is_object($xoopsTpl)) {
                $xoopsTpl->assign('errorMessage', $this->errorMessage);
            }
            // $xoopsTpl->display('db:bannerstats_login.html'); // REMOVE THIS
            return 'bannerstats_login.html';
        }

        if (BannerClientSession::login($login, $pass)) {
            header("Location: " . XOOPS_URL . "/modules/bannerstats/index.php?action=Stats");
            exit();
            // return null; // This would also satisfy the return type if exit() wasn't called
        } else {
            $this->errorMessage = "Invalid login or password.";
            if (is_object($xoopsTpl)) {
                $xoopsTpl->assign('errorMessage', $this->errorMessage);
                $xoopsTpl->assign('username', htmlspecialchars($this->username, ENT_QUOTES));
            }
            // $xoopsTpl->display('db:bannerstats_login.html'); // REMOVE THIS
            return 'bannerstats_login.html'; // Indicate to render login form again
        }
    }

    /**
     * Handles GET request for the login page.
     * @return string|null Template name to render, or null if redirected.
     */
    public function getDefaultView(): ?string
    {
        global $xoopsTpl; // Make $xoopsTpl available

        // Ensure BannerClientSession class is loaded
        if (!class_exists('BannerClientSession')) {
            if (is_object($xoopsTpl)) {
                 $xoopsTpl->assign('errorMessage', "System error: Session manager unavailable.");
            }
            // $xoopsTpl->display('db:bannerstats_login.html'); // REMOVE THIS
            return 'bannerstats_login.html'; // Or an error template
        }

        if (BannerClientSession::isAuthenticated()) {
            header("Location: " . XOOPS_URL . "/modules/bannerstats/index.php?action=Stats");
            exit();
            // return null; // This would also satisfy the return type if exit() wasn't called
        }

        if (is_object($xoopsTpl)) {
            $xoopsTpl->assign('errorMessage', $this->errorMessage);
            $xoopsTpl->assign('username', htmlspecialchars($this->username, ENT_QUOTES));
            $xoopsTpl->assign('contactLink', XOOPS_URL . '/modules/bannerstats/index.php?action=Contact');
        }
        // $xoopsTpl->display('db:bannerstats_login.html'); // REMOVE THIS
        return 'bannerstats_login.html';
    }
}
