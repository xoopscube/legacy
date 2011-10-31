<?php
// 添付されたExif画像ファイルのUserCommentを編集するプラグイン
// $Id: ucomedit.inc.php,v 1.2 2009/02/22 01:41:35 nao-pon Exp $
// $ORG: ucomedit.inc.php,v 1.12 2004/03/09 10:10:00 m-arai Exp $
// 
// ＊注意＊
// 以下をを見ればお分かりになると思いますが、プログラムを簡易にするために
// かなりいい加減なことをしています。ファイルが破損してしまうだけに終る
// かもしれません。
// いい加減さの一つとして、オリジナルにUserCommentタグが存在していない
// ものには使用できません
// また、大抵の場合、オリジナルのUserCommentデータはゴミとしてファイル中に
// 残ります。
// 修正の衝突も関知しません。
// 表示の体裁はおざなりです。
//
// #ucomedit( filename[, formonly]);
//

class xpwiki_plugin_ucomedit extends xpwiki_plugin {
	function plugin_ucomedit_init () {

		// データのエンディアン
		$this->config['TYPE_LE'] =  0;
		$this->config['TYPE_BE'] =  1;
	
		// テキストエリアの大きさ
		$this->config['UCOMED_TXTROWS'] =   6;
		$this->config['UCOMED_TXTCOLS'] =  60;
	
		// 画像表示幅
		$this->config['UCOMED_WIDTH'] =  200;
	
		// 編集権限確認を行なう
		$this->config['UCOMED_CHKAUTH'] =  0;
		
		// UserComment の文字コード
		$this->config['UCOMED_ENCORD'] = 'SJIS';

	}
	
	function plugin_ucomedit_convert()
	{
		list($fname,$mode) = func_get_args();
	
		$page = $this->root->vars['page'];
	
		return $this->ucomedit_makeform( $page, $fname, $mode == "formonly");
	}
	
	function ucomedit_makeform( $page, $fname, $mode=flase)
	{
		$rname = $this->cont['UPLOAD_DIR'].$this->func->encode($page)."_".$this->func->encode($fname);
	
		if ( !is_readable($rname)) {
			return "not found.";
		}
	
		$exif = exif_read_data( $rname, 0, true);
	
		if ( !array_key_exists('UserComment', $exif['COMPUTED'])) {
			return "unsupported type.";
		}
	
		$ucom = mb_convert_encoding( $exif['COMPUTED']['UserComment'],
				$this->cont['SOURCE_ENCODING'], "auto");
		$rows = $this->config['UCOMED_TXTROWS'];
		$cols = $this->config['UCOMED_TXTCOLS'];
		$text = <<<EOD
<form action="{$this->root->script}" method="post">
<div><textarea name="usercomm" rows="{$rows}" cols="$cols" rel="nohelper">{$ucom}
</textarea>
<input type="hidden" name="plugin" value="ucomedit" />
<input type="hidden" name="refer"  value="$page" />
<input type="hidden" name="target" value="$fname" />
</div><div><input type="submit" name="mode" value="write" /></div>
</form>
EOD;
	
		if ( $mode ) {
			return $text;
		}
	
		$width = $this->config['UCOMED_WIDTH'];
		$img = $this->func->do_plugin_inline('ref', $page . '/' . $fname . ',mw:'.$width);
		
		$pre = <<<EOD
<div style="align:right;">
<table border="1"><tr><td>
<table><tr class="style_th"><th>$fname</th><th>Comment</th></tr>
<tr><td style="width:{$width}px;">
$img
</td><td>
EOD;
		$suf = '</td></tr></table></td></tr></table></div>';
	
		return $pre.$text.$suf;
	}
	
	function plugin_ucomedit_action()
	{
		$s_target = htmlspecialchars($this->root->post['target']);
		$mode = $this->root->post['mode'];
		$attachObj = new XpWikiAttachFile($this->xpwiki, $this->root->post['refer'], $this->root->post['target']);
		if (! $attachObj->is_owner()) {
			$mode = 'failed';
		}
		switch ($mode) {
			case 'write':
				// 編集権限確認
				( $this->config['UCOMED_CHKAUTH'] && $this->func->check_editable($this->root->post['refer']));
	
				if (($ncom = $this->root->post['usercomm'])) {
					$ncom = mb_convert_encoding($ncom,$this->config['UCOMED_ENCORD'],"auto");
					$fname = $this->cont['UPLOAD_DIR'].$this->func->encode($this->root->post['refer'])."_".$this->func->encode($this->root->post['target']);
					$msg = $this->ucomedit_brand( $fname, $ncom) ?
						'Updated UserComment of $1.': 'Update UserComment of $1 failed.';
					$msg = str_replace('$1', $s_target, $msg);
				}
				return array('msg'=>$msg);
			case 'failed':
				$msg = 'You can not edit UserComment of $1.';
				$msg = str_replace('$1', $s_target, $msg);
				return array('msg'=>$msg);
			case 'edit':
			default:
				return array('msg'=>"Edit UserComment of {$s_target}",
				'body'=>$this->ucomedit_makeform( $this->root->post['refer'], $this->root->post['target'],false));
		}
	}
	
	function ucomedit_write_word( $idx, $word)
	{
	//	global $edm,$buff;
	
		if ( $this->root->edm == $this->config['TYPE_LE'] ) {
			$this->root->buff[$idx+1] = chr(($word>>8)&0xff);
			$this->root->buff[$idx]   = chr($word&0xff);
		} else {
			$this->root->buff[$idx]   = chr(($word>>8)&0xff);
			$this->root->buff[$idx+1] = chr($word&0xff);
		}
	
		return;
	}
	
	function ucomedit_read_word( $idx)
	{
	//	global $edm,$buff;
	
		$a = ord($this->root->buff[$idx]); $b = ord($this->root->buff[$idx+1]);
	
		return $this->root->edm == $this->config['TYPE_LE'] ? (($b<<8)+$a):(($a<<8)+$b);
	}
	
	function ucomedit_write_long( $idx, $long)
	{
	//	global $edm,$buff;
	
		if ( $this->root->edm == $this->config['TYPE_LE'] ) {
			$this->root->buff[$idx+3] = chr(($long>>24)&0xff);
			$this->root->buff[$idx+2] = chr(($long>>16)&0xff);
			$this->root->buff[$idx+1] = chr(($long>>8) &0xff);
			$this->root->buff[$idx]   = chr($long&0xff);
		} else {
			$this->root->buff[$idx]   = chr(($long>>24)&0xff);
			$this->root->buff[$idx+1] = chr(($long>>16)&0xff);
			$this->root->buff[$idx+2] = chr(($long>>8) &0xff);
			$this->root->buff[$idx+3] = chr($long&0xff);
		}
	
		return;
	}
	
	function ucomedit_read_long( $idx)
	{
	//	global $edm,$buff;
	
		$a = ord($this->root->buff[$idx]);   $b = ord($this->root->buff[$idx+1]);
		$c = ord($this->root->buff[$idx+2]); $d = ord($this->root->buff[$idx+3]);
	
		return $this->root->edm == $this->config['TYPE_LE'] ?
			(($d<<24)+($c<<16)+($b<<8)+$a):(($a<<24)+($b<<16)+($c<<8)+$d);
	}
	
	function ucomedit_search( $ibase)
	{
	//	global $buff; // 読み込みバッファ
	
		/* Directory entry no. */
		$i = $this->ucomedit_read_word( $ibase);
	
		$idxmax = strlen($this->root->buff);
	
		for ( $ptr=2,$j=0; $j<$i; $j++,$ptr+=12) {
			if ( ($ibase+$ptr) > $idxmax) {
				return;
			} 
			$tag	= $this->ucomedit_read_word( $ibase+$ptr);
			$data_o	= $this->ucomedit_read_long( $ibase+$ptr+8);
	
		    switch ( $tag) {
			    case 0x9286: /* UserComment */
	    			$ret = $ibase+$ptr;
					break;
		    	case 0x8769: /* ExifIFD */
					if ( $tmp = $this->ucomedit_search( $data_o+6)) {
						if ( $tmp ) {
							$ret = $tmp;
						}
						break;
					}
			}
	  }
	  return $ret;
	}
	
	function ucomedit_brand( $fname, $new)
	{
	//	global $buff,$edm; // 読み込みバッファ、エンディアン
	
		($fhr = fopen( $fname, "rb")) || die("can't open $fname<br>");
		flock($fhr, LOCK_EX);
	
		$tname = tempnam($this->cont['UPLOAD_DIR'], "ucomed");
		($fhw = fopen( $tname, "wb")) ||  die("can't open out.jpg<br>");
	
		if ( strlen($this->root->buff = fread( $fhr, 2)) != 2) {
			flock($fhr, LOCK_UN); fclose($fhr); fclose($fhw);
			unlink($tname);
			return false;
		}
		fwrite( $fhw, $this->root->buff, 2);
	
		if ( $this->root->buff != "\xff\xd8" ) {
			flock($fhr,LOCK_UN); fclose($fhr);fclose($fhw);
			unlink($tname);
			return false;
		}
	
		/* read 2byte: 1st = 0xff ,2nd = marker num */
		for ( $l=0; strlen($this->root->buff = fread( $fhr, 2)) == 2 ; $l++) {
			fwrite( $fhw, $this->root->buff, 2);
	
			if ($this->root->buff[0] != "\xff" ) {
				break;
			}
	
			$mnum = $this->root->buff[1]; // マーカ
	
			switch ( $mnum) {
				case "\xd9": /* EOI */
			      break;
	
				case "\xda": /* SOS */
			      break;
	
				case "\xe1": /* APP1 */
	
					if ( strlen( $szh = fread( $fhr, 2)) != 2) {
						flock($fhr,LOCK_UN); fclose($fhr);fclose($fhw);
						unlink($tname);
						return false;
					}
					$dsize = (ord($szh[0])<<8)+ord($szh[1]);
					if ( ($dsize-2) != strlen( $this->root->buff = fread( $fhr, $dsize-2))) {
						flock($fhr,LOCK_UN); fclose($fhr);fclose($fhw);
						unlink($tname);
						return false;
					}
	
					if ( strncmp( "Exif", $this->root->buff,4) != 0 ) {
						fwrite($fhw,$szh.$this->root->buff);
		   				break;
					}
	
					$fc = $this->root->buff[6].$this->root->buff[7];
	
					if ( $fc == 'II' ) {
						$this->root->edm = $this->config['TYPE_LE'];
					} else {
						$this->root->edm = $this->config['TYPE_BE'];
					}
	
					$idx = $this->ucomedit_read_long(10)+6;
	
					$uidx = $this->ucomedit_search( $idx);
	
					if ( $uidx ) {
						$cnt = $this->ucomedit_read_long( $uidx+4);	/*カウント*/
						$off = $this->ucomedit_read_long( $uidx+8);	/*オフセット*/
	
			/* $this->func->ユーザコメント(8byteのコードセット含む)は  buff+d+6 から始まる。*/
						$new = "\0\0\0\0\0\0\0\0".trim($new)."\0";
						$newlen = strlen($new);
	
						$this->ucomedit_write_long($uidx+4, $newlen);
	
						if (($cnt+$off+6) != ($dsize-2)) {
							/* ブロックの最後尾ではないので,無駄なことをする */
							$this->ucomedit_write_long($uidx+8, $dsize-8);
						}
	
						if (($cnt+$off+6) == ($dsize-2)) {
							/* 最後の$cnt byteはUserCommentの分 */
							$dsize -= $cnt;
						}
	
					}
	
					if ( $uidx ) {
						$szh[0] = chr(($dsize+$newlen)>>8);
						$szh[1] = chr(0xff&($dsize+$newlen));
					}
					fwrite( $fhw, $szh, 2);
					fwrite( $fhw, $this->root->buff, $dsize-2);
	
					if ( $uidx ) {
						fwrite( $fhw, $new, $newlen);
					}
	
					break;
	
				default:
					if (strlen($szh = fread($fhr,2)) != 2) {
						flock($fhr,LOCK_UN); fclose($fhr);fclose($fhw);
						unlink($tname);
						return false;
					}
					$dsize = (ord($szh[0])<<8)+ord($szh[1]);
					if (($dsize-2) != strlen( $this->root->buff = fread($fhr,$dsize-2))) {
						flock($fhr,LOCK_UN); fclose($fhr);fclose($fhw);
						unlink($tname);
						return false;
					}
	
					fwrite( $fhw, $szh.$this->root->buff, $dsize);
					break;
			}
		}
	
		do {
			$len = strlen( $this->root->buff = fread($fhr, 64*1024));
			fwrite( $fhw, $this->root->buff, $len);
		} while ( $len == 64*1024);
	
		flock($fhr,LOCK_UN); fclose($fhr); fclose( $fhw);
		unlink($fname);
		rename( $tname, $fname);
		
		return true;
	}
}
?>