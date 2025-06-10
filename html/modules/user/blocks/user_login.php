<?php

function b_user_login_show($options)
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

        // OAuth2 Login Options
        // $options[0] is from b_user_login_edit: 0 = Display individual buttons, 1 = Display link to oauth2/index.php
        $oauth2_display_mode = $options[0] ?? 0; 

        $block['show_oauth2_index_link'] = false;
        $block['oauth2_providers'] = [];
        
        $oauth2_module_dirname = 'oauth2'; // Your OAuth2 module's directory name
        $oauth2_module_obj = $module_handler->getByDirname($oauth2_module_dirname);

        if (is_object($oauth2_module_obj) && $oauth2_module_obj->getVar('isactive')) {
            if ($oauth2_display_mode == 1) {
                $block['show_oauth2_index_link'] = true;
                $block['oauth2_index_url'] = XOOPS_URL . '/modules/' . $oauth2_module_dirname . '/index.php';
            } else {
                // Load OAuth2 module's config
                $oauth2ModuleConfig = $config_handler->getConfigsByDirname($oauth2_module_dirname);
                
                // Include and use the OAuthHelper class
                $oauth_helper_path = XOOPS_ROOT_PATH . '/modules/' . $oauth2_module_dirname . '/class/OAuthHelper.class.php';
                if (file_exists($oauth_helper_path)) {
                    require_once $oauth_helper_path;
                    if (class_exists('OAuthHelper')) {
                        $block['oauth2_providers'] = OAuthHelper::getAvailableProviders($oauth2ModuleConfig, $oauth2_module_dirname);
                    } else {
                        // Log error: OAuthHelper class not found
                        error_log("User Login Block: OAuthHelper class not found in " . $oauth_helper_path);
                    }
                } else {
                    // Log error: OAuthHelper file not found
                    error_log("User Login Block: OAuthHelper.class.php not found at " . $oauth_helper_path);
                }
            }
        }
        return $block;
    }
    return false;
}

// b_user_login_edit function remains the same as you provided previously
function b_user_login_edit($options)
{
    $oauth2_display_mode = $options[0] ?? 0;

    $form = _MB_USER_OAUTH2_DISPLAY_MODE . "<br>";
    $form .= '<input type="radio" name="options[0]" value="0"' . ($oauth2_display_mode == 0 ? ' checked="checked"' : '') . ' /> ' . _MB_USER_OAUTH2_DISPLAY_BUTTONS;
    $form .= '&nbsp; <input type="radio" name="options[0]" value="1"' . ($oauth2_display_mode == 1 ? ' checked="checked"' : '') . ' /> ' . _MB_USER_OAUTH2_DISPLAY_LINK;
    
    return $form;
}
