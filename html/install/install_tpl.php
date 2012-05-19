<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <title>XOOPS Cube Install Wizard</title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo _INSTALL_CHARSET ?>" />
  <style type="text/css" media="all"><!-- @import url(../xoops.css); --></style>
  <link rel="stylesheet" type="text/css" media="all" href="style.css" />
  <script type="text/javascript" src="http://www.google.com/jsapi"></script>
  <script type="text/javascript"><!--
  google.load("language", "1"); 
  google.load("jquery", "1");
  google.setOnLoadCallback(function() {
    $("a[rel='external']").click(function(){
    window.open($(this).attr("href"));
    return false;
    });
  });
  //--></script>
  </script> 
</head>

<body>
<div id="container">
<form action="index.php" method="post" style="margin:0">


<br />
<table id="mainbody" cellspacing="0">
<tr>
<td>
<p align="center"><?php if(!empty($title)) { ?></p>
<h3><?php echo $title; ?></h3><?php } ?>


<div class="maincontents"><?php echo $content; ?></div></td>
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
