<?php
/**
 * legacy_themes.php
 * XOOPS2
 * @package    Legacy
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 * @brief      This file has been modified for Legacy from XOOPS2 System module block
 */

/**
 * @param $options
 * @return array|null
 * */
function b_legacy_themes_show($options): ?array
{
    global $xoopsConfig;

    if (0 === (is_countable($xoopsConfig['theme_set_allowed']) ? count($xoopsConfig['theme_set_allowed']) : 0)) {
        return null;
    }

    $block = [];
    if ('POST' === xoops_getenv('REQUEST_METHOD')) {
        $block['isEnableChanger'] = 0;
        return $block;
    }

    $block['isEnableChanger'] = 1;

    $theme_options = [];
    $handler = xoops_getmodulehandler('theme', 'legacy');
    foreach ($xoopsConfig['theme_set_allowed'] as $name) {
        $theme = $handler->get($name);
        if ( $theme !== null ) {
            $theme_option['name'] = $name;
            $theme_option['screenshot'] = $theme->getShow('screenshot');
            $theme_option['screenshotUrl'] = XOOPS_THEME_URL . '/' . $name . '/' . $theme->getShow('screenshot');
            if ($name === $xoopsConfig['theme_set']) {
                $theme_option['selected'] = 'selected="selected"';
                $block['theme_selected_screenshot'] = $theme->getShow('screenshot');
                $block['theme_selected_name'] = $name;
            } else {
                $theme_option['selected'] = '';
            }
            $theme_options[] = $theme_option;
        }
    }

    $block['count'] = is_countable($xoopsConfig['theme_set_allowed']) ? count($xoopsConfig['theme_set_allowed']) : 0;
    $block['mode'] = $options[0];
    $block['width'] = $options[1];
    $block['theme_options'] = $theme_options;
    return $block;
}

function b_legacy_themes_edit($options)
{
    $chk = '';
    $form = '<div>'._MB_LEGACY_LANG_THSHOW.'&nbsp;&nbsp;';
    if (1 == $options[0]) {
        $chk = ' checked="checked"';
    }
    $form .= '<input type="radio" name="options[0]" id="display-yes" value="1" '.$chk.'><label for="display-yes">'._YES.'</label>';
    $chk = '';
    if (0 == $options[0]) {
        $chk = ' checked="checked"';
    }
    $form .= '<input type="radio" name="options[0]" id="display-no" value="0" '.$chk.'><label for="display-no">'._NO.'</label></div>';
    $form .= '<div><label for="screenshot">'._MB_LEGACY_LANG_THWIDTH.' </label>';
    $form .= '<input type="text" name="options[1]" id="screenshot" size="3" value="'.$options[1].'"></div>';
    return $form;
}
