<?php
xoops_header(false);
?>
<script type="text/javascript" src="<?php echo $mydirurl; ?>/index.php?page=javascript"></script>
</head>
<body>
<?php
require_once(XOOPS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/search.php');

$groups = is_object($xoopsUser) ? $xoopsUser -> getGroups() : XOOPS_GROUP_ANONYMOUS;
$gperm_handler = & xoops_gethandler( 'groupperm' );
$available_modules = $gperm_handler->getItemIds('module_read', $groups);

// To avoid duplicate to replace the value
$my_true_trustdirname = $mytrustdirname;

$modules = array();
foreach($available_modules as $mid){
	$sql = "SELECT name, dirname FROM ".$xoopsDB->prefix('modules')." WHERE mid=$mid";
	$result = $xoopsDB->query($sql);
	list( $name, $dirname ) = $xoopsDB->fetchRow($result) ;

	if( file_exists(XOOPS_ROOT_PATH.'/modules/'.$dirname.'/relation.php') ){
		require_once(XOOPS_ROOT_PATH.'/modules/'.$dirname.'/relation.php');
		if( $mytrustdirname == $my_true_trustdirname){
			$modules_name[$mid] = $name;
			$modules_mid[$mid] = $mid;
			$modules_dir[$mid] = $dirname;
		}
	}
}

unset($mid);

// Restore the value to avoid duplication
$mytrustdirname = $my_true_trustdirname;

$query = isset($_GET['query']) ? $myts->stripslashesGPC($_GET['query']) : '';
$andor = isset($_GET['andor']) ? $myts->stripslashesGPC($_GET['andor']) : '';
$mids = isset($_GET['mids']) ? $_GET['mids'] : $modules_mid ;
$action = isset($_GET['action']) ? $_GET['action'] : '';
$showall = isset($_GET['showall']) ? intval($_GET['showall']) : 0;

if ( $andor != "OR" && $andor != "exact" && $andor != "AND" ) {
	$andor = "AND";
}

if ( $andor != "exact" ) {
	$ignored_queries = array(); // holds kewords that are shorter than allowed minmum length
	$temp_queries = preg_split('/[\s,]+/', mb_convert_kana($query, 's'));
	foreach ($temp_queries as $q) {
		$queries[] = addSlashes(trim($q));
	}
} else {
	$queries = array(addSlashes(trim($query)));
}

include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
// create form
$search_form = new XoopsThemeForm(_SR_SEARCH, "search", "index.php", 'get');
$search_form->addElement(new XoopsFormText(_SR_KEYWORDS, "query", 30, 255, htmlspecialchars(stripslashes(implode(" ", $queries)), ENT_QUOTES)), true);
$type_select = new XoopsFormSelect(_SR_TYPE, "andor", $andor);
$type_select->addOptionArray(array("AND"=>_SR_ALL, "OR"=>_SR_ANY, "exact"=>_SR_EXACT));
$search_form->addElement($type_select);
$mods_checkbox = new XoopsFormCheckBox(_SR_SEARCHIN, "mids[]", $mids);
$mods_checkbox->addOptionArray($modules_name);
$search_form->addElement($mods_checkbox);
$search_form->addElement(new XoopsFormHidden("action", "results"));
$search_form->addElement(new XoopsFormHidden("page", "search"));
$search_form->addElement(new XoopsFormButton("", "submit", _SR_SEARCH, "submit"));
$search_form->display();

if($action == 'results' ){
	echo '<div style="font-size:small">';
	echo "<h3>"._SR_SEARCHRESULTS."</h3>";
	echo _SR_KEYWORDS.':';
	if ($andor != 'exact') {
		foreach ($queries as $q) {
			echo ' <b>'.htmlspecialchars(stripslashes($q)).'</b>';
		}
	} else {
		echo ' "<b>'.htmlspecialchars(stripslashes($queries[0])).'</b>"';
	}

//ver3.0
	include_once XOOPS_TRUST_PATH."/modules/bulletin/class/bulletingp.php";

	echo '<form name="stories">';
	$time = time();
	foreach ($mids as $mid) {
		$mid = intval($mid);
		if ( in_array($mid, $modules_mid) ) {
			$sql = "SELECT storyid, title, published FROM ".$xoopsDB->prefix( $modules_dir[$mid].'_stories' )." WHERE type > 0 AND published > 0 AND published <= $time AND (expired = 0 OR expired > $time)";
//ver3.0
			$gperm =& BulletinGP::getInstance($modules_dir[$mid]) ;
			$can_read_topic_ids = $gperm->makeOnTopics('can_read');
			$sql .= " AND topicid IN (".implode(',',$can_read_topic_ids).")";

			if ( is_array($queries) && $count = count($queries) ) {
				$sql .= " AND ((hometext LIKE '%$queries[0]%' OR bodytext LIKE '%$queries[0]%' OR title LIKE '%$queries[0]%')";
				for($i=1;$i<$count;$i++){
					$sql .= " $andor ";
					$sql .= "(hometext LIKE '%$queries[$i]%' OR bodytext LIKE '%$queries[$i]%' OR title LIKE '%$queries[$i]%')";
				}
				$sql .= ") ";
			}
			$sql .= "ORDER BY published DESC";

			$result = $xoopsDB->query($sql, ($showall > 0 )?0:5, 0);

			$count = 0;
			echo '<div>';
			echo "<h4>".$myts->makeTboxData4Show($modules_name[$mid])."</h4>";
			while( $story = $xoopsDB->fetchArray($result)){
				echo '<input type="checkbox" name="storyidR[]" value="'.$story['storyid'].'" /> ';
				echo '<input type="hidden" name="titleR[]" value="'.htmlspecialchars($story['title']).'" /> ';
				echo '<input type="hidden" name="dirnameR[]" value="'.htmlspecialchars($modules_dir[$mid]).'" /> ';
				echo '<a href="'.XOOPS_URL.'/modules/'.htmlspecialchars($modules_dir[$mid]).'/index.php?page=article&amp;storyid='.$story['storyid'].'" target="_blank">'.htmlspecialchars($story['title']).'</a>';
				echo '('.formatTimestamp($story['published']). ')';
				echo '<br />';
				$count++;
			}
			if ( $count == 5 && $showall == 0) {
				$search_url = 'index.php?query='.urlencode(stripslashes(implode(' ', $queries)));
				$search_url .= "&page=search&action=results&mids[]=$mid&showall=1&andor=$andor";
				echo '<br /><a href="'.htmlspecialchars($search_url).'">'._SR_SHOWALLR.'</a></p>';
			}elseif( $count == 0 ){
				echo _SR_NOMATCH;
			}
			echo '</div>';

		}
	}
	echo '</form>';
	echo '</div>';
	echo '<input type="button" onClick="submitRelations(\'stories\', true)" value="'._MD_CHECKED_AS_RELATION.'" />';

}
echo '</body>';
echo '</html>';

?>