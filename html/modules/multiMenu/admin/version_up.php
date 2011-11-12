<?php
	include ("../../../mainfile.php");
	include XOOPS_ROOT_PATH.'/include/cp_header.php';
	include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';

	xoops_cp_header();

	if ( xoops_refcheck() ) {
		if ( $HTTP_POST_VARS['op'] == 'v114up' ) {
			$sql = "CREATE TABLE ".$xoopsDB->prefix('multimenu05')." ( ";
			$sql .= "id int(5) unsigned NOT NULL auto_increment,";
			$sql .= "title varchar(255) NOT NULL default '',";
			$sql .= "hide tinyint(1) unsigned NOT NULL default '0',";
			$sql .= "link varchar(255) default NULL,";
			$sql .= "weight tinyint(4) unsigned NOT NULL default '0',";
			$sql .= "target varchar(10) default NULL,";
			$sql .= "groups varchar(255) default NULL,";
			$sql .= "PRIMARY KEY (id)";
			$sql .= ") TYPE=MyISAM;";

			print $sql."...";
			if ( $xoopsDB->query ( $sql ) ) {
				print "<br><b>Created Table 'multimenu05' ok</b><br>";
			} else {
				print "<br><b><font color='#cc0000'>'multimenu05'...It failed. Please make this SQL reference and work manually.</font></b><br>";
			}

			$sql = "CREATE TABLE ".$xoopsDB->prefix('multimenu06')." ( ";
			$sql .= "id int(5) unsigned NOT NULL auto_increment,";
			$sql .= "title varchar(255) NOT NULL default '',";
			$sql .= "hide tinyint(1) unsigned NOT NULL default '0',";
			$sql .= "link varchar(255) default NULL,";
			$sql .= "weight tinyint(4) unsigned NOT NULL default '0',";
			$sql .= "target varchar(10) default NULL,";
			$sql .= "groups varchar(255) default NULL,";
			$sql .= "PRIMARY KEY (id)";
			$sql .= ") TYPE=MyISAM;";

			print $sql."...";
			if ( $xoopsDB->query ( $sql ) ) {
				print "<br><b>Created Table 'multimenu06' ok</b><br>";
			} else {
				print "<br><b><font color='#cc0000'>'multimenu06'...It failed. Please make this SQL reference and work manually.</font></b><br>";
			}

			$sql = "CREATE TABLE ".$xoopsDB->prefix('multimenu07')." ( ";
			$sql .= "id int(5) unsigned NOT NULL auto_increment,";
			$sql .= "title varchar(255) NOT NULL default '',";
			$sql .= "hide tinyint(1) unsigned NOT NULL default '0',";
			$sql .= "link varchar(255) default NULL,";
			$sql .= "weight tinyint(4) unsigned NOT NULL default '0',";
			$sql .= "target varchar(10) default NULL,";
			$sql .= "groups varchar(255) default NULL,";
			$sql .= "PRIMARY KEY (id)";
			$sql .= ") TYPE=MyISAM;";

			print $sql."...";
			if ( $xoopsDB->query ( $sql ) ) {
				print "<br><b>Created Table 'multimenu07' ok</b><br>";
			} else {
				print "<br><b><font color='#cc0000'>'multimenu07'...It failed. Please make this SQL reference and work manually.</font></b><br>";
			}

			$sql = "CREATE TABLE ".$xoopsDB->prefix('multimenu08')." ( ";
			$sql .= "id int(5) unsigned NOT NULL auto_increment,";
			$sql .= "title varchar(255) NOT NULL default '',";
			$sql .= "hide tinyint(1) unsigned NOT NULL default '0',";
			$sql .= "link varchar(255) default NULL,";
			$sql .= "weight tinyint(4) unsigned NOT NULL default '0',";
			$sql .= "target varchar(10) default NULL,";
			$sql .= "groups varchar(255) default NULL,";
			$sql .= "PRIMARY KEY (id)";
			$sql .= ") TYPE=MyISAM;";

			print $sql."...";
			if ( $xoopsDB->query ( $sql ) ) {
				print "<br><b>Created Table 'multimenu08' ok</b><br>";
			} else {
				print "<br><b><font color='#cc0000'>'multimenu08'...It failed. Please make this SQL reference and work manually.</font></b><br>";
			}

			xoops_cp_footer();
			exit;
		}
	}
?>
<table>
	<tr>
		<td>
			<form action='./version_up.php' method='post'>
			<input type='hidden' name='op' value='v114up'>
			<input type='submit' value='v1.14up'>
			</form>
		</td>
		<td>
		The user who updates from version 1.13 series to version 1.14 series needs this processing.<br/>
		</td>
	</tr>
</table>
<?php
	xoops_cp_footer();

?>