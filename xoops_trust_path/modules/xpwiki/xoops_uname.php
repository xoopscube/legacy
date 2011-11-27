<?php

error_reporting(0);

include(dirname(__FILE__).'/include/compat.php');

$q = (isset($_GET['q']))? (string)$_GET['q'] : "";
$enc = (isset($_GET['e']))? (string)$_GET['e'] : "";

$dats = array();
$oq = $q = str_replace("\0","",$q);
$enc = strtoupper(str_replace("\0","",$enc));
$encs = array( 'EUC-JP' , 'SJIS', 'EUCJP-WIN', 'SJIS-WIN', 'JIS', 'ISO-2022-JP' );
$use_mb = (in_array($enc, $encs));
$use_utf8 = ($enc === 'UTF-8');
$use_ujis = ($enc === 'EUC-JP');

if ($q !== "") {

	if ($use_mb) {
		$q = mb_convert_encoding($q, $enc, 'UTF-8');
	}
	$q = addslashesGPC($q);

	$where1 = " WHERE `uname` LIKE '".$q."%'";
	$where2 = " WHERE `uname` LIKE '%".$q."%' AND `uname` NOT LIKE '".$q."%'";
	$order = " ORDER BY `uname` ASC";
	$limit = 100;

	mysql_connect(XOOPS_DB_HOST, XOOPS_DB_USER, XOOPS_DB_PASS) or die(mysql_error());
	mysql_select_db(XOOPS_DB_NAME);

	if ($use_utf8) {
		mysql_query( "/*!40101 SET NAMES utf8 */" );
		mysql_query( "/*!40101 SET SESSION collation_connection=utf8_japanese_ci */" );
	} else if ($use_ujis) {
		mysql_query( "/*!40101 SET NAMES ujis */" );
		mysql_query( "/*!40101 SET SESSION collation_connection=ujis_japanese_ci */" );
	}

	$query = "SELECT `uid`, `uname` FROM `".XOOPS_DB_PREFIX."_users`".$where1.$order." LIMIT ".$limit;

	$unames = $suggests = $tags = array();
	if ($result = mysql_query($query))
	{
		while($dat = mysql_fetch_row($result))
		{
			$unames[] = '"'.str_replace('"','\"',$dat[1]).'['.$dat[0].']"';
		}
	}

	$count = count($unames);
	if ($count < $limit)
	{
		$query = "SELECT `uid`, `uname` FROM `".XOOPS_DB_PREFIX."_users`".$where2.$order." LIMIT ".($limit - $count);
		if ($result = mysql_query($query))
		{
			while($dat = mysql_fetch_row($result))
			{
				$unames[] = '"'.str_replace('"','\"',$dat[1]).'['.$dat[0].']"';
			}
		}
	}

}

$oq = '"'.str_replace('"','\"',$oq).'"';
$ret = join(", ",$unames);
if ($use_mb) {
	$ret = mb_convert_encoding($ret, 'UTF-8', $enc);
}
$ret = 'this.setSuggest(' . $oq . ',new Array(' . $ret . '));';

// clear output buffer
while( ob_get_level() ) {
	if (! ob_end_clean()) {
		break;
	}
}

header ("Content-Type: text/plain; charset=UTF-8");
header ("Content-Length: ".strlen($ret));
echo $ret;
exit;

// magic_quotes_gpc checked addslashes()
function addslashesGPC($str) {
	if (! get_magic_quotes_gpc()) {
		$str = addslashes($str);
	} else {
		if (ini_get('magic_quotes_sybase')) {
			$str = addslashes(str_replace("''", "'", $str));
		}
	}
	return $str;
}
