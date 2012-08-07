<?php
/**
 * @author Marijuana
 */
if (!defined('XOOPS_ROOT_PATH')) exit();
function smarty_function_message_suggestlist($params, &$smarty)
{
  $name = isset($params['name']) ? trim($params['name']) : 'uname';
  $size = isset($params['size']) ? intval($params['size']) : 30;
  $username = isset($params['uname']) ? trim($params['uname']) : '';
  
  $root = XCube_Root::getSingleton();
  $db = $root->mController->getDB();
  
  $sql = "SELECT `uname` FROM `".$db->prefix('users')."` ";
  $sql.= "WHERE `uid` <> ".$root->mContext->mXoopsUser->get('uid'). " ";
  $sql.= "ORDER BY `uname`";
  $result = $db->query($sql);
  $name = array();
  while (list($uname) = $db->fetchRow($result)) {
    $uname = htmlspecialchars($uname, ENT_QUOTES);
    $name[] = "'".$uname."'";
  }
  
  echo '<script type="text/javascript" language="javascript">'.chr(10);
  echo '  var list = ['. implode(",\n", $name).'];'.chr(10);
  echo "
  var js = document.createElement('script');
  js.type = 'text/javascript';
  js.charset = 'utf-8';
  js.src = '".XOOPS_MODULE_URL."/message/suggest.js';
  
  var css = document.createElement('link');
  css.type = 'text/css';
  css.rel = 'stylesheet';
  css.media = 'screen';
  css.href = '".XOOPS_MODULE_URL."/message/suggest.css';

  var head = document.getElementsByTagName('head');
  head[0].appendChild(js);
  head[0].appendChild(css);
  
  var start = function() {
    new Suggest.Local('txt_uname', 'suggest', list);
  };
  window.addEventListener ? window.addEventListener('load', start, false) : window.attachEvent('onload', start);
</script>\n";
echo '<input id="txt_uname" type="text" name="uname" value="'.htmlspecialchars($username, ENT_QUOTES).'" autocomplete="off" size="'.$size.'" style="display: block"/>
<div id="suggest"></div>
';
}
?>
