<?php

$xoopsOption['nocommon'] = 1 ;
define('_LEGACY_PREVENT_LOAD_CORE_',1) ;

include '../../mainfile.php' ;

if( defined( 'XOOPS_TRUST_PATH' ) && XOOPS_TRUST_PATH != '' && file_exists( XOOPS_TRUST_PATH.'/libs/altsys' ) ) {
	die( 'No problem with your XOOPS_TRUST_PATH' ) ;
}


// show the hint if password mathes
$hint = '' ;
if( @$_POST['dbpassword'] == XOOPS_DB_PASS ) {
	// find XOOPS_TRUST_PATH
	$xoops_trust_path = '' ;
	$base_dirs = array( XOOPS_ROOT_PATH , dirname(XOOPS_ROOT_PATH) , dirname(dirname(XOOPS_ROOT_PATH )) ) ;
	foreach( $base_dirs as $base_dir ) {
		$dh = @opendir( $base_dir ) ;
		if( ! empty( $dh ) ) {
			while( ( $file = readdir( $dh ) ) !== false ) {
				if( substr( $file , 0 , 1 ) == '.' ) continue ;
				$fullpath = $base_dir . '/' . $file ;
				if( ! is_dir( $fullpath ) ) continue ;
				if( is_dir( $fullpath.'/libs/altsys' ) ) {
					$xoops_trust_path = $fullpath ;
					break 2 ;
				}
			}
		}
	}
	// fall back
	if( empty( $xoops_trust_path ) ) {
		$xoops_trust_path = dirname(XOOPS_ROOT_PATH).'/xoops_trust_path' ;
	}
	// create the hint
	if( ! defined( 'XOOPS_TRUST_PATH' ) ) {
		$hint = "Insert the red line.<br />define('XOOPS_ROOT_PATH', '".htmlspecialchars(XOOPS_ROOT_PATH,ENT_QUOTES)."');<br /><ins style='color:red;'>define('XOOPS_TRUST_PATH', '".htmlspecialchars($xoops_trust_path,ENT_QUOTES)."');</ins>";
	} else {
		$hint = "<del>define('XOOPS_TRUST_PATH', '');</del><br /><ins style='color:red;'>define('XOOPS_TRUST_PATH', '".htmlspecialchars($xoops_trust_path,ENT_QUOTES)."');</ins>";
	}
}





// default form
?>
<html>
<head>
<title>set up XOOPS_TRUST_PATH</title>
</head>
<body>
<h1>Set up XOOPS_TRUST_PATH</h1>
<p>You missed inserting a line defining XOOPS_TRUST_PATH in mainfile.php</p>
<p>Insert it by yourself, or follow the procedures</p>
<form action="" method="post">
	Your MySQL Password:<input type="password" name="dbpassword" size="16" />
	<input type="submit" value="next" />
</form>
<?php if($hint) echo 'Edit mainfile.php like this: <blockquote style="border: black solid 1px;">'.$hint.'</blockquote>' ?>
</body>
</html>
