<?php
/**
 * @author Marijuana
 */
if (!defined('XOOPS_ROOT_PATH')) exit();
function smarty_function_message_newmessage($params, &$smarty)
{
  $name = isset($params['name']) ? trim($params['name']) : 'new_messages';
  $open = isset($params['open']) ? trim($params['open']) : 'open_message_alert';
  
  $new_messages = false;
  $root = XCube_Root::getSingleton();
  if ($root->mContext->mUser->isInRole('Site.RegisteredUser')) {
    $modHand = xoops_getmodulehandler('inbox', 'message');
    $new_messages = $modHand->getCountUnreadByFromUid($root->mContext->mXoopsUser->get('uid'));
    if ( empty($_SESSION[$name]) ) {
      $_SESSION[$name] = 0;
    }
    if ( $_SESSION['new_messages'] < $new_messages ) {
      $smarty->assign($open, 1);
    }
    $_SESSION[$name] = $new_messages ;
  }
  $smarty->assign($name, $new_messages);
}
?>
