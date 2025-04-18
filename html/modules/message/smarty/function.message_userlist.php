<?php
/**
 * Message module for private messages and forward to email
 * 
 * @package    Message
 * @version    2.5.0
 * @author     Nuno Luciano aka gigamaster, 2020 XCL23
 * @author     Osamu Utsugi aka Marijuana
 * @copyright  (c) 2005-2025 The XOOPSCube Project, Authors
 * @license    GPL 3.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
function smarty_function_message_userlist($params, &$smarty)
{
    $name = isset($params['name']) ? trim($params['name']) : 'uname';
    $username = isset($params['uname']) ? trim($params['uname']) : '';
    $buid = isset($params['uid']) ? true : false;
    $id = isset($params['id']) ? trim($params['id']) : 'user-select';
    $placeholder = isset($params['placeholder']) ? trim($params['placeholder']) : 'Select a user';
  
    $root = XCube_Root::getSingleton();
    $db = $root->mController->getDB();
  
    // Get users data
    $users = [];
    $sql = 'SELECT `uname`, `uid` FROM `' . $db->prefix('users') . '` ';
    $sql.= 'WHERE `uid` <> ' . $root->mContext->mXoopsUser->get('uid') . ' ';
    $sql.= 'ORDER BY `uname`';
    $result = $db->query($sql);
    while ([$uname, $uid] = $db->fetchRow($result)) {
        $users[] = [
            'uid' => $uid,
            'uname' => htmlspecialchars($uname, ENT_QUOTES)
        ];
    }
    
    // Create select element with modern styling
    $html = '<select name="'.$name.'" id="'.$id.'" class="user-select">';
    $html .= '<option value="">'.$placeholder.'</option>';
    
    foreach ($users as $user) {
        $value = $buid ? $user['uid'] : $user['uname'];
        $selected = ((false == $buid && $user['uname'] == $username) || 
                    ($buid && $user['uid'] == $username)) ? ' selected="selected"' : '';
        $html .= '<option value="'.$value.'"'.$selected.'>'.$user['uname'].'</option>';
    }
    
    $html .= '</select>';
    
    // Add minimal CSS for styling
    $html .= '<style>
        .user-select {
            width: 100%;
            padding: 8px;
            border: 1px solid #212121;
            border-radius: 4px;
            box-sizing: border-box;
        }
    </style>';
    
    echo $html;
}
