<?php
include "../../../include/cp_header.php";
xoops_cp_header();
$myfilename = basename(__FILE__);
echo "<a target='_self' href='index.php'>"._AD_MULTIMENU_ADMIN."</a>";
echo "<br/>";
echo "-------- attention please --------";
echo "<br/>";
echo "This file (multiMenu/admin/".$myfilename.") is not required at multiMenu ver1.20 of pack2011:Please delete this file from your site.";
echo "<br/>";
echo "このファイル (multiMenu/admin/".$myfilename.") はpack2011 multiMenu ver1.20 では不要になりました。あなたのサイトからこのファイルを削除してください";
xoops_cp_footer();
?>