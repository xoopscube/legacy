<?php /* vim: set fdm=marker: */
/*************************************************************************
                      ________________________________

                             jcode.php by TOMO
                      ________________________________


 [Version] : 1.35 (2004/04/10)
 [URL]     : http://www.spencernetwork.org/
 [E-MAIL]  : groove@spencernetwork.org
 [Changes] :
     v1.30 Changed XXXtoUTF8 and UTF8toXXX with conversion tables.
     v1.31 Deleted a useless and harmful line in JIStoUTF8() (^^;
     v1.32 Fixed miss type of jsubstr().
           Fixed HANtoZEN_EUC(), HANtoZEN_SJIS() and HANtoZEN_JIS().
     v1.33 Fixed JIStoXXX(), HANtoZEN_JIS() and ZENtoHAN_JIS().
           Added jstr_split() as O-MA-KE No.4.
           Added jstrcut() as O-MA-KE No.5.
           Changed the logic of AutoDetect()
     v1.34 Fixed ZENtoHAN_SJIS()
     v1.35 Fixed ZENtoHAN_SJIS()
           Fixed jstr_replace()
           Changed file extension from ".phps" to ".php".

 * jcode.phps is free but without any warranty.
 * use this script at your own risk.

***************************************************************************/

/* {{{ JcodeConvert() */
function JcodeConvert(&$str, $from, $to)
{
	//0:AUTO DETECT
	//1:EUC-JP
	//2:Shift_JIS
	//3:ISO-2022-JP(JIS)
	//4:UTF-8

	if ($from == 0) $from = AutoDetect($str);

	if ($from == 1 && $to == 2) return EUCtoSJIS($str);
	if ($from == 1 && $to == 3) return EUCtoJIS($str);
	if ($from == 1 && $to == 4) return EUCtoUTF8($str);
	if ($from == 2 && $to == 1) return SJIStoEUC($str);
	if ($from == 2 && $to == 3) return SJIStoJIS($str);
	if ($from == 2 && $to == 4) return SJIStoUTF8($str);
	if ($from == 3 && $to == 1) return JIStoEUC($str);
	if ($from == 3 && $to == 2) return JIStoSJIS($str);
	if ($from == 3 && $to == 4) return JIStoUTF8($str);
	if ($from == 4 && $to == 1) return UTF8toEUC($str);
	if ($from == 4 && $to == 2) return UTF8toSJIS($str);
	if ($from == 4 && $to == 3) return UTF8toJIS($str);

	return $str;
} /* }}} JcodeConvert() */

/* {{{ AutoDetect() */
function AutoDetect(&$str)
{
	//0:US-ASCII
	//1:EUC-JP
	//2:Shift_JIS
	//3:ISO-2022-JP(JIS)
	//4:UTF-8
	//5:Unknown

	if (!ereg("[\x80-\xFF]", $str)) {
		// --- Check ISO-2022-JP ---
		if (ereg("\x1B", $str)) return 3; // ISO-2022-JP(JIS)
		return 0; //US-ASCII
	}

	$b = unpack('C*', ereg_replace("^[^\x80-\xFF]+", "", $str));
	$n = count($b);

	// --- Check EUC-JP ---
	$euc = TRUE;
	for ($i = 1; $i <= $n; ++$i){
		if ($b[$i] < 0x80) {
			continue;
		}
		if ($b[$i] < 0x8E) {
			$euc = FALSE; break;
		}
		if ($b[$i] == 0x8E) {
			if (!isset($b[++$i])) {
				$euc = FALSE; break;
			}
			if (($b[$i] < 0xA1) || (0xDF < $b[$i])) {
				$euc = FALSE; break;
			}
		} elseif ((0xA1 <= $b[$i]) && ($b[$i] <= 0xFE)) {
			if (!isset($b[++$i])) {
				$euc = FALSE; break;
			}
			if (($b[$i] < 0xA1) || (0xFE < $b[$i])) {
				$euc = FALSE; break;
			}
		} else {
			$euc = FALSE; break;
		}
	}
	if ($euc) return 1; // EUC-JP

	// --- Check UTF-8 ---
	$utf8 = TRUE;
	for ($i = 1; $i <= $n; ++$i) {
		if (($b[$i] < 0x80)) {
			continue;
		}
		if ((0xC0 <= $b[$i]) && ($b[$i] <=0xDF)) {
			if (!isset($b[++$i])) {
				$utf8 = FALSE; break;
			}
			if (($b[$i] < 0x80) || (0xEF < $b[$i])) {
				$utf8 = FALSE; break;
			}
		} elseif ((0xE0 <= $b[$i]) && ($b[$i] <= 0xEF)) {
			if (!isset($b[++$i])) {
				$utf8 = FALSE; break;
			}
			if (($b[$i] < 0x80) || (0xBF < $b[$i])) {
				$utf8 = FALSE; break;
			}
			if (!isset($b[++$i])) {
				$utf8 = FALSE; break;
			}
			if (($b[$i] < 0x80) || (0xBF < $b[$i])) {
				$utf8 = FALSE; break;
			}
		} else {
			$utf8 = FALSE; break;
		}
	}
	if ($utf8) return 4; // UTF-8

	// --- Check Shift_JIS ---
	$sjis = TRUE;
	for ($i = 1; $i <= $n; ++$i) {
		if (($b[$i] <= 0x80) || (0xA1 <= $b[$i] && $b[$i] <= 0xDF)) {
			continue;
		}
		if (($b[$i] == 0xA0) || ($b[$i] > 0xEF)) {
			$sjis = FALSE; break;
		}
		if (!isset($b[++$i])) {
			$sjis = FALSE; break;
		}
		if (($b[$i] < 0x40) || ($b[$i] == 0x7F) || ($b[$i] > 0xFC)){
			$sjis = FALSE; break;
		}
	}
	if ($sjis) return 2; // Shift_JIS

	return 5; // Unknown
} /* }}} AutoDetect() */

/* {{{ HANtoZEN() */
function HANtoZEN(&$str, $encode)
{
	//0:PASS
	//1:EUC-JP
	//2:Shift_JIS
	//3:ISO-2022-JP(JIS)
	//4:UTF-8

	if ($encode == 0) return $str;
	if ($encode == 1) return HANtoZEN_EUC($str);
	if ($encode == 2) return HANtoZEN_SJIS($str);
	if ($encode == 3) return HANtoZEN_JIS($str);
	if ($encode == 4) return HANtoZEN_UTF8($str);

	return $str;
} /* }}} HANtoZEN() */

/* {{{ ZENtoHAN() */
function ZENtoHAN(&$str, $encode, $kana=1, $alph=1)
{
	//0:PASS
	//1:EUC-JP
	//2:Shift_JIS
	//3:ISO-2022-JP(JIS)

	if ($encode == 0) return $str;
	if ($encode == 1) return ZENtoHAN_EUC($str,  $kana, $alph, $kana);
	if ($encode == 2) return ZENtoHAN_SJIS($str, $kana, $alph, $kana);
	if ($encode == 3) return ZENtoHAN_JIS($str, $kana, $alph, $kana);

	return $str;
} /* }}} ZENtoHAN() */

/* {{{ JIStoSJIS() */
function JIStoSJIS(&$str_JIS)
{
	$str_SJIS = '';
	$mode = 0;
	$b = unpack('C*', $str_JIS);
	$n = count($b);

	for ($i = 1; $i <= $n; ++$i) {

		//Check escape sequence
		while ($b[$i] == 0x1B) {
			if (($b[$i+1] == 0x24 && $b[$i+2] == 0x42)
				|| ($b[$i+1] == 0x24 && $b[$i+2] == 0x40)) {
				$mode = 1;
			} elseif (($b[$i+1] == 0x28 && $b[$i+2] == 0x49)) {
				$mode = 2;
			} else {
				$mode = 0;
			}
			$i += 3;
			if (!isset($b[$i])) break 2;
		}

		//Do convert
		if ($mode == 1) {
			$b1 = $b[$i];
			$b2 = $b[++$i];
			if ($b1 & 0x01) {
				$b1 >>= 1;
				if ($b1 < 0x2F) $b1 += 0x71; else $b1 -= 0x4F;
				if ($b2 > 0x5F) $b2 += 0x20; else $b2 += 0x1F;
			} else {
				$b1 >>= 1;
				if ($b1 <= 0x2F) $b1 += 0x70; else $b1 -= 0x50;
				$b2 += 0x7E;
			}
			$str_SJIS .= chr($b1).chr($b2);
		} elseif ($mode == 2) {
			$str_SJIS .= chr($b[$i] + 0x80);
		} else {
			$str_SJIS .= chr($b[$i]);
		}
	}

	return $str_SJIS;
} /* }}} JIStoSJIS() */

/* {{{ JIStoEUC() */
function JIStoEUC(&$str_JIS)
{
	$str_EUC = '';
	$mode = 0;
	$b = unpack('C*', $str_JIS);
	$n = count($b);

	for ($i = 1; $i <= $n; ++$i) {

		//Check escape sequence
		while ($b[$i] == 0x1B) {
			if (($b[$i+1] == 0x24 && $b[$i+2] == 0x42)
				|| ($b[$i+1] == 0x24 && $b[$i+2] == 0x40)) {
				$mode = 1;
			} elseif (($b[$i+1] == 0x28 && $b[$i+2] == 0x49)) {
				$mode = 2;
			} else {
				$mode = 0;
			}
			$i += 3;
			if (!isset($b[$i])) break 2;
		}

		//Do convert
		if ($mode == 1) {
			$str_EUC .= chr($b[$i] + 0x80).chr($b[++$i] + 0x80);
		} elseif ($mode == 2) {
			$str_EUC .= chr(0x8E).chr($b[$i] + 0x80);
		} else {
			$str_EUC .= chr($b[$i]);
		}
	}

	return $str_EUC;
} /* }}} JIStoEUC() */

/* {{{ SJIStoJIS() */
function SJIStoJIS(&$str_SJIS)
{
	$str_JIS = '';
	$mode = 0;
	$b = unpack('C*', $str_SJIS);
	$n = count($b);

	//Escape sequence
	$ESC = array(chr(0x1B).chr(0x28).chr(0x42),
		     chr(0x1B).chr(0x24).chr(0x42),
		     chr(0x1B).chr(0x28).chr(0x49));

	for ($i = 1; $i <= $n; ++$i) {
		$b1 = $b[$i];
		if (0xA1 <= $b1 && $b1 <= 0xDF) {
			if ($mode != 2) {
				$mode = 2;
				$str_JIS .= $ESC[$mode];
			}
			$str_JIS .= chr($b1 - 0x80);
		} elseif ($b1 >= 0x80) {
			if ($mode != 1) {
				$mode = 1;
				$str_JIS .= $ESC[$mode];
			}
			$b2 = $b[++$i];
			$b1 <<= 1;
			if ($b2 < 0x9F) {
				if ($b1 < 0x13F) $b1 -= 0xE1; else $b1 -= 0x61;
				if ($b2 > 0x7E)  $b2 -= 0x20; else $b2 -= 0x1F;
			} else {
				if ($b1 < 0x13F) $b1 -= 0xE0; else $b1 -= 0x60;
				$b2 -= 0x7E;
			}
			$str_JIS .= chr($b1).chr($b2);
		} else {
			if ($mode != 0) {
				$mode = 0;
				$str_JIS .= $ESC[$mode];
			}
			$str_JIS .= chr($b1);
		}
	}
	if ($mode != 0) $str_JIS .= $ESC[0];

	return $str_JIS;
} /* }}} SJIStoJIS() */

/* {{{ SJIStoEUC() */
function SJIStoEUC(&$str_SJIS)
{
	$b = unpack('C*', $str_SJIS);
	$n = count($b);
	$str_EUC = '';

	for ($i = 1; $i <= $n; ++$i) {
		$b1 = $b[$i];
		if (0xA1 <= $b1 && $b1 <= 0xDF) {
			$str_EUC .= chr(0x8E).chr($b1);
		} elseif ($b1 >= 0x81) {
			$b2 = $b[++$i];
			$b1 <<= 1;
			if ($b2 < 0x9F) {
				if ($b1 < 0x13F) $b1 -= 0x61; else $b1 -= 0xE1;
				if ($b2 > 0x7E)  $b2 += 0x60; else $b2 += 0x61;
			} else {
				if ($b1 < 0x13F) $b1 -= 0x60; else $b1 -= 0xE0;
				$b2 += 0x02;
			}
			$str_EUC .= chr($b1).chr($b2);
		} else {
			$str_EUC .= chr($b1);
		}
	}

	return $str_EUC;
} /* }}} SJIStoEUC() */

/* {{{ EUCtoJIS() */
function EUCtoJIS(&$str_EUC)
{
	$str_JIS = '';
	$mode = 0;
	$b = unpack('C*', $str_EUC);
	$n = count($b);

	//Escape sequence
	$ESC = array(chr(0x1B).chr(0x28).chr(0x42),
		     chr(0x1B).chr(0x24).chr(0x42),
		     chr(0x1B).chr(0x28).chr(0x49));

	for ($i = 1; $i <= $n; ++$i) {
		$b1 = $b[$i];
		if ($b1 == 0x8E) {
			if ($mode != 2) {
				$mode = 2;
				$str_JIS .= $ESC[$mode];
			}
			$str_JIS .= chr($b[++$i] - 0x80);
		} elseif ($b1 > 0x8E) {
			if ($mode != 1) {
				$mode = 1;
				$str_JIS .= $ESC[$mode];
			}
			$str_JIS .= chr($b1 - 0x80).chr($b[++$i] - 0x80);
		} else {
			if ($mode != 0) {
				$mode = 0;
				$str_JIS .= $ESC[$mode];
			}
			$str_JIS .= chr($b1);
		}
	}
	if ($mode != 0) $str_JIS .= $ESC[0];

	return $str_JIS;
} /* }}} EUCtoJIS() */

/* {{{ EUCtoSJIS() */
function EUCtoSJIS(&$str_EUC)
{
	$str_SJIS = '';
	$b = unpack('C*', $str_EUC);
	$n = count($b);

	for ($i = 1; $i <= $n; ++$i) {
		$b1 = $b[$i];
		if ($b1 > 0x8E) {
			$b2 = $b[++$i];
			if ($b1 & 0x01) {
				$b1 >>= 1;
				if ($b1 < 0x6F) $b1 += 0x31; else $b1 += 0x71;
				if ($b2 > 0xDF) $b2 -= 0x60; else $b2 -= 0x61;
			} else {
				$b1 >>= 1;
				if ($b1 <= 0x6F) $b1 += 0x30; else $b1 += 0x70;
				$b2 -= 0x02;
			}
			$str_SJIS .= chr($b1).chr($b2);
		} elseif ($b1 == 0x8E) {
			$str_SJIS .= chr($b[++$i]);
		} else {
			$str_SJIS .= chr($b1);
		}
	}

	return $str_SJIS;
} /* }}} EUCtoSJIS() */

/* {{{ SJIStoUTF8() */
function SJIStoUTF8(&$str_SJIS)
{
	global $table_jis_utf8;

	$str_UTF8 = '';
	$b = unpack('C*', $str_SJIS);
	$n = count($b);

	for ($i = 1; $i <= $n; ++$i) {
		if (0xA1 <= $b[$i] && $b[$i] <= 0xDF) { //Hankaku
			$b2 = $b[$i] - 0x40;
			$u2 = 0xBC | (($b2 >> 6) & 0x03);
			$u3 = 0x80 | ($b2 & 0x3F);
			$str_UTF8 .= chr(0xEF).chr($u2).chr($u3);
		} elseif ($b[$i] >= 0x80) { //Zenkaku
			$b1 = $b[$i] << 1;
			$b2 = $b[++$i];
			if ($b2 < 0x9F) {
				if ($b1 < 0x13F) $b1 -= 0xE1; else $b1 -= 0x61;
				if ($b2 > 0x7E)  $b2 -= 0x20; else $b2 -= 0x1F;
			} else {
				if ($b1 < 0x13F) $b1 -= 0xE0; else $b1 -= 0x60;
				$b2 -= 0x7E;
			}
			$b1 &= 0xFF;
			$jis = ($b1 << 8) + $b2;
			if (isset($table_jis_utf8[$jis])) {
				$utf8 = $table_jis_utf8[$jis];
				if ($utf8 < 0xFFFF) {
					$str_UTF8 .= chr($utf8 >> 8).chr($utf8);
				} else {
					$str_UTF8 .= chr($utf8 >> 16).chr($utf8 >> 8).chr($utf8);
				}
			} else {
				$str_UTF8 .= '?'; //Unknown
			}
		} else { //ASCII
			$str_UTF8 .= chr($b[$i]);
		}
	}

	return $str_UTF8;
} /* }}} SJIStoUTF8() */

/* {{{ EUCtoUTF8() */
function EUCtoUTF8(&$str_EUC)
{
	global $table_jis_utf8;

	$str_UTF8 = '';
	$b = unpack('C*', $str_EUC);
	$n = count($b);

	for ($i = 1; $i <= $n; ++$i) {
		if ($b[$i] == 0x8E) { //Hankaku
			$b2 = $b[++$i] - 0x40;
			$u2 = 0xBC | (($b2 >> 6) & 0x03);
			$u3 = 0x80 | ($b2 & 0x3F);
			$str_UTF8 .= chr(0xEF).chr($u2).chr($u3);
		} elseif ($b[$i] >= 0x80) { //Zenkaku
			$jis = (($b[$i] - 0x80) << 8) + ($b[++$i] - 0x80);
			if (isset($table_jis_utf8[$jis])) {
				$utf8 = $table_jis_utf8[$jis];
				if ($utf8 < 0xFFFF) {
					$str_UTF8 .= chr($utf8 >> 8).chr($utf8);
				} else {
					$str_UTF8 .= chr($utf8 >> 16).chr($utf8 >> 8).chr($utf8);
				}
			} else { //Unknown
				$str_UTF8 .= '?';
			}
		} else { //ASCII
			$str_UTF8 .= chr($b[$i]);
		}
	}

	return $str_UTF8;
} /* }}} EUCtoUTF8() */

/* {{{ JIStoUTF8() */
function JIStoUTF8(&$str_JIS)
{
	global $table_jis_utf8;

	$str_UTF8 = '';
	$mode = 0;
	$b = unpack('C*', $str_JIS);
	$n = count($b);

	for ($i = 1; $i <= $n; ++$i) {

		//Check escape sequence
		while ($b[$i] == 0x1B) {
			if (($b[$i+1] == 0x24 && $b[$i+2] == 0x42)
				|| ($b[$i+1] == 0x24 && $b[$i+2] == 0x40)) {
				$mode = 1;
			} elseif ($b[$i+1] == 0x28 && $b[$i+2] == 0x49) {
				$mode = 2;
			} else {
				$mode = 0;
			}
			$i += 3;
			if (!isset($b[$i])) break 2;
		}

		if ($mode == 1) { //Zenkaku
			$jis = ($b[$i] << 8) + $b[++$i];
			if (isset($table_jis_utf8[$jis])) {
				$utf8 = $table_jis_utf8[$jis];
				if ($utf8 < 0xFFFF) {
					$str_UTF8 .= chr($utf8 >> 8).chr($utf8);
				} else {
					$str_UTF8 .= chr($utf8 >> 16).chr($utf8 >> 8).chr($utf8);
				}
			} else { //Unknown
				$str_UTF8 .= '?';
			}
		} elseif ($mode == 2) { //Hankaku
			$b2 = $b[$i] + 0x40;
			$u2 = 0xBC | (($b2 >> 6) & 0x03);
			$u3 = 0x80 | ($b2 & 0x3F);
			$str_UTF8 .= chr(0xEF).chr($u2).chr($u3);
		} else { //ASCII
			$str_UTF8 .= chr($b[$i]);
		}
	}

	return $str_UTF8;
} /* }}} JIStoUTF8() */

/* {{{ UTF8toSJIS() */
function UTF8toSJIS(&$str_UTF8)
{
	global $table_utf8_jis;

	$str_SJIS = '';
	$b = unpack('C*', $str_UTF8);
	$n = count($b);

	for ($i = 1; $i <= $n; ++$i) {
		if ($b[$i] >= 0x80) { //Not ASCII
			if ($b[$i] <= 0xDF) { //2 Bytes
				$utf8 = ($b[$i] << 8) + $b[++$i];
			} else { //3 Bytes
				$utf8 = ($b[$i] << 16) + ($b[++$i] << 8) + $b[++$i];
			}
			if (isset($table_utf8_jis[$utf8])) {
				$jis = $table_utf8_jis[$utf8];
				if ($jis < 0xFF) { //Hankaku
					$str_SJIS .= chr($jis + 0x80);
				} else { //Zenkaku
					$b1 = $jis >> 8;
					$b2 = $jis & 0xFF;
					if ($b1 & 0x01) {
						$b1 >>= 1;
						if ($b1 < 0x2F) $b1 += 0x71; else $b1 -= 0x4F;
						if ($b2 > 0x5F) $b2 += 0x20; else $b2 += 0x1F;
					} else {
						$b1 >>= 1;
						if ($b1 <= 0x2F) $b1 += 0x70; else $b1 -= 0x50;
						$b2 += 0x7E;
					}
					$str_SJIS .= chr($b1).chr($b2);
				}
			} else { //Unknown
				$str_SJIS .= '?';
			}
		} else { //ASCII
			$str_SJIS .= chr($b[$i]);
		}
	}

	return $str_SJIS;
} /* }}} UTF8toSJIS() */

/* {{{ UTF8toEUC() */
function UTF8toEUC(&$str_UTF8)
{
	global $table_utf8_jis;

	$str_EUC = '';
	$b = unpack('C*', $str_UTF8);
	$n = count($b);

	for ($i = 1; $i <= $n; $i++) {
		if ($b[$i] >= 0x80) { //Not ASCII
			if ($b[$i] <= 0xDF) { //2 Bytes
				$utf8 = ($b[$i++] << 8) + $b[$i];
			} else { //3 Bytes
				$utf8 = ($b[$i++] << 16) + ($b[$i++] << 8) + $b[$i];
			}
			if (isset($table_utf8_jis[$utf8])) {
				$jis = $table_utf8_jis[$utf8];
				if ($jis < 0xFF) { //Hankaku
					$str_EUC .= chr(0x8E).chr($jis - 0x80);
				} else { //Zenkaku
					$str_EUC .= chr(($jis >> 8) - 0x80).chr(($jis & 0xFF) - 0x80);
				}
			} else { //Unknown
				$str_EUC .= '?';
			}
		} else { //ASCII
			$str_EUC .= chr($b[$i]);
		}
	}

	return $str_EUC;
} /* }}} UTF8toEUC() */

/* {{{ UTF8toJIS() */
function UTF8toJIS(&$str_UTF8)
{
	global $table_utf8_jis;

	$str_JIS = '';
	$mode = 0;
	$b = unpack('C*', $str_UTF8);
	$n = count($b);

	//Escape sequence
	$ESC = array(chr(0x1B).chr(0x28).chr(0x42),
		     chr(0x1B).chr(0x24).chr(0x42),
		     chr(0x1B).chr(0x28).chr(0x49));

	for ($i = 1; $i <= $n; ++$i) {
		if ($b[$i] >= 0x80) { //Not ASCII
			if ($b[$i] <= 0xDF) { //2 Bytes
				$utf8 = ($b[$i] << 8) + $b[++$i];
			} else { //3 Bytes
				$utf8 = ($b[$i] << 16) + ($b[++$i] << 8) + $b[++$i];
			}
			if (isset($table_utf8_jis[$utf8])) {
				$jis = $table_utf8_jis[$utf8];
				if ($jis < 0xFF) { //Hankaku
					if ($mode != 2) {
						$mode = 2;
						$str_JIS .= $ESC[$mode];
					}
					$str_JIS .= chr($jis);
				} else { //Zenkaku
					if ($mode != 1) {
						$mode = 1;
						$str_JIS .= $ESC[$mode];
					}
					$str_JIS .= chr($jis >> 8).chr($jis & 0xFF);
				}
			} else { //Unknown
				if ($mode != 0) {
					$mode = 0;
					$str_JIS .= $ESC[$mode];
				}
				$str_JIS .= '?';
			}
		} else { //ASCII
			if ($mode != 0) {
				$mode = 0;
				$str_JIS .= $ESC[$mode];
			}
			$str_JIS .= chr($b[$i]);
		}
	}
	if ($mode != 0) $str_JIS .= $ESC[0];

	return $str_JIS;
} /* }}} UTF8toJIS() */

/* {{{ HANtoZEN_EUC() */
function HANtoZEN_EUC(&$str_HAN)
{
	$table_han2zen_euc = array(0xA1A3,0xA1D6,0xA1D7,0xA1A2,0xA1A6,0xA5F2,
	0xA5A1,0xA5A3,0xA5A5,0xA5A7,0xA5A9,0xA5E3,0xA5E5,0xA5E7,0xA5C3,0xA1BC,
	0xA5A2,0xA5A4,0xA5A6,0xA5A8,0xA5AA,0xA5AB,0xA5AD,0xA5AF,0xA5B1,0xA5B3,
	0xA5B5,0xA5B7,0xA5B9,0xA5BB,0xA5BD,0xA5BF,0xA5C1,0xA5C4,0xA5C6,0xA5C8,
	0xA5CA,0xA5CB,0xA5CC,0xA5CD,0xA5CE,0xA5CF,0xA5D2,0xA5D5,0xA5D8,0xA5DB,
	0xA5DE,0xA5DF,0xA5E0,0xA5E1,0xA5E2,0xA5E4,0xA5E6,0xA5E8,0xA5E9,0xA5EA,
	0xA5EB,0xA5EC,0xA5ED,0xA5EF,0xA5F3,0xA1AB,0xA1AC);

	$str_ZEN = '';
	$b = unpack('C*', $str_HAN);
	$n = count($b);

	for ($i = 1; $i <= $n; ++$i) {
		$b1 = $b[$i];
		if ($b1 == 0x8E) {
			$b2 = $b[++$i];
			$ofs = 0;
			if ((($b2 == 0xB3) || (0xB6 <= $b2 && $b2 <= 0xC4) || (0xCA <= $b2 && $b2 <= 0xCE))
				&& (isset($b[$i+1]) && $b[$i+1] == 0x8E)) {
				// Dakuten
				if ($b[$i+2] == 0xDE) {
					if ($b2 == 0xB3) $ofs = 78; else $ofs = 1;
					$i += 2;
				// Han-Dakuten
				} elseif (($b[$i+2] == 0xDF) && (0xCA <= $b2 && $b2 <= 0xCE)) {
					$ofs = 2;
					$i += 2;
				}
			}
			$b2 -= 0xA1;
			$c1 = (($table_han2zen_euc[$b2]) & 0xFF00) >> 8;
			$c2 = (($table_han2zen_euc[$b2]) & 0x00FF) + $ofs;
			$str_ZEN .= chr($c1).chr($c2);
		} elseif ($b1 >= 0xA1) {
			$str_ZEN .= chr($b1).chr($b[++$i]);
		} else {
			$str_ZEN .= chr($b1);
		}
	}

	return $str_ZEN;
} /* }}} HANtoZEN_EUC() */

/* {{{ HANtoZEN_SJIS() */
function HANtoZEN_SJIS(&$str_HAN)
{
	$table_han2zen_sjis = array(0x8142,0x8175,0x8176,0x8141,0x8145,0x8392,
	0x8340,0x8342,0x8344,0x8346,0x8348,0x8383,0x8385,0x8387,0x8362,0x815B,
	0x8341,0x8343,0x8345,0x8347,0x8349,0x834A,0x834C,0x834E,0x8350,0x8352,
	0x8354,0x8356,0x8358,0x835A,0x835C,0x835E,0x8360,0x8363,0x8365,0x8367,
	0x8369,0x836A,0x836B,0x836C,0x836D,0x836E,0x8371,0x8374,0x8377,0x837A,
	0x837D,0x837E,0x8380,0x8381,0x8382,0x8384,0x8386,0x8388,0x8389,0x838A,
	0x838B,0x838C,0x838D,0x838F,0x8393,0x814A,0x814B);

	$str_ZEN = '';
	$b = unpack('C*', $str_HAN);
	$n = count($b);

	for ($i = 1; $i <= $n; ++$i) {
		$b1 = $b[$i];
		if (0xA1 <= $b1 && $b1 <= 0xDF) {
			$ofs = 0;
			if ((($b1 == 0xB3) || (0xB6 <= $b1 && $b1 <= 0xC4) || (0xCA <= $b1 && $b1 <= 0xCE))
				&& isset($b[$i+1])) {
				// Dakuten
				if ($b[$i+1] == 0xDE) {
					if ($b1 == 0xB3) $ofs = 79; else $ofs = 1;
					++$i;
				// Han-Dakuten
				} elseif (($b[$i+1] == 0xDF) && (0xCA <= $b1 && $b1 <= 0xCE)) {
					$ofs = 2;
					++$i;
				}
			}
			$b1 -= 0xA1;
			$c1 = (($table_han2zen_sjis[$b1]) & 0xFF00) >> 8;
			$c2 = (($table_han2zen_sjis[$b1]) & 0x00FF) + $ofs;
			$str_ZEN .= chr($c1).chr($c2);
		} elseif ($b1 >= 0x80) {
			$str_ZEN .= chr($b1).chr($b[++$i]);
		} else {
			$str_ZEN .= chr($b1);
		}
	}

	return $str_ZEN;
} /* }}} HANtoZEN_SJIS() */

/* {{{ HANtoZEN_JIS() */
function HANtoZEN_JIS(&$str_HAN)
{
	$table_han2zen_jis = array(0x2123,0x2156,0x2157,0x2122,0x2126,0x2572,
	0x2521,0x2523,0x2525,0x2527,0x2529,0x2563,0x2565,0x2567,0x2543,0x213C,
	0x2522,0x2524,0x2526,0x2528,0x252A,0x252B,0x252D,0x252F,0x2531,0x2533,
	0x2535,0x2537,0x2539,0x253B,0x253D,0x253F,0x2541,0x2544,0x2546,0x2548,
	0x254A,0x254B,0x254C,0x254D,0x254E,0x254F,0x2552,0x2555,0x2558,0x255B,
	0x255E,0x255F,0x2560,0x2561,0x2562,0x2564,0x2566,0x2568,0x2569,0x256A,
	0x256B,0x256C,0x256D,0x256F,0x2573,0x212B,0x212C);

	$str_ZEN = '';
	$b = unpack('C*', $str_HAN);
	$n = count($b);
	$mode = 0;
	$new_mode = 0;
	$esc = FALSE;
	$ESC = array(chr(0x1B).chr(0x28).chr(0x42),
		     chr(0x1B).chr(0x24).chr(0x42),
		     chr(0x1B).chr(0x28).chr(0x49));

	for ($i = 1; $i <= $n; ++$i) {

		while ($b[$i] == 0x1B) {
			if (($b[$i+1] == 0x24 && $b[$i+2] == 0x42)
				|| ($b[$i+1] == 0x24 && $b[$i+2] == 0x40)) {
				$mode = 1; //Zenkaku
			} elseif ($b[$i+1] == 0x28 && $b[$i+2] == 0x49) {
				$mode = 2; //Hankaku
			} else {
				$mode = 0; //ASCII
			}
			$i += 3;
			if (!isset($b[$i])) break 2;
		}

		if ($mode == 2) {
			if ($new_mode != 1) $esc = TRUE;
			$new_mode = 1;
			$b1  = $b[$i];
			$ofs = 0;
			if ((($b1 == 0x33) || (0x36 <= $b1 && $b1 <= 0x44) || (0x4A <= $b1 && $b1 <= 0x4E))
				&& isset($b[$i+1])) {
				// Dakuten
				if ($b[$i+1] == 0x5E) {
					if ($b1 == 0x33) $ofs = 78; else $ofs = 1;
					++$i;
				// Han-Dakuten
				} elseif (($b[$i+1] == 0x5F) && (0x4A <= $b1 && $b1 <= 0x4E) ) {
					$ofs = 2;
					++$i;
				}
			}
			$b1 -= 0x21;
			$c1 = ($table_han2zen_jis[$b1] & 0xFF00) >> 8;
			$c2 = ($table_han2zen_jis[$b1] & 0x00FF) + $ofs;
			$str = chr($c1).chr($c2);
		} else {
			if ($new_mode != $mode) $esc = TRUE;
			$new_mode = $mode;
			$str = chr($b[$i]);
		}

		if ($esc) {  //add escape sequence
			$str_ZEN .= $ESC[$new_mode];
			$esc = FALSE;
		}
		$str_ZEN .= $str;
	}

	if ($new_mode != 0) $str_ZEN .= $ESC[0];

	return $str_ZEN;
} /* }}} HANtoZEN_JIS() */

/* {{{ HANtoZEN_UTF8() */
function HANtoZEN_UTF8(&$str_HAN)
{
	$table_han2zen_utf8_1 = array(0xE38082,0xE3808C,0xE3808D,0xE38081,0xE383BB,
	0xE383B2,0xE382A1,0xE382A3,0xE382A5,0xE382A7,0xE382A9,0xE383A3,0xE383A5,
	0xE383A7,0xE38383,0xE383BC,0xE382A2,0xE382A4,0xE382A6,0xE382A8,0xE382AA,
	0xE382AB,0xE382AD,0xE382AF,0xE382B1,0xE382B3,0xE382B5,0xE382B7,0xE382B9,
	0xE382BB,0xE382BD);

	$table_han2zen_utf8_2 = array(0xE382BF,0xE38381,0xE38384,0xE38386,0xE38388,
	0xE3838A,0xE3838B,0xE3838C,0xE3838D,0xE3838E,0xE3838F,0xE38392,0xE38395,
	0xE38398,0xE3839B,0xE3839E,0xE3839F,0xE383A0,0xE383A1,0xE383A2,0xE383A4,
	0xE383A6,0xE383A8,0xE383A9,0xE383AA,0xE383AB,0xE383AC,0xE383AD,0xE383AF,
	0xE383B3,0xE3829B,0xE3829C);

	$str_ZEN = '';
	$b = unpack('C*', $str_HAN);
	$n = count($b);

	for ($i = 1; $i <= $n; ++$i) {
		if ($b[$i] >= 0x80) {
			if (($b[$i] & 0xE0) == 0xC0) {
				$str_ZEN .= chr($b[$i]).chr($b[++$i]);
			} elseif (($b[$i] & 0xF0) == 0xE0) {
				if ($b[$i+1] == 0xBD && (0xA1 <= $b[$i+2] && $b[$i+2] <= 0xBF)) {
					$zen = $table_han2zen_utf8_1[$b[$i+2] - 0xA1];
					$b[$i]   = ($zen & 0xFF0000) >> 16;
					$b[$i+1] = ($zen & 0x00FF00) >> 8;
					$b[$i+2] =  $zen & 0x0000FF;
				} elseif ($b[$i+1] == 0xBE && (0x80 <= $b[$i+2] && $b[$i+2] <= 0x9F)) {
					$zen = $table_han2zen_utf8_2[$b[$i+2] - 0x80];
					$b[$i]   = ($zen & 0xFF0000) >> 16;
					$b[$i+1] = ($zen & 0x00FF00) >> 8;
					$b[$i+2] =  $zen & 0x0000FF;
				}
				$str_ZEN .= chr($b[$i]).chr($b[++$i]).chr($b[++$i]);
			}
		} else {
			$str_ZEN .= chr($b[$i]);
		}
	}

	return $str_ZEN;
} /* }}} HANtoZEN_UTF8() */

/* {{{ ZENtoHAN_EUC() */
function ZENtoHAN_EUC(&$str_ZEN, $kana = 1, $alph = 1, $sym = 1)
{
	$kana_euc = array(
	0x00A7,0x00B1,0x00A8,0x00B2,0x00A9,0x00B3,0x00AA,0x00B4,0x00AB,0x00B5,
	0x00B6,0xB6DE,0x00B7,0xB7DE,0x00B8,0xB8DE,0x00B9,0xB9DE,0x00BA,0xBADE,
	0x00BB,0xBBDE,0x00BC,0xBCDE,0x00BD,0xBDDE,0x00BE,0xBEDE,0x00BF,0xBFDE,
	0x00C0,0xC0DE,0x00C1,0xC1DE,0x00AF,0x00C2,0xC2DE,0x00C3,0xC3DE,0x00C4,
	0xC4DE,0x00C5,0x00C6,0x00C7,0x00C8,0x00C9,0x00CA,0xCADE,0xCADF,0x00CB,
	0xCBDE,0xCBDF,0x00CC,0xCCDE,0xCCDF,0x00CD,0xCDDE,0xCDDF,0x00CE,0xCEDE,
	0xCEDF,0x00CF,0x00D0,0x00D1,0x00D2,0x00D3,0x00AC,0x00D4,0x00AD,0x00D5,
	0x00AE,0x00D6,0x00D7,0x00D8,0x00D9,0x00DA,0x00DB,0x0000,0x00DC,0x0000,
	0x0000,0x00A6,0x00DD,0xB3DE,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000
	);

	$sym_euc = array(
	0x0020,0x8EA4,0x8EA1,0x0000,0x0000,0x8EA5,0x0000,0x0000,0x0000,0x0000,
	0x8EDE,0x8EDF,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x8EB0,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x8EA2,0x8EA3,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000
	);

	$str_HAN = '';
	$b = unpack('C*', $str_ZEN);
	$n = count($b);

	for ($i = 1; $i <= $n; ++$i) {
		$b1 = $b[$i];
		if ($b1 >= 0x80) {
			++$i;
			if ($kana == 1 && $b1 == 0xA5) { // Katakana
				$c  = $b[$i] - 0xA1;
				$c1 = ($kana_euc[$c] & 0xFF00) >> 8;
				$c2 =  $kana_euc[$c] & 0x00FF;
				if ($c1 == 0x00) {
					if ($c2 == 0x00) {
						$str_HAN .= chr($b1).chr($b[$i]);
					} else {
						$str_HAN .= chr(0x8E).chr($c2);
					}
				} else {
					$str_HAN .= chr(0x8E).chr($c1).chr(0x8E).chr($c2);
				}
			} elseif ($sym == 1 && $b1 == 0xA1) { // Symbol
				$c = $b[$i] - 0xA1;
				$c1 = ($sym_euc[$c] & 0xFF00) >> 8;
				$c2 =  $sym_euc[$c] & 0x00FF;
				if ($c1 == 0x00) {
					if ($c2 == 0x00) {
						$str_HAN .= chr($b1).chr($b[$i]);
					} else {
						$str_HAN .= chr($c2);
					}
				} else {
					$str_HAN .= chr($c1).chr($c2);
				}
			} elseif ( $alph == 1 && $b1 == 0xA3 ) { // Alphabet & Number
				$str_HAN .= chr($b[$i] - 0x80);
			} else { // Rest of Zenkaku
				$str_HAN .= chr($b1).chr($b[$i]);
			}
		} else {  // ASCII
			$str_HAN .= chr($b1);
		}
	}

	return $str_HAN;
} /* }}} ZENtoHAN_EUC() */

/* {{{ ZENtoHAN_SJIS() */
function ZENtoHAN_SJIS(&$str_ZEN, $kana = 1, $alph = 1, $sym = 1)
{
	$kana_sjis = array(
	0x00A7,0x00B1,0x00A8,0x00B2,0x00A9,0x00B3,0x00AA,0x00B4,0x00AB,0x00B5,
	0x00B6,0xB6DE,0x00B7,0xB7DE,0x00B8,0xB8DE,0x00B9,0xB9DE,0x00BA,0xBADE,
	0x00BB,0xBBDE,0x00BC,0xBCDE,0x00BD,0xBDDE,0x00BE,0xBEDE,0x00BF,0xBFDE,
	0x00C0,0xC0DE,0x00C1,0xC1DE,0x00AF,0x00C2,0xC2DE,0x00C3,0xC3DE,0x00C4,
	0xC4DE,0x00C5,0x00C6,0x00C7,0x00C8,0x00C9,0x00CA,0xCADE,0xCADF,0x00CB,
	0xCBDE,0xCBDF,0x00CC,0xCCDE,0xCCDF,0x00CD,0xCDDE,0xCDDF,0x00CE,0xCEDE,
	0xCEDF,0x00CF,0x00D0,0x0000,0x00D1,0x00D2,0x00D3,0x00AC,0x00D4,0x00AD,
	0x00D5,0x00AE,0x00D6,0x00D7,0x00D8,0x00D9,0x00DA,0x00DB,0x0000,0x00DC,
	0x0000,0x0000,0x00A6,0x00DD,0xB3DE,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000
	);

	$sym_sjis = array(
	0x20,0xA4,0xA1,0x00,0x00,0xA5,0x00,0x00,0x00,0x00,0xDE,0xDF,0x00,0x00,
	0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0xB0,
	0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,
	0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0xA2,0xA3,0x00,
	0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,
	0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,
	0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00
	);

	$str_HAN = '';
	$b = unpack('C*', $str_ZEN);
	$n = count($b);

	for ($i = 1; $i <= $n; ++$i) {

		$b1 = $b[$i];

		// ASCII or Hankaku
		if (($b1 < 0x81) || (0xA0 < $b1 && $b1 < 0xE0)) {
			// Do not convert
			$str_HAN .= chr($b1);
			continue;
		}

		//---------------------
		// Handle 2 bytes char
		//---------------------

		$b2 = $b[++$i];

		// Katakana
		if ($kana == 1 && $b1 == 0x83
			&& (0x3F < $b2 && $b2 < 0x9F)) {
			$c = $b2 - 0x40;
			$c1 = ($kana_sjis[$c] & 0xFF00) >> 8;
			$c2 =  $kana_sjis[$c] & 0x00FF;
			if ($c1 == 0x00) {
				if ($c2 == 0x00) {
					$str_HAN .= chr($b1).chr($b2);
				} else {
					$str_HAN .= chr($c2);
				}
			} else {
				$str_HAN .= chr($c1).chr($c2);
			}
			continue;
		}

		// Symbol
		if ($sym == 1 && $b1 == 0x81
			&& (0x3F < $b2 && $b2 < 0x9F)) {
			$c1 = $sym_sjis[ $b2 - 0x40 ];
			if ($c1 == 0x00) {
				$str_HAN .= chr($b1).chr($b2);
			} else {
				$str_HAN .= chr($c1);
			}
			continue;
		}

		// Alphabet & Number
		if ($alph == 1 && $b1 == 0x82
			&& (0x3F < $b2 && $b2 < 0x9F)) {
			if ($b2 < 0x80) {
				$str_HAN .= chr($b2 - 0x1F);
			} else {
				$str_HAN .= chr($b2 - 0x20);
			}
			continue;
		}

		// Rest of Zenkaku
		$str_HAN .= chr($b1).chr($b2);
	}

	return $str_HAN;
} /* }}} ZENtoHAN_SJIS() */

/* {{{ ZENtoHAN_JIS() */
function ZENtoHAN_JIS(&$str_ZEN, $kana = 1, $alph = 1, $sym = 1)
{
	$kana_jis = array(
	0x0027,0x0031,0x0028,0x0032,0x0029,0x0033,0x002A,0x0034,0x002B,0x0035,
	0x0036,0x365E,0x0037,0x375E,0x0038,0x385E,0x0039,0x395E,0x003A,0x3A5E,
	0x003B,0x3B5E,0x003C,0x3C5E,0x003D,0x3D5E,0x003E,0x3E5E,0x003F,0x3F5E,
	0x0040,0x405E,0x0041,0x415E,0x002F,0x0042,0x425E,0x0043,0x435E,0x0044,
	0x445E,0x0045,0x0046,0x0047,0x0048,0x0049,0x004A,0x4A5E,0x4A5F,0x004B,
	0x4B5E,0x4B5F,0x004C,0x4C5E,0x4C5F,0x004D,0x4D5E,0x4D5F,0x004E,0x4E5E,
	0x4E5F,0x004F,0x0050,0x0051,0x0052,0x0053,0x002C,0x0054,0x002D,0x0055,
	0x002E,0x0056,0x0057,0x0058,0x0059,0x005A,0x005B,0x0000,0x005C,0x0000,
	0x0000,0x0026,0x005D,0x335E,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000
	);

	$sym_jis = array(
	0x0020,0xFF24,0xFF21,0x0000,0x0000,0xFF25,0x0000,0x0000,0x0000,0x0000,
	0xFF5E,0xFF5F,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0xFF30,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0xFF22,0xFF23,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,0x0000,
	0x0000,0x0000,0x0000,0x0000,0x0000
	);

	$str_HAN = '';
	$b = unpack('C*', $str_ZEN);
	$n = count($b);
	$mode = 0;
	$new_mode = 0;
	$esc = FALSE;
	$ESC = array(chr(0x1B).chr(0x28).chr(0x42),
		     chr(0x1B).chr(0x24).chr(0x42),
		     chr(0x1B).chr(0x28).chr(0x49));

	for ($i = 1; $i <= $n; ++$i) {
		while ($b[$i] == 0x1B) {
			if (($b[$i+1] == 0x24 && $b[$i+2] == 0x42)
				|| ($b[$i+1] == 0x24 && $b[$i+2] == 0x40)) {
				$mode = 1;
			} elseif ($b[$i+1] == 0x28 && $b[$i+2] == 0x49) {
				$mode = 2;
			} else {
				$mode = 0;
			}
			$i += 3;
			if (!isset($b[$i])) break 2;
		}

		$b1 = $b[$i];
		if ($mode == 1) { //Zenkaku
			++$i;
			if ($alph == 1 && $b1 == 0x23) {  //Alphabet & Number
				if ($new_mode != 0) $esc = TRUE;
				$new_mode = 0;
				$str = chr($b[$i]);
			} elseif ($sym == 1 && $b1 == 0x21) {  //Symbol
				$c = $b[$i] - 0x21;
				$c1 = ($sym_jis[$c] & 0xFF00) >> 8;
				$c2 =  $sym_jis[$c] & 0x00FF;
				if ($c1 == 0x00) {
					if ($c2 == 0x00) {
						if ($new_mode != 1) $esc = TRUE;
						$new_mode = 1;
						$str = chr($b1).chr($b[$i]);
					} else {
						if ($new_mode != 0) $esc = true;
						$new_mode = 0;
						$str = chr($c2);
					}
				} else {
					if ($new_mode != 2) $esc = TRUE;
					$new_mode = 2;
					$str = chr($c2);
				}
			} elseif ($kana == 1 && $b1 == 0x25) {  //Katakana
				$c  = $b[$i] - 0x21;
				$c1 = ($kana_jis[$c] & 0xFF00) >> 8;
				$c2 =  $kana_jis[$c] & 0x00FF;
				if ($c1 == 0x00) {
					if ($c2 == 0x00) {
						if ($new_mode != 1) $esc = TRUE;
						$new_mode = 1;
						$str = chr($b1).chr($b[$i]);
					} else {
						if ($new_mode != 2) $esc = TRUE;
						$new_mode = 2;
						$str = chr($c2);
					}
				} else {
					if ($new_mode != 2) $esc = TRUE;
					$new_mode = 2;
					$str = chr($c1).chr($c2);
				}
			} else {
				if ($new_mode != 1) $esc = TRUE;
				$new_mode = 1;
				$str = chr($b1).chr($b[$i]);
			}
		} elseif ($mode == 2) {
			if ($new_mode != 2) $esc = TRUE;
			$new_mode = 2;
			$str = chr($b1);
		} else {
			if ($new_mode != 0) $esc = TRUE;
			$new_mode = 0;
			$str = chr($b1);
		}

		if ($esc) {  //add escape sequense
			$str_HAN .= $ESC[$new_mode];
			$esc = FALSE;
		}
		$str_HAN .= $str;
	}

	if ($new_mode != 0) $str_HAN .= $ESC[0];

	return $str_HAN;
} /* }}} ZENtoHAN_JIS() */

/* {{{ jsubstr() */
/*
    O-MA-KE No.1
    jsubstr() - substr() function for japanese(euc-jp)
    for using shift_jis encoding, remove comment string.
*/
function jsubstr($str, $start = 0, $length = 0)
{
	$b = unpack('C*', $str);
	$m = count($b);

	for ($i = 1; $i <= $m; ++$i) {
		if ($b[$i] >= 0x80) {  //Japanese
//			if ( 0xA0 < $b[$i] && $b[$i] < 0xE0 ) {  //SJIS Hankaku
//				$jstr[] = chr($b[$i]);
//			} else {
				$jstr[] = chr($b[$i]).chr($b[++$i]);
//			}
		} else {  //ASCII
			$jstr[] = chr($b[$i]);
		}
	}
	if (!isset($jstr)) $jstr[] = '';

	$n = count($jstr);
	if ($start < 0) $start += $n;
	if ($length < 0) $end = $n + $length; else $end = $start + $length;
	if ($end > $n) $end = $n;

	$s = '';
	for ($j = $start; $j < $end; ++$j) $s .= $jstr[$j];

	return $s;
} /* }}} jsubstr() */

/* {{{ jstrlen() */
/*
    O-MA-KE No.2
    jstrlen() - strlen() function for japanese(euc-jp)
    for using shift_jis encoding, remove comment string.
*/
function jstrlen($str)
{
	$b = unpack('C*', $str);
	$n = count($b);
	$l = 0;

	for ($i = 1; $i <= $n; ++$i) {
		if ($b[$i] >= 0x80
//			&& ($b[$i] <= 0xA0 || $b[$i] >= 0xE0)  //exclude SJIS Hankaku
		) {
			++$i;
		}
		++$l;
	}

	return $l;
} /* }}} jstrlen() */

/* {{{ jstr_replace() */
/*
    O-MA-KE No.3
    jstr_replace() - str_replace() function for japanese(euc-jp)
    for using shift_jis encoding, remove comment string.
*/
function jstr_replace($before, $after, $str)
{
	$b = unpack('C*', $str);
	$n = strlen($str);
	$l = strlen($before);
	if ($l < 1) return $str;
	$s = '';
	$i = 1;

	while($i <= $n) {
		for ($j = $i; $j < $i + $l; ++$j) {
			if ($b[$j] >= 0x80) {  //Japanese
//				if ( 0xA0 < $b[$j] && $b[$j] < 0xE0 ) {  //SJIS Hankaku
//					$c[] = chr($b[$j]);
//				} else {
					$c[] = chr($b[$j]).chr($b[++$j]);
//				}
			} else {  //ASCII
				$c[] = chr($b[$j]);
			}
			if (!isset($b[$j+1])) break;
		}
		if ($before == implode('', $c)) {
			$s .= $after;  //replace
			$i += $l;
		} else {
			$s .= $c[0];
			$i += strlen($c[0]);
		}
		unset($c);
	}

	return $s;
} /* }}} jstr_replace() */

/* {{{ jchunk_split() */
/*
    O-MA-KE No.4
    jchunk_split() - This function is similar to chunk_split() 
    and is designed for euc-jp encoding.
*/
function jchunk_split($str, $width = 76, $end = "\r\n")
{
	if ($width < 1) return '';

	$b = unpack('C*', $str);
	$n = count($b);
	$s = '';
	$l = 0;

	for ($i = 1; $i <= $n; ++$i) {

		if ($b[$i] >= 0x80) {  // 8bit (Japanese)
//			if ( 0xA0 < $b[$i] && $b[$i] < 0xE0 ) {  // SJIS Hankaku
//				$c = chr($b[$i]);
//				$w = 1;
//			} else {
				if ($b[$i] == 0x8E) {  // EUC-JP Hankaku
					$w = 1;
				} else {  // Zenkaku
					$w = 2;
				}
				$c = chr($b[$i]).chr($b[++$i]);
//			}
		} else {  // 7bit (ASCII)
			$w = 1;
			$c = chr($b[$i]);
		}

		if (($l += $w) > $width) {
			$l  = $w;
			$s .= $end;
		}

		$s .= $c;
	}

	return $s;
} /* }}} jchunk_split() */

/* {{{ jstrcut() */
/*
    O-MA-KE No.5
    jstrcut() - This function is similar to mb_strcut() and substr(),
    and is designed for euc-jp encoding.
*/
function jstrcut($str, $start, $len = 0)
{
	$b = unpack('C*', $str);
	$n = count($b);
	$s = '';
	$c = '';
	$l = 0;

	if ($start < 0) $start += $n;
	if ($start < 0) $start = 0;

	if ($len == 0) $len = $n;
	if ($len < 0)  $len += ($n - $start);
	if ($len > $n) $len = $n;
	if ($len < 1)  return '';

	for ($i = 1; $i <= $n; ++$i) {

		if ($b[$i] >= 0x80) {  // 8bit (Japanese)
//			if ( 0xA0 < $b[$i] && $b[$i] < 0xE0 ) {  // SJIS Hankaku
//				$c = chr($b[$i]);
//				$w = 1;
//			} else {
				if ($b[$i] == 0x8E) {  // EUC-JP Hankaku
					$w = 1;
				} else {  // Zenkaku
					$w = 2;
				}
				$c = chr($b[$i]).chr($b[++$i]);
//			}
		} else {  // 7bit (ASCII)
			$w = 1;
			$c = chr($b[$i]);
		}

		if ($i > $start) {
			$l += $w;
			if ($l > $len) break;
			$s .= $c;
		}

	}

	return $s;
} /* }}} jstrcut() */

?>
