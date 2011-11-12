<?php
/**
 * Functions for block
 * @version $Rev: 255 $
 * @link $URL: https://ajax-discuss.svn.sourceforge.net/svnroot/ajax-discuss/openid/trunk/openid/blocks/openidurl.php $
 */
function b_openid_login_show($options)
{
    global $xoopsUser, $xoopsModule;
    if (is_object(@$xoopsUser)) {
        return false;
    }
    $label = empty($options[0]) ? '' : trim($options[0]);
    $allowInput = empty($options[1]) ? false : true;

    include_once XOOPS_ROOT_PATH . '/modules/openid/class/handler/buttons.php';
    $handler_buttons = new Openid_Handler_Buttons();
    $buttons =& $handler_buttons->getObjects();

    $frompage = (is_object($xoopsModule) && $xoopsModule->getVar('dirname') != 'openid') ? $_SERVER['REQUEST_URI'] : '';

    return array(
        'label' => $label,
        'buttons' => $buttons,
        'allowInput' => $allowInput,
        'frompage' => $frompage
    );
}

function b_openid_login_edit($options)
{
    $label = empty($options[0]) ? '' : trim($options[0]);
    if (empty($options[1])) {
        $yes = '';
        $no = ' checked="checked"';
    } else {
        $yes = ' checked="checked"';
        $no = '';
    }

    $form = '
     <table width="100%">
      <tr>
       <td width="40%">' . _MB_OPENID_DESCRIPTION . '</td>
       <td><input type="text" name="options[0]" value="' . $label . '" /></td>
      </tr>
      <tr>
       <td width="40%">' . _MB_OPENID_ALLOW_INPUT . '</td>
       <td>
        <input type="radio" name="options[1]" value="1" ' . $yes . ' />' . _YES . '
        <input type="radio" name="options[1]" value="0" ' . $no . ' />' . _NO . '
       </td>
      </tr>
     </table>
    ';
    return $form;
}
?>