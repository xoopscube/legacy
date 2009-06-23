<?php
/**
 * @author Marijuana
 */
if (!defined('XOOPS_ROOT_PATH')) exit();
function smarty_function_message_userlist($params, &$smarty)
{
  $name = isset($params['name']) ? trim($params['name']) : 'uname';
  $username = isset($params['uname']) ? trim($params['uname']) : '';
  $buid = isset($params['uid']) ? true : false;
  
  $root = XCube_Root::getSingleton();
  $db = $root->mController->getDB();
  
  $option = '<option value=""></option>';
  
  $sql = "SELECT `uname`, `uid` FROM `".$db->prefix('users')."` ";
  $sql.= "WHERE `uid` <> ".$root->mContext->mXoopsUser->get('uid'). " ";
  $sql.= "ORDER BY `uname`";
  $result = $db->query($sql);
  while (list($uname, $uid) = $db->fetchRow($result)) {
    $uname = htmlspecialchars($uname, ENT_QUOTES);
    $option.= '<option value="';
    $option.= $buid ? $uid : $uname;
    if ( ($buid == false && $uname == $username) || ($buid && $uid == $username ) ) {
      $option.= '" selected="selected';
    }
    $option.= '">'.$uname.'</option>'.chr(10);
  }
  echo '<select name="'.$name.'">';
  echo $option;
  echo '</select>';
}
?>
