<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bluemooninc
 * Date: 2013/02/14
 * Time: 14:25
 * To change this template use File | Settings | File Templates.
 */
function smarty_modifier_backtime($timestamp,$format,$newtime=null)
{
	$new = ($newtime)? $newtime : time();
	$tt = $new - $timestamp;

	// 渡された時間と、現在の時間の差分を計算
	if( $tt >= 0 && $tt < 10  ){   // 0秒～10秒
		$result = sprintf("たった今");
	}elseif( $tt >= 10 && $tt < 60 ){ // 10秒～60秒
		$result = sprintf("%d秒前",$tt);
	}elseif( $tt >= 60 && $tt < 3600 ){  // 60秒～1時間
		$result = sprintf("%d分前",$tt/60);
	}elseif( $tt >= 3600 && $tt < 86400 ){  // 1時間～24時間
		$result = sprintf("%d時間前",$tt/60/60);
	}elseif( $tt >= 86400 && $tt < 864000 ){  // 24時間～10日
		$result = sprintf("%d日前",$tt/60/60/24);
	}else{
		$result = strftime ( $format, xoops_getUserTimestamp ( $timestamp ) );
	}

	return $result;
}