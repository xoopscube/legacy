<?php
function b_user_login_show()
{
    global $xoopsUser;
    
    if (!$xoopsUser) {
        $block = [];
        $config_handler =& xoops_gethandler('config');
        $moduleConfigUser =& $config_handler->getConfigsByDirname('user');
        
        if (isset($_COOKIE[$moduleConfigUser['usercookie']])) {
            $block['unamevalue'] = htmlspecialchars($_COOKIE[$moduleConfigUser['usercookie']], ENT_QUOTES);
        } else {
            $block['unamevalue'] = '';
        }

        $block['allow_register'] = $moduleConfigUser['allow_register'];

        $block['use_ssl'] = $moduleConfigUser['use_ssl'];
        if (1 == $moduleConfigUser['use_ssl'] && '' != $moduleConfigUser['sslloginlink']) {
            $block['sslloginlink'] = htmlspecialchars($moduleConfigUser['sslloginlink'], ENT_QUOTES);
        } else {
            $block['use_ssl'] = 0;
            $block['sslloginlink'] = '';
        }

        // SAML Login Button (existing logic)
        $block['show_saml_login'] = false;
        $module_handler = xoops_gethandler('module');
        $saml_module = $module_handler->getByDirname('saml');
        if (is_object($saml_module) && $saml_module->getVar('isactive')) {
            $block['show_saml_login'] = true;
            $block['saml_login_url'] = XOOPS_URL . '/modules/saml/login.php';
        }

        // OAuth2 Login Buttons
        $block['oauth2_providers'] = [];
        $oauth2_module = $module_handler->getByDirname('oauth2');
        if (is_object($oauth2_module) && $oauth2_module->getVar('isactive')) {
            $moduleConfigOAuth2 =& $config_handler->getConfigsByDirname('oauth2');

            if (!empty($moduleConfigOAuth2['enable_google_oidc'])) {
                $block['oauth2_providers'][] = [
                    'name' => 'Google',
                    'url'  => XOOPS_URL . '/modules/oauth2/login.php?provider=google',
                    // 'icon' => XOOPS_URL . '/modules/oauth2/images/google_icon.png' // Optional
                ];
            }
            if (!empty($moduleConfigOAuth2['enable_facebook_auth'])) {
                $block['oauth2_providers'][] = [
                    'name' => 'Facebook',
                    'url'  => XOOPS_URL . '/modules/oauth2/login.php?provider=facebook',
                ];
            }
            if (!empty($moduleConfigOAuth2['enable_github_auth'])) {
                $block['oauth2_providers'][] = [
                    'name' => 'GitHub',
                    'url'  => XOOPS_URL . '/modules/oauth2/login.php?provider=github',
                ];
            }
            if (!empty($moduleConfigOAuth2['enable_instagram_auth'])) {
                $block['oauth2_providers'][] = [
                    'name' => 'Instagram',
                    'url'  => XOOPS_URL . '/modules/oauth2/login.php?provider=instagram',
                ];
            }
            if (!empty($moduleConfigOAuth2['enable_microsoft_auth'])) {
                $block['oauth2_providers'][] = [
                    'name' => 'Microsoft',
                    'url'  => XOOPS_URL . '/modules/oauth2/login.php?provider=microsoft',
                ];
            }
            if (!empty($moduleConfigOAuth2['enable_apple_auth'])) {
                $block['oauth2_providers'][] = [
                    'name' => 'Apple',
                    'url'  => XOOPS_URL . '/modules/oauth2/login.php?provider=apple',
                ];
            }
            if (!empty($moduleConfigOAuth2['enable_twitter_auth'])) {
                $block['oauth2_providers'][] = [
                    'name' => 'X (Twitter)',
                    'url'  => XOOPS_URL . '/modules/oauth2/login.php?provider=twitter',
                ];
            }
            if (!empty($moduleConfigOAuth2['enable_generic_oidc'])) {
                $displayName = !empty($moduleConfigOAuth2['generic_oidc_display_name']) ? $moduleConfigOAuth2['generic_oidc_display_name'] : 'OpenID Connect';
                $block['oauth2_providers'][] = [
                    'name' => htmlspecialchars($displayName, ENT_QUOTES),
                    'url'  => XOOPS_URL . '/modules/oauth2/login.php?provider=generic-oidc',
                ];
            }
        }
        return $block;
    }
    return false;
}
