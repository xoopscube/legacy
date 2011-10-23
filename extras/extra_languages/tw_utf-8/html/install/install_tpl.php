<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <title>XOOPS Cube 安裝精靈</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8 echo _INSTALL_CHARSET ?>" />
  <style type="text/css" media="all"><!-- @import url(../xoops.css); --></style>
  <link rel="stylesheet" type="text/css" media="all" href="style.css" />
</head>

<body>
<div id="container">
<form action="index.php" method="post" style="margin:0">

<table width="100%" cellspacing="0">
<tr>
<td id="headerL"><img src="img/cube_logo.gif" alt="XOOPS Cube" /></td>
<td id="headerR">
  <h4><?php echo _INSTALL_HEADER_MESSAGE ?></h4>
</td>
</tr>
<tr>
<td id="headerBar" colspan="2">
<div id="header_buttons">
<?php echo b_back($b_back); ?>&nbsp;&nbsp;&nbsp;
<?php echo b_reload($b_reload); ?>&nbsp;&nbsp;&nbsp;
<?php echo b_next($b_next); ?>
</div>
</td>
</tr>
</table>

<table id="mainbody" cellspacing="0">
<tr>
<td class="leftcolumn"><img src="img/left_bg.gif" alt="Cubekun" /></td>
<td>
<?php if(!empty($title)) { ?><h3><?php echo $title; ?></h3><?php } ?>


<div class="maincontents"><?php echo $content; ?></div>
</td>
</tr>
</table>

<div id="footer">
<?php echo b_back($b_back); ?>&nbsp;&nbsp;&nbsp;
<?php echo b_reload($b_reload); ?>&nbsp;&nbsp;&nbsp;
<?php echo b_next($b_next); ?>
</div>
</form>

</div>
</body>
</html>
