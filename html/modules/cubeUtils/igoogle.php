<?php
include_once('../../mainfile.php');
include_once('./include/blockFunc.inc.php');
session_write_close();

$up_block = (!empty($_GET['block'])) ? intval($_GET['block']) :'?';
$mode = (!empty($_GET['mode'])) ? intval($_GET['mode']) :'';
$content = '';
$preflist = '';

$blockHandler =& xoops_gethandler('block');
$blockObjects = $blockHandler->getAllBlocksByGroup(XOOPS_GROUP_ANONYMOUS);
$blockCount = 0;
foreach ($blockObjects as $blockObject ) {
    if ($blockObject->getVar('mid') == $GLOBALS['xoopsModule']->getVar('mid') && $blockObject->getVar('show_func')=='b_cubeUtils_igoogle_show') {
        $block_type = $blockObject->getVar("block_type");
        $name = $blockObject->getVar("name");
        $blockCount++;
        if ($mode=='raw' && $up_block == $blockObject->getVar('bid')){
            $bid = $blockObject->getVar('options');
            $bid = explode('|', $bid);
            $bid = $bid[0];
            $resut = cubeUtils_GetBlock($bid, $useCache=true);
            $content = xoops_utf8_encode($resut['content']);
        } else {
            $bid = $blockObject->getVar('bid');
            $preflist .= '<EnumValue value="'.$bid.'" display_value="['.$bid.']'.xoops_utf8_encode($name).'" />';
        }
    }
}
if ($blockCount == 0) {
    header('HTTP/1.0 404 Not Found');
    exit('404 Not Found');
}
if (!empty($_GET['mode']) && $_GET['mode'] === 'raw') {
   $refresh = (!empty($_GET['up_refresh'])) ? intval($_GET['up_refresh']) : 600;
   if ($refresh < 300) $refresh = 300;
    header('Content-type: text/html; charset=UTF-8', true);
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja" >
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Refresh" content="<?echo $refresh; ?>" />
<link rel="stylesheet" type="text/css" charset="EUC-JP" media="all" href="<?php echo XOOPS_URL;?>/modules/cubeUtils/images/igoogle.css" />
<base target="__blank" />
</head>
<body><div class="xoops-content">
    <?php echo $content; ?>
</div></body></html>
<?php
} else {
    $site = xoops_utf8_encode(htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES));
    header('Content-type: text/xml; charset=UTF-8', true);
    echo '<?xml version="1.0" encoding="UTF-8" ?>'
?>
<Module>
  <ModulePrefs
    title="__UP_title__"
    title_url="<?php echo XOOPS_URL?>"
    height="200" > 
    <Require feature="dynamic-height" />
  </ModulePrefs>
  <UserPref name="title" default_value="<?echo $site ?> Block" />
  <UserPref name="height" display_name="Height" default_value="200" />
  <UserPref name="block" display_name="Block" default_value="<?php echo $up_block; ?>" datatype="enum">
    <EnumValue value="?" display_value="None" /><?php echo $preflist; ?>
  </UserPref>
  <UserPref name="refresh" display_name="Refresh Rate" default_value="600" datatype="enum">
    <EnumValue display_value="5min" value="300" />
    <EnumValue display_value="10min" value="600" />
    <EnumValue display_value="20min" value="1200" />
    <EnumValue display_value="30min" value="1800" />
    <EnumValue display_value="60min" value="3600" />
  </UserPref>
  <Content type="html"><![CDATA[
  <script>_IG_RegisterOnloadHandler(_IG_AdjustIFrameHeight);</script>
  <iframe id = "dumFrame___MODULE_ID__" style="border:0px;padding:0px;margin:0px;overflow:auto;height:__UP_height__px;" width="100%" src="<?php echo XOOPS_URL.'/modules/cubeUtils/'.basename(__FILE__);?>?mode=raw&amp;block=__UP_block__&amp;up_refresh=__UP_refresh__"></iframe>
  ]]>
  </Content>
</Module>
<?php
}
?>
