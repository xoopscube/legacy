<?php

include dirname(dirname(__FILE__)).'/include/common_prepend.inc.php' ;

$lid = empty( $_GET['lid'] ) ? 0 : intval( $_GET['lid'] ) ;


if( $lid > 0 ){
	include "item.php" ;
} else {
	if($gnavi_indexpage=='map'){
		include "map.php" ;
	}else{
		include "category.php" ;
	}
}

?>