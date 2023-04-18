<?php

/**
 * ファイルサイズを人が分かる形式に変換する
 *
 * 基本的な使い方:
 *
 * <{$file_size|human_file_size}>
 *
 * 小数点以下を指定する:
 *
 * <{$file_size|human_file_size:1}> 小数点以下1桁まで表示
 *
 * 国際単位系(SI)として計算する:
 *
 * <{$file_size|human_file_size:2:"si"}>
 *
 * "si"を指定した場合は 1KB = 1000 バイトとして扱います。
 * デフォルトは2進接頭辞です。これは 1KB = 1024 バイトとして処理します。
 *
 * @param int    $bytes
 * @param int    $precision
 * @param string $type
 * @return string
 */
function smarty_modifier_human_file_size($bytes, $precision = 2, $type = 'bin')
{
	// 言語定数があれば言語定数を利用する
	$units = [defined('_HUMAN_FILE_SIZE_BYTES') ? _HUMAN_FILE_SIZE_BYTES : 'Bytes', defined('_HUMAN_FILE_SIZE_KB')    ? _HUMAN_FILE_SIZE_KB    : 'KB', defined('_HUMAN_FILE_SIZE_MB')    ? _HUMAN_FILE_SIZE_MB    : 'MB', defined('_HUMAN_FILE_SIZE_GB')    ? _HUMAN_FILE_SIZE_GB    : 'GB', defined('_HUMAN_FILE_SIZE_TB')    ? _HUMAN_FILE_SIZE_TB    : 'TB', defined('_HUMAN_FILE_SIZE_PB')    ? _HUMAN_FILE_SIZE_PB    : 'PB', defined('_HUMAN_FILE_SIZE_EB')    ? _HUMAN_FILE_SIZE_EB    : 'EB', defined('_HUMAN_FILE_SIZE_ZB')    ? _HUMAN_FILE_SIZE_ZB    : 'ZB', defined('_HUMAN_FILE_SIZE_YB')    ? _HUMAN_FILE_SIZE_YB    : 'YB'];

	if ( abs($bytes) < 1024 )
	{
		$precision = 0;
	}

	if ( $bytes < 0 )
	{
		$sign = '-';
		$bytes = abs($bytes);
	}
	else
	{
		$sign = '';
	}

	if ( strtolower($type) == 'si' )
	{
		$log = 1000;
	}
	else
	{
		$log = 1024;
	}

	$exp   = intval(log($bytes) / log($log));
	$unit  = $units[$exp];
	$bytes = $bytes / $log ** floor($exp);
	$bytes = sprintf('%.'.$precision.'f', $bytes);
	return $sign.$bytes.' '.$unit;
}

