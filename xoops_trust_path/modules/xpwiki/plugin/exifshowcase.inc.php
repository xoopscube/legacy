<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
//
// ref.inc.php,v 1.20をベースに作成
// $Id: exifshowcase.inc.php,v 1.6 2009/02/22 02:01:56 nao-pon Exp $
//

/*
*プラグイン exifshowcase
そのページに添付されたExif画像ファイルのサムネイルやExif情報
を一覧表示する

*Usage
 #exifshowcase([pattern,[option parameter]])

*パラメータ
-パラメータ
--pattern
eregで比較して、ファイル名がマッチするものだけを対象とする。
指定無き場合、全ての*.(jpg|jpeg)((case insensitive)) が対象になる。
--Left|Center|Right~
横の位置合わせ
--Wrap|Nowrap~
テーブルタグで囲む/囲まない
-Around~
テキストの回り込み
-nolink~
元ファイルへのリンクを張らない
-noimg~
画像を展開しない
-999x999~
サイズを指定(幅x高さ)
-999%~
サイズを指定(拡大率)
-info~
ファイル情報を表示する
-nomapi~
マピオンへのリンクを生成しない
-nokash~
カシミールLMLサーバへのリンクを生成しない
-noexif~
Exif情報表示を行なわない
-reverse~
表示順を逆にする
-整数値~
複数列指定。この場合、Exif情報は表示されない。
-ucomedit~
ucomeditプラグインによるUserComment編集フォームへのボタンを出力する。
*/

class xpwiki_plugin_exifshowcase extends xpwiki_plugin {
	function plugin_exifshowcase_init () {
		// 言語ファイルの読み込み
		$this->load_language();

		// default alignment
		$this->config['DEFAULT_ALIGN'] = 'left'; // 'left','center','right'

		// force wrap on default
		$this->config['WRAP_TABLE'] = FALSE; // TRUE,FALSE
	
		// 画像表示にサムネイルを使うか
		$this->config['THUMB_USE'] = TRUE;
	
		// サムネイルの長辺サイズ
		$this->config['THUMB_WSIDE_LEN'] =  160;

		// default Max width
		$this->config['DEFAULT_MW'] = 160; // px

		// default Max height
		$this->config['DEFAULT_MH'] = 160; // px
	
		// カシミールアイコン
		$this->config['KASH_ICON'] = $this->cont['LOADER_URL'] . '?src=kash3d.png';
	
		// マピオンアイコン
		$this->config['MAPI_ICON'] = 'http://www.mapion.co.jp/QA/user/img/mapion_a.gif';

	}
	
	
	function plugin_exifshowcase_inline()
	{
		$params = $this->plugin_exifshowcase_body(func_get_args(),$this->root->vars['page']);
		
		return ($params['_error'] != '') ? $params['_error'] : $params['_body'];
	}
	function plugin_exifshowcase_convert()
	{
		$params = $this->plugin_exifshowcase_body(func_get_args(),$this->root->vars['page']);
		
		if ($params['_error'] != '')
		{
			return "<p>{$params['_error']}</p>";
		}
		
		if (($this->config['WRAP_TABLE'] and !$params['nowrap']) or $params['wrap'])
		{
			// 枠で包む
			// margin:auto Moz1=x(wrap,aroundが効かない),op6=oNN6=x(wrap,aroundが効かない)IE6=x(wrap,aroundが効かない)
			// margin:0px Moz1=x(wrapで寄せが効かない),op6=x(wrapで寄せが効かない),nn6=x(wrapで寄せが効かない),IE6=o
			$margin = ($params['around'] ? '0px' : 'auto');
			$margin_align = ($params['_align'] == 'center') ? '' : ";margin-{$params['_align']}:0px";
			$params['_body'] = <<<EOD
<table summary="ShowCase" class="style_table" style="margin:$margin$margin_align">
 <tr>
  <td class="style_td">{$params['_body']}</td>
 </tr>
</table>
EOD;
		}
		// divで包む
		if ($params['around'])
		{
			$style = ($params['_align'] == 'right') ? 'float:right' : 'float:left';
		}
		else
		{
			$style = 'text-align:'.$this->config['DEFAULT_ALIGN'].';';
		}
		return "<div style=\"$style\">{$params['_body']}</div>\n";
	}
	
	function plugin_exifshowcase_body($args,$page)
	{
	//	global $script,$WikiName,$BracketName;
		
		// 戻り値
		$params = array();
		
		//パラメータ
		$params = array(
			'left'   => FALSE, // 左寄せ
			'center' => FALSE, // 中央寄せ
			'right'  => FALSE, // 右寄せ
			'wrap'   => FALSE, // TABLEで囲む
			'nowrap' => FALSE, // TABLEで囲まない
			'around' => FALSE, // 回り込み
			'nolink' => FALSE, // 元ファイルへのリンクを張らない
			'noimg'  => FALSE, // 画像を展開しない
			'nomapi' => FALSE, // マピオンへのリンクを張らない
			'nokash' => FALSE, // カシミールLMLサーバへのリンクを張らない
			'noexif' => FALSE, // Exif情報を表示しない
			'reverse'=> FALSE, // 表示順を逆に
			'ucomedit'=> FALSE, // ucomeditプラグイン連係ボタン出力
			'col'    => 1,
			'row'    => 0,
			'pattern'=> '',
			'sort'   => FALSE,
			'_body'  => '',
			'_args'  => array(),
			'_done'  => FALSE,
			'_error' => ''
		);
	
		if (count($args) > 0)
		{
			// compat
			foreach($args as $key=>$val) {
				if (is_numeric($val)) {
					$args[$key] = 'col:' . $val;
				}
			}
			
			$this->fetch_options($params, $args, array('pattern'));
		}
		$pattern = trim($params['pattern']);

		$colmn = intval($params['col']);
		
		$exif_extension = ($colmn == 1) && (!$params['noexif']) && extension_loaded('exif');
		
		$file = $title = $url = $url2 = $info = '';
		$width = $height = 0;
		
		if (!is_dir($this->cont['UPLOAD_DIR']))
		{
			$params['_error'] = 'no UPLOAD_DIR.';
			return $params;
		}

		// fetch DB
		$where = $files = $aname = array();
		
		$where[] = "`pgid` = " . $this->func->get_pgid_by_name($page);
		$where[] = "`type` LIKE 'image%'";
		$where[] = "`age` = 0";
		if ($pattern) {
			$pattern4sql = addslashes($pattern);
			$where[] = "`name` REGEXP '{$pattern4sql}'";
		}
		$where = join(' AND ',$where);
		
		// 並べ替え
		if ($params['row'])
		{
			// ランダム表示？
			$show_count = $params['row'] * $colmn;
			$order = " ORDER BY RAND() LIMIT {$show_count}";
		}
		else if ($params['sort'])
		{
			// ファイル名順
			$order = " ORDER BY `name` ASC";
		}
		else
		{
			// タイムスタンプ順
			$order = " ORDER BY `mtime` ASC";
		}
		
		$query = "SELECT name FROM `".$this->xpwiki->db->prefix($this->root->mydirname . "_attach")."` WHERE {$where}{$order};";
		$result = $this->xpwiki->db->query($query);
		while($_row = mysql_fetch_row($result))
		{
			$files[$_row[0]] = $this->cont['UPLOAD_DIR'].$this->func->encode($page).'_'.$this->func->encode($_row[0]);
		}

		if(!$files) {
			$params['_body'] = $this->msg['err_noimage'] . ($pattern? '(' . htmlspecialchars($pattern) . ')': '');
			return $params;
		}
	
		if ( $params['reverse']) {
			$files = array_reverse( $files);
		}
	
		$params['_body'] = 
			'<table summary="UnitCell" class="style_table" style="margin:0px;">'.
		( $exif_extension ? '<tr class="style_th"><th abbr="file">'.$this->msg['cap_file'].'</th><th abbr="info">'.$this->msg['cap_info'].'</th><th abbr="desc.">'.$this->msg['cap_comment'].'</th></tr>': '');
	
		$cnt = 0;
		foreach ( $files as $aname=>$fname ) {
			$url = "{$this->root->script}?plugin=attach&amp;openfile={$aname}&amp;refer=".rawurlencode($page);
	
			if ( $exif_extension ) {
				$exif  = @ exif_read_data($fname, 0, true);
				$eh = @ $exif["COMPUTED"]["Height"];
				$ew = @ $exif["COMPUTED"]["Width"];
			}
	
			$info = "";
	
			if (!$exif_extension) {
				$sz = filesize($fname);
				list($ew,$eh) = getimagesize($fname);
			} else {
				if (!( $edate = @ $exif["EXIF"]["DateTimeOriginal"])) {
					if (!( $edate = @ $exif["EXIF"]["DateTimeDigitized"])) {
						$edate = @ $exif["IFD0"]["DateTime"];
					}
				}
				$edate = htmlentities(trim($edate), ENT_QUOTES, $this->cont['SOURCE_ENCODING']);
	
				if ( $edate) {
					$info .= "<tr><td style=\"white-space:nowrap;\">".$this->msg['cap_time']."</td><td>:</td><td>{$edate}</td></tr>";
				}
	
				if ( $edesc = trim(@ $exif["IFD0"]["ImageDescription"])) {
					$edesc = mb_convert_encoding($edesc,$this->cont['SOURCE_ENCODING'], "auto");
					$edesc = htmlentities($edesc, ENT_QUOTES, $this->cont['SOURCE_ENCODING']);
					$info .= "<tr style=\"vertical-align:top;\"><td>".$this->msg['cap_title']."</td><td>:</td><td>{$edesc}</td></tr>";
				}
	
				$cright = rtrim( @ $exif["COMPUTED"]["Copyright"]);
				$cphoto = rtrim( @ $exif["COMPUTED"]["Copyright.Photographer"]);
				$cedit  = rtrim( @ $exif["COMPUTED"]["Copyright.Editor"]);
	
				if ( $cphoto ){
					$cphoto = mb_convert_encoding($cphoto,$this->cont['SOURCE_ENCODING'], "auto");
					$cphoto = htmlentities($cphoto, ENT_QUOTES, $this->cont['SOURCE_ENCODING']);
					$info .= "<tr style=\"vertical-align:top;\"><td style=\"white-space:nowrap;\">".$this->msg['cap_shot_author']."</td><td>:</td><td>{$cphoto}</td></tr>";
				}
	
				if ( $cedit ){
					$cedit  = mb_convert_encoding($cedit,$this->cont['SOURCE_ENCODING'], "auto");
					$cedit  = htmlentities($cedit, ENT_QUOTES, $this->cont['SOURCE_ENCODING']);
					$info .= "<tr style=\"vertical-align:top;\"><td style=\"white-space:nowrap;\">".$this->msg['cap_edit_author']."</td><td>:</td><td>{$cedit}</td></tr>";
				}
	
				if ( ($cright) && !( $cphoto || $cedit ) ){ 
					$cright = mb_convert_encoding($cright,$this->cont['SOURCE_ENCODING'], "auto");
					$cright = htmlentities($cright, ENT_QUOTES, $this->cont['SOURCE_ENCODING']);
					$info .= "<tr style=\"vertical-align:top;\"><td>".$this->msg['cap_author']."</td><td>:</td><td>{$cright}</td></tr>";
				}
	
				$model = trim( @ $exif["IFD0"]["Model"]);
				$make  = trim( @ $exif["IFD0"]["Make"]);
				if ( $model ) {
					$model = htmlentities($model, ENT_QUOTES, $this->cont['SOURCE_ENCODING']);
					$make  = htmlentities( $make, ENT_QUOTES, $this->cont['SOURCE_ENCODING']);
					$info .= "<tr style=\"vertical-align:top;\"><td>".$this->msg['cap_model']."</td><td>:</td><td>{$model}". ( $make ? " ({$make})": "") . "</td></tr>";
				}
	
				if ( @ $exif["GPS"] ) {
					$lar = @ $exif["GPS"]["GPSLatitudeRef"];
					$lad = $this->ratstr2num(@ $exif["GPS"]["GPSLatitude"][0]);
					$lam = $this->ratstr2num(@ $exif["GPS"]["GPSLatitude"][1]);
					$las = $this->ratstr2num(@ $exif["GPS"]["GPSLatitude"][2]);
					list ($lad,$lam,$las) = $this->dms2dms($lad,$lam,$las);
					$lasm = round($las,2);
	
					$lor = @ $exif["GPS"]["GPSLongitudeRef"];
					$lod = $this->ratstr2num(@ $exif["GPS"]["GPSLongitude"][0]);
					$lom = $this->ratstr2num(@ $exif["GPS"]["GPSLongitude"][1]);
					$los = $this->ratstr2num(@ $exif["GPS"]["GPSLongitude"][2]);
					list ($lod,$lom,$los) = $this->dms2dms($lod,$lom,$los);
					$losm = round($los,2);
	
					if ( $datum = @ $exif["GPS"]["GPSMapDatum"] ) {
						$datum  = htmlentities($datum, ENT_QUOTES, $this->cont['SOURCE_ENCODING']);
						$edatum = "({$datum})";
					}
	
					if ( !$params['nokash'] ) {
						$lml = "<a href=\"http://lml.kashmir3d.com/getlml?".
						$this->MakeLMLURL($lar,$lad,$lam,$las,$lor,$lod,$lom,$los,$datum).
						"&amp;icon=915001&amp;name={$aname}&amp;url=http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}".rawurlencode($url)."\"><img src=\"".$this->config['KASH_ICON']."\" alt=\"kashmir3d\" /></a>";
					}
	
					if (!$params['nomapi']) {
						$mpi = $this->MakeMapionURL($lar,$lad,$lam,$las,$lor,$lod,$lom,$los,$datum);
						if ( $mpi ) {
							$mpi = "<a href=\"http://www.mapion.co.jp/c/f?scl=250000&amp;pnf=1&amp;uc=1&amp;grp=all&amp;size=500,500&amp;{$mpi}\"><img width=\"15\" height=\"15\" src=\"".$this->config['MAPI_ICON']."\" alt=\"mapion\" /></a>";
						}
					}
	
					$info .= <<<EOD
<tr style="vertical-align:top;"><td style="white-space:nowrap;">{$this->msg['cap_location']}</td><td>:</td><td>{$lar}{$lad}'{$lam}'{$lasm}"-{$lor}{$lod}'{$lom}'{$losm}"{$edatum}</td></tr><tr><td></td><td></td><td>$lml $mpi</td></tr>
EOD;
				}
	
				if ( $ucom = trim(@ $exif["COMPUTED"]["UserComment"])) {
					$ucom = mb_convert_encoding($ucom,$this->cont['SOURCE_ENCODING'], "auto");
					$ucom = "<p>".htmlentities("$ucom", ENT_QUOTES, $this->cont['SOURCE_ENCODING'])."</p>";
					$ucom = str_replace( "\r\n", "</p><p>","$ucom");
					$ucom = str_replace( "\r",   "</p><p>","$ucom");
					$ucom = str_replace( "\n",   "</p><p>","$ucom");
					$ucom = str_replace( "</p><p></p><p>","</p><p>","$ucom");
					$ucom = str_replace( "</p><p></p><p>","</p><p>","$ucom");
				}
				if ( $params['ucomedit'] &&
					is_array($exif["COMPUTED"]) && array_key_exists('UserComment', $exif["COMPUTED"])) {
					$attachObj = new XpWikiAttachFile($this->xpwiki, $page, $aname);
					if ($attachObj->is_owner()) {
						$script = $this->func->get_script_uri();
						$ucom .= <<<EOD
<form action="{$script}" method="post">
<div style="text-align:right;margin:0px 4px 4px 0px;"><input type="hidden" name="plugin" value="ucomedit" />
<input type="hidden" name="refer"  value="$page" />
<input type="hidden" name="target" value="{$aname}" />
<input type="submit" name="mode" value="edit" /></div>
</form>
EOD;
					}
				}
	
				$sz = @ $exif['FILE']['FileSize'];
			}
	
			if ( $sz > 1024*10) {
				$sz = (int)($sz/1024)."KB";
			} else {
				$sz = $sz."Bytes";
			}
	
			$sztype = $eh > $ew ? "height": "width";
	
			if ( $this->config['THUMB_USE'] ) {
				$img = $this->func->do_plugin_inline('ref', $page . '/' . $aname . ',mh:'. $this->config['DEFAULT_MH']. ',mw:'.$this->config['DEFAULT_MW']);
			}
	
			$img = "<a href=\"{$url}\">" . 
			(( $params['noimg'] ) ? 
					"{$aname}</a>" :
					( $this->config['THUMB_USE'] ?
					$img . '</a>' :
					"<img {$sztype}=\"".$this->config['THUMB_WSIDE_LEN']."\" src=\"{$url}\" /></a>" ));
	
			$params['_body'] .= 
				(( $cnt % $colmn) == 0 ?"<tr class=\"style_td\">":'').
			"<td align=\"center\">{$img}".
			($params['noimg']?'':"<br />{$aname}").
			"<br />{$ew}x{$eh}<br />({$sz})</td>".
			($exif_extension ? "<td>".($info ? "<table summary=\"SubInfo\" style=\"border-spacing:0px 0px;\">{$info}</table>":'')."</td><td style=\"text-indent:1em;\">{$ucom}</td>":'').
			(( $colmn-($cnt%$colmn)) == 1 ? "</tr>":'');
	
			$cnt++;
		}
		$params['_body'] .= (($cnt%$colmn)?"</tr>":'').'</table>';
	
		return $params;
	}
	
	//-----------------------------------------------------------------------------
	function ratstr2num( $str)
	{
		list( $ch, $mot) = explode( "/", $str);
	
		return $mot == 0 ? 0: ($ch/$mot);
	}
	
	
	function dms2dms($d,$m,$s)
	{
		$do = $d*600 + $m*10.0 +$s/6.0;
	
		$td = (int)($do/600);
		$tm = (int)(($do - $td*600)/10);
		$ts = ( $do - $td*600 - $tm*10)*6.0;
	
		return array( $td,$tm,$ts);
	}
	
	//function dms2dms($d,$m,$s)
	//{
	//	$do = $d + $m/60.0 +$s/3600.0;
	//
	//	$td = ceil($do)-1;
	//	$td = $td < 0 ? 0: $td;
	//	$tm = ceil(( $do - $td )*60)-1;
	//	$tm = $tm < 0 ? 0: $tm;
	//	$ts = ( $do - $td - $tm/60.0)*3600.0;
	//
	//	return array( $td,$tm,$ts);
	//}
	
	
	function MakeLMLURL($latr,$latd,$latm,$lats,$lotr,$lotd,$lotm,$lots,$datum)
	{
	    if ( stristr( $datum, "WGS") && stristr( $datum, "84")) {
		$datum = "WGS84";
	    } else {
		$datum = "Tokyo";
	    }
	
	    if ( !strcmp("$latr","N")) { $latr=""; } else { $latr="-"; }
	    if ( !strcmp("$lotr","E")) { $lotr=""; } else { $lotr="-"; }
	
	    $lats = ceil($lats*10)-1+1000;
	    $lots = ceil($lots*10)-1+1000;
	
	    $lats = substr("$lats",1);
	    $lots = substr("$lots",1);
	
	    $latm = $latm+100;
	    $latm = substr("$latm",1,2);
	    $lotm = $lotm+100;
	    $lotm = substr("$lotm",1,2);
	
	    return ( "lat=$latr$latd.$latm$lats&amp;lon=$lotr$lotd.$lotm$lots&amp;datum=$datum");
	}
	
	function MakeMapionURL($latr,$latd,$latm,$lats,$lotr,$lotd,$lotm,$lots,$datum)
	{
	    if ( !strcmp($latr,"S") || !strcmp($lotr,"W") || $latd > 50 || $latd < 20 ||
		$lotd > 150 || $lotd < 120 ) {
		return "";
	    }
	
	    if ( stristr( $datum, "WGS") && stristr( $datum, "84")) {
		list ($latd,$latm,$lats,$lotd,$lotm,$lots) = $this->WGS84toTOKYO($latd,$latm,$lats,
							     $lotd,$lotm,$lots);
	    }
	
	    if ( !strcmp("$latr","N")) { $latr="nl"; } else { $latr="sl"; }
	    if ( !strcmp("$lotr","E")) { $lotr="el"; } else { $lotr="wl"; }
	
	    $lats = round($lats,2);
	    $lots = round($lots,2);
	
	    return ( "$latr=$latd/$latm/$lats&amp;$lotr=$lotd/$lotm/$lots" );
	}
	
	function WGS84toTOKYO($latd,$latm,$lats,$lotd,$lotm,$lots)
	{
	    $b = $latd + $latm/60.0 + $lats/3600.0;
	    $l = $lotd + $lotm/60.0 + $lots/3600.0;
	
	    // Mr. Toshiaki UMEMURA's simple trans. method
	    // See  http://member.nifty.ne.jp/Nowral/
	
	    $tb = $b + 0.000106960*$b - 0.000017467*$l - 0.0046020;
	    $tl = $l + 0.000046047*$b + 0.000083049*$l - 0.0100410;
	
	    list ($latd,$latm,$lats) = $this->dms2dms($tb,0,0);
	    list ($lotd,$lotm,$lots) = $this->dms2dms($tl,0,0);
	
	    return array($latd,$latm,$lats,$lotd,$lotm,$lots);
	}
}
?>